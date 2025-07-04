<?
namespace App\Modules\Auth\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest{

    public function authorize(): bool
    {
        return true;
    }

    public function rules():array
    {
        return [
            'password'=>'required|string',
            'email'=>'required|string|email'
        ];
    }

    public function messages(): array
    {
        return [
             'password'=>[
                'required'=>"E nessário enviar a senha!",
                'string'=>"Senha Invalida",
               
             ],
            'email'=>[
                'required'=>"E nessário enviar o E-mail!",
                'string'=>"E-mail Invalido",
                'email'=>"Formato de E-mail Invalido"
            ]
        ];
    }
}