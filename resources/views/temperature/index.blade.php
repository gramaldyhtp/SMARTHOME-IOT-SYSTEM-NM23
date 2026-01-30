@extends('layouts.app')

@section('content')

<!-- CDN (AMAN: TIDAK SENTUH <body>) -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

<style>
.card { transition: all 0.25s ease }
.card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.08) }
.title-gradient {
    background: linear-gradient(to right, #2563eb, #1d4ed8);
    -webkit-background-clip: text;
    color: transparent;
}
</style>

<!-- ================== WRAPPER AMAN ================== -->
<div class="p-6 bg-gray-100 min-h-screen">

<div class="max-w-6xl mx-auto">

    <h1 class="text-3xl font-extrabold text-center mb-8 title-gradient">
        Smart Temperature & Humidity
    </h1>

    <!-- ================= CARD SENSOR ================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- SUHU -->
        <div class="card bg-white p-6 rounded-2xl shadow border-l-4 border-blue-500">
            <h2 class="text-xl font-semibold text-gray-600">Suhu Ruangan</h2>
            <p id="suhuValue" class="text-6xl font-extrabold text-blue-600 mt-4">-- °C</p>
            <p class="text-gray-400 text-sm mt-2">
                Update: <span id="lastUpdate">-</span>
            </p>
        </div>

        <!-- KELEMBAPAN -->
        <div class="card bg-white p-6 rounded-2xl shadow border-l-4 border-cyan-500">
            <h2 class="text-xl font-semibold text-gray-600">Kelembapan</h2>
            <p id="humValue" class="text-6xl font-extrabold text-cyan-600 mt-4">-- %</p>
            <p class="text-gray-400 text-sm mt-2">
                Update: <span id="humUpdate">-</span>
            </p>
        </div>

        <!-- KONTROL KIPAS -->
        <div class="card bg-white p-6 rounded-2xl shadow border-l-4 border-gray-500">
            <h2 class="text-xl font-semibold text-gray-600 mb-3">Kontrol Kipas</h2>

            <div class="flex items-center gap-3 mb-4">
                <div id="indicator" class="w-4 h-4 rounded-full bg-gray-300"></div>
                <p id="relayStatus" class="text-3xl font-extrabold text-gray-400">
                    Menunggu...
                </p>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <button onclick="sendCommand('AUTO')" class="py-2 bg-gray-700 text-white rounded-lg font-bold hover:bg-gray-800">AUTO</button>
                <button onclick="sendCommand('ON')" class="py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700">ON</button>
                <button onclick="sendCommand('OFF')" class="py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700">OFF</button>
            </div>

            <p id="modeStatus" class="text-center text-gray-400 mt-3 text-sm font-medium">
                Mode: -
            </p>
        </div>

    </div>

    <!-- ================= GRAFIK ================= -->
    <div class="card bg-white p-6 rounded-2xl shadow mt-8">
        <h2 class="text-xl font-semibold text-gray-600 mb-4">
            Grafik Suhu Real-Time
        </h2>
        <div class="relative h-64">
            <canvas id="chartSuhu"></canvas>
        </div>
    </div>

</div>
</div>

<script>
/* ================= MQTT CONFIG ================= */
const BROKER_URL = "wss://broker.emqx.io:8084/mqtt";
const TOPIC_DATA = "suhu/smarthome/samuel/all";
const TOPIC_CMD  = "suhu/smarthome/samuel/cmd";
const CLIENT_ID  = "TempWeb_" + Math.random().toString(16).substr(2,8);

/* ================= DOM ================= */
const elSuhu  = document.getElementById("suhuValue");
const elHum   = document.getElementById("humValue");
const elUpd   = document.getElementById("lastUpdate");
const elHumU  = document.getElementById("humUpdate");
const elRelay = document.getElementById("relayStatus");
const elMode  = document.getElementById("modeStatus");
const elInd   = document.getElementById("indicator");

/* ================= CHART ================= */
const ctx = document.getElementById("chartSuhu").getContext("2d");
const chart = new Chart(ctx, {
    type: "line",
    data: {
        labels: [],
        datasets: [{
            data: [],
            borderColor: "#2563eb",
            backgroundColor: "rgba(37,99,235,0.15)",
            borderWidth: 3,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
    }
});

/* ================= MQTT ================= */
const client = mqtt.connect(BROKER_URL, { clientId: CLIENT_ID });

client.on("connect", () => {
    client.subscribe(TOPIC_DATA);
});

client.on("message", (topic, message) => {
    const data = JSON.parse(message.toString());
    const now = new Date().toLocaleTimeString("id-ID");

    if (data.suhu !== undefined) {
        elSuhu.innerText = data.suhu + " °C";
        elUpd.innerText = now;

        chart.data.labels.push(now);
        chart.data.datasets[0].data.push(data.suhu);
        if (chart.data.labels.length > 20) {
            chart.data.labels.shift();
            chart.data.datasets[0].data.shift();
        }
        chart.update();
    }

    if (data.kelembapan !== undefined) {
        elHum.innerText = data.kelembapan + " %";
        elHumU.innerText = now;
    }

    if (data.relay !== undefined) {
        elRelay.innerText = data.relay;
        elInd.className = data.relay === "NYALA"
            ? "w-4 h-4 rounded-full bg-green-500"
            : "w-4 h-4 rounded-full bg-red-500";
    }

    if (data.mode !== undefined) {
        elMode.innerText = "Mode: " + data.mode;
    }
});

/* ================= COMMAND ================= */
function sendCommand(cmd) {
    if (client.connected) {
        client.publish(TOPIC_CMD, cmd);
    } else {
        alert("MQTT belum terhubung");
    }
}
</script>

@endsection
