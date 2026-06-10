<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Responsable extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'cargo',
        'email',
        'telefono',
    ];

    public function procesos(): HasMany
    {
        return $this->hasMany(Proceso::class, 'responsable_id');
    }
}
