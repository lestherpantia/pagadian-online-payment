@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )

@if (config('adminlte.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $register_url = $register_url ? route($register_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
    @php( $register_url = $register_url ? url($register_url) : '' )
@endif

@section('auth_header', __('adminlte::adminlte.register_message'))

@section('auth_body')

    <form action="{{ $register_url }}" method="post" style="font-size: 14px">
        {{ csrf_field() }}

        {{-- Name field --}}
        <label>Full name</label>
        <div class="input-group mb-3">
            <input type="text" name="name" class="form-control p-2 rounded {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}" autofocus>
            @if($errors->has('name'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('name') }}</strong>
                </div>
            @endif
        </div>

        <div class="row">
            {{-- Password field --}}
            <div class="col-6">
                <label>Password</label>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control p-2 rounded {{ $errors->has('password') ? 'is-invalid' : '' }}">
                    @if($errors->has('password'))
                        <div class="invalid-feedback">
                            <strong>{{ $errors->first('password') }}</strong>
                        </div>
                    @endif
                </div>
            </div>
            {{-- Confirm password field --}}
            <div class="col-6">
                <label>Re-type Password</label>
                <div class="input-group mb-3">
                    <input type="password" name="password_confirmation" class="form-control p-2 rounded {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}">
                    @if($errors->has('password_confirmation'))
                        <div class="invalid-feedback">
                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Email field --}}
        <label>Email Address</label>
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control p-2 rounded {{ $errors->has('email') ? 'is-invalid' : '' }}"
                   value="{{ old('email') }}">
            @if($errors->has('email'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('email') }}</strong>
                </div>
            @endif
        </div>

        {{-- Mobile Number --}}
        <label>Mobile Number</label>
        <div class="input-group mb-3">
            <input type="text" name="mobile" class="form-control p-2 rounded {{ $errors->has('mobile') ? 'is-invalid' : '' }}" maxlength="11">
            @if($errors->has('mobile'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('mobile') }}</strong>
                </div>
            @endif
        </div>

        <div class="term-box">
            <input type="checkbox" name="terms" id="terms">
            <label for="terms" style="font-weight: normal">I agree to the <a style="color:#01AA4F">Terms</a> and <a style="color:#01AA4F">Privacy Policy</a>.</label>
            @if($errors->has('terms'))
                <div>
                    <strong style="color: #dc3545; font-size: 11px;">Please check the Terms and Agreement.</strong>
                </div>
            @endif
        </div>

        <style>
            ul {
                list-style: none;
            }

            ul li:before {
                content: '✓';
                margin-right: 5px;
            }
        </style>

        <ul style="font-size: 12px; padding-left: 0">
            <li>At least 8 Characters</li>
            <li>At least one uppercase letter</li>
            <li>At least one number</li>
        </ul>

        {{-- Register button --}}

        <button type=submit class="btn btn-block rounded mt-3" style="background: #01AA4F; color: #fff; font-size: 15px">
            Sign up now!
        </button>

        <p class="my-0 mt-2 text-center">
            Already had an account? <a style="color: #01AA4F" href="{{ $login_url }}"> {{ 'Login' }}</a>
        </p>

    </form>
@stop
