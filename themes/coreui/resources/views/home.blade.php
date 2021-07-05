@extends('layouts.app')

@section('content')

    <div class="card">
        <h5 class="card-header">Início</h5>
        <div class="card-body">
            <h2 class="card-title">Bem-vindo(a), {{ auth()->user()->name }}!</h2>
        </div>
    </div>

@endsection

@section('scripts')

@endsection
