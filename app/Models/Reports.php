<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    use HasFactory;

      protected $fillable = [
        'status',
        'url',
        'report_created_at'
      ];
}
