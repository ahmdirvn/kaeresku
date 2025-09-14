@extends('layouts.app') {{-- atau buat layout sendiri --}}

@section('content')
<div class="container">
    <h2>Login</h2>
    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>
@endsection
