<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 
use App\Models\Company; 
use App\Models\Regional; 
use App\Models\Position; 

class User extends Authenticatable 
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name_user',
        'last_name_user',
        'birthdate',
        'email',
        'number_document',
        'password',
        // --- CAMPOS AÑADIDOS PARA EL REGISTRO ---
        'company_id', 
        'regional_id',
        'position_id',
    ];

   /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // Si el password no se hashea automáticamente, puedes descomentar la línea de abajo
            // 'password' => 'hashed', 
        ];
    }

    // Listas para filtros dinámicos (ajustadas a tus relaciones)
    protected $allowIncluded = ['company', 'regional', 'position', 'roles']; 
    protected $allowFilter = ['id', 'email']; 
    protected $allowSort = ['id', 'email', 'created_at'];

    // Relaciones

    // --- RELACIÓN DE ROLES (Muchos a Muchos) ---

    public function roles()
    {
        
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id');
    }


    public function getRoleNamesAttribute()
    {
        
        return $this->roles->pluck('name_role')->toArray();
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function regional()
    {
        return $this->belongsTo(Regional::class, 'regional_id');
    }
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    // Scopes (tus Scopes están bien definidos para el Query Builder)
    public function scopeIncluded(Builder $query)
    {
        if (empty(request('included'))) return;

        $relations = explode(',', request('included'));
        $allowIncluded = collect($this->allowIncluded);

        $filteredRelations = array_filter($relations, fn($relation) => $allowIncluded->contains($relation));

        if (!empty($filteredRelations)) {
            $query->with($filteredRelations);
        }
    }

    public function scopeFilter(Builder $query)
    {
        if (empty(request('filter'))) return;

        $filters = request('filter');
        $allowFilter = collect($this->allowFilter);

        foreach ($filters as $filter => $value) {
            if ($allowFilter->contains($filter)) {
                $query->where($filter, 'LIKE', "%{$value}%");
            }
        }
    }

    public function scopeSort(Builder $query)
    {
        if (empty(request('sort'))) return;

        $sortFields = explode(',', request('sort'));
        $allowSort = collect($this->allowSort);

        foreach ($sortFields as $sortField) {
            $direction = 'asc';

            if (substr($sortField, 0, 1) === '-') {
                $direction = 'desc';
                $sortField = substr($sortField, 1);
            }

            if ($allowSort->contains($sortField)) {
                $query->orderBy($sortField, $direction);
            }
        }
    }

    public function scopeGetOrPaginate(Builder $query)
    {
        if (request('perPage')) {
            $perPage = intval(request('perPage'));
            if ($perPage > 0) {
                return $query->paginate($perPage);
            }
        }
        return $query->get();
    }
}