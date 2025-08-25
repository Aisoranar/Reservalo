<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'reservation_id',
        'overall_rating',
        'cleanliness_rating',
        'communication_rating',
        'check_in_rating',
        'accuracy_rating',
        'location_rating',
        'value_rating',
        'comment',
        'host_response',
        'host_response_at',
        'is_verified',
        'is_helpful',
        'helpful_count'
    ];

    protected $casts = [
        'overall_rating' => 'integer',
        'cleanliness_rating' => 'integer',
        'communication_rating' => 'integer',
        'check_in_rating' => 'integer',
        'accuracy_rating' => 'integer',
        'location_rating' => 'integer',
        'value_rating' => 'integer',
        'is_verified' => 'boolean',
        'is_helpful' => 'boolean',
        'host_response_at' => 'datetime'
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function getAverageRatingAttribute(): float
    {
        $ratings = [
            $this->overall_rating,
            $this->cleanliness_rating,
            $this->communication_rating,
            $this->check_in_rating,
            $this->accuracy_rating,
            $this->location_rating,
            $this->value_rating
        ];

        return round(array_sum($ratings) / count($ratings), 1);
    }
}
