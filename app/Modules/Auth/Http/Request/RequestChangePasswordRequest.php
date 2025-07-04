<?
namespace App\Modules\Auth\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class RequestChangePasswordRequest extends FormRequest{

    public function authorize(): bool
    {
        return true;
    }

    public function rules():array
    {
        return [
            'email'=>'required|string',
            'document'=>[
                'required',
                'regex:/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
             'password'=>[
                'required'=>"E nessário enviar a senha!",
                'string'=>"Senha Invalida",
               
             ],
            'document'=>[
                'required' => 'O campo CPF é obrigatório.',
                'string'=>"E-mail Invalido",
                'regex' => 'O CPF deve estar no formato 000.000.000-00'
            ],
        ];
    }
}