<?php
namespace App\Modules\User\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $table = "entities";

    protected $fillable = [
        'name',
        'acronym',
    ];

    public function users()
    {
        return $this->hasMany(User::class , 'entity_id');
    }
}
