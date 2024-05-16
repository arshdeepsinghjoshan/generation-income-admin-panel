@extends("layouts.master")
@section('title', 'User Index')

@section("content")

<div class="container-xxl mt-2 ">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('rbac/user') }}">Users</a></li>
        </ol>
    </nav>
</div>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">

                <h5 class="card-header">{{ __('Index') }}</h5>

                <div class="card-body">

                    <x-a-update-menu-items :model="$model" :action="'index'" />

                    <div class="table-responsive">
                        <x-a-grid-view :id="'user_table'" :model="$model" :url="Request::segment(2) ? 'user/get-list/' . Request::segment(2) : 'user/get-list/0'" :columns="
                               [
                                 'id',
                                'name',
                                'role_id',
                                'email',
                                'status',
                                'created_at',
                                'created_by',
                                'action'
                                 ]" />
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection