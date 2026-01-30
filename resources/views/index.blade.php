@extends('layouts.app')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @include('dashboard.cards')
</div>

<div class="mt-10">
    @include('dashboard.charts')
</div>

@endsection
