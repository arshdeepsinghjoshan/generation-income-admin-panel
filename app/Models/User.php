<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\AActiveRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, AActiveRecord;
    const STATE_INACTIVE = 0;

    const STATE_ACTIVE = 1;
    const STATE_DELETE = 2;

    const ROLE_ADMIN = 0;

    const ROLE_USER = 1;

    const POSITION_LEFT = 0;

    const POSITION_RIGHT = 1;

    const POSITION_MID = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [''];


    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'created_by_id');
    }



    public function transactions()
    {
        return $this->hasManyThrough(WalletTransaction::class, Wallet::class, 'created_by_id', 'wallet_id');
    }

    public function subscribedPlan()
    {
        return $this->hasMany(SubscribedPlan::class, 'created_by_id');
    }

    public function getTotalSubscribedPlanAmount()
    {
        return $this->subscribedPlan->sum(function ($subscribedPlan) {
            return $subscribedPlan->subscriptionPlan->price;
        });
    }

    public function password()
    {
        $this->password = Hash::make($this->password);
    }

    public function generateReferralCode()
    {
        $randomString = strtoupper(Str::random(4));
        $timestamp = Carbon::now()->timestamp;
        $code = $randomString . $timestamp;
        $existingCode = User::where('referral_id', $code)->exists();
        if ($existingCode) {
            return $this->generateReferralCode();
        }
        return $this->referral_id = $code;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function getRoleOptions($id = null)
    {
        $list = array(
            self::ROLE_USER => "User",
        );
        if ($id === null)
            return $list;
        return isset($list[$id]) ? $list[$id] : 'Not Defined';
    }
    public function getRole()
    {
        $list = self::getRoleOptions();
        $list[self::ROLE_ADMIN] = 'Admin';
        return isset($list[$this->role_id]) ? $list[$this->role_id] : 'Not Defined';
    }





    public static function isAdmin()
    {
        $user = Auth::user();
        if ($user == null) {
            return false;
        }
        return ($user->isActive() &&  $user->role_id == User::ROLE_ADMIN);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function scopeSearchRole($query, $search)
    {
        $roleOptions = self::getRoleOptions();
        return $query->where(function ($query) use ($search, $roleOptions) {
            foreach ($roleOptions as $roleId => $roleName) {
                if (stripos($roleName, $search) !== false) {
                    $query->orWhere('role_id', $roleId);
                }
            }
        });
    }

    public function isActive()
    {
        return ($this->state_id == User::STATE_ACTIVE);
    }

    public static function isUser()
    {
        $user = Auth::user();
        if ($user == null) {
            return false;
        }
        return ($user->isActive() && $user->role_id == User::ROLE_USER);
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
                    'url' => url('user/'),

                ];
                $menu['login'] = [
                    'label' => 'fa fa-sign-in',
                    'color' => 'btn  btn-warning',
                    'title' => __(' Login'),
                    'text' => true,
                    'url' => url('user-login/' . ($model->id ?? 0) . '/' . ($model->slug ?? '')),
                    'visible' => ($model->role_id != User::ROLE_ADMIN  && $model->id != Auth::id())

                ];

                $menu['update'] = [
                    'label' => 'fa fa-edit',
                    'color' => 'btn btn-icon btn-warning',
                    'title' => __('Update'),
                    'url' => url('user/edit/' . ($model->id ?? 0) . '/' . ($model->slug ?? '')),

                ];
                break;
            case 'index':
                $menu['add'] = [
                    'label' => 'fa fa-plus',
                    'color' => 'btn btn-icon btn-success',
                    'title' => __('Add'),
                    'url' => url('user/create'),
                    'visible' => true
                ];
        }
        return $menu;
    }

    public function profitSalesTransactions($type = null)
    {
        $previousTotalProfit = Auth::user()->wallet->balance ?? 0;
        $transactions = Auth::user()->transactions()->get();

        $totalProfit = $transactions->reduce(function ($carry, $transaction) {
            if ($transaction->getType() === 'Credit') {
                return $carry + $transaction->amount;
            } elseif ($transaction->getType() === 'Debit') {
                return $carry - $transaction->amount;
            }
            return $carry;
        });
        if ($previousTotalProfit !== null && $previousTotalProfit != 0) {
            $percentageChange = (($totalProfit - $previousTotalProfit) / $previousTotalProfit) * 100;
        } else {
            $percentageChange = 0;
        }

        switch ($type) {
            case "profit":
                return $totalProfit;
                break;
            case "percentageChange":
                return $percentageChange;
                break;

            case "sales":
                return 0;
                break;

            case "payments":
                echo 0;
                break;

            case "transactions":
                echo 0;
                break;

            default:
                echo 0;
        }
    }
}
