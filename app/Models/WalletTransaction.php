<?php

namespace App\Models;

use App\Traits\AActiveRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DataTables;


class WalletTransaction extends Model
{
    use HasFactory, AActiveRecord;

    protected $guarded = [''];
    const STATE_PENDING = 0;

    const STATE_COMPLETED = 1;

    const STATE_FAILED = 2;

    const TYPE_CREDIT = 0;
    const TYPE_DEBIT = 1;

    const TRANSACTION_ROI = 0;

    const TRANSACTION_LEVEL = 1;

    const TRANSACTION_USER_INVEST = 2;


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
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

    public function scopeTypeId($query, $search)
    {
        $roleOptions = self::getTypeOptions();
        return $query->where(function ($query) use ($search, $roleOptions) {
            foreach ($roleOptions as $roleId => $roleName) {
                if (stripos($roleName, $search) !== false) {
                    $query->orWhere('type_id', $roleId);
                }
            }
        });
    }


    public function scopeTransactionType($query, $search)
    {
        $roleOptions = self::getTransactionTypeOptions();
        return $query->where(function ($query) use ($search, $roleOptions) {
            foreach ($roleOptions as $roleId => $roleName) {
                if (stripos($roleName, $search) !== false) {
                    $query->orWhere('transaction_type', $roleId);
                }
            }
        });
    }


    public function getStateBadgeOption()
    {
        $list = [
            self::STATE_COMPLETED => "success",
            self::STATE_PENDING => "secondary",
            self::STATE_FAILED => "danger",
        ];
        return isset($list[$this->state_id]) ? 'badge bg-' . $list[$this->state_id] : 'Not Defined';
    }

    public function getStateButtonOption($state_id = null)
    {
        $list = [
            self::STATE_COMPLETED => "success",
            self::STATE_PENDING => "secondary",
            self::STATE_FAILED => "danger",
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
            self::STATE_COMPLETED => "Active",
            self::STATE_PENDING => "Inactive",
            self::STATE_FAILED => "Delete",
        );
        if ($id === null)
            return $list;
        return isset($list[$id]) ? $list[$id] : 'Not Defined';
    }
    public function getTypeBadgeOption()
    {
        $list = [
            self::TYPE_CREDIT => "success",
            self::TYPE_DEBIT => "secondary",
        ];
        return isset($list[$this->type_id]) ? 'badge bg-' . $list[$this->type_id] : 'Not Defined';
    }

    public function getTypeButtonOption($type_id = null)
    {
        $list = [
            self::TYPE_CREDIT => "success",
            self::TYPE_DEBIT => "secondary",
        ];
        return isset($list[$type_id]) ? 'btn btn-' . $list[$type_id] : 'Not Defined';
    }

    public function getType()
    {
        $list = self::getTypeOptions();
        return isset($list[$this->type_id]) ? $list[$this->type_id] : 'Not Defined';
    }
    public static function getTypeOptions($id = null)
    {
        $list = array(
            self::TYPE_CREDIT => "Credit",
            self::TYPE_DEBIT => "Debit",
        );
        if ($id === null)
            return $list;
        return isset($list[$id]) ? $list[$id] : 'Not Defined';
    }





    public function getTransactionTypeBadgeOption()
    {
        $list = [
            self::TRANSACTION_LEVEL => "success",
            self::TRANSACTION_ROI => "secondary",
            self::TRANSACTION_USER_INVEST => "secondary",

        ];
        return isset($list[$this->transaction_type]) ? 'badge bg-' . $list[$this->transaction_type] : 'Not Defined';
    }

    public function getTransactionTypeButtonOption($transaction_type = null)
    {
        $list = [
            self::TRANSACTION_LEVEL => "success",
            self::TRANSACTION_ROI => "secondary",
            self::TRANSACTION_USER_INVEST => "secondary",

        ];
        return isset($list[$transaction_type]) ? 'btn btn-' . $list[$transaction_type] : 'Not Defined';
    }

    public function getTransactionType()
    {
        $list = self::getTransactionTypeOptions();
        return isset($list[$this->transaction_type]) ? $list[$this->transaction_type] : 'Not Defined';
    }
    public static function getTransactionTypeOptions($id = null)
    {
        $list = array(
            self::TRANSACTION_LEVEL => "Level",
            self::TRANSACTION_ROI => "ROI",
            self::TRANSACTION_USER_INVEST => "User Investment",
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


    public static function add($amount, $wallet_id, $description, $transaction_type = WalletTransaction::TRANSACTION_USER_INVEST, $type_id = WalletTransaction::TYPE_DEBIT, $state_id = WalletTransaction::STATE_COMPLETED)
    {
        $walletTransactionModel = new WalletTransaction();
        $walletTransactionModel->state_id = $state_id;
        $walletTransactionModel->created_by_id = Auth::id();
        $walletTransactionModel->transaction_type = $transaction_type;
        $walletTransactionModel->type_id = $type_id;
        $walletTransactionModel->wallet_id = $wallet_id;
        $walletTransactionModel->amount = $amount;
        $walletTransactionModel->description = $description;
        return  $walletTransactionModel->save();
    }


    public function relationGridView($queryRelation, $request)
    {

        $dataTable =   Datatables::of($queryRelation)
            ->addColumn('wallet_number', function ($data) {
                return !empty($data->wallet && $data->wallet->wallet_number) ? $data->wallet->wallet_number : 'N/A';
            })
            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('name', function ($data) {
                return !empty($data->name) ? (strlen($data->name) > 60 ? substr(ucfirst($data->name), 0, 60) . '...' : ucfirst($data->name)) : 'N/A';
            })
            ->addColumn('status', function ($data) {
                return '<span class="' . $data->getStateBadgeOption() . '">' . $data->getState() . '</span>';
            })
            ->addColumn('transaction_type', function ($data) {
                return '<span class="' . $data->getTransactionTypeBadgeOption() . '">' . $data->getTransactionType() . '</span>';
            })

            ->addColumn('type_id', function ($data) {
                return '<span class="' . $data->getTypeBadgeOption() . '">' . $data->getType() . '</span>';
            })
            ->rawColumns(['created_by'])

            ->addColumn('created_at', function ($data) {
                return (empty($data->created_at)) ? 'N/A' : date('Y-m-d h:i:s A', strtotime($data->created_at));
            })
            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                // $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('wallet/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('wallet/wallet-transaction/view/' . $data->id) . '"  ><i class="fa fa-eye
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
                'customerClickAble',
                'transaction_type',
                'type_id'
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
                            (isset($item->wallet) && strpos($item->wallet->wallet_number, $term) !== false) ||
                            $item->searchState($term) ||
                            $item->typeId($term) ||
                            $item->transactionType($term)

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
}
