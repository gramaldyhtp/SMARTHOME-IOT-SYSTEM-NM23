<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Energy Monitoring</title>

    <style>
        /* ====== CSS DARI KELOMPOK 3 (Digabung Utuh) ====== */

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0d1220;
            min-height: 100vh;
            padding: 20px;
            color: #fff;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: #11182d;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #fff;
            font-size: 2em;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #aaa;
        }

        .clock-container {
            background: #11182d;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .clock {
            font-size: 3em;
            font-weight: bold;
            color: #6ab7ff;
            letter-spacing: 3px;
        }

        .date {
            font-size: 1.2em;
            color: #aaa;
            margin-top: 10px;
        }

        .main-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
        }

        .card {
            background: #11182d;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: 0.3s;
        }

        .card-title {
            font-size: 1.1em;
            color: #aaa;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .card-value {
            font-size: 3em;
            font-weight: bold;
            color: #fff;
        }

        .card-unit { font-size: 1.5em; color: #888; }

        .detail-grid {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .detail-card {
            background: #11182d;
            padding: 20px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .status-bar {
            margin-top: 30px;
            background: #11182d;
            padding: 20px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #4ade80;
        }

        .status-dot.disconnected {
            background: #ef4444;
        }

    </style>
</head>

<body>

<div class="container">

    <div class="header">
        <h1>⚡ Smart Energy Monitoring</h1>
        <p class="subtitle">PZEM-004T + ESP32 + MQTT + Node-RED + Laravel</p>
    </div>

    <div class="clock-container">
        <div id="clock" class="clock">00:00:00</div>
        <div id="date" class="date">Loading...</div>
    </div>

    <div class="main-grid">

        <div class="card">
            <div class="card-title">Daya Sekarang</div>
            <div id="power" class="card-value">0.00</div>
            <div class="card-unit">Watt</div>
        </div>

        <div class="card">
            <div class="card-title">Energi</div>
            <div id="energy" class="card-value">0.0000</div>
            <div class="card-unit">kWh</div>
        </div>

        <div class="card">
            <div class="card-title">Biaya</div>
            <div id="cost" class="card-value">Rp 0</div>
        </div>

    </div>

    <div class="detail-grid">
        <div class="detail-card"><span>Tegangan</span><span id="voltage">0 V</span></div>
        <div class="detail-card"><span>Arus</span><span id="current">0 A</span></div>
        <div class="detail-card"><span>Frekuensi</span><span id="frequency">0 Hz</span></div>
        <div class="detail-card"><span>PF</span><span id="pf">0.00</span></div>
        <div class="detail-card"><span>Tarif</span><span id="tarif">Rp 0/kWh</span></div>
    </div>

    <div class="status-bar">
        <div style="display:flex;align-items:center;gap:10px;">
            <div id="statusDot" class="status-dot"></div>
            <span id="statusText">Menghubungkan...</span>
        </div>

        <div id="timestamp">Update terakhir: --</div>
    </div>

</div>

<script>
    /* ====== JAVASCRIPT PERBAIKAN (BUG FIXED) ====== */

    function updateClock() {
        const now = new Date();
        document.getElementById("clock").textContent =
            `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}:${String(now.getSeconds()).padStart(2,'0')}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    const WS_URL = "ws://localhost:1880/ws/pzem";
    let ws = new WebSocket(WS_URL);

    ws.onopen = () => {
        document.getElementById("statusDot").classList.remove("disconnected");
        document.getElementById("statusText").textContent = "Terhubung";
    };

    ws.onclose = () => {
        document.getElementById("statusDot").classList.add("disconnected");
        document.getElementById("statusText").textContent = "Terputus";
    };

    ws.onmessage = (event) => {
        let data = JSON.parse(event.data);

        document.getElementById("power").textContent = data.power;
        document.getElementById("energy").textContent = data.energy_kwh;
        document.getElementById("cost").textContent = "Rp " + data.biaya;

        document.getElementById("voltage").textContent = data.voltage + " V";
        document.getElementById("current").textContent = data.current + " A";
        document.getElementById("frequency").textContent = data.frequency + " Hz";
        document.getElementById("pf").textContent = data.power_factor;
        document.getElementById("tarif").textContent = "Rp " + data.tarif + "/kWh";

        document.getElementById("timestamp").textContent =
            "Update terakhir: " + new Date().toLocaleTimeString();
    }
</script>

</body>
</html>
