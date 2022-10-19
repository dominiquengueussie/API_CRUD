<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

//use Illuminate\Foundation\Auth\User;

class UserController extends Controller
{
    public function inscription(Request $request)
    {
        $dataUser = $request->validate([
            'name' => ['required', 'string', 'min:15', 'max:200'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:12',
                'confirmed',
            ],
        ]);

        $insertion = User::create([
            'name' => $dataUser['name'],
            'email' => $dataUser['email'],
            'password' => bcrypt($dataUser['password']),
        ]);
        return response($insertion, 201);
    }

    public function connexion(Request $request)
    {
        $connect = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'max:12'],
        ]);

        $userconnect = User::where('email', $connect['email'])->first();
        if (!$userconnect) {
            return response(
                [
                    'message' =>
                        "Utilisateur non trouvé avec l'email:" .
                        ' ' .
                        $connect['email'],
                ],
                401
            );
        }
        if (!Hash::check($connect['password'], $userconnect->password)) {
            return response(
                [
                    'message' =>
                        "Oups! nous n'avons trouvé aucun utilisateur avec ce mot de passe",
                ],
                401
            );
        }
        $token = $userconnect->createToken("CLE_SECRETE")->plainTextToken;
        return response(
            [
                "utilisateur connecté" => $userconnect,
                "token:" => $token
            ],
            200
        );
    }
}
