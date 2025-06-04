<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $fillable = ['texto', 'imagen'];

    public function botones(): HasMany
    {
        return $this->hasMany(Boton::class);
    }
}
