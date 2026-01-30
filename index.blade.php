<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>⚡ Monitoring Listrik Real-Time</title>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f5f6fa;
    padding: 20px;
}

.container { max-width: 1200px; margin: auto; }

.header {
    background: white;
    padding: 25px;
    border-radius: 15px;
    text-align: center;
    margin-bottom: 30px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.clock-container {
    background: white;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 25px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.clock { font-size: 2.5em; font-weight: bold; color: #667eea; }

.main-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px,1fr));
    gap: 20px;
}

.card {
    padding: 25px;
    border-radius: 15px;
    color: white;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.card.power { background: linear-gradient(135deg,#667eea,#764ba2); }
.card.energy { background: linear-gradient(135deg,#f093fb,#f5576c); }
.card.cost { background: linear-gradient(135deg,#4facfe,#00f2fe); }

.detail-grid {
    margin-top: 25px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px,1fr));
    gap: 20px;
}

.detail-card {
    background: white;
    padding: 18px;
    display: flex;
    justify-content: space-between;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.chart-container {
    background: white;
    padding: 20px;
    border-radius: 15px;
    margin-top: 25px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.status-bar {
    margin-top: 25px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    display: flex;
    justify-content: space-between;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.status-dot {
    width: 14px; height: 14px; border-radius: 50%;
    background: red;
    display: inline-block;
}

.status-dot.connected { background: #4ade80; }

.debug-panel {
    background: white;
    padding: 20px;
    border-radius: 12px;
    margin-top: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.debug-log {
    background: #1e1e1e;
    color: #d4d4d4;
    padding: 15px;
    height: 250px;
    overflow-y: auto;
    border-radius: 8px;
    font-family: monospace;
}
</style>
</head>

<body>

<div class="container">

    <div class="header">
        <h1>⚡ Monitoring Listrik Real-Time</h1>
        <p>Sistem PZEM-004T + ESP32 + MQTT + Node-RED</p>
    </div>

    <div class="clock-container">
        <div class="clock" id="clock">00:00:00</div>
        <div id="date">Memuat...</div>
    </div>

    <div class="main-grid">
        <div class="card power">
            <h3>Daya Sesaat</h3>
            <h1 id="power">0.00</h1>
            <p>Watt</p>
        </div>

        <div class="card energy">
            <h3>Energi</h3>
            <h1 id="energy">0.0000</h1>
            <p>kWh</p>
        </div>

        <div class="card cost">
            <h3>Biaya</h3>
            <h1 id="cost">Rp 0</h1>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-card"><span>Tegangan</span><span id="voltage">0 V</span></div>
        <div class="detail-card"><span>Arus</span><span id="current">0 A</span></div>
        <div class="detail-card"><span>Frekuensi</span><span id="frequency">0 Hz</span></div>
        <div class="detail-card"><span>Power Factor</span><span id="pf">0</span></div>
        <div class="detail-card"><span>Tarif</span><span id="tarif">Rp 0/kWh</span></div>
    </div>

    <div class="chart-container">
        <h3>📈 Grafik Daya Real-Time</h3>
        <canvas id="powerChart"></canvas>
    </div>

    <div class="status-bar">
        <div>
            <span id="statusDot" class="status-dot"></span>
            <span id="statusText">Menghubungkan...</span>
        </div>
        <div id="timestamp">Update terakhir: --</div>
    </div>

    <div class="debug-panel">
        <button onclick="toggleDebug()">Debug Log</button>
        <div id="debugSection" style="display:block;">
            <div class="debug-log" id="debugLog"></div>
        </div>
    </div>

</div>

<script>

const SERVER_IP = "localhost";
const WS_URL = `ws://${SERVER_IP}:1880/ws/energy`;
const API_URL = `http://${SERVER_IP}:1880/api/data`;

let ws = null;
let debugEnabled = true;
let lastUpdateTime = null;

function debugLog(msg) {
    if (!debugEnabled) return;
    const box = document.getElementById("debugLog");
    const time = new Date().toLocaleTimeString("id-ID");
    box.innerHTML += `[${time}] ${msg}<br>`;
    box.scrollTop = box.scrollHeight;
}

function toggleDebug() {
    debugEnabled = !debugEnabled;
    document.getElementById("debugSection").style.display = debugEnabled ? "block" : "none";
}

function updateClock() {
    const now = new Date();
    document.getElementById("clock").innerText =
        now.toLocaleTimeString("id-ID", { hour12: false });
    document.getElementById("date").innerText =
        now.toLocaleDateString("id-ID", { weekday: "long", year: "numeric", month: "long", day: "numeric" });
}
setInterval(updateClock, 1000);
updateClock();

// CHART
const powerChart = new Chart(
    document.getElementById("powerChart").getContext("2d"),
    {
        type: "line",
        data: { labels: [], datasets: [{ label:"Daya (W)", data:[], borderColor:"#667eea" }]},
        options: { responsive: true }
    }
);

function updateDisplay(data) {
    document.getElementById("power").innerText = data.power.toFixed(2);
    document.getElementById("energy").innerText = data.energy_kwh.toFixed(4);
    document.getElementById("cost").innerText = `Rp ${data.biaya}`;
    document.getElementById("voltage").innerText = `${data.voltage} V`;
    document.getElementById("current").innerText = `${data.current} A`;
    document.getElementById("frequency").innerText = `${data.frequency} Hz`;
    document.getElementById("pf").innerText = data.power_factor;
    document.getElementById("tarif").innerText = `Rp ${data.tarif}/kWh`;

    powerChart.data.labels.push(new Date().toLocaleTimeString("id-ID"));
    powerChart.data.datasets[0].data.push(data.power);
    if (powerChart.data.labels.length > 60) {
        powerChart.data.labels.shift();
        powerChart.data.datasets[0].data.shift();
    }
    powerChart.update();

    document.getElementById("statusDot").classList.add("connected");
    document.getElementById("statusText").innerText = "Terhubung";

    lastUpdateTime = new Date();
    document.getElementById("timestamp").innerText =
        "Update terakhir: " + lastUpdateTime.toLocaleTimeString("id-ID");
}

function connectWebSocket() {
    debugLog("Menyambungkan WebSocket...");

    ws = new WebSocket(WS_URL);

    ws.onopen = () => debugLog("WS Connected!");

    ws.onmessage = evt => {
        try {
            const data = JSON.parse(evt.data);
            updateDisplay(data);
            debugLog("Data diterima via WS.");
        } catch (err) {
            debugLog("Error parsing WS: " + err);
        }
    };

    ws.onclose = () => {
        debugLog("WS Disconnected. Reconnecting...");
        document.getElementById("statusDot").classList.remove("connected");
        document.getElementById("statusText").innerText = "Terputus";
        setTimeout(connectWebSocket, 3000);
    };
}

connectWebSocket();

// API FETCH
setInterval(() => {
    fetch(API_URL)
        .then(r => r.json())
        .then(data => {
            updateDisplay(data);
            debugLog("Data diterima via API.");
        })
        .catch(() => debugLog("API Error"));
}, 10000);

</script>

</body>
</html>
