@extends('layouts.app') 
{{-- Pastikan 'layouts.app' sesuai dengan nama file layout utama Anda (bisa juga 'layouts.admin' atau 'layouts.master') --}}

@section('content')
<div class="container-fluid">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Smart Appliance Dashboard</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <iframe 
                src="http://127.0.0.1:5000" 
                style="width: 100%; height: 850px; border: none;"
                title="Python Smart Dashboard">
                Browser Anda tidak mendukung iFrame.
            </iframe>
        </div>
    </div>

</div>
@endsection

{{-- 
    Jika Web Python Anda sudah punya styling sendiri, 
    Anda bisa MENGHAPUS bagian @push('styles') di bawah ini 
    agar file lebih bersih. 
--}}

@push('styles')
<style>
/* CSS Lama Anda (Bisa dihapus jika tidak dipakai lagi) */
.schedule-section { margin-top: 50px; }
.schedule-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 25px;
}
/* ... sisa css lainnya ... */
</style>
@endpush
