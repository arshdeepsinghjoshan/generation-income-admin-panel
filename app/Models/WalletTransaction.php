<?php

namespace App\Models;

use App\Traits\AActiveRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WalletTransaction extends Model
{
    use HasFactory, AActiveRecord;

    protected $guarded = [''];
    const STATE_PENDING = 0;

    const STATE_COMPLETED = 1;

    const STATE_FAILED = 2;

    const TYPE_CREDIT = 0;
    const TYPE_DEBIT = 1;

    const TRANSACTION_LEVEL = 0;
    const TRANSACTION_ROI   = 1;

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
        ];
        return isset($list[$this->transaction_type]) ? 'badge bg-' . $list[$this->transaction_type] : 'Not Defined';
    }

    public function getTransactionTypeButtonOption($transaction_type = null)
    {
        $list = [
            self::TRANSACTION_LEVEL => "success",
            self::TRANSACTION_ROI => "secondary",
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
}
