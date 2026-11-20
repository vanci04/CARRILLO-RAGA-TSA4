<!doctype html>
<html lang="en"><body style="font-family:Arial,sans-serif;color:#33251d">
<h1 style="font-size:22px">Task completed</h1>
<p><strong>{{ $task->title }}</strong> was marked done.</p>
<p>Scheduled date: {{ $task->task_date->format('F j, Y') }}</p>
<p>Ember Daily recorded this update at {{ $task->updated_at->format('F j, Y g:i A') }}.</p>
</body></html>
