<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

     protected $fillable = [
        'type_publication'
    ];

    // Listas para filtros dinámicos
    protected $allowIncluded = ['companies'];
    protected $allowFilter = ['id', 'type_publication'];
    protected $allowSort = ['id', 'type_publication', 'created_at'];

    // Relaciones
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

    // Scopes
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
