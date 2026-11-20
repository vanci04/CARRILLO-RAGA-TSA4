@extends('layouts.app')
@section('title', 'Today')
@section('content')
@php
    $completed = $tasks->where('status', 'done')->count();
    $percent = $tasks->isEmpty() ? 0 : (int) round($completed / $tasks->count() * 100);
@endphp
<div class="page-heading"><div><p class="eyebrow">{{ today()->format('l, d F Y') }} · A FRESH START</p><h1>A little focus. A good day.</h1><p>Welcome back, {{ config('developer.username') }}. Let’s take today one task at a time.</p></div>@auth<a class="button dark" href="{{ route('tasks.create', ['from' => 'today']) }}"><span aria-hidden="true">+</span> Add a task</a>@endauth</div>
<section class="day-hero"><div class="day-hero-copy"><span class="pill">TODAY AT EMBER</span><h2>Good days are made<br><em>one small task at a time.</em></h2><p>{{ $tasks->isEmpty() ? 'A fresh page and a warm cup. Add your first task for today.' : ($completed === $tasks->count() ? 'Everything on today’s list is done. Take a well-earned coffee break.' : 'A fresh cup, a clear counter, a little progress. You’ve got this.') }}</p><a href="#today-list" class="hero-text-link">{{ $tasks->isEmpty() ? 'Make a little room for today' : 'Let’s make a little progress' }} <span aria-hidden="true">↓</span></a></div><div class="hero-progress"><div class="progress-ring" style="--progress: {{ $percent }}%" role="img" aria-label="{{ $completed }} of {{ $tasks->count() }} tasks completed today, {{ $percent }} percent"><div><span aria-hidden="true">✳</span><strong>{{ $completed }}<small>/{{ $tasks->count() }}</small></strong><span>TASKS COMPLETED</span></div></div><span class="progress-caption">A little closer, every time.</span></div><span class="hero-decoration" aria-hidden="true">✳</span></section>
@include('partials.stats', ['scope' => 'today'])
<section class="task-panel" id="today-list" aria-labelledby="today-list-title"><div class="task-panel-heading"><div><p class="eyebrow">MAKE SPACE FOR WHAT MATTERS</p><h2 id="today-list-title">Today’s little to-dos <span class="number-badge">{{ $tasks->count() }}</span></h2></div><a href="{{ route('tasks.index') }}" class="text-link">View all tasks <span aria-hidden="true">↗</span></a></div>
@if($tasks->isNotEmpty())
<div class="task-filter-bar"><div class="task-tabs" role="group" aria-label="Filter today’s tasks"><button type="button" class="selected" data-task-filter="all" aria-pressed="true">All tasks <span>{{ $tasks->count() }}</span></button><button type="button" data-task-filter="pending" aria-pressed="false">Pending <span>{{ $tasks->count() - $completed }}</span></button><button type="button" data-task-filter="done" aria-pressed="false">Done <span>{{ $completed }}</span></button></div><span class="filter-note">Your café checklist for today</span></div>
<div class="task-rows">@foreach($tasks as $task)@include('partials.task-row', ['returnTo' => 'today'])@endforeach</div><p id="no-task-results" class="empty-search" hidden>No tasks in this view. Try another filter.</p><div class="task-panel-foot"><span id="visible-count">Showing {{ $tasks->count() }} tasks for today</span><span>Small steps count.</span></div>
@else
<div class="task-empty"><span aria-hidden="true">✳</span><h3>Your day is a fresh page.</h3><p>There are no tasks scheduled for today. A small first step is a good start.</p>@auth<a class="button dark" href="{{ route('tasks.create', ['from' => 'today']) }}">Add today’s first task <span aria-hidden="true">+</span></a>@endauth</div>
@endif
</section>
<div class="daily-note"><span aria-hidden="true">☕</span><div><b>Pause. Breathe. Keep brewing.</b><p>Your list is here when you’re ready. A little progress is still progress.</p></div><a href="{{ route('about') }}">The story behind Ember <span aria-hidden="true">↗</span></a></div>
@endsection
