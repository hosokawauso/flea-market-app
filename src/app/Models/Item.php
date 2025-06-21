<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_img',
        'condition',
        'item_name',
        'brand_name',
        'description',
        'price',
    ];

    protected $guarded = [
        'is_sold',
        'id'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsToMany(Category::class, 'item_category');
    }

    public function favorite()
    {
        return $this->hasMany(Favorite::class);
    }

    public function comment()
    {
        return $this->hasMany(Comments::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

}
