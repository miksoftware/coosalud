<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Consultas')</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-primary: #0f0f23;
            --bg-secondary: #1a1a3e;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --accent: #00bfa6;
            --accent-hover: #00e5c3;
            --danger: #ff5252;
            --warning: #ffc107;
            --success: #4caf50;
            --text-primary: #e0e0e0;
            --text-secondary: #9e9e9e;
            --text-muted: #666;
            --radius: 12px;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg-primary);
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(0, 191, 166, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(63, 81, 181, 0.08) 0%, transparent 50%);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-brand {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-brand svg { width: 24px; height: 24px; }

        .navbar-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            list-style: none;
        }

        .navbar-links a {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .navbar-links a:hover, .navbar-links a.active {
            color: var(--accent);
            background: rgba(0, 191, 166, 0.1);
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .navbar-user span {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-admin { background: rgba(0, 191, 166, 0.2); color: var(--accent); }
        .badge-consulta { background: rgba(63, 81, 181, 0.2); color: #7986cb; }

        /* Main content */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        /* Glass card */
        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        /* Forms */
        .form-group { margin-bottom: 1rem; }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 0.3rem;
        }

        .form-control {
            width: 100%;
            padding: 0.6rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(0, 191, 166, 0.15);
        }

        .form-control::placeholder { color: var(--text-muted); }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%239e9e9e' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 2.5rem;
        }

        select.form-control option { background: var(--bg-secondary); color: var(--text-primary); }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            color: #fff;
        }

        .btn-primary { background: var(--accent); }
        .btn-primary:hover { background: var(--accent-hover); transform: translateY(-1px); }
        .btn-danger { background: var(--danger); }
        .btn-danger:hover { background: #ff6b6b; }
        .btn-secondary { background: rgba(255,255,255,0.1); color: var(--text-secondary); }
        .btn-secondary:hover { background: rgba(255,255,255,0.15); }
        .btn-sm { padding: 0.35rem 0.8rem; font-size: 0.8rem; }

        /* Table */
        .table-wrapper { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 0.7rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
            font-size: 0.85rem;
        }

        th {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:hover td { background: rgba(255, 255, 255, 0.02); }

        /* Alerts */
        .alert {
            padding: 0.8rem 1.2rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            border: 1px solid;
        }

        .alert-success { background: rgba(76, 175, 80, 0.1); border-color: rgba(76, 175, 80, 0.3); color: var(--success); }
        .alert-error { background: rgba(255, 82, 82, 0.1); border-color: rgba(255, 82, 82, 0.3); color: var(--danger); }
        .alert-warning { background: rgba(255, 193, 7, 0.1); border-color: rgba(255, 193, 7, 0.3); color: var(--warning); }

        /* Progress bar */
        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
            margin: 1rem 0;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent), var(--accent-hover));
            border-radius: 4px;
            transition: width 0.3s ease;
            width: 0%;
        }

        /* Grid */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }

        @media (max-width: 768px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .navbar { padding: 0 1rem; }
            .navbar-links { display: none; }
        }

        /* Status indicators */
        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 6px;
        }

        .status-success { background: var(--success); }
        .status-error { background: var(--danger); }
        .status-pending { background: var(--warning); }
        .status-processing { background: var(--accent); animation: pulse 1.5s infinite; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        /* Flex utilities */
        .flex { display: flex; }
        .flex-between { display: flex; justify-content: space-between; align-items: center; }
        .flex-center { display: flex; align-items: center; }
        .gap-1 { gap: 0.5rem; }
        .gap-2 { gap: 1rem; }
        .mt-1 { margin-top: 0.5rem; }
        .mt-2 { margin-top: 1rem; }
        .mb-1 { margin-bottom: 0.5rem; }
        .mb-2 { margin-bottom: 1rem; }
        .text-center { text-align: center; }
        .text-muted { color: var(--text-muted); }
        .text-accent { color: var(--accent); }
        .text-sm { font-size: 0.8rem; }

        /* File input */
        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type="file"] {
            width: 100%;
            padding: 0.6rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px dashed var(--glass-border);
            border-radius: 8px;
            color: var(--text-secondary);
            cursor: pointer;
        }

        .file-input-wrapper input[type="file"]:hover {
            border-color: var(--accent);
        }

        /* Logout button */
        .btn-logout {
            background: none;
            border: 1px solid rgba(255, 82, 82, 0.3);
            color: var(--danger);
            padding: 0.35rem 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: rgba(255, 82, 82, 0.1);
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active { display: flex; }

        .modal {
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            padding: 2rem;
            width: 90%;
            max-width: 500px;
            box-shadow: var(--shadow);
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('consultas.index') }}" class="navbar-brand">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6zm3-7h-2V8c0-.55-.45-1-1-1s-1 .45-1 1v3c0 .55.45 1 1 1h3c.55 0 1-.45 1-1s-.45-1-1-1z"/></svg>
            Coosalud
        </a>

        <ul class="navbar-links">
            @auth
                <li><a href="{{ route('consultas.index') }}" class="{{ request()->routeIs('consultas.index') ? 'active' : '' }}">Consultas</a></li>
                @if(auth()->user()->isAdmin())
                    <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Usuarios</a></li>
                    <li>
                        <a href="{{ route('coosalud.credentials') }}" class="{{ request()->routeIs('coosalud.*') ? 'active' : '' }}" style="display:inline-flex;align-items:center;gap:6px;">
                            API
                            @if(session()->has('coosalud_api_url'))
                                <span style="width:8px;height:8px;border-radius:50%;background:#69f0ae;display:inline-block;" title="URL personalizada activa"></span>
                            @else
                                <span style="width:8px;height:8px;border-radius:50%;background:#9999bb;display:inline-block;" title="Usando URL por defecto"></span>
                            @endif
                        </a>
                    </li>
                @endif
                <li><a href="{{ route('consultas.search') }}" class="{{ request()->routeIs('consultas.search') ? 'active' : '' }}">Buscar</a></li>
                <li><a href="{{ route('consultas.files') }}" class="{{ request()->routeIs('consultas.files') ? 'active' : '' }}">Archivos</a></li>
            @endauth
        </ul>

        @auth
            <div class="navbar-user">
                <span>{{ auth()->user()->name }}</span>
                <span class="badge badge-{{ auth()->user()->role }}">{{ auth()->user()->role }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="btn-logout">Salir</button>
                </form>
            </div>
        @endauth
    </nav>

    <main class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
