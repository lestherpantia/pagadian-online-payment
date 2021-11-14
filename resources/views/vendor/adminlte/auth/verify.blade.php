@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', __('adminlte::adminlte.verify_message'))

@section('auth_body')

    @if(session('resent'))
        <div class="alert alert-success" role="alert">
            {{ __('adminlte::adminlte.verify_email_sent') }}
        </div>
    @endif

    <p class="text-center" style="font-size: 14px">
        We sent email link to complete registration<br>
        Tip: Check your spam folder in case the email was incorrectly identified.
    </p>

    <p class="text-center mb-5" style="font-size: 14px">
        if you have not received a verification email or if you mistyped your email <br>
        address, you can resend the verification email.
    </p>

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="form-control" style="background: #01AA4F; color: #fff">
            Resend
        </button>
    </form>

@stop
