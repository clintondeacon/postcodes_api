<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = ['name','lat','long','is_open','store_type_id','max_delivery_distance'];

    protected $hidden = ['created_at', 'updated_at','deleted_at'];

    protected $casts = [
        'max_delivery_distance' => 'float',
        'is_open' => 'boolean',
    ];

    /**
     * Store type.
     *
     * @return BelongsTo
     */
    public function store_type(): BelongsTo
    {
        return $this->belongsTo(StoreType::class, 'store_type_id');
    }

    /**
     * Get all postcodes within the store's delivery range.
     */
    public function getPostcodesAttribute()
    {
        return Postcode::whereRaw("
            3959 * ACOS(
                COS(RADIANS(?)) * COS(RADIANS(lat)) *
                COS(RADIANS(long) - RADIANS(?)) +
                SIN(RADIANS(?)) * SIN(RADIANS(lat))
            ) <= ?
        ", [$this->lat, $this->long, $this->lat, $this->max_delivery_distance])->get();
    }

    /**
     * Scope to get stores within a certain distance.
     */
    public function scopeStoresWithinDistance($query, $lat, $long, $distance = 10)
    {
        return $query->whereRaw("
        3959 * ACOS(
            COS(RADIANS(?)) * COS(RADIANS(lat)) *
            COS(RADIANS(long) - RADIANS(?)) +
            SIN(RADIANS(?)) * SIN(RADIANS(lat))
        ) <= ?
    ", [$lat, $long, $lat, $distance]);
    }

}
