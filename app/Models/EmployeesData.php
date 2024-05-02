<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EmployeesData extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable. sdddd
     *
     * @var array<int, string>
     */
    protected $table = 'employee_data';
    protected $fillable = [
        'emp_id',
        'first_name',
        'last_name',
        'email',
        'department',
        'job_title',
        'division',
        'empty_job_field',
        'empty_personal_field',
        'empty_emergency_field'
    ];
}
