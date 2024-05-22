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
            'url' => 'subscription/subscribed-plan',
            'label' => 'subscribed Plan',
        ],
        !empty($model->subscriptionPlan && $model->subscriptionPlan->title) ? $model->subscriptionPlan->title : 'N/A',
    ]" />

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-body">
                    <h5>{{ !empty($model->subscriptionPlan && $model->subscriptionPlan->title) ? $model->subscriptionPlan->title : 'N/A' }}
                        <span class="{{ $model->getStateBadgeOption() }}">{{ $model->getState() }}</span>
                    </h5>

                    <x-a-detail-view :model="$model" :type="'double'" :column="[
                            'id',
                            
                            [
                                'attribute' => 'plan',
                                'value' => !empty($model->subscriptionPlan && $model->subscriptionPlan->title) ? $model->subscriptionPlan->title : 'N/A'
                            ],

                            [
                                'attribute' => 'price',
                                'value' => !empty($model->subscriptionPlan && $model->subscriptionPlan->price) ? $model->subscriptionPlan->price : 'N/A'
                            ],



                            [
                                'attribute' => 'duration_type',
                                'value' => !empty($model->subscriptionPlan && $model->subscriptionPlan->getDurationType()) ? $model->subscriptionPlan->getDurationType() : 'N/A'
                            ],

                            [
                                'attribute' => 'duration',
                                'value' => !empty($model->subscriptionPlan && $model->subscriptionPlan->duration) ? $model->subscriptionPlan->duration : 'N/A'
                            ],
                          [
                                'attribute' => 'roi_count',
                                'value' => $model->roi_count,
                                'visible' => User::isAdmin(),
                            ],
                            'roi_complete_count',
                            [
                                'attribute' => 'start_date',
                                'value' => empty($model->start_date)
                                    ? 'N/A'
                                    : date('Y-m-d h:i:s A', strtotime($model->start_date)),
                            ],
                            [
                                'attribute' => 'end_date',
                                'value' => empty($model->end_date)
                                    ? 'N/A'
                                    : date('Y-m-d h:i:s A', strtotime($model->end_date)),
                              
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



</div>
@endsection