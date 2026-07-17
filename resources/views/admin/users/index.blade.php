@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div id="users-page" class="card" data-graphql-endpoint="/graphql/graphql">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <div>
            <h1 style="margin:0;">User Management</h1>
            <p class="muted" style="margin:6px 0 0;">Danh sách người dùng được load từ GraphQL.</p>
        </div>
        <a href="/admin" class="btn btn-secondary">Back</a>
    </div>

    <form id="user-form" style="margin-bottom:16px;">
        <div class="grid grid-3">
            <div class="form-group"><input id="name" name="name" placeholder="Name" required /></div>
            <div class="form-group"><input id="email" name="email" placeholder="Email" required /></div>
            <div class="form-group"><input id="password" name="password" placeholder="Password" type="password" required /></div>
        </div>
        <button class="btn" type="submit">Create User</button>
    </form>

    <p id="users-feedback" class="muted" style="margin:8px 0 0;">Loading users...</p>

    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="users-body">
        <tr><td colspan="4" class="muted">Loading users...</td></tr>
        </tbody>
    </table>
</div>

@vite(['resources/js/app.js', 'resources/js/admin/users.js'])
@endsection
