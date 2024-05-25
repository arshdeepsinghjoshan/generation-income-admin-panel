@extends('layouts.master')
@section('title', 'wallet Index')

@section('content')



<x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
      'Support Department'
    ]" />



<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">

            <div class="card">
                <div class="card-header">
                    <h3>{{ __('Add') }}</h3>
                </div>
                <div class="card-body">
                    <form id="blog-category-form" class="row needs-validation justify-content-center" Action="{{ route('supportDepartment.add')}}" method="post" novalidate>
                        @csrf
                        <div class="col-md-6">
                            <div class="d-md-flex align-items-start">
                                <div class="flex-grow-1">
                                    <div class="mb-3 field-category-title required">
                                        <label class="form-label" for="category-title">Name</label>
                                        <input type="text" id="category-title" class="form-control" value="{{ old('title') }}" id="validationCustom01" name="title" maxlength="150">
                                    </div>
                                </div>
                                <div class="mt-3 mt-sm-4 text-center ml-sm-3">
                                    <button type="submit" id="blog-category-form-submit" class="btn btn-primary m-1">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <h5 class="card-header">{{ __('Index') }}</h5>
                <div class="card-body">
                    <x-a-update-menu-items :model="$model" :action="'index'" />
                    <div class="table-responsive">
                        <x-a-grid-view :id="'support_department_table'" :model="$model" :url="'support/department/get-list/'" :columns="[
                                'id',
                                'title',
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