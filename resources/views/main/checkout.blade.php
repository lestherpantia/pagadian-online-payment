@extends('layout.main')

@section('header')
    @include('layout.header')
@stop

@section('content')

    <div id="app">
        <checkout-component></checkout-component>
    </div>

@stop


@section('footer')
    @include('layout.footer')
@stop

