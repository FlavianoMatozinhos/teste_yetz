<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function index() 
    {
        // Buscar todas as classes disponíveis
        $classes = ClassModel::all(); // Adaptar para a tabela de classes no seu banco de dados

        // Passar a variável $classes para a view
        return view("auth.register", compact('classes'));
    }

    public function store(Request $request)
    {
        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'class_id' => 'required|exists:classes,id',
        ]);
    
        // Verifica se a validação falhou
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Inicia a transação
        DB::beginTransaction();
    
        try {
            // Criação do usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => Role::where('name', 'player')->first()->id, // Atribuindo o papel de "player"
                'class_id' => $request->class_id, // Armazenando o ID da classe no banco
                'xp' => 0,  // Ou qualquer valor padrão que você queira para a experiência inicial
                'confirmed' => false, // Usuário ainda não confirmado (pode ser alterado conforme necessário)
            ]);
    
            // Confirma a transação no banco
            DB::commit();
    
            // Retornar o usuário e o token
            return redirect('/login');
    
        } catch (\Exception $e) {
            // Caso algum erro ocorra, desfaz a transação
            DB::rollBack();
    
            // Retornar uma resposta com erro
            return response()->json([
                'message' => 'Erro ao criar usuário.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
