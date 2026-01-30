@extends('layouts.app')

@section('content')
<style>
    /* Jangan sentuh body/html supaya sidebar layout tidak ketiban */
    .cam-wrap{
        padding: 22px;
        border-radius: 18px;
        min-height: calc(100vh - 120px); /* mengikuti tinggi area content layout */
        background: radial-gradient(1200px 600px at 50% 0%, rgba(59,130,246,.12), rgba(0,0,0,0) 60%),
                    linear-gradient(180deg, #0b1020 0%, #0b1020 100%);
        color: #e5e7eb;
    }

    .cam-header{
        text-align: center;
        margin-bottom: 18px;
    }
    .cam-header h2{
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        letter-spacing: .2px;
    }
    .cam-header p{
        margin: 6px 0 0;
        font-size: 12px;
        color: rgba(229,231,235,.7);
    }

    .alert-bar{
        max-width: 760px;
        margin: 16px auto 14px;
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        gap: 12px;
        align-items: center;
        background: linear-gradient(90deg, rgba(239,68,68,.95), rgba(220,38,38,.95));
        box-shadow: 0 12px 32px rgba(239,68,68,.22);
    }
    .alert-icon{
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        background: rgba(255,255,255,.12);
        flex: 0 0 auto;
    }
    .alert-title{
        font-weight: 700;
        margin: 0;
        font-size: 14px;
        line-height: 1.2;
    }
    .alert-sub{
        margin: 2px 0 0;
        font-size: 12px;
        opacity: .9;
    }

    .card-video{
        max-width: 760px;
        margin: 0 auto;
        border-radius: 18px;
        padding: 14px;
        background: rgba(17,24,39,.45);
        border: 1px solid rgba(255,255,255,.06);
        box-shadow: 0 18px 50px rgba(0,0,0,.45);
        backdrop-filter: blur(8px);
    }

    .video-frame{
        width: 100%;
        aspect-ratio: 4 / 3;
        border-radius: 14px;
        overflow: hidden;
        background: #000;
        position: relative;
    }

    .video-frame img,
    .video-frame iframe,
    .video-frame video{
        width: 100%;
        height: 100%;
        object-fit: cover;
        border: 0;
        display: block;
    }

    .video-footer{
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        font-size: 12px;
        color: rgba(229,231,235,.65);
    }
    .badge-live{
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .dot-live{
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #22c55e;
        box-shadow: 0 0 0 3px rgba(34,197,94,.18);
    }

    /* Responsive */
    @media(max-width: 768px){
        .cam-wrap{ padding: 16px; }
        .card-video{ padding: 12px; }
    }
</style>

<div class="cam-wrap">
    <div class="cam-header">
        <h2>📷 ESP32-CAM Security Monitor</h2>
        <p>Smart Home Gateway • Kelompok 10</p>
    </div>

    {{-- ALERT (bisa kamu hide/show via JS) --}}
    <div class="alert-bar" id="motionAlert" style="display:none;">
        <div class="alert-icon">⚠️</div>
        <div>
            <p class="alert-title">Gerakan Terdeteksi!</p>
            <p class="alert-sub">Terdeteksi pada: <span id="motionTime">--</span></p>
        </div>
    </div>

    {{-- VIDEO CARD --}}
    <div class="card-video">
        <div class="video-frame" id="videoFrame">
            {{-- GANTI sumber video sesuai ESP32-CAM kamu --}}
            {{-- Contoh stream: http://IP_ESP32_CAM:81/stream --}}
            <img id="camStream" src="{{ $streamUrl ?? 'http://192.168.1.10:81/stream' }}" alt="ESP32-CAM Stream">
        </div>

        <div class="video-footer">
            <div class="badge-live">
                <span class="dot-live" id="liveDot"></span>
                <span id="liveText">Live Monitoring</span>
            </div>
            <div id="timeNow">--</div>
        </div>
    </div>
</div>

<script>
    // Jam pojok kanan bawah (biar mirip contohmu)
    function tick(){
        const now = new Date();
        const t = now.toLocaleString('id-ID');
        document.getElementById('timeNow').innerText = t;
    }
    tick();
    setInterval(tick, 1000);

    // Contoh trigger alert manual (hapus kalau kamu sudah pakai MQTT / event)
    // Misal nanti kamu panggil showMotionAlert("2025-12-15 18:38:36")
    function showMotionAlert(ts){
        document.getElementById('motionTime').innerText = ts || new Date().toLocaleString('id-ID');
        document.getElementById('motionAlert').style.display = 'flex';
        // auto hide 8 detik
        setTimeout(()=> document.getElementById('motionAlert').style.display='none', 8000);
    }

    // DEBUG: uncomment untuk test alert
    // setTimeout(()=>showMotionAlert(), 1500);
</script>
@endsection
