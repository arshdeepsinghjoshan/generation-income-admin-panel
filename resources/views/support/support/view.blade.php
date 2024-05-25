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
            'url' => 'support',
            'label' => 'Support',
        ],
        !empty($model->title) ? (strlen($model->title) > 100 ? substr($model->title, 0, 100) . '...' : $model->title) : 'N/A'
    ]" />

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-body">
                    <h5>{{ !empty($model->title) ? (strlen($model->title) > 100 ? substr($model->title, 0, 100) . '...' : $model->title) : 'N/A' }}
                        <span class="{{ $model->getStateBadgeOption() }}">{{ $model->getState() }}</span>
                    </h5>

                    <x-a-detail-view :model="$model" :type="'double'" :column="
    [
        'id',
      'title',
     [
        'attribute' => 'department_id',
        'label' => 'Department',
        'value' => !empty($model->getDepartment) ? $model->getDepartment->title : 'N/A'
     ],
     [
        'attribute' => 'priority_id',
        'label' => 'Priority',
        'value' => $model->getPriority(),
     ],
     [
        'attribute' => 'created_at',
        'label' => 'Created at',
        'value' => (empty($model->created_at)) ? 'N/A' : date('Y-m-d h:i:s A', strtotime($model->created_at)),
     ],
     [
        'attribute' => 'created_by_id',
        'label' => 'Created By',
        'value' => !empty($model->createdBy && $model->createdBy->name) ? $model->createdBy->name : 'N/A',
     ],
     'message'
    
    ]
    " />
                </div>
            </div>
        </div>
    </div>



</div>
@endsection