<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_role',
        'description_role'
    ];

    // Listas para filtros dinámicos
    protected $allowIncluded = ['users'];
    protected $allowFilter = ['id', 'name_role', 'description_role'];
    protected $allowSort = ['id', 'name_role', 'description_role'];

    // Relaciones
    public function users()
    {
        // ✅ INTEGRACIÓN/CORRECCIÓN: Se utiliza belongsToMany para la relación N:M
        // Asumo que la tabla pivote es 'role_users' y las claves son 'role_id' y 'user_id'.
        return $this->belongsToMany(User::class, 'role_users', 'role_id', 'user_id');
    }

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