<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;

    // Minden mező engedelyézese, kivéve...
    protected $guarded = [];

    // Időbélyeg használata
    public $timestamps = true;

    // Típuskonverzió
    protected $casts = [
        'properties' => 'object',
    ];
}
