@extends('layouts.app')
@section('title', 'Task list')
@section('content')
<div class="page-heading"><div><p class="eyebrow">THE BIGGER PICTURE</p><h1>Every little thing, in its place.</h1><p>Your full café checklist, from yesterday’s plans to tomorrow’s possibilities.</p></div>@auth<a class="button dark" href="{{ route('tasks.create') }}"><span aria-hidden="true">+</span> Add a task</a>@endauth</div>
@include('partials.stats', ['scope' => 'all'])
<section class="all-tasks" aria-labelledby="all-tasks-title"><div class="all-task-heading"><div><h2 id="all-tasks-title">The full task list</h2><p>All dates · Earliest first</p></div>@if($tasks->isNotEmpty())<label class="search"><span aria-hidden="true">⌕</span><input type="search" id="task-search" placeholder="Find a task…" aria-label="Search all tasks"></label>@endif</div>
@forelse($tasks->groupBy(fn($task) => $task->task_date->toDateString()) as $date => $dateTasks)
@php($day = $dateTasks->first()->task_date)
<section class="task-date-group" data-date-group aria-labelledby="date-{{ $date }}"><div class="date-group-heading"><h3 id="date-{{ $date }}"><span class="date-symbol {{ $day->isToday() ? 'date-today' : '' }}" aria-hidden="true">{{ $day->format('d') }}</span><span>{{ $day->isToday() ? 'Today' : ($day->isYesterday() ? 'Yesterday' : ($day->isTomorrow() ? 'Tomorrow' : $day->format('l'))) }}<small>{{ $day->format('l, d F Y') }}</small></span></h3><span>{{ $dateTasks->count() }} {{ $dateTasks->count() === 1 ? 'task' : 'tasks' }}</span></div><div class="task-panel task-date-panel">@foreach($dateTasks as $task)@include('partials.task-row', ['returnTo' => 'tasks'])@endforeach</div></section>
@empty
<div class="task-empty task-panel"><span aria-hidden="true">✳</span><h3>A little room for what’s next.</h3><p>Your task list is empty. Add something you’d like to get done.</p>@auth<a class="button dark" href="{{ route('tasks.create') }}">Add your first task <span aria-hidden="true">+</span></a>@endauth</div>
@endforelse
<p id="no-task-results" class="empty-search" hidden>No tasks match your search. Try a different word.</p>
@if($tasks->isNotEmpty())<div class="all-task-foot"><span id="visible-count">Showing all {{ $tasks->count() }} tasks across {{ $tasks->groupBy(fn($task) => $task->task_date->toDateString())->count() }} dates</span><a href="{{ route('today') }}" class="text-link">Back to today <span aria-hidden="true">↗</span></a></div>@endif
</section>
@endsection
