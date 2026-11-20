@extends('layouts.app')
@section('title', 'Profile')
@section('content')
<div class="page-heading"><div><p class="eyebrow">THE PERSON BEHIND THE PLANS</p><h1>A little space that’s yours.</h1><p>Your Ember Daily profile, all in one place.</p></div><span class="count-pill">Personal workspace</span></div>
@if($profile)
<section class="profile-card"><div class="profile-cover"><span class="pill">A LITTLE FOCUS. A LOT OF HEART.</span><span class="profile-cover-star" aria-hidden="true">✳</span></div><div class="profile-card-body"><div class="profile-avatar" aria-hidden="true">{{ mb_substr($profile->username, 0, 1) }}</div><p class="eyebrow">HELLO, {{ mb_strtoupper($profile->username) }}</p><h2>{{ $profile->full_name }}</h2><p class="profile-handle">{{ '@'.$profile->username }}</p><div class="profile-fields"><div><span>FULL NAME</span><strong>{{ $profile->full_name }}</strong></div><div><span>USERNAME</span><strong>{{ $profile->username }}</strong></div><div><span>EMAIL ADDRESS</span><a href="mailto:{{ $profile->email }}">{{ $profile->email }}</a></div></div><div class="profile-bottom"><span class="status-dot" aria-hidden="true"></span><span>Your personal café task workspace</span><a class="text-link" href="{{ route('today') }}">Let’s see what’s brewing <span aria-hidden="true">↗</span></a></div></div></section>
@else
<section class="task-panel task-empty"><span aria-hidden="true">✳</span><h2>Your profile is not ready yet.</h2><p>No profile record was found. Run the project setup to add your workspace profile.</p></section>
@endif
<div class="daily-note"><span aria-hidden="true">☕</span><div><b>Thoughtful plans start with you.</b><p>A familiar space for a focused day.</p></div><a href="{{ route('about') }}">About this little project <span aria-hidden="true">↗</span></a></div>
@endsection
