<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    use HasFactory;

    protected $fillable = ['type_monitor', 'name', 'url', 'schedule', 'tries', 'amount_send_notification', 'status_code', 'notification', 'description'];
}
