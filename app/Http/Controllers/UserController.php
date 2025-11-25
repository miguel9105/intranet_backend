<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth; // Lo usaremos para el helper auth()
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Constructor para aplicar el middleware de autenticación.
     * El middleware 'auth:api' (que ahora usa JWT) protegerá
     * todas las rutas de este controlador, EXCEPTO 'login' y 'store'.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'store']]);
        
        // --- Middleware de Permisos de Spatie ---
        // Descomenta y ajusta esto según tus necesidades de producción.
        // Solo 'Administrador' puede listar, crear, ver y borrar usuarios.
        // $this->middleware('role:Administrador', ['except' => ['login']]);
        
        // O, mejor aún, basado en permisos:
        // $this->middleware('permission:gestionar usuarios', ['except' => ['login', 'logout', 'refresh', 'me']]);
    }

    /**
     * Display a listing of the resource.
     * (Tu código está perfecto, no se toca)
     */
    public function index()
    {
        $users = User::with('roles')

            ->included() // Maneja la inclusión
            ->filter()   // Aplica los filtros
            ->sort()     // Aplica el ordenamiento
            ->getOrPaginate(); // Devuelve get() o paginate()

        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     * (MODIFICADO para asignar un ROL al crear)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_user'      => 'required|string|max:255',
            'last_name_user' => 'required|string|max:255',
            'birthdate'      => 'nullable|date',
            'email'          => 'required|email|unique:users|max:255',
            'number_document'=> 'required|string|unique:users|max:255',
            'company_id'     => 'required|integer|exists:companies,id',
            'regional_id'    => 'required|integer|exists:regionals,id',
            'position_id'    => 'required|integer|exists:positions,id',
            'password'       => 'required|min:8|max:255',

            // --- ¡AÑADIDO! Validación de Rol (de Spatie) ---
            // 'role_name' es más legible que 'role_id'
            'role_name'      => 'required|string|exists:roles,name', 
        ]);

        $user = User::create([
            'name_user'      => $request->name_user,
            'last_name_user' => $request->last_name_user,
            'birthdate'      => $request->birthdate,
            'email'          => $request->email,
            'number_document'=> $request->number_document,
            'company_id'     => $request->company_id,
            'regional_id'    => $request->regional_id,
            'position_id'    => $request->position_id,
            
            // No usamos Hash::make() porque tu modelo User.php
            // ya tiene el 'cast' de 'password' => 'hashed'
            'password'       => Hash::make($request->password), 
        ]);

        // --- ¡AÑADIDO! Asignar el rol de Spatie al usuario nuevo ---
        $user->assignRole($request->role_name);

        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     * (Tu código está perfecto, no se toca)
     */
    public function show($id)
    {
        // Añadimos 'with' para cargar los roles y permisos del usuario
        $user = User::with(['roles', 'permissions'])->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     * (MODIFICADO para actualizar el ROL)
     */
public function update(Request $request, User $user)
{
    $request->validate([
        'name_user'      => 'required|string|max:255',
        'last_name_user' => 'required|string|max:255',
        'birthdate'      => 'nullable|date',
        'email'          => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        
        // CORRECCIÓN 1: number_document
        // El error 422 indica que no es reconocido como string. Al quitar la regla 'string'
        // y mantener 'max', Laravel es más flexible, aunque el campo debe ser requerido.
        'number_document'=> ['required', 'max:255', Rule::unique('users')->ignore($user->id)],
        
        'company_id'     => 'required|integer|exists:companies,id',
        'regional_id'    => 'required|integer|exists:regionals,id',
        'position_id'    => 'required|integer|exists:positions,id',
        'password'       => 'nullable|min:8|max:255', 
        'role_name'      => 'required|string|exists:roles,name', 
    ]);

    // 1. Prepara los datos a actualizar
    $data = $request->except(['password', 'role_name']);

    // 2. Si se proporciona una contraseña, hasheala e inclúyela
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    // 3. Actualiza el usuario
    $user->update($data);

    // 4. Actualiza el rol (SOLUCIÓN al error de Spatie: RoleDoesNotExist for guard 'api')
    $newRoleName = $request->input('role_name');

    // CORRECCIÓN 2: Buscamos el rol explícitamente en el 'web' guard, 
    // ya que tus roles se crearon sin guard, lo que hace que Spatie use 'web'.
    // Si el rol ya existe en la DB, esta búsqueda es segura.
    try {
        $role = Role::findByName($newRoleName, 'web'); 
        
        // Sincroniza (reemplaza) todos los roles del usuario con el rol encontrado
        if ($role) {
            $user->syncRoles([$role]);
        }
    } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
        // En caso de error, devolvemos un error 422 descriptivo.
        return response()->json(['message' => 'Error al asignar el rol: ' . $e->getMessage()], 422);
    }
    
    // 5. Devuelve el usuario actualizado (con sus roles)
    return response()->json($user->load('roles'), 200); 
}
    /**
     * Remove the specified resource from storage.
     * (Tu código está perfecto, no se toca)
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }


   //Funciones de Autenticación con JWT y Spatie
    /**
     * Autentica un usuario y devuelve un token JWT.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. Intentar autenticar y crear el token JWT
        // Usamos el guardia 'api' que configuramos para usar 'jwt'
        if (! $token = auth('api')->attempt($credentials)) {
            // Error de autenticación
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        // 2. Si el token se crea, llamamos a nuestra función de respuesta
        return $this->respondWithToken($token);
    }

    /**
     * Cierra la sesión del usuario (Invalida el token JWT).
     * (REEMPLAZA tu función de logout de Sanctum)
     */
    public function logout()
    {
        auth('api')->logout(); // Invalida el token JWT
        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }

    /**
     * Obtiene los datos del usuario autenticado actualmente.
     * (Ruta 'me' muy útil para el frontend)
     */
    public function me()
    {
        // auth('api')->user() te da el modelo User
        // pero lo formateamos para incluir roles/permisos
        $user = auth('api')->user(); 
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name_user,
            'email' => $user->email,
            'roles' => $user->getRoleNames(), // Método de Spatie
            'permissions' => $user->getAllPermissions()->pluck('name'), // Método de Spatie
        ]);
    }

    /**
     * Refresca un token JWT.
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Función helper para formatear la respuesta del token.
     * Aquí es donde unimos JWT y SPATIE.
     */    // --- ========================================== ---
    protected function respondWithToken($token)
    {
        // Obtiene el usuario autenticado
        $user = auth('api')->user();

        // Obtiene los roles y permisos (¡Gracias a Spatie!)
        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions()->pluck('name');

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60, // Expiración en segundos
            
            // Adjuntamos la información del usuario para el frontend
            'user' => [
                'id'          => $user->id,
                'name'        => $user->name_user,
                'email'       => $user->email,
                'roles'       => $roles,
                'permissions' => $permissions,
            ]
        ]);
    }
}