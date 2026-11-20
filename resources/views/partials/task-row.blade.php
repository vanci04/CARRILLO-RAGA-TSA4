<article class="task-row {{ $task->status === 'done' ? 'task-completed' : '' }}" data-task-row data-title="{{ mb_strtolower($task->title) }}" data-status="{{ $task->status }}">
    @auth
    <form method="post" action="{{ route('tasks.status', $task) }}" class="task-check-form">
        @csrf @method('PATCH')
        <input type="hidden" name="status" value="{{ $task->status === 'done' ? 'pending' : 'done' }}">
        <input type="hidden" name="return_to" value="{{ $returnTo }}">
        <button type="submit" class="task-check" aria-label="{{ $task->status === 'done' ? 'Mark pending: ' : 'Complete: ' }}{{ $task->title }}"><span aria-hidden="true">{{ $task->status === 'done' ? '✓' : '' }}</span></button>
    </form>
    @endauth
    <div class="task-text"><h3>{{ $task->title }}</h3><div><time datetime="{{ $task->task_date->toDateString() }}">{{ $task->task_date->isToday() ? 'Today' : $task->task_date->format('d M Y') }}</time><span class="task-meta-divider">·</span><span>Ember Café</span></div></div>
    <span class="task-status status-{{ $task->status }}">{{ ucfirst($task->status) }}</span>
    @auth<a class="edit-task" href="{{ route('tasks.edit', ['task' => $task, 'from' => $returnTo]) }}" aria-label="Edit task: {{ $task->title }}"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m16 3 5 5-12 12-6 1 1-6Z"/><path d="m13 6 5 5"/></svg></a>@endauth
</article>
