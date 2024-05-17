@extends('layouts.master')
@section('title', 'User View')
@section('content')
<?php

use App\Models\User;
?>
<x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        [
            'url' => 'user',
            'label' => 'Users',
        ],
        $model->name,
    ]" />

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-body">
                    <h5>{{ !empty($model->name) ? (strlen($model->name) > 100 ? substr($model->name, 0, 100) . '...' : $model->name) : 'N/A' }}
                        <span class="{{ $model->getStateBadgeOption() }}">{{ $model->getState() }}</span>
                    </h5>

                    <x-a-detail-view :model="$model" :type="'double'" :column="[
                            'id',
                            'email',
                            'name',
                            'referral_id',
                            'referrad_code',
                            [
                                'attribute' => 'role_id',
                                'label' => 'Role',
                                'value' => $model->getRole(),
                                'visible' => true,
                            ],
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
                                'visible' => $model->role_id != User::ROLE_ADMIN && $model->id != Auth::id(),
                            ],
                        
                            [
                                'attribute' => 'created_by_id',
                                'label' => 'Created By',
                                'value' => !empty($model->createdBy && $model->createdBy->name)
                                    ? $model->createdBy->name
                                    : 'N/A',
                                'visible' => true,
                            ],
                        ]" />
                </div>
            </div>
        </div>
    </div>


    @if ($model->role_id != User::ROLE_ADMIN && $model->id != Auth::id())
    <x-a-user-action :model="$model" attribute="state_id" :states="$model->getStateOptions()" />
    @endif


    <!-- <div class="card m"> -->
    <div class="row mt-4">

        <div class="col-xl-12">
            <div class="nav-align-top ">
                <ul class="nav nav-tabs nav-fill" role="tablist">

                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-wallet" aria-controls="navs-justified-messages" aria-selected="false">
                            <i class="tf-icons bx bx-message-square"></i> Wallet
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-wallet-history" aria-controls="navs-justified-messages" aria-selected="false">
                            <i class="tf-icons bx bx-message-square"></i> Wallet Transaction
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane show active" id="navs-justified-wallet" role="tabpanel">
                        <div class="table-responsive">

                            <x-a-grid-view :id="'wallet_table'" :model="$model" :url="'wallet/get-list/'.$model->id" :columns="[
                                'id',
                                'wallet_number',
                                'balance',
                                'status',
                                'created_at',
                                'created_by',
                                'action',
                            ]" />

                        </div>
                    </div>


                    <div class="tab-pane fade" id="navs-justified-wallet-history" role="tabpanel">
                        <div class="table-responsive">

                            <x-a-grid-view :id="'wallet_transaction_table'" :model="$model" :url="'wallet/wallet-transaction/get-list/'.  (isset($model->wallet) && isset($model->wallet->wallet_number) ? $model->wallet->id : 'null')" :columns="[
                                'id',
                                'wallet_number',
                                'amount',
                                'type_id',
                                'transaction_type',
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
</div>
@endsection