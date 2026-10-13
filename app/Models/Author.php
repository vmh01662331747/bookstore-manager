<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    protected $fillable = [
        'name',
        'nationality',
        'birth_year',
        'bio',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/u', trim($this->name));
        if (count($words) >= 2) {
            return mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1));
        }
        return mb_strtoupper(mb_substr($this->name, 0, 2));
    }
}
