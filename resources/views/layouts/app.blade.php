k<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Smart Home Gateway</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            padding: 1.5rem 1rem;
            position: fixed;
            inset: 0 auto 0 0;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .sidebar-title {
            font-weight: 700;
            font-size: 1.25rem;
        }

        .nav { display: flex; flex-direction: column; gap: .35rem; }

        .nav-link {
            padding: .6rem .9rem;
            border-radius: .5rem;
            font-size: .9rem;
            color: #475569;
            text-decoration: none;
        }

        .nav-link:hover { background: #e5e7eb; }

        .nav-link.active {
            background: #2563eb;
            color: #ffffff;
        }

        /* MAIN */
        .main {
            margin-left: 260px;
            padding: 1.5rem 2rem;
            width: calc(100% - 260px);
        }

        .top-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .search-input {
            flex: 1;
            padding: .65rem .9rem;
            border-radius: 999px;
            border: 1px solid #d1d5db;
        }

        .btn {
            padding: .55rem .9rem;
            border-radius: .5rem;
            border: none;
            font-size: .8rem;
            cursor: pointer;
        }

        .btn-status-offline {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-status-online {
            background: #dcfce7;
            color: #166534;
        }

        .btn-ghost {
            background: white;
            border: 1px solid #d1d5db;
        }

        @media (max-width: 900px) {
            .sidebar { display: none; }
            .main { margin-left: 0; width: 100%; }
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-title">Dashboard</div>

    <nav class="nav">
        <a href="/dashboard" class="nav-link">Dashboard</a>
        <a href="/lighting" class="nav-link">Smart Lighting System</a>
        <a href="/doorlock" class="nav-link">Smart Door Lock & Access</a>
        <a href="/energy-monitoring" class="nav-link">Smart Energy Monitoring</a>
        <a href="/temperature" class="nav-link">Smart Temperature & Humidity</a>
        <a href="/curtain" class="nav-link">Smart Curtain System</a>
        <a href="/fire" class="nav-link">Smart Fire & Gas Detection</a>
        <a href="/garden" class="nav-link">Smart Garden & Watering</a>
        <a href="/camera" class="nav-link">Security Camera & Intrusion</a>
        <a href="/appliance" class="nav-link">Smart Appliance Control</a>
    </nav>
</aside>

<!-- MAIN -->
<main class="main">
    <div class="top-bar">
        <input type="text" class="search-input" placeholder="Search here...">
        <button id="mqtt-status" class="btn btn-status-offline">MQTT Offline</button>
        <button class="btn btn-ghost" onclick="location.reload()">Refresh</button>
    </div>

    @yield('content')
</main>

<script>
    const mqttConnected = false;
    const btn = document.getElementById('mqtt-status');

    if (mqttConnected) {
        btn.className = 'btn btn-status-online';
        btn.innerText = 'MQTT Online';
    }
</script>

</body>
</html>
