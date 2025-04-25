<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'gadget_id',
        'source',
        'product_name',
        'price',
        'link',
        'link_hash',
        'image_url'
    ];

    public function gadget()
    {
        return $this->belongsTo(Gadget::class);
    }
}
