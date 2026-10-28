<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $guarded = [];

    // Esta es la relación que Filament busca
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
