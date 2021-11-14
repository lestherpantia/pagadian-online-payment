@extends('layout.main')

@section('header')
    @include('layout.header')
@stop

@section('content')

    <div id="app">
        <home-component></home-component>
    </div>

@stop

@section('footer')
    @include('layout.footer')
@stop
