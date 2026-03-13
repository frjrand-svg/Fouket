<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'type',
        'unit',
        'purchase_price',
        'sale_price',
        'low_stock_threshold',
        'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'low_stock_threshold' => 'integer',
        'is_active' => 'boolean',
    ];

    public const TYPE_NOURRITURE = 'nourriture';
    public const TYPE_BOISSON = 'boisson';

    public const UNIT_BOUTEILLE = 'bouteille';
    public const UNIT_CASIER = 'casier';
    public const UNIT_PLAT = 'plat';
    public const UNIT_UNITE = 'unite';

    public static function units(): array
    {
        return [
            self::UNIT_PLAT => 'Plat',
            self::UNIT_BOUTEILLE => 'Bouteille',
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_NOURRITURE => 'Nourriture',
            self::TYPE_BOISSON => 'Boisson',
        ];
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
