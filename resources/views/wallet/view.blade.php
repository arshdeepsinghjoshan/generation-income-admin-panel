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
            'url' => 'wallet',
            'label' => 'wallets',
        ],
        $model->name,
    ]" />

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <div class="card-body">
                        <h5>{{ !empty($model->wallet_number) ? (strlen($model->wallet_number) > 100 ? substr($model->wallet_number, 0, 100) . '...' : $model->wallet_number) : 'N/A' }}
                            <span class="{{ $model->getStateBadgeOption() }}">{{ $model->getState() }}</span>
                        </h5>

                        <x-a-detail-view :model="$model" :type="'double'" :column="[
                            'id',
                            'wallet_number',
                           'balance',
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


        @if ($model->role_id != User::ROLE_ADMIN && $model->id != Auth::id())
            <x-a-user-action :model="$model" attribute="state_id" :states="$model->getStateOptions()" />
        @endif


    </div>
@endsection
