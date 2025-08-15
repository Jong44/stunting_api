<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Children extends Model
{
    use HasUuid;

    protected $fillable = [
        'name',
        'birth_date',
        'gender',
        'parent_id'
    ];

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function growths()
    {
        return $this->hasMany(Growth::class, 'child_id');
    }

}
