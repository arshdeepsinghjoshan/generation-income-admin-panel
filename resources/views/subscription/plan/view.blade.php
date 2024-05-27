@extends('layouts.master')
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
            'url' => 'subscription/plan',
            'label' => 'subscription Plan',
        ],
        $model->title,
    ]" />

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-body">
                    <h5>{{ !empty($model->title) ? (strlen($model->title) > 100 ? substr($model->title, 0, 100) . '...' : $model->title) : 'N/A' }}
                        <span class="{{ $model->getStateBadgeOption() }}">{{ $model->getState() }}</span>
                    </h5>

                    <x-a-detail-view :model="$model" :type="'double'" :column="[
                            'id',
                            'title',
                           'price',
                           'duration',
                           [
                                'attribute' => 'duration_type',
                                'value' => $model->getDurationType()
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


    <x-a-user-action :model="$model" attribute="state_id" :states="$model->getStateOptions()" />



    <div class="row mt-4">

        <div class="col-xl-12">
            <div class="nav-align-top ">
                <ul class="nav nav-tabs nav-fill" role="tablist">

                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-wallet-transaction" aria-controls="navs-justified-messages" aria-selected="false">
                            <i class="tf-icons bx bx-message-square"></i> Purchased Plan
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-user" aria-controls="navs-justified-messages" aria-selected="false">
                            <i class="tf-icons bx bx-message-square"></i> User
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane show active" id="navs-justified-wallet-transaction" role="tabpanel">
                        <div class="table-responsive">

 

                            <x-a-relation-grid :id="'wallet_transaction_table'" :relation="'subscribedPlans'" :model="$model"  :columns="[
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

                    
                    <div class="tab-pane fade" id="navs-justified-user" role="tabpanel">
                        <div class="table-responsive">



                            <x-a-relation-grid :id="'user_table'" :relation="'createdBy'" :model="$model" :columns="[
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
</div>
@endsection