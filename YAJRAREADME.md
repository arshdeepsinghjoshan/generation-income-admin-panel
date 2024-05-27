## Laravel Yajra DataTables Integration

This Laravel project demonstrates the integration of Yajra DataTables, a jQuery DataTables API for Laravel. Yajra DataTables provides a fluent interface to DataTables jQuery plugin, making it easy to create complex, interactive tables from Eloquent models, collections, or custom data sources.


## Features

-Seamless integration with Laravel Eloquent models.
-Supports server-side processing for handling large datasets efficiently.
-Easy customization and configuration options for DataTables.
-Provides methods for simple CRUD operations within DataTables.


## Installation

## Requirements

Laravel 9|10
jQuery DataTables v1.10.x



Run the following command in your project to get the latest version of the package:

```
composer require yajra/laravel-datatables-oracle:"^10.3.1"
```


Laravel 9

```
composer require yajra/laravel-datatables:"^9.0"
```




Laravel 10

```
composer require yajra/laravel-datatables:^10.0
```



## Configuration

Open the file config/app.php and then add following service provider.

'providers' => [
    // ...
    Yajra\DataTables\DataTablesServiceProvider::class,
],


```
php artisan vendor:publish --tag=datatables
```


-First add yajra datatable componet in file


```
<x-a-grid-view :id="'department_table'" :buttons="
                                [
                                    'excel',
                                    [
                                     'extend'=>'pdf',
                                     'className'=>'class name',
                                     'title'=>'file name',
                                     'header'=>true,
                                    ],
                                    
                                ]" :url="'department/get-list'" :columns="
                               [
                                 'id',
                                'title',
                                'status',
                                'created_at',
                                'created_by',
                                'action',
                                 ]" />
```

## If you want to change heading names, create this function for the current model.

updated code


```
 <x-a-grid-view :id="'abac_action_table'" :model="$model" :url="'admin/rbac/action/get-list'" :columns="
                               [
                              
                                 'id',
                                'controller_id',
                                [
                                 'attribute'=> 'action_type',
                                 'label'=>'Title'
                                ],
                                'created_at',
                                'status',
                                'created_by',
                                'action',
                                 ]" />







     public function attributeLabels($label)
    {
        $list =  [
            'id' => __('ID'),
            'name' => __('Full Name'),
            'email' => __('Email'),
            'unique_id' => __('Username'),
            'referral_unique_id' => __('Referral'),
            'sponser_unique_id' => __('Upline'),
        ];

        return $list[is_array($label) ? $label['attribute'] ?? 'Invalid label format' : $label] ?? ucwords(str_replace('_', ' ', is_array($label) ? $label['label'] ?? $label['attribute'] : $label));
    }



## Create Route 

--Route::get('/department/get-list', [DepartmentController::class, 'getDepartmenttList']);


## create function for the current controller.

     public function getDepartmenttList(Request $request)
    {

        $query  = Department::with(['createdBy']);
        if (!empty($request->get('search')['value'])) {
            $searchValue = $request->get('search')['value'];
            $searchTerms = explode(' ', $searchValue);
            $query->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $setFilteredRecord =  $q->where('id', 'like', "%$term%")
                        ->orWhere('title', 'like', "%$term%") \\your table column
                        ->orWhere('description', 'like', "%$term%")
                        ->orWhere('created_at', 'like', "%$term%")
                        ->orWhereHas('createdBy', function ($query) use ($term) {
                            $query->where('first_name', 'like', "%$term%");
                            $query->where('last_name', 'like', "%$term%");
                        })->orWhere(function ($query) use ($term) {
                            $query->searchState($term);
                        });
                    $this->setFilteredRecords =    $setFilteredRecord->count();
                }
            });
        }
        $totalRecords = $query->count();
        $start = ($request->start) ? $request->start : 0;
        $pageSize = ($request->length) ? $request->length : 10;
        $query->skip($start)->take($pageSize);
        $data = $query->get();
        return Datatables::of($data)
        ->addColumn('title', function ($data) {
            return !empty($data->title) ? (strlen($data->title) > 50 ? substr(ucfirst($data->title), 0, 50) . '...' : ucfirst($data->title)) : 'N/A';
        })
            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('status', function ($data) {
                return '<span class="badge badge-' . $data->getState() . '">' . $data->getState() . '</span>';
            })

         
            ->addColumn('id', function ($data) {
                return $this->s_no ++;
            })

            ->addColumn('created_at', function ($data) {
                return (empty($data->created_at)) ? 'N/A' : date('Y-m-d', strtotime($data->created_at));
            })
            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                $html .= ' <a class="btn btn-primary " href="' . url('department/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
                $html .=    '  <a class="btn btn-primary " href="' . url('department/view/' . $data->id) . '"  ><i class="fa fa-eye
                    "data-toggle="tooltip"  title="View"></i></a>';
                $html .=    ' <a class="btn btn-danger custom-delete" href="' . url('department/stateChange/' . $data->id . '/' . Department::STATE_DELETE) . '"  ><i class="fa fa-trash"data-toggle="tooltip"  title="Delete"></i></a>';
                $html .=  '</div>';
                return $html;
            })->addColumn('customerClickAble', function ($data) {
                $html = 0;

                return $html;
            })
            ->rawColumns(['action', 'customerClickAble', 'status', 'created_by'])->setTotalRecords($totalRecords)->setFilteredRecords($this->setFilteredRecords)->skipPaging()
            ->make(true);
    }




Custom filter with custom button index side

 <x-a-grid-view :id="'order_table'" :url="'client/order/get-list'" :columns="
                             
                             
                                 
                                 
                                 :filterButtonId="'order_filter_button'"
                                 
                                  :customfilterIds="
                                [
                                    'start_date',
                                    'warehouse_id',
                                ]" 
                                />


Custom filter with custom button add controller
       if (!empty($request->start_date)) {
            $query->where('start_date', $request->start_date);
        }
        if (!empty($request->warehouse_id)) {
            $query->where('warehouse_id', $request->warehouse_id);
        }