<?php

namespace App\Models;

use App\Traits\AActiveRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DataTables;

class Wallet extends Model
{
    use HasFactory, AActiveRecord;

    protected $guarded = [''];
    const STATE_INACTIVE = 0;

    const STATE_ACTIVE = 1;

    const STATE_DELETE = 2;

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'wallet_id');
    }
    public function generateWalletNumber()
    {
        $randomString = strtoupper(Str::random(4));
        $timestamp = Carbon::now()->timestamp;
        $code = $randomString . $timestamp . $this->created_by_id;
        $existingCode = Wallet::where('wallet_number', $code)->exists();
        if ($existingCode) {
            return $this->generateWalletNumber();
        }
        return $this->wallet_number = $code;
    }
    public function scopeSearchState($query, $search)
    {
        $roleOptions = self::getStateOptions();
        return $query->where(function ($query) use ($search, $roleOptions) {
            foreach ($roleOptions as $roleId => $roleName) {
                if (stripos($roleName, $search) !== false) {
                    $query->orWhere('state_id', $roleId);
                }
            }
        });
    }
    public function getStateBadgeOption()
    {
        $list = [
            self::STATE_ACTIVE => "success",
            self::STATE_INACTIVE => "secondary",
            self::STATE_DELETE => "danger",
        ];
        return isset($list[$this->state_id]) ? 'badge bg-' . $list[$this->state_id] : 'Not Defined';
    }

    public function getStateButtonOption($state_id = null)
    {
        $list = [
            self::STATE_ACTIVE => "success",
            self::STATE_INACTIVE => "secondary",
            self::STATE_DELETE => "danger",
        ];
        return isset($list[$state_id]) ? 'btn btn-' . $list[$state_id] : 'Not Defined';
    }

    public function getState()
    {
        $list = self::getStateOptions();
        return isset($list[$this->state_id]) ? $list[$this->state_id] : 'Not Defined';
    }
    public static function getStateOptions($id = null)
    {
        $list = array(
            self::STATE_INACTIVE => "Inactive",
            self::STATE_ACTIVE => "Active",
            self::STATE_DELETE => "Delete",
        );
        if ($id === null)
            return $list;
        return isset($list[$id]) ? $list[$id] : 'Not Defined';
    }
    public function updateMenuItems($action, $model = null)
    {
        $menu = [];
        switch ($action) {
            case 'view':
                $menu['manage'] = [

                    'label' => 'fa fa-step-backward',
                    'color' => 'btn btn-icon btn-warning',
                    'title' => __('Manage'),
                    'text' => false,
                    'url' => url('wallet'),

                ];
                $menu['update'] = [
                    'label' => 'fa fa-edit',
                    'color' => 'btn btn-icon btn-warning',
                    'title' => __('Update'),
                    'url' => url('wallet/edit/' . ($model->id ?? 0) . '/' . ($model->slug ?? '')),
                    'visible' => false


                ];
                break;
            case 'index':
                $menu['add'] = [
                    'label' => 'fa fa-plus',
                    'color' => 'btn btn-icon btn-success',
                    'title' => __('Add'),
                    'url' => url('wallet/create'),
                    'visible' => false
                ];
        }
        return $menu;
    }


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
        ->addColumn('status', function ($data) {
            return '<span class="' . $data->getStateBadgeOption() . '">' . $data->getState() . '</span>';
        })
       
        ->rawColumns(['created_by'])

        ->addColumn('created_at', function ($data) {
            return (empty($data->updated_at)) ? 'N/A' : date('Y-m-d', strtotime($data->updated_at));
        })
        ->addColumn('action', function ($data) {
            $html = '<div class="table-actions text-center">';
            // $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('wallet/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
            $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('wallet/view/' . $data->id) . '"  ><i class="fa fa-eye
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


    public function attributeLabels($label)
    {
        $list =  [
            'id' => __('ID'),
            'wallet_number' => __('Full Name'),
            'email' => __('Email'),
            'unique_id' => __('Username'),
            'referral_unique_id' => __('Referral'),
            'sponser_unique_id' => __('Upline'),
        ];

        return $list[is_array($label) ? $label['attribute'] ?? 'Invalid label format' : $label] ?? ucwords(str_replace('_', ' ', is_array($label) ? $label['label'] ?? $label['attribute'] : $label));
    }
}
