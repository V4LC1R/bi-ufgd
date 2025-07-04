<?
namespace App\Modules\Auth\Http\Request;

use App\Modules\Auth\Http\DTOs\SignUpDTO;
use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest{

    public function authorize(): bool
    {
        return true;
    }

    public function rules():array
    {
        return [
            'password'=>'required|string',
            'document'=>[
                'required',
                'regex:/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/'
            ],
            'entity_id'=>'required|int',
            'email'=>'required|string|email',
            'name'=>'required|string'
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
            ],
            'document'=>[
                'required' => 'O campo CPF é obrigatório.',
                'string'=>"E-mail Invalido",
                'regex' => 'O CPF deve estar no formato 000.000.000-00'
            ],
            'entity_id'=>[
                'required' => 'O usuario tem que estar vinculado a uma entidade da universidade',
                
            ]
        ];
    }
    public function toDTO(): SignUpDTO
    {
        return SignUpDTO::fromArray($this->validated());
    }
}