<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class patient extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'patient_id',
        'name',
        'age',
        'gender',
        'transaction',
        'created_at',
        'ruangan',
    ];

    public function bills()
    {
        return $this->hasMany(Bill::class, 'patient_id');
    }
}
