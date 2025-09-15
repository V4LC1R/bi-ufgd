<?php
namespace App\Modules\Querry\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Querry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'querries';

    protected $fillable = [
        'connection_id',
        'hash',
        'type',
        'struct',
    ];

    protected $casts = [
        'struct' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
