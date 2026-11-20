<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" href="{{ asset('cafe-base.css') }}">
        <link rel="stylesheet" href="{{ asset('daily.css') }}">
        <style>body{margin:0}.auth-shell{min-height:100vh;display:grid;place-items:center;padding:24px;background:#f7f1e8}.auth-card{width:min(440px,100%);box-sizing:border-box;background:#fff;border:1px solid #e5d8c8;border-radius:20px;padding:32px;box-shadow:0 18px 50px rgba(58,40,26,.1)}.auth-brand{display:block;margin-bottom:20px;color:#6d3929;font-size:24px;font-weight:800;text-decoration:none}.auth-card input{box-sizing:border-box;width:100%;padding:12px;border:1px solid #ccbba9;border-radius:9px}.auth-card label{font-weight:700}.auth-card form>div{margin-top:16px}.auth-card button{padding:11px 18px;border:0;border-radius:9px;background:#3b2a20;color:white;font-weight:800}.auth-card a{color:#7a4330}</style>
    </head>
    <body><div class="auth-shell"><div class="auth-card"><a class="auth-brand" href="/">ember · DAILY</a>{{ $slot }}</div></div>
    </body>
</html>
