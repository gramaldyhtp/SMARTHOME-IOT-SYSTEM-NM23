@extends('layouts.app')

@section('content')

{{-- CSS KHUSUS HALAMAN --}}
<style>
:root {
    --bg-color: #f0fbfc;
    --card-bg: #ffffff;
    --primary-orange: #ff9800;
    --primary-green: #00c853;
    --text-dark: #37474f;
    --text-gray: #90a4ae;
}

.header {
    text-align: center;
    margin-bottom: 30px;
}
.header h2 { margin: 0; color: #00838f; font-size: 24px; }
.header p { margin-top: 5px; color: var(--text-gray); font-size: 14px; }

.container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    gap: 20px;
}

.sensors-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.bottom-row {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 20px;
}

.card {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #00897b;
    font-weight: 600;
    margin-bottom: 15px;
}

.value-big {
    font-size: 38px;
    font-weight: 600;
    color: var(--primary-orange);
}

.progress-container {
    width: 100%;
    background-color: #eceff1;
    height: 8px;
    border-radius: 4px;
    margin-top: 15px;
}

.progress-bar {
    height: 100%;
    background-color: var(--primary-orange);
    width: 0%;
    border-radius: 4px;
}

.control-panel {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.switch {
    position: relative;
    width: 44px;
    height: 24px;
}
.switch input { display: none; }

.slider {
    position: absolute;
    inset: 0;
    background: #ccc;
    border-radius: 34px;
}
.slider:before {
    content: "";
    position: absolute;
    width: 18px;
    height: 18px;
    left: 3px;
    bottom: 3px;
    background: white;
    border-radius: 50%;
    transition: .3s;
}
input:checked + .slider {
    background: #00838f;
}
input:checked + .slider:before {
    transform: translateX(20px);
}

.power-btn {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #ccc;
    border: none;
    font-size: 30px;
    cursor: pointer;
}
.power-btn.active {
    background: linear-gradient(135deg,#00e676,#00c853);
}
</style>

{{-- HEADER --}}
<div class="header">
    <h2><i class="fas fa-tint"></i> Smart Watering Dashboard</h2>
    <p>Sistem Monitoring Kelembaban Tanah Real-Time</p>
</div>

<div class="container">

    {{-- SENSOR --}}
    <div class="sensors-row">
        @foreach ([1,2,3] as $i)
        <div class="card">
            <div class="card-header">
                <i class="fas fa-seedling"></i> Sensor {{ $i }}
            </div>
            <div>
                <span class="value-big" id="s{{ $i }}-val">--</span> %
            </div>
            <div id="s{{ $i }}-status">Menunggu...</div>
            <div class="progress-container">
                <div class="progress-bar" id="s{{ $i }}-bar"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- BAWAH --}}
    <div class="bottom-row">

        <div class="card">
            <h4>Rata-Rata</h4>
            <div class="value-big" id="avg-val">--</div>
            <div id="avg-status">Normal</div>
        </div>

        <div class="card control-panel">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <strong>Status Pompa</strong>
                <label class="switch">
                    <input type="checkbox" id="mode-toggle" checked>
                    <span class="slider"></span>
                </label>
            </div>

            <div style="margin-top:20px;display:flex;gap:20px;align-items:center;">
                <button id="btn-power" class="power-btn" disabled>
                    <i class="fas fa-power-off"></i>
                </button>
                <div>
                    <h3 id="pump-text-main">MATI</h3>
                    <p id="relay-text">OFF</p>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- SCRIPT --}}
<script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
<script>
const brokerUrl = "ws://broker.emqx.io:8083/mqtt";
const topicData = "smarthome/garden/moisture";
const topicMode = "smarthome/garden/set/mode";
const topicPump = "smarthome/garden/set/pump";

let isAuto = true;
let pumpIsOn = false;

const client = mqtt.connect(brokerUrl);
client.on("connect", () => client.subscribe(topicData));

client.on("message", (t, m) => {
    const d = JSON.parse(m.toString());
    update("s1", d.s1);
    update("s2", d.s2);
    update("s3", d.s3);

    document.getElementById("avg-val").innerText = d.average.toFixed(1);
    document.getElementById("avg-status").innerText =
        d.average < 40 ? "Kering" : d.average > 60 ? "Basah" : "Normal";

    const auto = d.mode === "AUTO";
    if (auto !== isAuto) {
        isAuto = auto;
        document.getElementById("mode-toggle").checked = isAuto;
        toggle();
    }

    pump(isOn = d.pump_status === "ON");
});

function update(id, v) {
    document.getElementById(id+"-val").innerText = v.toFixed(1);
    document.getElementById(id+"-bar").style.width = v+"%";
}

function pump(on) {
    pumpIsOn = on;
    document.getElementById("pump-text-main").innerText = on ? "AKTIF" : "MATI";
    document.getElementById("relay-text").innerText = on ? "ON" : "OFF";
    document.getElementById("btn-power").classList.toggle("active", on);
}

function toggle() {
    document.getElementById("btn-power").disabled = isAuto;
}

document.getElementById("mode-toggle").onchange = e => {
    isAuto = e.target.checked;
    toggle();
    client.publish(topicMode, isAuto ? "AUTO" : "MANUAL");
};

document.getElementById("btn-power").onclick = () => {
    if (!isAuto) client.publish(topicPump, pumpIsOn ? "OFF" : "ON");
};
</script>

@endsection
