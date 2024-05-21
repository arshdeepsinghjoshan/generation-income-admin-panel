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
<form id="blog-category-form" class="row needs-validation justify-content-center" action="{{ route(empty($model->exists) ? 'subscriptionPlan.add' : 'subscriptionPlan.update',$model->id) }}" method="post" novalidate>
    @csrf
    <div class="row align-items-starts">
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Title </label>
                <input type="text" class="form-control d-block" name="title" value="{{ old('title',$model->title) }}">
            </div>
            @error("title")
            <p style="color:red;">{{ $errors->first("title")}}</p>
            @enderror
        </div>


        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Description </label>
                <input type="text" class="form-control d-block" name="description" value="{{ old('description',$model->description) }}">
            </div>
            @error("description")
            <p style="color:red;">{{ $errors->first("description")}}</p>
            @enderror
        </div>
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Price </label>
                <input type="text" class="form-control d-block" name="price" value="{{ old('price',$model->price) ?? $model->price}}">
            </div>
            @error("price")
            <p style="color:red;">{{ $errors->first("price")}}</p>
            @enderror
        </div>


        <div class="col-xl-4 col-lg-4 col-md-6 col-12 change-password-field">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1">Duration Type </label>
                <select name="duration_type" class="validate form-control" id="duration_type">
                    <option value="">Select Duration Type</option>
                    @foreach ($model->getDurationTypeOptions() as $key => $role)
                    <option value="{{ $key }}" {{ old('duration_type', $model->duration_type) == $key ? 'selected' : '' }}>
                        {{ $role }}
                    </option>
                    @endforeach
                </select>
            </div>
            @error("duration")
            <p style="color:red;">{{ $errors->first("duration")}}</p>
            @enderror
        </div>


        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="position-relative">

                <div class="mb-3 required">
                    <label class="pt-2 fw-bold" for="btncheck1"> Duration </label>
                    <input type="text" id="duration"  value="{{ old('duration',$model->duration) ?? $model->duration}}" class="form-control d-block" name="duration">
                </div>
              
            </div>

            @error("duration")
            <p style="color:red;">{{ $errors->first("duration")}}</p>
            @enderror
        </div>

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