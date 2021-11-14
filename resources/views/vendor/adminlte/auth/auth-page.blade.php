@extends('adminlte::master')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body'){{ ($auth_type ?? 'login') . '-page' }}@stop



@section('body')
<style>

    .logo {
       width: 70px;
    }

    .box-login {
        width: 100%;
        height: 100vh;
        position: relative;
    }

    .box-login .card {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 400px;
    }

    label {
        margin: 0;
    }

    @media screen and (max-width: 1133px) {


    }

</style>


    {{--             Logo--}}
{{--    <img class="logo" src="{{ asset('image/pagadian_round.png') }}" height="80" style="position: absolute; top: 15px; left: 40px;">--}}

    <div class="box-login">
{{--         Card Box--}}
        <div class="card" style="border-radius: 15px; padding: 10px 10px">
            <div style="width: 100%" class="text-center">
                <img class="logo" src="{{ asset('image/pagadian_round.png') }}">
            </div>

{{--             Card Body--}}
            <?php $segment = request()->segments() ?>
            <div class="card-body">
                <h3 class="text-center mb-2" style="font-size: 25px; font-weight: 600">
                    @if($segment[0] == 'login')
                        Login to your account
                    @elseif($segment[0] == 'register')
                        Register your account
                    @else
                        Check your email inbox
                    @endif
                </h3>
                @yield('auth_body')
            </div>

{{--             Card Footer--}}
            @hasSection('auth_footer')
                <div class="card-footer {{ config('adminlte.classes_auth_footer', '') }}">
                    @yield('auth_footer')
                </div>
            @endif

        </div>
    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
