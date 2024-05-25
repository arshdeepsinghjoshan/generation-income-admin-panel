<?php

namespace App\Models;

use App\Traits\AActiveRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SupportReply extends Model
{
    use HasFactory;

    use AActiveRecord;
    
    const STATE_PENDING = 0;

    const STATE_ACTIVE = 1;

    const STATE_REJECTED = 2;

    const STATE_COMPLETE = 3;

    protected $fillable = [
        'support_id',
        'type_id',
        'state_id',
        'created_by_id',
        'priority_id',
        'message',
        'image',

    ];
    public function setTypeId($supportModel)
    {

        return $this->type_id = $supportModel->created_by_id == Auth::user()->id ? 0 : 1;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
