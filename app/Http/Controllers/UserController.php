<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Añadir el Facade de Auth

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //metodos los cuales nos permiten relacionar los datos
        $users = User::query()
        ->included() // Maneja la inclusión de company, regional, position y roles
        ->filter()   // Aplica los filtros
        ->sort()     // Aplica el ordenamiento
        ->getOrPaginate(); // Devuelve get() o paginate()

    return response()->json($users);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_user'      => 'required|string|max:255',
            'last_name_user' => 'required|string|max:255',
            'birthdate'      => 'nullable|date',
            'email'          => 'required|email|unique:users|max:255', 
            // number_document debe ser string y debe ser unique en la BD
            'number_document'=> 'required|string|unique:users|max:255', 
            // --- VALIDACIONES AÑADIDAS PARA LAS CLAVES FORÁNEAS ---\
            'company_id'     => 'required|integer|exists:companies,id',
            'regional_id'    => 'required|integer|exists:regionals,id',
            'position_id'    => 'required|integer|exists:positions,id',
            
            'password'       => 'required|min:8|max:255',
        ]);

        $user = User::create([
            'name_user'      => $request->name_user,
            'last_name_user' => $request->last_name_user,
            'birthdate'      => $request->birthdate,
            'email'          => $request->email,
            'number_document'=> $request->number_document,
            // --- CAMPOS AÑADIDOS AL CREATE ---
            'company_id'     => $request->company_id,
            'regional_id'    => $request->regional_id,
            'position_id'    => $request->position_id,
            
            'password'       => Hash::make($request->password),
        ]);
        
        return response()->json($user, 201);
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name_user'      => 'required|string|max:255',
            'last_name_user' => 'required|string|max:255',
            'birthdate'      => 'nullable|date',
            'email'          => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'number_document'=> 'required|string|max:255', 
        ]);

        $user->update([
            'name_user'      => $request->name_user,
            'last_name_user' => $request->last_name_user,
            'birthdate'      => $request->birthdate,
            'email'          => $request->email,
            'number_document'=> $request->number_document,
        ]);
        
        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        $user->delete();
        
        return response()->json(null, 204);
    }

    // --- NUEVAS FUNCIONES DE AUTENTICACIÓN ---

  /**
     * Log the user in and create an API token.
     */
    public function login(Request $request)
    {
        // 1. Validar las credenciales
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // 2. Buscar el usuario y CARGAR LA RELACIÓN DE ROLES
        // Se añade 'with('roles')' para que el accessor 'role_names' funcione correctamente.
        $user = User::where('email', $request->email)->with('roles')->first();

        // 3. Verificar usuario y contraseña
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales inválidas.'
            ], 401);
        }

        // 4. Crear el token de Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Devolver la respuesta con el token y un objeto de usuario limpio que incluya los roles.
        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'user'    => [
                'id'               => $user->id,
                'name_user'        => $user->name_user,
                'last_name_user'   => $user->last_name_user,
                'email'            => $user->email,
                'number_document'  => $user->number_document,
                'company_id'       => $user->company_id,
                'regional_id'      => $user->regional_id,
                'position_id'      => $user->position_id,
                // *** ESTE CAMPO ES CLAVE PARA EL FRONTEND ***
                'roles'            => $user->role_names, // Usa el accessor del modelo User.php
            ],
            'token'   => $token,
        ], 200);
    }

    /**
     * Log the user out by revoking the current API token.
     */
    public function logout(Request $request)
    {
        // El token actual se obtiene del request a través del middleware 'auth:sanctum'.
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente.'
        ], 200);
    }
}