<?php
namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class Product extends Model
{
    const ProductStoreType = [
        'ENTRY' => 'ENTRY',
        'FETCH' => 'FETCH',
    ];

    protected $fillable = ['category_id', 'name', 'barcode', 'status'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
