<aside class="sidebar">
    <div class="sidebar-title">Dashboard</div>

    <nav class="nav">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        <a href="{{ route('lighting') }}" class="nav-link {{ request()->routeIs('lighting') ? 'active' : '' }}">
            Smart Lighting System
        </a>

        <a href="{{ route('doorlock') }}" class="nav-link {{ request()->routeIs('doorlock') ? 'active' : '' }}">
            Smart Door Lock & Access
        </a>

        <a href="{{ route('energy-monitoring') }}" class="nav-link {{ request()->routeIs('energy') ? 'active' : '' }}">
            Smart Energy Monitoring
        </a>

        <a href="{{ route('temperature') }}" class="nav-link {{ request()->routeIs('temperature') ? 'active' : '' }}">
            Smart Temperature & Humidity
        </a>

        <a href="{{ route('curtain') }}" class="nav-link {{ request()->routeIs('curtain') ? 'active' : '' }}">
            Smart Curtain System
        </a>

        <a href="{{ route('firegas') }}" class="nav-link {{ request()->routeIs('firegas') ? 'active' : '' }}">
            Smart Fire & Gas Detection
        </a>

        <a href="{{ route('garden') }}" class="nav-link {{ request()->routeIs('garden') ? 'active' : '' }}">
            Smart Garden & Watering
        </a>

        <a href="{{ route('camera') }}" class="nav-link {{ request()->routeIs('camera') ? 'active' : '' }}">
            Security Camera & Intrusion
        </a>

        <a href="{{ route('appliance') }}" class="nav-link {{ request()->routeIs('appliance') ? 'active' : '' }}">
            Smart Appliance Control
        </a>
    </nav>
</aside>
