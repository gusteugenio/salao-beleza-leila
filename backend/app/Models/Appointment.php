<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'scheduled_at',
    ];

    // Relacionamento com cliente
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento N:N com services
    public function services()
    {
        return $this->belongsToMany(Service::class, 'appointment_service')->withTimestamps();;
    }
}
