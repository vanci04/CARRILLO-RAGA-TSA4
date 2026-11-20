<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $today = today();
        $tasks = [
            ['title' => 'Receive the weekly coffee bean delivery', 'status' => 'done', 'task_date' => $today->copy()->subDay()],
            ['title' => 'Update the pastry display labels', 'status' => 'done', 'task_date' => $today->copy()->subDay()],
            ['title' => 'Review the milk and syrup inventory', 'status' => 'pending', 'task_date' => $today->copy()->subDay()],
            ['title' => 'Dial in the espresso and taste the first shot', 'status' => 'done', 'task_date' => $today],
            ['title' => 'Set up the pastry counter', 'status' => 'done', 'task_date' => $today],
            ['title' => 'Prepare the cold brew for the afternoon', 'status' => 'pending', 'task_date' => $today],
            ['title' => 'Restock takeaway cups and lids', 'status' => 'pending', 'task_date' => $today],
            ['title' => 'Wipe down the coffee station before closing', 'status' => 'pending', 'task_date' => $today],
            ['title' => 'Plan the weekend coffee special', 'status' => 'pending', 'task_date' => $today->copy()->addDay()],
            ['title' => 'Check the next pastry order with the bakery', 'status' => 'pending', 'task_date' => $today->copy()->addDay()],
        ];
        // Initial sample data only. Reseeding must not reset the user's checklist.
        if (Task::exists()) {
            return;
        }
        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
