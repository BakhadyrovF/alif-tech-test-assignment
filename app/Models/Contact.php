<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function emails(): HasMany
    {
        return $this->hasMany(ContactEmail::class, 'contact_id', 'id');
    }

    public function phones(): HasMany
    {
        return $this->hasMany(ContactPhone::class, 'contact_id', 'id');
    }
}
