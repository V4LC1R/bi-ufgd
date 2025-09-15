<?
namespace App\Modules\Connection\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Connection extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'connections';

    protected $fillable = [
        'name',
        'host',
        'user',
        'password',
        'database',
        'type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function tables()
    {
        return $this->hasMany(Tables::class, 'connection_id');
    }
}