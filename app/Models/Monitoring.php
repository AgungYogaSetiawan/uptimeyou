<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Monitoring extends Model
{
    use HasFactory;

    protected $fillable = ['type_monitor', 'name', 'url', 'schedule', 'tries', 'email', 'notification', 'description', 'slug', 'user_id', 'avg_response_time'];

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }
}
