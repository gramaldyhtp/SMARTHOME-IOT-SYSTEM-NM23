@extends('layouts.app')

@section('content')
<style>
    .page-title{
        font-size:28px;
        font-weight:800;
        margin:10px 0 18px;
    }
    .stats{
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:14px;
        margin-bottom:18px;
    }
    .stat{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:14px;
        padding:14px 16px;
        box-shadow:0 8px 24px rgba(17,24,39,.06);
    }
    .stat .k{color:#6b7280;font-size:12px}
    .stat .v{font-size:22px;font-weight:800;margin-top:6px}
    .section-title{font-size:14px;font-weight:800;color:#111827;margin:16px 0 10px}
    .grid{
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:14px;
    }
    .card{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:14px;
        padding:14px 16px;
        box-shadow:0 8px 24px rgba(17,24,39,.06);
        cursor:pointer;
        transition:.15s;
    }
    .card:hover{transform:translateY(-2px)}
    .card h4{margin:0 0 4px;font-size:14px}
    .card p{margin:0;color:#6b7280;font-size:12px}
    .row{display:flex;align-items:center;justify-content:space-between;margin-top:10px}
    .dot{width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block;margin-right:6px}
    .status{font-size:12px;color:#6b7280;display:flex;align-items:center}
    .small{font-size:12px;color:#6b7280;margin-top:6px}

    @media(max-width:1200px){
        .grid{grid-template-columns:repeat(3,1fr)}
        .stats{grid-template-columns:repeat(2,1fr)}
    }
    @media(max-width:900px){
        .grid{grid-template-columns:repeat(2,1fr)}
    }
</style>

<div class="page-title">Smart Home IoT System NM 23</div>

<div class="stats">
    <div class="stat"><div class="k">Total Devices</div><div class="v" id="stTotal">9</div><div class="small" id="stToday">0 today</div></div>
    <div class="stat"><div class="k">Online</div><div class="v" id="stOnline">0</div><div class="small">Live devices</div></div>
    <div class="stat"><div class="k">Offline</div><div class="v" id="stOffline">9</div><div class="small">Needs attention</div></div>
    <div class="stat"><div class="k">Alerts</div><div class="v" id="stAlerts">1</div><div class="small">Active warnings</div></div>
</div>

<div class="section-title">IoT Devices</div>

<div class="grid">
    <div class="card" data-search-item="true" onclick="location.href='{{ route('lighting') }}'">
        <h4>Smart Lighting System</h4><p>Ruang Tamu</p>
        <div class="row"><div class="status"><span class="dot"></span><span id="stLight">Offline</span></div><div class="small" id="lightMeta">0%</div></div>
        <div class="small">ID: light-001</div>
    </div>

    <div class="card" data-search-item="true" onclick="location.href='{{ route('doorlock') }}'">
        <h4>Smart Door Lock & Access</h4><p>Pintu Masuk</p>
        <div class="row"><div class="status"><span class="dot"></span><span id="stDoor">Offline</span></div><div class="small" id="doorMeta">Unlocked</div></div>
        <div class="small">ID: door-001</div>
    </div>

    <div class="card" data-search-item="true" onclick="location.href='{{ route('energy') }}'">
        <h4>Smart Energy Monitoring</h4><p>Panel Listrik</p>
        <div class="row"><div class="status"><span class="dot"></span><span id="stEnergy">Offline</span></div><div class="small" id="energyMeta">0 W</div></div>
        <div class="small">ID: energy-001</div>
    </div>

    <div class="card" data-search-item="true" onclick="location.href='{{ route('temperature') }}'">
        <h4>Temperature & Humidity</h4><p>Ruang Keluarga</p>
        <div class="row"><div class="status"><span class="dot"></span><span id="stTemp">Offline</span></div><div class="small" id="tempMeta">0°C / 0%</div></div>
        <div class="small">ID: temp-001</div>
    </div>

    <div class="card" data-search-item="true" onclick="location.href='{{ route('curtain') }}'">
        <h4>Smart Curtain System</h4><p>Kamar Tidur</p>
        <div class="row"><div class="status"><span class="dot"></span><span id="stCurtain">Offline</span></div><div class="small" id="curtainMeta">0%</div></div>
        <div class="small">ID: curtain-001</div>
    </div>

    <div class="card" data-search-item="true" onclick="location.href='{{ route('firegas') }}'">
        <h4>Fire & Gas Detection</h4><p>Dapur</p>
        <div class="row"><div class="status"><span class="dot"></span><span id="stFire">Offline</span></div><div class="small" id="fireMeta">Safe</div></div>
        <div class="small">ID: fire-001</div>
    </div>

    <div class="card" data-search-item="true" onclick="location.href='{{ route('garden') }}'">
        <h4>Smart Garden & Watering</h4><p>Taman Belakang</p>
        <div class="row"><div class="status"><span class="dot"></span><span id="stGarden">Offline</span></div><div class="small" id="gardenMeta">0%</div></div>
        <div class="small">ID: garden-001</div>
    </div>

    <div class="card" data-search-item="true" onclick="location.href='{{ route('camera') }}'">
        <h4>Security Camera & Intrusion</h4><p>Pintu Depan</p>
        <div class="row"><div class="status"><span class="dot"></span><span id="stCam">Offline</span></div><div class="small" id="camMeta">No video</div></div>
        <div class="small">ID: cam-001</div>
    </div>

    <div class="card" data-search-item="true" onclick="location.href='{{ route('appliance') }}'">
        <h4>Smart Appliance Control</h4><p>Seluruh Rumah</p>
        <div class="row"><div class="status"><span class="dot"></span><span id="stApp">Offline</span></div><div class="small" id="appMeta">0 device aktif</div></div>
        <div class="small">ID: appliance-001</div>
    </div>
</div>

@push('scripts')
<script>
    // default offline (nanti kamu bisa update real-time lewat MQTT/WS)
    window.setMqttStatus(false);
</script>
@endpush

@endsection
