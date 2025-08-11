<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Growth extends Model
{
    use HasUuid;

    protected $fillable = [
        'weight',
        'height',
        'head_circumference',
        'measurement_date',
    ];

    public function child()
    {
        return $this->belongsTo(Children::class, 'child_id');
    }
}
