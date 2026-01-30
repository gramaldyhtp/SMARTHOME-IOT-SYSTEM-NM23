@extends('layouts.app')

@section('content')
<style>
/* =======================
   GLOBAL
======================= */
body {
    background-color: #0b1216;
    font-family: Arial, Helvetica, sans-serif;
    color: #ffffff;
}

.dashboard {
    padding: 32px;
}

/* =======================
   HEADER
======================= */
.header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 30px;
}

.icon-box {
    width: 42px;
    height: 42px;
    background: #facc15;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #000;
    font-weight: bold;
    font-size: 18px;
}

.header-title h1 {
    margin: 0;
    font-size: 22px;
}

.header-title p {
    margin: 0;
    font-size: 13px;
    color: #9ca3af;
}

.mode-badge {
    margin-left: auto;
    background: #374151;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 12px;
}

/* =======================
   CARD
======================= */
.card {
    background: #121a20;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
}

/* =======================
   MODE CONTROL
======================= */
.mode-control {
    display: flex;
    align-items: center;
}

.mode-control p {
    margin: 0;
    font-size: 13px;
    color: #9ca3af;
}

.toggle {
    margin-left: auto;
}

.toggle input {
    display: none;
}

.toggle label {
    width: 44px;
    height: 24px;
    background: #4b5563;
    border-radius: 20px;
    display: block;
    position: relative;
    cursor: pointer;
}

.toggle label::after {
    content: "";
    width: 18px;
    height: 18px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
    top: 3px;
    left: 3px;
    transition: 0.3s;
}

.toggle input:checked + label {
    background: #22c55e;
}

.toggle input:checked + label::after {
    left: 23px;
}

/* =======================
   STATUS GRID
======================= */
.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.status-box {
    background: #121a20;
    border-radius: 16px;
    padding: 20px;
}

.status-purple {
    background: rgba(168, 85, 247, 0.25);
}

.status-box p {
    margin: 0;
    font-size: 13px;
    color: #d1d5db;
}

.status-box h2 {
    margin: 6px 0 0;
    font-size: 26px;
}

/* =======================
   ROOM GRID
======================= */
.room-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
}

.room-card {
    background: #121a20;
    border-radius: 16px;
    padding: 20px;
}

.room-card h3 {
    margin-top: 0;
    margin-bottom: 16px;
}

.sensor {
    background: #1a232b;
    padding: 8px;
    border-radius: 8px;
    font-size: 13px;
    text-align: center;
    margin-bottom: 12px;
}

.relay {
    display: flex;
    gap: 10px;
}

.relay button {
    flex: 1;
    padding: 8px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: bold;
}

.btn-on {
    background: rgba(34,197,94,0.3);
    color: #22c55e;
}

.btn-off {
    background: #374151;
    color: #fff;
}
</style>

<div class="dashboard">

    <!-- HEADER -->
    <div class="header">
        <div class="icon-box">⚡</div>
        <div class="header-title">
            <h1>Dashboard</h1>
            <p>Smart Lighting System</p>
        </div>
        <div class="mode-badge">Manual</div>
    </div>

    <!-- PANEL -->
    <div class="card">
        <h3>Panel Kontrol</h3>
        <p>Kelola pengaturan sistem pencahayaan anda</p>
    </div>

    <!-- MODE CONTROL -->
    <div class="card mode-control">
        <div>
            <h4>Mode Kontrol</h4>
            <p>Kontrol manual pada setiap lampu</p>
        </div>
        <div class="toggle">
            <input type="checkbox" id="mode">
            <label for="mode"></label>
        </div>
    </div>

    <!-- STATUS -->
    <div class="status-grid">
        <div class="status-box status-purple">
            <p>Lampu Nyala</p>
            <h2>0 / 3</h2>
        </div>
        <div class="status-box">
            <p>PIR Sensor</p>
            <h2>N/A</h2>
        </div>
        <div class="status-box">
            <p>LDR Sensor</p>
            <h2>N/A</h2>
        </div>
    </div>

    <!-- ROOMS -->
    <div class="room-grid">
        @foreach (['Ruang Depan','Ruang Tamu','Ruang Kamar'] as $room)
        <div class="room-card">
            <h3>{{ $room }}</h3>

            <p class="text-sm">Sensor PIR</p>
            <div class="sensor">N/A</div>

            <p class="text-sm">Sensor LDR</p>
            <div class="sensor">N/A</div>

            <p class="text-sm">Relay</p>
            <div class="relay">
                <button class="btn-on">ON</button>
                <button class="btn-off">OFF</button>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
