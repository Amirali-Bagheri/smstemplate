<?php

namespace Modules\SMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sms_template()
    {
        return $this->belongsTo(SmsTemplate::class, 'sms_template_id', 'id');
    }

}
