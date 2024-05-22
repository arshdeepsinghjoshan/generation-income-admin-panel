@extends('layouts.master')
@section('content')

@section('title', 'Wallet View')

<?php

use App\Models\User;
?>
<x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        [
            'url' => 'wallet/wallet-transaction',
            'label' => 'Wallet Transaction',
        ],
        !empty($model->wallet && $model->wallet->wallet_number) ? $model->wallet->wallet_number : 'N/A',
    ]" />

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-body">
                    <h5>{{ !empty($model->wallet && $model->wallet->wallet_number) ? $model->wallet->wallet_number : 'N/A' }}
                        <span class="{{ $model->getStateBadgeOption() }}">{{ $model->getState() }}</span>
                    </h5>

                    <x-a-detail-view :model="$model" :type="'double'" :column="[
                            'id',
                            [
                                'attribute' => 'wallet_number',
                                'value' => !empty($model->wallet && $model->wallet->wallet_number) ? $model->wallet->wallet_number : 'N/A',
                              
                            ],

                            [
                                'attribute' => 'transaction_type',
                                'value' => $model->getTransactionType(),
                              
                            ],

                            [
                                'attribute' => 'type_id',
                                'value' => $model->getType(),
                              
                            ],

                           'amount',
                            [
                                'attribute' => 'created_at',
                                'label' => 'Created at',
                                'value' => empty($model->created_at)
                                    ? 'N/A'
                                    : date('Y-m-d h:i:s A', strtotime($model->created_at)),
                            ],
                            [
                                'attribute' => 'updated_at',
                                'label' => 'Updated at',
                                'value' => empty($model->updated_at)
                                    ? 'N/A'
                                    : date('Y-m-d h:i:s A', strtotime($model->updated_at)),
                              
                            ],
                        
                            [
                                'attribute' => 'created_by_id',
                                'label' => 'Created By',
                                'value' => !empty($model->createdBy && $model->createdBy->name)
                                    ? $model->createdBy->name
                                    : 'N/A',
                                'visible' => true,
                            ],
                            'description'
                        ]" />
                </div>
            </div>
        </div>
    </div>


    @if ($model->role_id != User::ROLE_ADMIN && $model->id != Auth::id())
    <x-a-user-action :model="$model" attribute="state_id" :states="$model->getStateOptions()" />
    @endif


</div>
@endsection