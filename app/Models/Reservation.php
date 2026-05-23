<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'room_id', 'check_in', 'check_out',
        'guests', 'total_price', 'status', 'notes'
    ];

    protected $casts = [
        'check_in'  => 'date',
        'check_out' => 'date',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Nombre de nuits
    public function getNightsAttribute()
    {
        return $this->check_in->diffInDays($this->check_out);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('check_in', '>=', now())->where('status', 'confirmed');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('check_in', now()->month);
    }
}