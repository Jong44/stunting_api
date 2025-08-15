<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Growth extends Model
{
    use HasUuid;

    protected $fillable = [
        'child_id',
        'weight',
        'height',
        'measurement_date',
    ];

    public function child()
    {
        return $this->belongsTo(Children::class, 'child_id');
    }
}
