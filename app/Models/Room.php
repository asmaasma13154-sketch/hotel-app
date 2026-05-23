<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id', 'number', 'type', 'price_per_night',
        'capacity', 'description', 'image', 'status'
    ];

    // Relation : appartient à un hôtel
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    // Relation : a plusieurs réservations
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Scope : chambres disponibles uniquement
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    // Scope : filtrer par type
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Vérifier si disponible pour des dates
    public function isAvailableFor($checkIn, $checkOut)
    {
        return !$this->reservations()
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out', [$checkIn, $checkOut])
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                      });
            })->exists();
    }
}