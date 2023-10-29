<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Tag;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function getLists()
    {
        return (object)[
            'tags'          => Tag::orderBy('name')->get()->map->only(['id', 'name'])->all(),
            'brands'        => Brand::orderBy('name')->get()->map->only(['id', 'name', 'code'])->all(),
            'categories'    => Category::orderBy('name')->get()->map->only(['id', 'name', 'code'])->all(),
            'distributors'  => Company::whereJsonContains('role', 'distributor')->orderBy('name')->get()->map->only(['id', 'name', 'code'])->all(),
            'manufacturers' => Company::whereJsonContains('role', 'manufacturer')->orderBy('name')->get()->map->only(['id', 'name', 'code'])->all(),
        ];
    }
}
