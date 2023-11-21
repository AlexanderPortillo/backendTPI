<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Mostramos todos los usuarios de la db
        return User::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs = $request->input();

        // Transformamos la primer letra de cada palabra del first_name en mayuscula
        if (isset($inputs['first_name']) && is_string($inputs['first_name'])) {
            $inputs['first_name'] = ucwords($inputs['first_name']);
        }

        // Transformamos la primer letra de cada palabra del last_name en mayuscula
        if (isset($inputs['last_name']) && is_string($inputs['last_name'])) {
            $inputs['last_name'] = ucwords($inputs['last_name']);
        }

        // Verificamos si el valor es un email
        if (isset($inputs['email']) && !filter_var($inputs['email'], FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'error' => true,
                'mensaje' => 'El valor proporcionado para el email no es válido.',
            ]);
        }

        // Verificamos si ya existe un usuario con el mismo user_name o email
        $existingUser = User::where('user_name', $inputs['user_name'])
            ->orWhere('email', $inputs['email'])
            ->first();

        if ($existingUser) {
            if ($existingUser->user_name === $inputs['user_name'] && $existingUser->email === $inputs['email']) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Ya existe un usuario con el mismo user_name y email en la base de datos.',
                ]);
            }

            // Guardamos la informacion de que elemento se repite
            $duplicateField = $existingUser->user_name === $inputs['user_name'] ? 'user_name' : 'email';

            return response()->json([
                'error' => true,
                'mensaje' => "Ya existe un usuario con el mismo $duplicateField en la base de datos",
            ]);
        }

        // Verificamos y transformamos la contraseña
        if (isset($inputs['password']) && is_string($inputs['password'])) {
            // Eliminamos espacios al inicio y al final de la contraseña
            $inputs['password'] = trim($inputs['password']);

            // // Verificar que tenga al menos 8 caracteres alfanuméricos
            // if (strlen($inputs['password']) < 8 || !ctype_alnum($inputs['password'])) {
            //     return response()->json([
            //         'error' => true,
            //         'mensaje' => 'La contraseña debe tener al menos 8 caracteres alfanuméricos y contener al menos un símbolo de los siguientes: +*.',
            //     ]);
            // }

            // // Verificar y agregar un símbolo de los siguientes: +*
            // $symbols = ['+', '*'];
            // $hasSymbol = false;

            // foreach ($symbols as $symbol) {
            //     if (strpos($inputs['password'], $symbol) !== false) {
            //         $hasSymbol = true;
            //         break;
            //     }
            // }

            // if (!$hasSymbol) {
            //     return response()->json([
            //         'error' => true,
            //         'mensaje' => 'La contraseña debe contener al menos un símbolo de los siguientes: +*.',
            //     ]);
            // }

            // Encriptar la contraseña antes de almacenarla
            // $inputs['password'] = Hash::make($inputs['password']);
        }

        // Creamos el nuevo usuario
        $newUser = User::create($inputs);
        return response()->json([
            'data' => $newUser,
            'mensaje' => 'Usuario registrado con éxito',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($value)
    {
        $id = null;
        $users = null;

        switch (true) {
            case is_numeric($value):
                // Buscamos por ID un usuario en la db
                $id = User::find($value);
                break;

            case is_string($value):
                // Buscamos por nombre de usuario
                $users = User::where('user_name', $value)->get();
                break;

            default:
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Parámetro no válido. Se espera un ID numérico o un nombre de usuario.',
                ]);
        }

        if ($id !== null) {
            return response()->json([
                'data' => $id,
                'mensaje' => 'Usuario encontrado con éxito',
            ]);
        } elseif ($users !== null && $users->isNotEmpty()) {
            return response()->json([
                'data' => $users,
                'mensaje' => 'Usuarios encontrados con éxito',
            ]);
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => 'No existe usuario con el criterio de búsqueda proporcionado.',
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => true,
                'mensaje' => 'No existe el usuario.',
            ]);
        }

        // Verificamos si se está cambiando el first_name
        if ($request->has('first_name') && $request->input('first_name') !== $user->first_name) {
            $newFirstName = ucwords($request->input('first_name'));

            $user->first_name = $newFirstName;
        }

        // Verificamos si se está cambiando el last_name
        if ($request->has('last_name') && $request->input('last_name') !== $user->last_name) {
            $newLastName = ucwords($request->input('last_name'));

            $user->last_name = $newLastName;
        }

        // Verificamos si se está cambiando el user_name
        if ($request->has('user_name') && $request->input('user_name') !== $user->user_name) {
            $newUserName = ucfirst($request->input('user_name'));

            // Verificamos si ya existe un usuario con el nuevo user_name para que este no se pueda actualizar
            if (User::where('user_name', $newUserName)->exists()) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Ya existe un usuario con este nombre.',
                ]);
            }

            $user->user_name = $newUserName;
        }

        // Verificamos si se está cambiando el email
        if ($request->has('email') && $request->input('email') !== $user->email) {
            $newEmail = ucfirst($request->input('email'));

            // Verificamos si el valor es un email
            if (isset($request['email']) && !filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'El valor proporcionado para el email no es válido.',
                ]);
            }

            // Verificamos si ya existe un usuario con el nuevo email para que este no se pueda actualizar
            if (User::where('email', $newEmail)->exists()) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Ya existe un usuario con este email.',
                ]);
            }

            $user->email = $newEmail;
        }

        // Actualizamos los demás campos o los dejamos como estan
        $user->age = $request->input('age');
        $user->gender = $request->input('gender');
        $user->img_profile = $request->input('img_profile');
        $user->country = $request->input('country');
        $user->main_address = $request->input('main_address');
        $user->shipping_address = $request->input('shipping_address');

        // Guardamos la informacion
        if ($user->save()) {
            return response()->json([
                'data' => $user,
                'mensaje' => 'Usuario actualizado con éxito.',
            ]);
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => 'No se pudo actualizar el usuario.',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Buscamos por ID un usuario
        $user = User::find($id);
        if (isset($user)) {
            $res = User::destroy($id);
            if ($res) {
                return response()->json([
                    'data' => $user,
                    'mensaje' => 'Usuario eliminado con exito.',
                ]);
            } else {
                return response()->json([
                    'data' => [],
                    'mensaje' => 'Usuario no existe.',
                ]);
            }
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => 'No existe el usuario.',
            ]);
        }
    }
}
