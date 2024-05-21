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
            @foreach ($model as $plan)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card text-center">
                    <div class="card-header">{{$plan->title ?? ''}}</div>
                    <div class="card-body">
                        <h5>Rs. {{$plan->price ?? ''}}</h5>
                        <p class="card-text">{{$plan->description ?? ''}}.</p>
                        <a href="{{url('subscription/plan/select/'.$plan->id ?? '')}}" class="btn btn-primary">Select</a>
                    </div>
                    <div class="card-footer text-muted">{{empty($plan->created_at)
                                    ? 'N/A'
                                    : date('Y-m-d h:i:s A', strtotime($plan->created_at)) ?? ''}}</div>
                </div>
            </div>
            @endforeach

        </div>

    </div>
</div>
@endsection