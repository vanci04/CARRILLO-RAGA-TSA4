@php($completed = $tasks->where('status', 'done')->count())
<section class="daily-stats" aria-label="Task summary">
    <article><span class="stat-icon stat-all" aria-hidden="true">≡</span><div><span>{{ $scope === 'today' ? 'ON TODAY’S LIST' : 'ALL TASKS' }}</span><strong>{{ $tasks->count() }}<small>{{ $tasks->count() === 1 ? 'task' : 'tasks' }}</small></strong></div><span class="stat-mark" aria-hidden="true">↗</span></article>
    <article><span class="stat-icon stat-pending" aria-hidden="true">◷</span><div><span>STILL BREWING</span><strong>{{ $tasks->count() - $completed }}<small>pending</small></strong></div><span class="stat-mark" aria-hidden="true">·</span></article>
    <article><span class="stat-icon stat-completed" aria-hidden="true">✓</span><div><span>NICELY DONE</span><strong>{{ $completed }}<small>completed</small></strong></div><span class="stat-mark" aria-hidden="true">✳</span></article>
</section>
