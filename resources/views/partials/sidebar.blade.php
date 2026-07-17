<aside class="sidebar">
    <h2>Cypress Admin</h2>
    <a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}">Dashboard</a>
    <a href="/admin/users" class="{{ request()->is('admin/users*') ? 'active' : '' }}">Users</a>
    <a href="#">Roles</a>
    <a href="#">Settings</a>
</aside>
