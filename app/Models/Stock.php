<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'product_id',
        'location',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public const LOCATIONS = [
        'central_nourriture' => 'Stock central nourriture',
        'central_boisson' => 'Stock central boisson',
        'serveur' => 'Stock serveur',
        'frigo_cuisine' => 'Frigo cuisine',
        'frigo_vente' => 'Frigo boisson',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
