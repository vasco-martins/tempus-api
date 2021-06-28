@extends('layouts.app')

@section('content')
    <h2 class="mb-4">{{ __('Utilizadores') }}</h2>

    @livewire('user.index')
@endsection

