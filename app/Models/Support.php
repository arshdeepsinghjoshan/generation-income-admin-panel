<?php

namespace App\Models;

use App\Traits\AActiveRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;

    const STATE_PENDING = 0;

    const STATE_INPROGRESS = 1;

    const STATE_REJECTED = 4;

    const STATE_HOLD = 2;

    const STATE_COMPLETE = 3;


    use AActiveRecord;

    protected $fillable = [
        'title',
        'department_id',
        'type_id',
        'state_id',
        'created_by_id',
        'priority_id',
        'message',
        'image',

    ];

    public static function getStateOptions()
    {
        return [
            self::STATE_PENDING => "New",
            self::STATE_INPROGRESS => "Inprogress",
            self::STATE_HOLD => "Hold",
            self::STATE_COMPLETE => "Complete",
        ];
    }

    public function getState()
    {
        $list = self::getStateOptions();
        return isset($list[$this->state_id]) ? $list[$this->state_id] : 'Not Defined';
    }
    public function getStateBadgeOption()
    {
        $list = [
            self::STATE_PENDING => "secondary",
            self::STATE_INPROGRESS => "secondary",
            self::STATE_HOLD => "warning",
            self::STATE_COMPLETE => "success",
        ];
        return isset($list[$this->state_id]) ? 'badge bg-' . $list[$this->state_id] : 'Not Defined';
    }
    const PRIORITY_LOW = 0;
    const PRIORITY_MEDIUM = 1;
    const PRIORITY_HIGH = 2;

    public static function getPriorityOptions()
    {
        return [
            self::PRIORITY_LOW => "Low",
            self::PRIORITY_MEDIUM => "Medium",
            self::PRIORITY_HIGH => "High",
        ];
    }

    public function getPriority()
    {
        $list = self::getPriorityOptions();
        return isset($list[$this->priority_id]) ? $list[$this->priority_id] : 'Not Defined';
    }


    public function getDepartmentOption()
    {
        return SupportDepartment::where('state_id', SupportDepartment::STATE_ACTIVE)->get();
    }


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function getDepartment()
    {
        return $this->belongsTo(SupportDepartment::class, 'department_id');
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
                    'url' => url('support'),

                ];
                break;
            case 'index':
                $menu['add'] = [
                    'label' => 'fa fa-plus',
                    'color' => 'btn btn-success',
                    'title' => __('Add'),
                    'url' => url('support/create'),
                    'visible' => User::isUser()
                ];
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




    public function scopeSearchPriority($query, $search)
    {
        $stateOptions = self::getPriorityOptions();
        return $query->where(function ($query) use ($search, $stateOptions) {
            foreach ($stateOptions as $stateId => $stateName) {
                if (stripos($stateName, $search) !== false) {
                    $query->orWhere('priority_id', $stateId);
                }
            }
        });
    }
}
