@extends("layouts.master")
@section('content')

<?php

use App\Models\User;
?>

<div class="container-xxl mt-2 ">

   <nav>
      <ol class="breadcrumb">
         <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
         <li class="breadcrumb-item"><a href="{{ url('/user') }}">Users</a></li>
         <li class="breadcrumb-item">{{ empty($model) ? 'N/A' : $model->full_name }}</li>
      </ol>
   </nav>
</div>

<div class="container-xxl flex-grow-1 container-p-y">
   <div class="row">
      <div class="col-lg-12 mb-4 order-0">
         <div class="card">
            <div class="card-body">
               <h5>{{ !empty($model->name) ? (strlen($model->name) > 100 ? substr($model->name, 0, 100) . '...' : $model->name) : 'N/A' }} <span class="{{$model->getStateBadgeOption()}}">{{ $model->getState()}}</span></h5>

               <x-a-detail-view :model="$model" :type="'double'" :column="
    [
    'id',
      'email',
      'name',
      [
        'attribute' => 'role_id',
        'label' => 'Role',
        'value' => $model->getRole(),
        'visible'=> true

          
     ],
     [
        'attribute' => 'created_at',
        'label' => 'Created at',
        'value' => (empty($model->created_at)) ? 'N/A' : date('Y-m-d h:i:s A', strtotime($model->created_at)),
     ],
      [
        'attribute' => 'updated_at',
        'label' => 'Updated at',
        'value' => (empty($model->updated_at)) ? 'N/A' : date('Y-m-d h:i:s A', strtotime($model->updated_at)),
        'visible'=> ($model->role_id != User::ROLE_ADMIN && $model->id != Auth::id())    
     ],
     
     [
        'attribute' => 'created_by_id',
        'label' => 'Created By',
        'value' => !empty($model->createdBy && $model->createdBy->name) ? $model->createdBy->name : 'N/A',
        'visible'=> true

     ],
    ]
    " />
            </div>
         </div>
      </div>
   </div>


   @if($model->role_id != User::ROLE_ADMIN && $model->id != Auth::id())
   <x-a-user-action :model="$model" attribute="state_id" :states="$model->getStateOptions()" />
   @endif


</div>
@endsection