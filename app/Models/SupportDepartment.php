<?php

namespace App\Models;

use App\Traits\AActiveRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportDepartment extends Model
{
    const STATE_INACTIVE = 0;

    const STATE_ACTIVE = 1;

    const STATE_DELETE = 2;

    use HasFactory;

    use AActiveRecord;
    
    protected $fillable = [
        'title',
        'state_id',
        'created_by_id'

    ];
    public static function getStateOptions()
    {
        return [
            self::STATE_INACTIVE => "New",
            self::STATE_ACTIVE => "Active",
            self::STATE_DELETE => "Deleted",
        ];
    }

    public static function getStateOptionsBadge($stateValue)
    {
        $list = [
            self::STATE_INACTIVE => "btn btn-primary",
            self::STATE_ACTIVE => "btn btn-primary",
            self::STATE_DELETE => "btn btn-danger",
        ];
        return isset($stateValue) ? $list[$stateValue] : 'Not Defined';
    }
    public function getState()
    {
        $list = self::getStateOptions();
        return isset($list[$this->state_id]) ? $list[$this->state_id] : 'Not Defined';
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function getStateBadge()
    {
        $list = [
            self::STATE_INACTIVE => "New",
            self::STATE_ACTIVE => "Active",
            self::STATE_DELETE => "Delete",
        ];
        return isset($list[$this->state_id]) ?  'badge badge-' . $list[$this->state_id] : 'Not Defined';
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

    public function updateMenuItems($action, $model = null)
    {
        $menu = [];
        switch ($action) {
            case 'view':
                $menu['manage'] = [
                    'label' => 'fa fa-step-backward',
                    'color' => 'btn btn-warning',
                    'title' => __('Manage'),
                    'url' => url('support/department'),
                    'visible' => true
                ];
                $menu['delete'] = [
                    'label' => 'fa fa-trash',
                    'color' => 'btn btn-danger',
                    'title' => __('Add'),
                    'url' =>  url('support/department/delete/' . $model->id),
                    'visible' => true
                ];
                break;

            case 'index':
                $menu['add'] = [
                    'label' => 'fa fa-plus',
                    'color' => 'btn btn-icon btn-success',
                    'title' => __('Add'),
                    'url' => url('support/department/create'),
                    'visible' => false
                ];
                break;
        }
        return $menu;
    }


    public function scopeSearchState($query, $search)
    {
        $stateOptions = self::getStateOptions();
        return $query->where(function ($query) use ($search, $stateOptions) {
            foreach ($stateOptions as $stateId => $stateName) {
                if (stripos($stateName, $search) !== false) {
                    $query->orWhere('state_id', $stateId);
                }
            }
        });
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
}
