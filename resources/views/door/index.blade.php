@extends('layouts.app')

@section('content')

<!-- Tailwind hanya untuk halaman ini (AMAN) -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

@php
$mqtt_broker = "broker.emqx.io";
$mqtt_port   = 8083;

$topic_status = "home/doorlock/access";
$topic_cmd    = "home/doorlock/control";
@endphp

<div class="max-w-6xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Smart Door Lock Dashboard</h1>
        <p class="text-sm text-gray-500">
            Realtime control & monitoring via MQTT WebSocket
        </p>
    </div>

    <!-- Control Panel -->
    <div class="bg-white rounded-xl shadow p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="font-semibold text-lg">Door Control</h2>
            <p class="text-sm text-gray-500">OPEN = buka pintu, LOCK = tutup pintu</p>
            <div id="mqtt_status" class="mt-2 text-sm font-semibold text-gray-500">
                MQTT: connecting...
            </div>
        </div>

        <div class="flex gap-3 flex-wrap items-center">
            <input
                id="user_id_input"
                class="px-3 py-2 border rounded-lg text-sm"
                placeholder="user_id"
                value="web_admin"
            />

            <button
                onclick="sendCmd('open')"
                class="px-5 py-3 rounded-xl font-semibold text-white bg-green-600 hover:bg-green-700 transition"
            >
                🔓 OPEN
            </button>

            <button
                onclick="sendCmd('close')"
                class="px-5 py-3 rounded-xl font-semibold text-white bg-red-600 hover:bg-red-700 transition"
            >
                🔒 LOCK
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Status Terakhir</h2>

            <div class="flex items-end gap-3">
                <div id="door_state" class="text-4xl font-bold text-gray-700">-</div>
                <span id="door_badge" class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-sm">
                    unknown
                </span>
            </div>

            <div class="mt-4">
                <div id="access_status" class="text-xl font-semibold">-</div>
                <p class="text-gray-500 text-sm">Access Status</p>
            </div>

            <div class="mt-3">
                <div id="user_id" class="font-semibold">-</div>
                <p class="text-gray-500 text-sm">User</p>
            </div>

            <div class="mt-3">
                <div id="card_id" class="font-semibold">-</div>
                <p class="text-gray-500 text-sm">Card ID</p>
            </div>

            <div class="mt-3">
                <div id="timestamp" class="text-sm text-gray-600">-</div>
                <p class="text-gray-500 text-sm">Timestamp</p>
            </div>

            <div class="mt-4 text-sm text-gray-500" id="status_msg">
                Status: menunggu data MQTT...
            </div>
        </div>

        <!-- Log Table -->
        <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-xl font-semibold">Log (Realtime)</h2>
                <button
                    onclick="clearLog()"
                    class="px-3 py-2 rounded bg-gray-100 hover:bg-gray-200 text-sm"
                >
                    Clear
                </button>
            </div>

            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left p-2">Time</th>
                        <th class="text-left p-2">User</th>
                        <th class="text-left p-2">Card</th>
                        <th class="text-left p-2">Status</th>
                        <th class="text-left p-2">Door</th>
                    </tr>
                </thead>
                <tbody id="log_table"></tbody>
            </table>

            <p class="text-xs text-gray-500 mt-2">
                Log realtime dari MQTT (InfluxDB opsional untuk histori)
            </p>
        </div>
    </div>
</div>

<script>
const brokerUrl   = "ws://{{ $mqtt_broker }}:{{ $mqtt_port }}/mqtt";
const topicStatus = "{{ $topic_status }}";
const topicCmd    = "{{ $topic_cmd }}";

let logs = [];
const logLimit = 30;

const mqttStatusEl = document.getElementById("mqtt_status");
const statusMsg    = document.getElementById("status_msg");

const client = mqtt.connect(brokerUrl);

client.on("connect", () => {
    mqttStatusEl.textContent = "MQTT: connected";
    mqttStatusEl.className = "mt-2 text-sm font-semibold text-green-600";
    client.subscribe(topicStatus);
});

client.on("message", (topic, message) => {
    if (topic !== topicStatus) return;

    const data = JSON.parse(message.toString());

    document.getElementById("door_state").textContent = data.door_state || "-";
    document.getElementById("access_status").textContent = data.access_status || "-";
    document.getElementById("user_id").textContent = data.user_id || "-";
    document.getElementById("card_id").textContent = data.card_id || "-";
    document.getElementById("timestamp").textContent = data.timestamp || "-";

    setBadge(data.door_state);
    pushLog(data);
    statusMsg.textContent = "Status: data MQTT diterima";
});

function setBadge(state) {
    const badge = document.getElementById("door_badge");
    if (state === "open") {
        badge.textContent = "OPEN";
        badge.className = "px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm";
    } else if (state === "closed") {
        badge.textContent = "CLOSED";
        badge.className = "px-3 py-1 rounded-full bg-red-100 text-red-700 text-sm";
    } else {
        badge.textContent = "UNKNOWN";
        badge.className = "px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-sm";
    }
}

function pushLog(data) {
    logs.unshift(data);
    if (logs.length > logLimit) logs.pop();

    const tbody = document.getElementById("log_table");
    tbody.innerHTML = "";

    logs.forEach(l => {
        const tr = document.createElement("tr");
        tr.className = "border-b";
        tr.innerHTML = `
            <td class="p-2">${l.timestamp || ""}</td>
            <td class="p-2">${l.user_id || ""}</td>
            <td class="p-2">${l.card_id || ""}</td>
            <td class="p-2">${l.access_status || ""}</td>
            <td class="p-2">${l.door_state || ""}</td>
        `;
        tbody.appendChild(tr);
    });
}

function clearLog() {
    logs = [];
    document.getElementById("log_table").innerHTML = "";
}

function sendCmd(cmd) {
    const payload = {
        command: cmd,
        user_id: document.getElementById("user_id_input").value || "web_admin",
        timestamp: new Date().toISOString()
    };
    client.publish(topicCmd, JSON.stringify(payload));
}
</script>

@endsection
