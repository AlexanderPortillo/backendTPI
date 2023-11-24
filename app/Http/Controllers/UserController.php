<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        // Verificamos si el campo 'first_name' está presente y no está vacío
        if (isset($inputs['first_name']) && is_string($inputs['first_name']) && trim($inputs['first_name']) !== '') {
            // Transformamos la primera letra de cada palabra en mayúscula
            $inputs['first_name'] = ucwords($inputs['first_name']);
        } else {
            // Manejar el caso en el que 'first_name' está ausente o vacío
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo first_name es obligatorio y no puede estar vacío.',
            ]);
        }

        // Verificamos si el campo 'last_name' está presente y no está vacío
        if (isset($inputs['last_name']) && is_string($inputs['last_name']) && trim($inputs['last_name']) !== '') {
            // Transformamos la primera letra de cada palabra en mayúscula
            $inputs['last_name'] = ucwords($inputs['last_name']);
        } else {
            // Manejar el caso en el que 'last_name' está ausente o vacío
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo last_name es obligatorio y no puede estar vacío.',
            ]);
        }

        // Verificamos si el campo 'age' está presente y es un entero válido
        if (isset($inputs['age']) && is_numeric($inputs['age']) && is_int($inputs['age'] + 0)) {
            // Transformamos el valor a un entero (por si acaso)
            $inputs['age'] = (int) $inputs['age'];

            // Verificamos que la edad sea mayor de 18 años y no mayor que 100
            if ($inputs['age'] < 18 || $inputs['age'] > 100) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'El campo age debe ser mayor de 18 años y no mayor que 100.',
                ]);
            }
        } else {
            // Manejar el caso en el que 'age' está ausente, no es un número o no es un entero válido
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo "age" es obligatorio y debe ser un entero válido.',
            ]);
        }

        // Verificamos si el campo 'gender' está presente y no está vacío
        if (isset($inputs['gender']) && is_string($inputs['gender']) && trim($inputs['gender']) !== '') {
            // Convertimos a minúsculas para asegurarnos de que coincida con las opciones permitidas
            $gender = strtolower($inputs['gender']);

            // Verificamos que 'gender' sea una de las opciones permitidas
            if ($gender !== 'masculino' && $gender !== 'femenino') {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'El campo gender solo puede ser masculino o femenino.',
                ]);
            }

            // Asignamos el valor normalizado de 'gender'
            $inputs['gender'] = $gender;
        } else {
            // Manejar el caso en el que 'gender' está ausente o vacío
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo gender es obligatorio y no puede estar vacío.',
            ]);
        }

        // Verificamos si el campo 'country' está presente y es un string no vacío
        if (isset($inputs['country']) && is_string($inputs['country']) && trim($inputs['country']) !== '') {
            // Asignamos el valor normalizado de 'country'
            $inputs['country'] = $inputs['country'];
        } else {
            // Manejar el caso en el que 'country' está ausente o vacío
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo country es obligatorio y no puede estar vacío.',
            ]);
        }

        // Verificamos si el campo 'main_address' está presente y es un string no vacío
        if (isset($inputs['main_address']) && is_string($inputs['main_address']) && trim($inputs['main_address']) !== '') {
            // Asignamos el valor normalizado de 'main_address'
            $inputs['main_address'] = $inputs['main_address'];
        } else {
            // Manejar el caso en el que 'main_address' está ausente o vacío
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo main_address es obligatorio y no puede estar vacío.',
            ]);
        }

        // Verificamos si el campo 'shipping_address' está presente y es un string no vacío
        if (isset($inputs['shipping_address']) && is_string($inputs['shipping_address']) && trim($inputs['shipping_address']) !== '') {
            // Asignamos el valor normalizado de 'shipping_address'
            $inputs['shipping_address'] = $inputs['shipping_address'];
        } else {
            // Manejar el caso en el que 'shipping_address' está ausente o vacío
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo shipping_address es obligatorio y no puede estar vacío.',
            ]);
        }

        // Verificamos si el campo 'user_name' está presente y es un string no vacío
        if (isset($inputs['user_name']) && is_string($inputs['user_name']) && trim($inputs['user_name']) !== '') {
            // Asignamos el valor normalizado de 'user_name'
            $inputs['user_name'] = $inputs['user_name'];
        } else {
            // Manejar el caso en el que 'user_name' está ausente o vacío
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo user_name es obligatorio y no puede estar vacío.',
            ]);
        }

        // Verificamos si el valor del email no está vacío
        if (empty($inputs['email'])) {
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo email no puede estar vacío.',
            ]);
        }

        // Verificamos si el valor es un email válido
        if (!filter_var($inputs['email'], FILTER_VALIDATE_EMAIL)) {
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

        // Verificamos si el valor del email no está vacío
        if (empty($inputs['password'])) {
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo password no puede estar vacío.',
            ]);
        }

        // Verificamos y transformamos la contraseña
        if (isset($inputs['password']) && is_string($inputs['password'])) {
            // Eliminamos espacios al inicio y al final de la contraseña
            $inputs['password'] = trim($inputs['password']);

            // Verificamos que tenga al menos 8 caracteres alfanuméricos
            if (strlen($inputs['password']) < 8) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'La contraseña debe tener al menos 8 caracteres alfanuméricos y contener al menos un símbolo de los siguientes: +*.',
                ]);
            }

            // Verificamos si se agrego un símbolo de los siguientes: +*
            $symbols = ['+', '*'];
            $hasSymbol = false;

            foreach ($symbols as $symbol) {
                if (strpos($inputs['password'], $symbol) !== false) {
                    $hasSymbol = true;
                    break;
                }
            }

            if (!$hasSymbol) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'La contraseña debe contener al menos un símbolo de los siguientes: +*.',
                ]);
            }

            // Encriptar la contraseña antes de almacenarla
            // $inputs['password'] = Hash::make($inputs['password']);
        }

        if (!isset($inputs['rol'])) {
            $inputs['rol'] = 1;
        }

        if (!isset($inputs['referral_link'])) {
            $inputs['referral_link'] = Str::slug($inputs['user_name'] . $inputs['first_name'] . Str::random(6));
        }

        if (!isset($inputs['discount_percentage'])) {
            $inputs['discount_percentage'] = '15.00';
        }

        $defaultProfile = 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png';

        if (!isset($inputs['img_profile'])) {
            $inputs['img_profile'] = $defaultProfile;
        }

        if ($inputs['referral']) {
            $userEmail = User::where('referral_link', $inputs['referral']);
            if ($userEmail) {
                $userEmail->increment('money_reffer', 2.00);
            }
        }

        $inputs['money_reffer'] = '0.00';
        $inputs['remember_token'] = Str::random(10);

        // Creamos el nuevo usuario
        $newUser = User::create($inputs);
        return response()->json([
            // 'data' => $newUser,
            'mensaje' => 'Usuario registrado con éxito',
        ]);
    }

    public function signIn(Request $request)
    {
        $inputs = $request->input();

        // Verificamos si el campo 'first_name' está presente y no está vacío
        if (isset($inputs['first_name']) && is_string($inputs['first_name']) && trim($inputs['first_name']) !== '') {
            // Transformamos la primera letra de cada palabra en mayúscula
            $inputs['first_name'] = ucwords($inputs['first_name']);
        } else {
            // Manejar el caso en el que 'first_name' está ausente o vacío
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo first_name es obligatorio y no puede estar vacío.',
            ]);
        }

        // Verificamos si el campo 'last_name' está presente y no está vacío
        if (isset($inputs['last_name']) && is_string($inputs['last_name']) && trim($inputs['last_name']) !== '') {
            // Transformamos la primera letra de cada palabra en mayúscula
            $inputs['last_name'] = ucwords($inputs['last_name']);
        } else {
            // Manejar el caso en el que 'last_name' está ausente o vacío
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo last_name es obligatorio y no puede estar vacío.',
            ]);
        }

        // Verificamos si el campo 'age' está presente y es un entero válido
        if (isset($inputs['age']) && is_numeric($inputs['age']) && is_int($inputs['age'] + 0)) {
            // Transformamos el valor a un entero (por si acaso)
            $inputs['age'] = (int) $inputs['age'];

            // Verificamos que la edad sea mayor de 18 años y no mayor que 100
            if ($inputs['age'] < 18 || $inputs['age'] > 100) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'El campo age debe ser mayor de 18 años y no mayor que 100.',
                ]);
            }
        } else {
            // Manejar el caso en el que 'age' está ausente, no es un número o no es un entero válido
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo "age" es obligatorio y debe ser un entero válido.',
            ]);
        }

        // Verificamos si el campo 'gender' está presente y no está vacío
        if (isset($inputs['gender']) && is_string($inputs['gender']) && trim($inputs['gender']) !== '') {
            // Convertimos a minúsculas para asegurarnos de que coincida con las opciones permitidas
            $gender = strtolower($inputs['gender']);

            // Verificamos que 'gender' sea una de las opciones permitidas
            if ($gender !== 'masculino' && $gender !== 'femenino') {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'El campo gender solo puede ser masculino o femenino.',
                ]);
            }

            // Asignamos el valor normalizado de 'gender'
            $inputs['gender'] = $gender;
        } else {
            // Manejar el caso en el que 'gender' está ausente o vacío
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo gender es obligatorio y no puede estar vacío.',
            ]);
        }

        // Verificamos si el campo 'country' está presente y es un string no vacío
        if (isset($inputs['country']) && is_string($inputs['country']) && trim($inputs['country']) !== '') {
            // Asignamos el valor normalizado de 'country'
            $inputs['country'] = $inputs['country'];
        } else {
            // Manejar el caso en el que 'country' está ausente o vacío
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo country es obligatorio y no puede estar vacío.',
            ]);
        }

        // Verificamos si el campo 'main_address' está presente y es un string no vacío
        if (isset($inputs['main_address']) && is_string($inputs['main_address']) && trim($inputs['main_address']) !== '') {
            // Asignamos el valor normalizado de 'main_address'
            $inputs['main_address'] = $inputs['main_address'];
        } else {
            // Manejar el caso en el que 'main_address' está ausente o vacío
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo main_address es obligatorio y no puede estar vacío.',
            ]);
        }

        // Verificamos si el campo 'shipping_address' está presente y es un string no vacío
        if (isset($inputs['shipping_address']) && is_string($inputs['shipping_address']) && trim($inputs['shipping_address']) !== '') {
            // Asignamos el valor normalizado de 'shipping_address'
            $inputs['shipping_address'] = $inputs['shipping_address'];
        } else {
            // Manejar el caso en el que 'shipping_address' está ausente o vacío
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo shipping_address es obligatorio y no puede estar vacío.',
            ]);
        }

        // Verificamos si el campo 'user_name' está presente y es un string no vacío
        if (isset($inputs['user_name']) && is_string($inputs['user_name']) && trim($inputs['user_name']) !== '') {
            // Asignamos el valor normalizado de 'user_name'
            $inputs['user_name'] = $inputs['user_name'];
        } else {
            // Manejar el caso en el que 'user_name' está ausente o vacío
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo user_name es obligatorio y no puede estar vacío.',
            ]);
        }

        // Verificamos si el valor del email no está vacío
        if (empty($inputs['email'])) {
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo email no puede estar vacío.',
            ]);
        }

        // Verificamos si el valor es un email válido
        if (!filter_var($inputs['email'], FILTER_VALIDATE_EMAIL)) {
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

        // Verificamos si el valor del email no está vacío
        if (empty($inputs['password'])) {
            return response()->json([
                'error' => true,
                'mensaje' => 'El campo password no puede estar vacío.',
            ]);
        }

        // Verificamos y transformamos la contraseña
        if (isset($inputs['password']) && is_string($inputs['password'])) {
            // Eliminamos espacios al inicio y al final de la contraseña
            $inputs['password'] = trim($inputs['password']);

            // Verificamos que tenga al menos 8 caracteres alfanuméricos
            if (strlen($inputs['password']) < 8) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'La contraseña debe tener al menos 8 caracteres alfanuméricos y contener al menos un símbolo de los siguientes: +*.',
                ]);
            }

            // Verificamos si se agrego un símbolo de los siguientes: +*
            $symbols = ['+', '*'];
            $hasSymbol = false;

            foreach ($symbols as $symbol) {
                if (strpos($inputs['password'], $symbol) !== false) {
                    $hasSymbol = true;
                    break;
                }
            }

            if (!$hasSymbol) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'La contraseña debe contener al menos un símbolo de los siguientes: +*.',
                ]);
            }

            // Encriptar la contraseña antes de almacenarla
            // $inputs['password'] = Hash::make($inputs['password']);
        }

        $inputs['rol'] = 1;
        $inputs['referral_link'] = Str::slug($inputs['user_name'] . $inputs['first_name'] . Str::random(6));
        $inputs['discount_percentage'] = '15.00';

        $defaultProfile = 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png';

        if (!isset($inputs['img_profile'])) {
            $inputs['img_profile'] = $defaultProfile;
        }

        if ($inputs['referral']) {
            $userEmail = User::where('referral_link', $inputs['referral']);
            if ($userEmail) {
                $userEmail->increment('money_reffer', 2.00);
            }
        }

        $inputs['money_reffer'] = '0.00';
        $inputs['remember_token'] = Str::random(10);

        // Creamos el nuevo usuario
        $newUser = User::create($inputs);
        return response()->json([
            // 'data' => $newUser,
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
                $users = User::where('user_name', 'LIKE', '%' . $value . '%')->get();
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
        $user->rol = $request->input('rol');
        $user->money_reffer = $request->input('money_reffer');

        // Verificar y asignar valor predeterminado si el rol está vacío
        if (empty($user->rol)) {
            $user->rol = 1;
        }

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $idUser
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request, $id)
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
        $user->rol = 0;

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
