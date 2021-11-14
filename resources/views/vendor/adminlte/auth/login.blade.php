@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )
@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )

@if (config('adminlte.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $register_url = $register_url ? route($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
    @php( $register_url = $register_url ? url($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif

@section('auth_header', __('adminlte::adminlte.login_message'))

@section('auth_body')
    <form action="{{ $login_url }}" method="post" style="font-size: 15px">
        {{ csrf_field() }}

        {{-- Email field --}}
        <label>E-mail</label>
        <div class="input-group mb-3">
            <input id="email" type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }} p-3 rounded" value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>
            @if($errors->has('email'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('email') }}</strong>
                </div>
            @endif
        </div>

        <label>Password</label>
        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }} p-3 rounded" placeholder="{{ __('adminlte::adminlte.password') }}">
            @if($errors->has('password'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('password') }}</strong>
                </div>
            @endif
        </div>

        {{-- Login field --}}
        <div class="row">
            <div class="col-7">
                <div class="icheck-primary">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">{{ __('adminlte::adminlte.remember_me') }}</label>
                </div>
            </div>
            <div class="col-5 text-right">
                {{-- Register link --}}

                @if($password_reset_url)
                    <p class="my-0">
                        <a href="{{ $password_reset_url }}">Forgot password?</a>
                    </p>
                @endif
            </div>
        </div>

        <button type=submit class="btn btn-block rounded mt-3" style="background: #01AA4F; color: #fff; font-size: 15px">
            Login
        </button>

        @if($register_url)
            <p class="my-0 text-center" style="margin-top: 15px !important;">
                Dont have account yet? <a href="{{ $register_url }}" style="font-weight: bold; color: #01AA4F">Sign up here.</a>
            </p>
        @endif
    </form>
@stop
