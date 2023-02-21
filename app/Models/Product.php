<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function ingredients() {
        return $this->hasMany(ProductIngredient::class, 'product_id', 'id');
    }
}
