@extends('layout.main')

@section('header')
    @include('layout.header')
@stop

@section('content')

    <div id="app">
        <payment-component></payment-component>
    </div>

@stop


@section('footer')
    @include('layout.footer')
@stop

