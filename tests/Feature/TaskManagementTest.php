<?php

namespace Tests\Feature;

use App\Mail\TaskCompleted;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    private function task(array $attributes = []): Task
    {
        return Task::create(array_merge(['title' => 'A café task', 'status' => 'pending', 'task_date' => today()->toDateString()], $attributes));
    }

    private function user(): User
    {
        return User::factory()->create();
    }

    public function test_public_pages_are_open_and_management_actions_require_login(): void
    {
        foreach (['/', '/tasks', '/profile', '/about'] as $path) {
            $this->get($path)->assertOk();
        }
        $this->get('/tasks/create')->assertRedirect('/login');
        $task = $this->task();
        $this->put('/tasks/'.$task->id, ['title' => 'Blocked', 'task_date' => today()->toDateString(), 'status' => 'pending'])->assertRedirect('/login');
        $this->delete('/tasks/'.$task->id)->assertRedirect('/login');
    }

    public function test_seeded_data_meets_the_assessment_and_renders_required_pages(): void
    {
        $this->seed();
        $this->assertDatabaseCount('users', 1);
        $this->assertGreaterThanOrEqual(8, Task::count());
        $this->assertGreaterThanOrEqual(3, Task::distinct()->count('task_date'));
        $this->assertSame(5, Task::forToday()->count());
        foreach (['/', '/tasks', '/profile', '/about'] as $path) {
            $this->get($path)->assertOk();
        }
        $this->get('/profile')->assertSee('Maverick John Klyde L. Carrillo')->assertSee('Klyde')->assertSee('klyde_carrillo@yahoo.com');
        $this->get('/about')->assertSee('Maverick John Klyde L. Carrillo');
    }

    public function test_welcome_only_contains_today_including_pending_and_completed(): void
    {
        $this->task(['title' => 'Yesterday excluded', 'task_date' => today()->subDay()->toDateString()]);
        $this->task(['title' => 'Today pending']);
        $this->task(['title' => 'Today completed', 'status' => 'done']);
        $this->task(['title' => 'Tomorrow excluded', 'task_date' => today()->addDay()->toDateString()]);
        $this->get('/')->assertOk()->assertSee('Today pending')->assertSee('Today completed')->assertDontSee('Yesterday excluded')->assertDontSee('Tomorrow excluded');
    }

    public function test_today_uses_manila_date_when_utc_is_still_yesterday(): void
    {
        $this->travelTo(Carbon::parse('2026-11-19T16:30:00Z'));
        try {
            $this->assertSame('Asia/Manila', config('app.timezone'));
            $this->task(['title' => 'Manila today', 'task_date' => '2026-11-20']);
            $this->task(['title' => 'UTC yesterday', 'task_date' => '2026-11-19']);
            $this->get('/')->assertSee('Manila today')->assertDontSee('UTC yesterday');
        } finally {
            $this->travelBack();
        }
    }

    public function test_all_tasks_include_all_dates_and_are_ordered_earliest_first(): void
    {
        $this->task(['title' => 'Future task', 'task_date' => '2030-01-03']);
        $this->task(['title' => 'Oldest task', 'task_date' => '2020-01-01']);
        $this->task(['title' => 'Middle task', 'task_date' => '2025-01-02']);
        $this->get('/tasks')->assertOk()->assertSeeInOrder(['Oldest task', 'Middle task', 'Future task']);
    }

    public function test_seeding_again_preserves_progress_and_exactly_one_profile(): void
    {
        $this->seed();
        $task = Task::where('status', 'pending')->firstOrFail();
        $task->update(['status' => 'done', 'title' => 'Edited task survives restart']);
        $this->seed();
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('tasks', 10);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Edited task survives restart', 'status' => 'done']);
    }

    public function test_task_default_status_is_pending_at_database_level(): void
    {
        DB::table('tasks')->insert(['title' => 'Default status', 'task_date' => today()->toDateString()]);
        $this->assertDatabaseHas('tasks', ['title' => 'Default status', 'status' => 'pending']);
    }

    public function test_username_is_unique_at_database_level(): void
    {
        User::create(['username' => 'Klyde', 'full_name' => 'Profile one', 'email' => 'one@example.com', 'password' => 'password']);
        $this->expectException(QueryException::class);
        User::create(['username' => 'Klyde', 'full_name' => 'Profile two', 'email' => 'two@example.com', 'password' => 'password']);
    }

    public function test_create_edit_and_reschedule_are_persisted(): void
    {
        $this->actingAs($this->user());
        $this->get('/tasks/create')->assertOk();
        $this->post('/tasks', ['title' => '  Brew a fresh pot  ', 'task_date' => today()->toDateString(), 'status' => 'pending', 'return_to' => 'today'])->assertRedirect('/');
        $task = Task::firstOrFail();
        $this->assertSame('Brew a fresh pot', $task->title);
        $this->get('/tasks/'.$task->id.'/edit')->assertOk()->assertSee('Brew a fresh pot');
        $tomorrow = today()->addDay()->toDateString();
        $this->put('/tasks/'.$task->id, ['title' => 'Prepare tomorrow’s cold brew', 'task_date' => $tomorrow, 'status' => 'done', 'return_to' => 'tasks'])->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Prepare tomorrow’s cold brew', 'status' => 'done']);
        $this->assertSame($tomorrow, $task->fresh()->task_date->toDateString());
        $this->get('/')->assertDontSee('Prepare tomorrow’s cold brew');
        $this->get('/tasks')->assertSee('Prepare tomorrow’s cold brew');
    }

    public function test_completion_can_be_reopened_and_reflected_in_summary(): void
    {
        Mail::fake();
        $this->actingAs($this->user());
        $task = $this->task(['title' => 'Restock cups']);
        $this->patch('/tasks/'.$task->id.'/status', ['status' => 'done', 'return_to' => 'today'])->assertRedirect('/');
        $this->assertSame('done', $task->fresh()->status);
        Mail::assertSent(TaskCompleted::class, 1);
        $this->patch('/tasks/'.$task->id.'/status', ['status' => 'done', 'return_to' => 'today'])->assertRedirect('/');
        Mail::assertSent(TaskCompleted::class, 1);
        $this->get('/')->assertSee('Everything on today’s list is done.');
        $this->patch('/tasks/'.$task->id.'/status', ['status' => 'pending', 'return_to' => 'tasks'])->assertRedirect('/tasks');
        $this->assertSame('pending', $task->fresh()->status);
    }

    public function test_invalid_inputs_do_not_create_tasks(): void
    {
        $this->actingAs($this->user());
        $valid = ['title' => 'Fresh brew', 'task_date' => '2026-11-20', 'status' => 'pending'];
        foreach ([['title' => '   '], ['title' => str_repeat('x', 256)], ['title' => ['unexpected']], ['task_date' => '2026-02-30'], ['task_date' => 'not-a-date'], ['status' => 'unknown']] as $invalid) {
            $this->postJson('/tasks', array_merge($valid, $invalid))->assertUnprocessable();
        }
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_invalid_update_keeps_original_values(): void
    {
        $this->actingAs($this->user());
        $task = $this->task(['title' => 'Keep the original']);
        $this->putJson('/tasks/'.$task->id, ['title' => '', 'task_date' => 'bad', 'status' => 'bad'])->assertUnprocessable();
        $this->patchJson('/tasks/'.$task->id.'/status', ['status' => 'invalid'])->assertUnprocessable();
        $this->assertSame('Keep the original', $task->fresh()->title);
        $this->assertSame('pending', $task->fresh()->status);
    }

    public function test_removing_task_keeps_other_tasks_and_unknown_ids_return_404(): void
    {
        $this->actingAs($this->user());
        $removed = $this->task(['title' => 'Remove this']);
        $kept = $this->task(['title' => 'Keep this']);
        $this->delete('/tasks/'.$removed->id, ['return_to' => 'tasks'])->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', ['id' => $removed->id, 'is_archived' => true]);
        $this->assertDatabaseHas('tasks', ['id' => $kept->id]);
        $this->get('/tasks')->assertDontSee('data-title="remove this"', false)->assertSee('Keep this');
        $this->get('/tasks/999999/edit')->assertNotFound();
        $this->patch('/tasks/999999/status', ['status' => 'done'])->assertNotFound();
    }

    public function test_task_titles_are_escaped_and_do_not_render_html(): void
    {
        $this->task(['title' => '<script>alert("hello")</script>']);
        $this->get('/')->assertSee('<script>alert("hello")</script>')->assertDontSee('<script>alert("hello")</script>', false);
    }

    public function test_empty_states_are_usable_without_records(): void
    {
        $this->get('/')->assertOk()->assertSee('Your day is a fresh page.');
        $this->get('/tasks')->assertOk()->assertSee('Your task list is empty.');
        $this->get('/profile')->assertOk()->assertSee('Your profile is not ready yet.');
        $this->get('/about')->assertOk()->assertSee('Maverick John Klyde L. Carrillo');
    }
}
