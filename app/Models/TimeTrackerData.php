<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TimeTrackerData extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable. sdddd
     *
     * @var array<int, string>
     */
    protected $table = 'time_tracker';
    protected $fillable = [
        'emp_id',
        'emp_table_id',
        'expiration',
        'type'
    ];

    public function timetracker()
    {
        return $this->belongsTo(EmployeesData::class, 'emp_id', 'emp_id');
    }
}
