<?php
namespace App\Modules\Querry\Models;

use App\Modules\Connection\Models\Connection;
use App\Modules\Querry\Constants\QuerryStatusEnum;
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
        'status',
        'binds',
        'literal_query',
        'error_message',
        'description'
    ];

    protected $casts = [
        'struct' => 'array',
        'binds' => 'array',
        'status' => QuerryStatusEnum::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function changeStatus(QuerryStatusEnum $status)
    {
        $this->status = $status;
        $this->save();
    }

    public function connection()
    {
        return $this->belongsTo(Connection::class);
    }
}
