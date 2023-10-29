<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\User;
use App\Models\Company;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    // Tábla kapcsolatok (egy-egy)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function distributor()
    {
        return $this->belongsTo(Company::class, 'distributor_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Tábla kapcsolatok (több-több)
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'audit_tags', 'audit_id', 'tag_id');
    }

    // Egyéni attribútumok
    protected function sumPrice($type = 'net'): Attribute
    {
        return Attribute::make(
            get: fn () => $this->{'price_'.$type} ?? 0 * $this->quantity ?? 0,
        );
    }
}
