@extends('layout.main')

@section('header')
    @include('layout.header')
@stop

@section('content')


        @if(session()->get('error') != null)
            <div class="alert danger p-3 mt-1 mb-2 rounded" role="alert" style="font-size: 12px; background: #ff7675; border: 1px solid #d63031;color: #fff;">
                {{ session()->get('error') }}
            </div>
        @endif

        @if(session()->get('success') != null)
            <div class="alert success p-3 mt-1 mb-2 rounded" role="alert" style="font-size: 12px; background: #55efc4;border: 1px solid #00b894;color: #10ac84;">
                {{ session()->get('success') }}
            </div>
        @endif


    <div id="app">
        <home-component></home-component>
    </div>

@stop

@section('footer')
    @include('layout.footer')
@stop
