<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'city', 'address', 'description',
        'stars', 'image', 'phone', 'email'
    ];

    // Relation : un hôtel a plusieurs chambres
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    // Scope : chercher par ville
    public function scopeInCity($query, $city)
    {
        return $query->where('city', $city);
    }

    // Accessor : nombre de chambres disponibles
    public function getAvailableRoomsCountAttribute()
    {
        return $this->rooms()->where('status', 'available')->count();
    }
}