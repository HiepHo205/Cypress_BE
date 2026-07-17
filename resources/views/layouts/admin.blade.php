<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cypress Admin')</title>
    <style>
        :root { color-scheme: light; }
        body { margin:0; font-family: Arial, sans-serif; background:#f4f6fb; color:#111827; }
        * { box-sizing: border-box; }
        .admin-shell { display:flex; min-height:100vh; }
        .sidebar { width:260px; background:#111827; color:white; padding:24px 16px; }
        .sidebar h2 { margin-top:0; font-size:20px; }
        .sidebar a { color:#d1d5db; text-decoration:none; display:block; padding:10px 12px; border-radius:8px; margin-bottom:6px; }
        .sidebar a.active, .sidebar a:hover { background:#1f2937; color:white; }
        .main { flex:1; display:flex; flex-direction:column; }
        .topbar { height:64px; background:white; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; padding:0 24px; }
        .content { padding:24px; }
        .card { background:white; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.06); padding:24px; }
        .muted { color:#6b7280; }
        .btn { display:inline-block; padding:10px 14px; border:0; border-radius:8px; background:#2563eb; color:white; cursor:pointer; text-decoration:none; }
        .btn-secondary { background:#e5e7eb; color:#111827; }
        table { width:100%; border-collapse:collapse; margin-top:12px; }
        th, td { padding:12px; border-bottom:1px solid #e5e7eb; text-align:left; }
        .grid { display:grid; gap:12px; }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .form-group { margin-bottom:12px; }
        input { width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px; }
        @media (max-width: 900px) { .admin-shell { flex-direction:column; } .sidebar { width:100%; } .grid-3 { grid-template-columns:1fr; } }
    </style>
</head>
<body>
<div class="admin-shell">
    @include('partials.sidebar')
    <div class="main">
        @include('partials.topbar')
        <div class="content">
            @yield('content')
        </div>
    </div>
</div>
</body>
</html>
