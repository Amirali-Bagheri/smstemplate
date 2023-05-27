<?php

namespace Modules\SMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sms_logs()
    {
        return $this->hasMany(SmsLog::class,'sms_template_id','id');
    }
}
