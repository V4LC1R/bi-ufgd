<?
namespace App\Modules\Auth\Services;

use App\Modules\Auth\Http\DTOs\SignUpDTO;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepo,
    ) {}

    public function createUser(SignUpDTO $data):User
    {
        $userCreatePayload = $data->toArray();
        $userCreatePayload['password'] = Hash::make($data->password);
        return $this->userRepo->create($userCreatePayload);
    }
}