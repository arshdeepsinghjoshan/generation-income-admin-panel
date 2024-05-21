@extends('layouts.master')
@section('title', 'wallet Index')

@section('content')



    <x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        [
            'url' => 'subscription/subscribed-plan',
            'label' => 'subscribed Plan',
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
                            <x-a-grid-view :id="'subscribed__plan_table'" :model="$model" :url="'subscription/subscribed-plan/get-list/'" :columns="[
                                'id',
                                'plan_id',
                                'price',
                                'duration_type',
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
