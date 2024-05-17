@php
use App\Models\User;

@endphp

<style>
    .eye-icon {
        position: absolute;
        top: 40px;
        right: 20px;
    }

    .position-relative {
        position: relative !important;
    }
</style>
<form id="blog-category-form" class="row needs-validation justify-content-center" action="{{ route(empty($model->exists) ? 'wallet-transaction.add' : 'wallet-transaction.update',$model->id) }}" method="post" novalidate>
    @csrf
    <div class="row align-items-starts">
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> First Name </label>
                <input type="text" class="form-control d-block" name="name" value="{{ old('name',$model->name) }}">
            </div>
            @error("name")
            <p style="color:red;">{{ $errors->first("name")}}</p>
            @enderror
        </div>


        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Email </label>
                <input type="text" class="form-control d-block" name="email" value="{{ old('email',$model->email) }}">
            </div>
            @error("email")
            <p style="color:red;">{{ $errors->first("email")}}</p>
            @enderror
        </div>
        @if($model->role_id != User::ROLE_ADMIN)
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Referral Id </label>
                <input type="text" class="form-control d-block" name="referral_id" value="{{ old('referral_id',$model->referral_id) ?? Auth::user()->referral_id }}">
            </div>
            @error("referral_id")
            <p style="color:red;">{{ $errors->first("referral_id")}}</p>
            @enderror
        </div>

        @endif




        @if(empty($model->exists))

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Referrad Code </label>
                <input type="text" class="form-control d-block" name="referrad_code" value="{{ old('referrad_code',$model->referrad_code) ?? Auth::user()->referrad_code }}">
            </div>
            @error("referrad_code")
            <p style="color:red;">{{ $errors->first("referrad_code")}}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="position-relative">

                <div class="mb-3 required">
                    <label class="pt-2 fw-bold" for="btncheck1"> Password </label>
                    <input type="password" id="password" class="form-control d-block" name="password">
                </div>
                <div class="eye-icon">
                    <i toggle="#user-confirm_password" class="fa toggle-password fa-eye-slash"></i>

                </div>
            </div>

            @error("password")
            <p style="color:red;">{{ $errors->first("password")}}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-12 change-password-field">
            <div class="position-relative">
                <div class="mb-3 required">

                    <label class="pt-2 fw-bold" for="btncheck1"> Confirm Password </label>
                    <input type="password" id="confirm_password" class="form-control d-block" name="confirm_password">
                </div>

                <div class="eye-icon">
                    <i toggle="#user-confirm_password" class="fa toggle-password-confirm fa-eye-slash"></i>

                </div>
            </div>
            @error("confirm_password")
            <p style="color:red;">{{ $errors->first("confirm_password")}}</p>
            @enderror
        </div>


        @endif

        @if(User::isAdmin() && ($model->role_id != User::ROLE_ADMIN))

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Role </label>
                <select name="role_id" class="validate form-control" id="role_id">
                    <option value="">Select Role</option>
                    @foreach($model->getRoleOptions() as $key => $role)
                    <option value="{{ $key }}" {{ (old('role_id', $model->role_id) == $key) ? 'selected' : '' }}>
                        {{ $role }}
                    </option>
                    @endforeach
                </select>
            </div>
            @error("role_id")
            <p style="color:red;">{{ $errors->first("role_id")}}</p>
            @enderror
        </div>

        @endif
        <div class="col-lg-12">
            <div class="d-flex align-items-center justify-content-end">
                <div class="downoad-btns text-end my-4">
                    <button class="btn btn-primary text-white ms-2">@empty($model->exists) {{ __('Add') }} @else {{ __('Update') }} @endempty</button>
                </div>
            </div>
        </div>
    </div>
</form>

<x-a-typeahead :model="''" :column="[
    [
        'id' =>'typeahead-input',
        'url'=>'pin-code/list'
    ],
    ]" />