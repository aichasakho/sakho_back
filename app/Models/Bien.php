<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bien extends Model
{
    use HasFactory;

    protected $fillable = ['titre', 'description', 'prix', 'disponible', 'imagePath', 'nombre_douches', 'nombre_chambres', 'superficie'];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
