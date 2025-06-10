<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Para onde redirecionar após o registro.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Construtor
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Validação dos dados
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-zÀ-ÿ\s]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'image' => ['nullable', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Não permite .gif
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais de 100 caracteres.',
            'name.regex' => 'O nome deve conter apenas letras e espaços.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',
            'image.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg ou webp.',
            'image.max' => 'A imagem não pode ter mais de 2MB.',
        ]);
    }

    /**
     * Criação do usuário no banco de dados
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $imageName = null;

        if (request()->hasFile('image') && request()->file('image')->isValid()) {
            $image = request()->file('image');

            // Renomeia para evitar conflitos
            $imageName = time() . '_' . $image->getClientOriginalName();

            // Move para a pasta de imagens de perfil
            $image->move(public_path('imagens/profile_images'), $imageName);
        }

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'image' => $imageName,
        ]);
    }
}
