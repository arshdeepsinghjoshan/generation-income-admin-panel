<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    const STATE_INACTIVE = 0;

    const STATE_ACTIVE = 1;

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
        $list[self::ROLE_ADMIN]='Admin';
        return isset($list[$this->role_id]) ? $list[$this->role_id] : 'Not Defined';
    }
}
