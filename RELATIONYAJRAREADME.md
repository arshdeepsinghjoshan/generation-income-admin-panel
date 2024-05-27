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
<x-a-relation-grid :id="'department_table'" :buttons="
                                [
                                    'excel',
                                    [
                                     'extend'=>'pdf',
                                     'className'=>'class name',
                                     'title'=>'file name',
                                     'header'=>true,
                                    ],
                                    
                                ]" :columns="
                               [
                                 'id',
                                'title',
                                'status',
                                'created_at',
                                'created_by',
                                'action',
                                 ]" />
```


-Url is option

## If you want to change heading names, create this function for the current model.

updated code


```
 <x-a-relation-grid :id="'abac_action_table'" :model="$model"  :columns="
                               [
                              
                                 'id',
                                'controller_id',
                                'created_at',
                                'status',
                                'created_by',
                                'action',
                                 ]" />






--create current model in which attributeLabels method not create relation model
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




## create method for the relation model and same name (relationGridView).

       public function relationGridView($queryRelation, $request)
    {
        $dataTable = Datatables::of($queryRelation)
        ->addIndexColumn()

        ->addColumn('created_by', function ($data) {
            return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
        })
        ->addColumn('name', function ($data) {
            return !empty($data->name) ? (strlen($data->name) > 60 ? substr(ucfirst($data->name), 0, 60) . '...' : ucfirst($data->name)) : 'N/A';
        })
        ->addColumn('role_id', function ($data) {
            return  $data->getRole();
        })
        ->addColumn('status', function ($data) {
            return '<span class="' . $data->getStateBadgeOption() . '">' . $data->getState() . '</span>';
        })
        ->rawColumns(['created_by'])

        ->addColumn('created_at', function ($data) {
            return (empty($data->created_at)) ? 'N/A' : date('Y-m-d', strtotime($data->created_at));
        })
        ->addColumn('action', function ($data) {
            $html = '<div class="table-actions text-center">';
            $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('user/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
            $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('user/view/' . $data->id) . '"  ><i class="fa fa-eye
                "data-toggle="tooltip"  title="View"></i></a>';
            $html .=  '</div>';
            return $html;
        })->addColumn('customerClickAble', function ($data) {
            $html = 0;

            return $html;
        })
        ->rawColumns([
            'action',
            'created_at',
            'status',
            'customerClickAble'
        ]);
        if (!($queryRelation instanceof \Illuminate\Database\Query\Builder)) {
            $searchValue = $request->input('search.value');
            if ($searchValue) {
                $searchTerms = explode(' ', $searchValue);
                $collection = $queryRelation->filter(function ($item) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        if (
                            strpos($item->id, $term) !== false ||
                            strpos($item->name, $term) !== false ||
                            strpos($item->email, $term) !== false ||
                            strpos($item->created_at, $term) !== false ||
                            (isset($item->createdBy) && strpos($item->createdBy->name, $term) !== false) ||
                            $item->searchState($term)
                        ) {
                            return true;
                        }
                    }
                    return false;
                });
            }
        }

        return $dataTable->make(true);
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



  public function getrelationData(Request $request, $id = null)
    {
        return  $this->relationTable($request);
    }



use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionMethod;

  public function relationTable(Request $request, $id = null)
    {
        // Retrieve the relation and model instance
        $relation = $request->relation;
        $modelInstance = $request->modelType::find($request->modelId);

        // Check if the model instance exists
        if (!$modelInstance) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        // Use reflection to get the related model class name
        $reflectionMethod = new ReflectionMethod($modelInstance, $relation);
        $relationInstance = $reflectionMethod->invoke($modelInstance);
        $relatedModelClass = get_class($relationInstance->getRelated());

        // Retrieve related data
        $relatedData = $modelInstance->$relation;

        // Ensure related data is always treated as a collection
        $relatedCollection = $relatedData instanceof \Illuminate\Database\Eloquent\Collection ?
            $relatedData : ($relatedData !== null ? collect([$relatedData]) : collect());

        // Call the relationGridView method on the related model class
        return (new $relatedModelClass())->relationGridView($relatedCollection, $request);
    }
