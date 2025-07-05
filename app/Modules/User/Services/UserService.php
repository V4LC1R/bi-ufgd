<?
namespace App\Modules\User\Services;

use App\Modules\Auth\Http\DTOs\SignUpDTO;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepo,
    ) {}

    public function createFromSignUp(SignUpDTO $data):User
    {
        return $this->userRepo->create($data->toArray());
    }
}