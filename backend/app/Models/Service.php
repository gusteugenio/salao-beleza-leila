<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration',
        'price',
    ];

    // Relacionamento N:N com appointments
    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'appointment_service');
    }
}
