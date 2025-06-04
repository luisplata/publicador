<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Boton extends Model
{
    protected $fillable = ['mensaje_id', 'texto', 'url'];
    protected $table = 'botones';

    public function mensaje(): BelongsTo
    {
        return $this->belongsTo(Mensaje::class);
    }
}
