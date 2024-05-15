@extends('layouts.guest')

@section('title', 'Login Page')
@section('content')

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register Card -->
                <div class="card">
                    <div class="card-body">
                        @include('include.message')
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <h2>{{ env('APP_NAME') }}</h2>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-2">Adventure starts here</h4>
                        <p class="mb-4">Make your app management easy and fun!</p>

                        <form id="formAuthentication" class="mb-3" action="{{ route('add.registration') }}" method="POST">

                            @csrf
                            <div class="mb-3 required">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name"
                                    value="{{ old('name', $model->name) }}" name="name" placeholder="Enter your name" />
                            </div>
                            <div class="mb-3 required">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email"
                                    value="{{ old('email', $model->email) }}" name="email"
                                    placeholder="Enter your email" />
                            </div>
                            <div class="mb-3 required">
                                <label for="referrad_code" class="form-label">Referrad Code</label>
                                <input type="text" class="form-control" id="referrad_code"
                                    value="{{ old('referrad_code', $model->referrad_code) }}" name="referrad_code"
                                    placeholder="Enter your rferrad code" />
                            </div>
                            <div class="mb-3 required form-password-toggle">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>

                            <div class="mb-3 required">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" />
                                    <label class="form-check-label" for="terms-conditions">
                                        I agree to
                                        <a href="javascript:void(0);">privacy policy & terms</a>
                                    </label>
                                </div>
                            </div>
                            <button class="btn btn-primary d-grid w-100">Sign up</button>
                        </form>

                        <p class="text-center">
                            <span>Already have an account?</span>
                            <a href="{{ url('login') }}">
                                <span>Sign in instead</span>
                            </a>
                        </p>
                    </div>
                </div>
                <!-- Register Card -->
            </div>
        </div>
    </div>
@stop
