<?
namespace App\Modules\Connection\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tables extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tables';

    protected $fillable = [
        'connection_id',
        'name',
        'alias',
        'columns',
        'type'
    ];

    protected $casts = [
        'columns' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function connection()
    {
        return $this->belongsTo(Connection::class, 'connection_id');
    }
}