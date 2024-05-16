@extends("layouts.master")
@section('content')


<div class="container-xxl mt-2 ">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/user') }}">Users</a></li>
        </ol>
    </nav>
</div>



<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header">
                    <h3>@empty($model->exists) {{ __('Add') }} @else {{ __('Update') }} @endempty</h3>
                </div>
                <div class="card-body">
                    @include('user._form')

                </div>
            </div>
        </div>

    </div>
</div>
    @endsection