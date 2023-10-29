<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductCategory;
use App\Models\Brand;
use App\Models\Audit;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Category;

class Product extends Model
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
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function manufacturer()
    {
        return $this->belongsTo(Company::class, 'manufacturer_id');
    }

    // Tábla kapcsolatok (egy-több)
    public function audits()
    {
        return $this->hasMany(Audit::class);
    }

    // Tábla kapcsolatok (több-több)
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }

    // Egyéni attribútumok
    protected function distributors(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->audits->unique('distributor')->pluck('distributor'),
        );
    }
    protected function quantity(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->audits->sum('quantity'),
        );
    }
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn () => (object)[
                'gross' => (object)[
                    'min' => $this->audits->min('price_gross'),
                    'max' => $this->audits->max('price_gross'),
                ],
                'net'   => (object)[
                    'min' => $this->audits->min('price_net'),
                    'max' => $this->audits->max('price_net'),
                ],
            ],
        );
    }
    protected function brandName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->brand?->name,
        );
    }
    protected function manufacturerName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->manufacturer?->name,
        );
    }
    protected function sku(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->properties?->sku ?? null,
        );
    }
    protected function originalName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->properties?->original_name ?? null,
        );
    }

}
