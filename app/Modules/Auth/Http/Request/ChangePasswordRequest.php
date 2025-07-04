<?
namespace App\Modules\Auth\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest{

    public function authorize(): bool
    {
        return true;
    }

    public function rules():array
    {
        return [
            'password'=>'required|string',
            'token'=>'required|uuid'
        ];
    }

    public function messages(): array
    {
        return [
             'password'=>[
                'required'=>"E nessário enviar a senha!",
                'string'=>"Senha Invalida",
               
             ],
            'token'=>[
                'required'=>"Token e necessario para troca de senha!",
                'uuid'=>"Token invalido",
            ]
        ];
    }
}