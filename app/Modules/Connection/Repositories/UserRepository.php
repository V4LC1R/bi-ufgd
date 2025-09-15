<?
namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;

class UserRepository
{
    public function create($data){
        return User::create($data);
    }

    
}