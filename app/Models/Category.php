<?php
namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'status'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', '_id');
    }
}
