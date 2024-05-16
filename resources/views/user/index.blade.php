@extends('layouts.master')
@section('title', 'User Index')

@section('content')



    <x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        [
            'url' => 'user',
            'label' => 'Users',
        ],
    ]" />

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">

                    <h5 class="card-header">{{ __('Index') }}</h5>

                    <div class="card-body">

                        <x-a-update-menu-items :model="$model" :action="'index'" />

                        <div class="table-responsive">
                            <x-a-grid-view :id="'user_table'" :model="$model" :url="Request::segment(2) ? 'user/get-list/' . Request::segment(2) : 'user/get-list/0'" :columns="[
                                'id',
                                'name',
                                'role_id',
                                'email',
                                'status',
                                'created_at',
                                'created_by',
                                'action',
                            ]" />
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
