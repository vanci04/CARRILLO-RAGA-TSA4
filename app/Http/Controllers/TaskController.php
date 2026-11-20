<?php

namespace App\Http\Controllers;

use App\Mail\TaskCompleted;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function today()
    {
        $tasks = Task::active()->forToday()->chronological()->get();

        return view('today', compact('tasks'));
    }

    public function index()
    {
        // The full list deliberately has no date or status filter.
        $tasks = Task::active()->chronological()->get();

        return view('tasks.index', compact('tasks'));
    }

    public function create(Request $request)
    {
        $returnTo = $request->query('from') === 'today' ? 'today' : 'tasks';

        return view('tasks.form', ['task' => new Task(['task_date' => today(), 'status' => 'pending']), 'returnTo' => $returnTo]);
    }

    public function store(Request $request)
    {
        Task::create($this->validatedTask($request));

        return redirect()->route($request->input('return_to') === 'today' ? 'today' : 'tasks.index')->with('success', 'A little progress starts here. Task added.');
    }

    public function edit(Request $request, Task $task)
    {
        abort_if($task->is_archived, 404);

        return view('tasks.form', ['task' => $task, 'returnTo' => $request->query('from') === 'today' ? 'today' : 'tasks']);
    }

    public function update(Request $request, Task $task)
    {
        abort_if($task->is_archived, 404);
        $wasDone = $task->status === 'done';
        $task->update($this->validatedTask($request));
        if (! $wasDone && $task->status === 'done') {
            $this->sendCompletionNotification($task);
        }

        return redirect()->route($request->input('return_to') === 'today' ? 'today' : 'tasks.index')->with('success', 'Task updated. You’re all set.');
    }

    public function status(Request $request, Task $task)
    {
        abort_if($task->is_archived, 404);
        $data = $request->validate(['status' => ['required', Rule::in(['pending', 'done'])], 'return_to' => ['nullable', Rule::in(['today', 'tasks'])]]);
        $wasDone = $task->status === 'done';
        $task->update(['status' => $data['status']]);
        if (! $wasDone && $task->status === 'done') {
            $this->sendCompletionNotification($task);
        }

        return redirect()->route(($data['return_to'] ?? '') === 'today' ? 'today' : 'tasks.index')->with('success', $data['status'] === 'done' ? 'One more thing done. Notification sent.' : 'Task moved back to pending.');
    }

    public function destroy(Request $request, Task $task)
    {
        $request->validate(['return_to' => ['nullable', Rule::in(['today', 'tasks'])]]);
        $task->update(['is_archived' => true]);

        return redirect()->route($request->input('return_to') === 'today' ? 'today' : 'tasks.index')->with('success', 'Task archived and removed from the active list.');
    }

    private function validatedTask(Request $request): array
    {
        if (is_string($request->input('title'))) {
            $request->merge(['title' => trim($request->input('title'))]);
        }

        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'task_date' => ['required', 'date_format:Y-m-d'],
            'status' => ['required', Rule::in(['pending', 'done'])],
        ]);
    }

    private function sendCompletionNotification(Task $task): void
    {
        if ($recipient = config('tasks.notification_recipient')) {
            Mail::to($recipient)->send(new TaskCompleted($task));
        }
    }
}
