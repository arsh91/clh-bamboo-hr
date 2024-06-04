<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmptyFieldsData extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable. sdddd
     *
     * @var array<int, string>
     */
    protected $table = 'blank_field_data';
    protected $fillable = [
        'emp_id',
        'emp_table_id',
        'field_name',
        'tab'
    ];

    public function EmployeesData()
    {
        return $this->belongsTo(EmployeesData::class, 'emp_id', 'emp_id');
    }
}
