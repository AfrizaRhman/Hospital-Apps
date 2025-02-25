<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class recept extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'dokter',
        'obat',
        'bentuk',
        'jumlah',
        'pemakaian',
    ];

    
        protected $dates = ['deleted_at'];
    
}
