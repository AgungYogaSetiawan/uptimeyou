<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Result extends Model
{
    use HasFactory;
    protected $table = 'results_monitorings';
    protected $fillable = ['response_time', 'avg_response_time', 'status_code', 'monitoring_id', 'user_id'];

    public function monitorings(): BelongsTo
    {
        return $this->belongsTo(Monitoring::class);
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
