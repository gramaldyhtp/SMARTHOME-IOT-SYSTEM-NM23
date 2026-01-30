@extends('layouts.app')

@section('content')

<style>
:root{
    --bg:#94B4C1;
    --card:#EBF4DD;
    --primary:#434E78;
    --secondary:#607B8F;
    --accent:#5A0E24;
    --green:#4ade80;
    --red:#ef4444;
}

.energy-wrap{
    background:var(--bg);
    padding:24px;
    border-radius:16px;
    min-height:calc(100vh - 120px);
}

/* ===== HEADER ===== */
.energy-header{text-align:center;margin-bottom:30px}
.energy-header h2{color:var(--accent);margin:0;font-size:2em}
.energy-header p{color:#666;margin-top:8px}

/* ===== CLOCK ===== */
.clock-box{
    background:var(--card);
    border-radius:16px;
    padding:24px;
    text-align:center;
    margin-bottom:24px;
    box-shadow:0 4px 20px rgba(0,0,0,.1);
}
.clock{font-size:3em;font-weight:bold;color:var(--accent)}
.date{color:#666;margin-top:8px}

/* ===== GRID ===== */
.energy-container{max-width:1400px;margin:auto}
.main-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;margin-bottom:24px}
.detail-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px}

/* ===== CARD ===== */
.card{
    background:var(--card);
    border-radius:16px;
    padding:28px;
    box-shadow:0 4px 20px rgba(0,0,0,.1);
}
.card.power{background:linear-gradient(135deg,var(--primary),#5a6fa8);color:#fff}
.card.energy{background:linear-gradient(135deg,var(--secondary),#7a98af);color:#fff}
.card.cost{background:linear-gradient(135deg,var(--secondary),#7a98af);color:#fff}

.card-label{font-size:0.9em;opacity:0.9;margin-bottom:12px}
.card-value{font-size:3em;font-weight:bold;margin:12px 0}
.card-unit{font-size:0.9em;opacity:0.9}

/* ===== DETAIL CARD ===== */
.detail-card{
    background:var(--card);
    border-radius:12px;
    padding:16px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
}
.detail-label{color:#666;font-size:0.9em}
.detail-value{font-weight:600;color:var(--accent)}

/* ===== CHART ===== */
.chart-box{
    background:var(--card);
    border-radius:16px;
    padding:24px;
    margin-bottom:24px;
    box-shadow:0 4px 20px rgba(0,0,0,.1);
}
.chart-title{color:var(--accent);margin-bottom:16px;font-weight:600}

/* ===== STATUS BAR ===== */
.status-bar{
    background:var(--card);
    border-radius:12px;
    padding:16px 24px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
    flex-wrap:wrap;
    gap:16px;
}
.status-dot{
    width:12px;height:12px;border-radius:50%;
    background:var(--red);display:inline-block;margin-right:8px;
}
.status-dot.connected{background:var(--green)}
.status-text{color:#666}
.timestamp{color:#999;font-size:0.9em}

@media(max-width:768px){
    .main-cards{grid-template-columns:1fr}
    .detail-cards{grid-template-columns:repeat(2,1fr)}
}
</style>

<div class="energy-wrap">

<div class="energy-header">
    <h2>⚡ Monitoring Listrik Real-Time</h2>
    <p>PZEM-004T • ESP32 • MQTT Langsung</p>
</div>

<div class="energy-container">

    {{-- CLOCK --}}
    <div class="clock-box">
        <div class="clock" id="clock">00:00:00</div>
        <div class="date" id="date">Loading...</div>
    </div>

    {{-- MAIN CARDS --}}
    <div class="main-cards">
        <div class="card power">
            <div class="card-label">Daya Sesaat</div>
            <div class="card-value" id="power">0</div>
            <div class="card-unit">Watt</div>
        </div>
        <div class="card energy">
            <div class="card-label">Energi</div>
            <div class="card-value" id="energy">0</div>
            <div class="card-unit">kWh</div>
        </div>
        <div class="card cost">
            <div class="card-label">Biaya</div>
            <div class="card-value" id="cost">Rp 0</div>
            <div class="card-unit"></div>
        </div>
    </div>

    {{-- DETAIL CARDS --}}
    <div class="detail-cards">
        <div class="detail-card">
            <span class="detail-label">Tegangan</span>
            <span class="detail-value" id="voltage">0 V</span>
        </div>
        <div class="detail-card">
            <span class="detail-label">Arus</span>
            <span class="detail-value" id="current">0 A</span>
        </div>
        <div class="detail-card">
            <span class="detail-label">Frekuensi</span>
            <span class="detail-value" id="frequency">0 Hz</span>
        </div>
        <div class="detail-card">
            <span class="detail-label">Power Factor</span>
            <span class="detail-value" id="pf">0</span>
        </div>
        <div class="detail-card">
            <span class="detail-label">Tarif</span>
            <span class="detail-value" id="tarif">Rp 0</span>
        </div>
    </div>

    {{-- CHART --}}
    <div class="chart-box">
        <h3 class="chart-title">📈 Grafik Energi (60 Menit)</h3>
        <canvas id="energyChart"></canvas>
    </div>

    {{-- STATUS BAR --}}
    <div class="status-bar">
        <div>
            <span class="status-dot" id="statusDot"></span>
            <span class="status-text" id="statusText">Menghubungkan…</span>
        </div>
        <div class="timestamp" id="timestamp">Update terakhir: --</div>
    </div>

</div>
</div>

{{-- SCRIPTS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
<script>
// ===== CONFIG =====
const broker = "ws://broker.emqx.io:8083/mqtt";
const topic = "home/energy/pzem";
const TARIF = 1444.70;

// ===== CHART SETUP =====
const ctx = document.getElementById("energyChart").getContext("2d");
const chart = new Chart(ctx, {
    type: "line",
    data: {
        labels: [],
        datasets: [{
            label: 'Energi (kWh)',
            data: [],
            borderColor: "#f093fb",
            backgroundColor: "rgba(240, 147, 251, 0.2)",
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: true } },
        scales: {
            y: { 
                beginAtZero: true,
                ticks: {
                    callback: (v) => v.toFixed(4) + ' kWh'
                }
            }
        }
    }
});

// ===== HELPER =====
const pf = (v, d = 0) => {
    const n = parseFloat(v);
    return isNaN(n) ? d : n;
};

// ===== MQTT CONNECTION =====
console.log('🔌 Menghubungkan ke MQTT...');
const c = mqtt.connect(broker, {
    reconnectPeriod: 5000,
    clientId: 'energy_web_' + Math.random().toString(16).substr(2, 8)
});

// ===== ON CONNECT =====
c.on("connect", () => {
    console.log('✅ MQTT terhubung!');
    document.getElementById("statusDot").classList.add("connected");
    document.getElementById("statusText").innerText = "Terhubung • Real-Time";
    
    c.subscribe(topic, (err) => {
        if (!err) console.log('✅ Subscribe:', topic);
        else console.error('❌ Subscribe error:', err);
    });
});

// ===== ON ERROR =====
c.on("error", (err) => {
    console.error('❌ MQTT Error:', err);
    document.getElementById("statusDot").classList.remove("connected");
    document.getElementById("statusText").innerText = "Error Koneksi";
});

// ===== ON RECONNECT =====
c.on("reconnect", () => {
    console.warn('🔄 Reconnecting...');
    document.getElementById("statusDot").classList.remove("connected");
    document.getElementById("statusText").innerText = "Reconnecting...";
});

// ===== ON OFFLINE =====
c.on("offline", () => {
    console.warn('⚠ MQTT Offline');
    document.getElementById("statusDot").classList.remove("connected");
    document.getElementById("statusText").innerText = "Offline";
});

// ===== ON MESSAGE =====
c.on("message", (t, m) => {
    try {
        const d = JSON.parse(m.toString());
        console.log('📨 Data:', d);
        
        // Parse data
        const voltage = pf(d.voltage);
        const current = pf(d.current);
        const power = pf(d.power);
        const energy_kwh = pf(d.energy_kwh);
        const frequency = pf(d.frequency);
        const power_factor = pf(d.power_factor, 1.0);
        const tarif = pf(d.tarif, TARIF);
        const biaya = d.biaya ? pf(d.biaya) : (energy_kwh * tarif);
        
        // Update UI
        document.getElementById("power").innerText = power.toFixed(0);
        document.getElementById("energy").innerText = energy_kwh.toFixed(4);
        document.getElementById("cost").innerText = "Rp " + biaya.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById("voltage").innerText = voltage.toFixed(1) + " V";
        document.getElementById("current").innerText = current.toFixed(3) + " A";
        document.getElementById("frequency").innerText = frequency.toFixed(1) + " Hz";
        document.getElementById("pf").innerText = power_factor.toFixed(2);
        document.getElementById("tarif").innerText = "Rp " + tarif.toLocaleString('id-ID') + "/kWh";

        // Update chart
        const time = new Date().toLocaleTimeString("id-ID", {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        chart.data.labels.push(time);
        chart.data.datasets[0].data.push(energy_kwh);
        
        if (chart.data.labels.length > 60) {
            chart.data.labels.shift();
            chart.data.datasets[0].data.shift();
        }
        chart.update('none');

        // Update status
        document.getElementById("statusDot").classList.add("connected");
        document.getElementById("statusText").innerText = "Terhubung • Real-Time";
        document.getElementById("timestamp").innerText = "Update: " + time;
        
    } catch (err) {
        console.error('❌ Error parsing:', err);
    }
});

// ===== CLOCK UPDATE =====
setInterval(() => {
    const now = new Date();
    document.getElementById("clock").innerText = now.toLocaleTimeString("id-ID");
    document.getElementById("date").innerText = now.toLocaleDateString("id-ID", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric"
    });
}, 1000);
</script>

@endsection
