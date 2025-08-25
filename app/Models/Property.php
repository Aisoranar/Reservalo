<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Schema;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'location',
        'price',
        'services',
        'capacity',
        'type',
        'is_active',
        'city_id',
        'amenities',
        'features',
        'bedrooms',
        'bathrooms',
        'size',
        'parking',
        'pet_friendly',
        'smoking_allowed',
        'check_in_time',
        'check_out_time',
        'min_stay',
        'cleaning_fee',
        'security_deposit',
        'house_rules',
        'cancellation_policy',
        'rating',
        'review_count',
        'status',
        'featured_until'
    ];

    protected $casts = [
        'services' => 'array',
        'amenities' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
        'pet_friendly' => 'boolean',
        'smoking_allowed' => 'boolean',
        'price' => 'decimal:2',
        'size' => 'decimal:2',
        'cleaning_fee' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'rating' => 'decimal:2',
        'featured_until' => 'datetime'
    ];

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function availabilityBlocks(): HasMany
    {
        return $this->hasMany(AvailabilityBlock::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PropertyReview::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function nightlyPrices(): HasMany
    {
        return $this->hasMany(NightlyPrice::class);
    }

    public function averageRating(): float
    {
        return $this->rating ?? 0.0;
    }

    public function isFeatured(): bool
    {
        return $this->featured_until && $this->featured_until->isFuture();
    }

    public function isFavoritedBy(User $user): bool
    {
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 0);
    }

    public function getRatingAttribute(): float
    {
        try {
            // Si ya tenemos las reviews cargadas, calcular el promedio
            if ($this->relationLoaded('reviews')) {
                return $this->reviews->avg('overall_rating') ?? 0.0;
            }
            
            // Si no están cargadas, hacer una consulta directa
            // Verificar si la tabla existe y tiene datos
            if (Schema::hasTable('property_reviews')) {
                return $this->reviews()->avg('overall_rating') ?? 0.0;
            }
            
            return 0.0;
        } catch (\Exception $e) {
            // Si hay algún error, retornar 0
            return 0.0;
        }
    }

    public function getReviewCountAttribute(): int
    {
        try {
            // Si ya tenemos las reviews cargadas, usar la colección
            if ($this->relationLoaded('reviews')) {
                return $this->reviews->count();
            }
            
            // Si no están cargadas, hacer una consulta directa
            // Verificar si la tabla existe y tiene datos
            if (Schema::hasTable('property_reviews')) {
                return $this->reviews()->count();
            }
            
            return 0;
        } catch (\Exception $e) {
            // Si hay algún error, retornar 0
            return 0;
        }
    }

    public function getFormattedSizeAttribute(): string
    {
        return $this->size ? $this->size . ' m²' : 'N/A';
    }

    public function primaryImage()
    {
        return $this->hasOne(PropertyImage::class)->where('is_primary', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function isAvailableForDates($startDate, $endDate)
    {
        // Check if there are any reservations for these dates
        $conflictingReservations = $this->reservations()
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($conflictingReservations) {
            return false;
        }

        // Check if there are any availability blocks for these dates
        $conflictingBlocks = $this->availabilityBlocks()
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        return !$conflictingBlocks;
    }

    public function calculatePriceForDates($startDate, $endDate)
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $nights = $start->diffInDays($end);
        
        return $this->price * $nights;
    }
}
