<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
    ];

    /**
     * Obtener el usuario que marcó como favorito
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener la propiedad marcada como favorita
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
