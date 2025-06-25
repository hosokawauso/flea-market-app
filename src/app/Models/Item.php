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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item')->withTimestamps();
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function comments()
    {
        return $this->hasMany(Comments::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

}
