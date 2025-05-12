<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gadget extends Model
{
    use HasFactory;

    // Mass-assignable fields
    protected $fillable = [
		'name',
		'slug',
		'description',
		'year',
		'category_id',
		'image_url',
		'is_visible',
		'published_at',
	];
	

    /**
     * Category relationship (many-to-one)
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Prices relationship (one-to-many)
     */
    public function prices()
    {
        return $this->hasMany(Price::class, 'gadget_id');
    }
}
