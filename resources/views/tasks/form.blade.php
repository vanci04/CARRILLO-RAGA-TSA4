@extends('layouts.app')
@section('title', $task->exists ? 'Edit task' : 'Add a task')
@section('content')
@php($backRoute = $returnTo === 'today' ? 'today' : 'tasks.index')
<a href="{{ route($backRoute) }}" class="back-link">← {{ $returnTo === 'today' ? 'Back to today' : 'Back to the task list' }}</a>
<div class="page-heading form-page-heading"><div><p class="eyebrow">A LITTLE INTENTION GOES A LONG WAY</p><h1>{{ $task->exists ? 'A little change of plan.' : 'Make room for something good.' }}</h1><p>{{ $task->exists ? 'Adjust the details, then get back to your day.' : 'Write it down. Pick a day. Take it one small step at a time.' }}</p></div></div>
<div class="task-form-layout"><section class="task-form-card" aria-labelledby="task-form-title"><h2 id="task-form-title">{{ $task->exists ? 'Edit your task' : 'A new little to-do' }}</h2>
@if($errors->any())<div class="form-errors" role="alert"><strong>Please check the details below.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="post" action="{{ $task->exists ? route('tasks.update', $task) : route('tasks.store') }}" class="task-form">
@csrf @if($task->exists) @method('PUT') @endif
<input type="hidden" name="return_to" value="{{ $returnTo }}">
<div class="form-field"><label for="title">What needs doing? <span>Required</span></label><input type="text" name="title" id="title" value="{{ old('title', $task->title) }}" maxlength="255" required placeholder="e.g. Restock the coffee beans" @error('title') aria-invalid="true" aria-describedby="title-error" @enderror>@error('title')<p class="field-error" id="title-error">{{ $message }}</p>@enderror<small>Keep it clear and manageable. Up to 255 characters.</small></div>
<div class="form-field-grid"><div class="form-field"><label for="task_date">A day for it <span>Required</span></label><input type="date" name="task_date" id="task_date" value="{{ old('task_date', $task->task_date?->toDateString()) }}" required @error('task_date') aria-invalid="true" aria-describedby="date-error" @enderror>@error('task_date')<p class="field-error" id="date-error">{{ $message }}</p>@enderror</div><div class="form-field"><label for="status">Where things stand</label><select name="status" id="status" required @error('status') aria-invalid="true" @enderror><option value="pending" @selected(old('status', $task->status) === 'pending')>Pending</option><option value="done" @selected(old('status', $task->status) === 'done')>Done</option></select>@error('status')<p class="field-error">{{ $message }}</p>@enderror</div></div>
<div class="form-actions"><a class="button light" href="{{ route($backRoute) }}">Cancel</a><button type="submit" class="button dark">{{ $task->exists ? 'Save changes' : 'Add task' }} <span aria-hidden="true">↗</span></button></div>
</form>
@if($task->exists)<div class="delete-zone"><span>No longer on your list?</span><form method="post" action="{{ route('tasks.destroy', $task) }}" data-delete-form data-task-name="{{ $task->title }}">@csrf @method('DELETE')<input type="hidden" name="return_to" value="{{ $returnTo }}"><button type="submit" class="delete-link">Remove this task</button></form></div>@endif
</section><aside class="form-ritual"><span class="eyebrow">A GENTLE REMINDER</span><span class="form-star" aria-hidden="true">✳</span><h2>Big days.<br><em>Small steps.</em></h2><p>You don’t have to do everything at once. Just the next good thing.</p><span>EMBER DAILY · MADE FOR YOUR EVERYDAY</span></aside></div>
@endsection
