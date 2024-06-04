<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentData extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable. sdddd
     *
     * @var array<int, string>
     */
    protected $table = 'document_data';
    protected $fillable = [
        'emp_id',
        'emp_table_id',
        'doc_id',
        'doc_name'
    ];

    public function EmployeesDocs()
    {
        return $this->belongsTo(EmployeesData::class, 'emp_id', 'emp_id');
    }
}
