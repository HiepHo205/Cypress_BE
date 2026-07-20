@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div id="app">
    {{-- Nội dung sẽ được render bởi Vue.js tại đây --}}
</div>

@vite(['resources/js/app.js'])
@endsection
