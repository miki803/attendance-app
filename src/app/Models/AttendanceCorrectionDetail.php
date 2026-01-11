<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionDetail extends Model
{
    use HasFactory;

    public function request()
    {
        return $this->belongsTo(AttendanceCorrectionRequest::class);
    }

    protected $fillable = [
        'request_id',
        'target',
        'start_time',
        'end_time',
        'note',
    ];
}
