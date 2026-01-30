@extends('layouts.app')

@section('content')

<style>
/* WRAPPER WAJIB – BIAR TIDAK NUTUP SIDEBAR */
.page-wrapper {
    width: 100%;
}

/* GRID SESUAI MOCKUP */
.curtain-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.curtain-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 6px 20px rgba(0,0,0,.06);
}

.curtain-card h3 {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 10px;
    color: #0f172a;
}

.status-big {
    font-size: 26px;
    font-weight: 700;
}

.status-sub {
    font-size: 13px;
    color: #64748b;
}

.btn-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.btn {
    padding: 10px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
}

.btn-open { background: #14b8a6; color: #fff; }
.btn-close { background: #ef4444; color: #fff; }
.btn-auto { background: #fbbf24; color: #fff; }

canvas {
    width: 100% !important;
    height: 220px !important;
}

@media(max-width: 1200px) {
    .curtain-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media(max-width: 600px) {
    .curtain-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="page-wrapper">

    <h1 style="font-size:20px;font-weight:700;margin-bottom:18px;">
        Smart Curtain Control Dashboard
    </h1>

    <div class="curtain-grid">

        <!-- STATUS -->
        <div class="curtain-card">
            <h3>Status Tirai</h3>
            <div class="status-big">open</div>
            <div class="status-sub">Servo 1</div>
            <br>
            <div class="status-big">open</div>
            <div class="status-sub">Servo 2</div>
            <br>
            <div class="status-sub">Mode: ON</div>
            <div class="status-sub">MQTT: connected</div>
        </div>

        <!-- KONTROL -->
        <div class="curtain-card">
            <h3>Kontrol Tirai</h3>
            <div class="btn-grid">
                <button class="btn btn-open">Buka 1</button>
                <button class="btn btn-close">Tutup 1</button>
                <button class="btn btn-open">Buka 2</button>
                <button class="btn btn-close">Tutup 2</button>
            </div>
            <br>
            <div class="btn-grid">
                <button class="btn btn-auto">AUTO ON</button>
                <button class="btn btn-close">AUTO OFF</button>
            </div>
            <p class="status-sub" style="margin-top:10px">
                Mode Auto akan override manual sampai dimatikan
            </p>
        </div>

        <!-- SENSOR -->
        <div class="curtain-card">
            <h3>Sensor Cahaya (LDR)</h3>
            <div class="status-sub">Kondisi</div>
            <div class="status-big">terang</div>
            <br>
            <div class="status-sub">Intensitas</div>
            <div class="status-big">100 %</div>
            <p class="status-sub">
                Threshold open: 60%, close: 40%
            </p>
        </div>

        <!-- GRAFIK -->
        <div class="curtain-card">
            <h3>Grafik Intensitas Cahaya</h3>
            <canvas id="lightChart"></canvas>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('lightChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: Array(20).fill(''),
        datasets: [{
            data: Array(20).fill(100),
            borderWidth: 2,
            fill: true,
            tension: .3
        }]
    },
    options: {
        plugins:{legend:{display:false}},
        scales:{y:{min:0,max:100}}
    }
});
</script>

@endsection
