<?php

namespace App\Models\Concerns;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Aisla los datos por empresa (multi-tenancy en una sola base de datos).
 * - Global scope que filtra por la empresa del usuario autenticado.
 * - Al crear un registro, asigna la empresa del usuario automaticamente.
 * El super admin no tiene empresa, por lo que ve todos los registros.
 *
 * IMPORTANTE: se usa auth()->hasUser() para no forzar la resolucion del usuario
 * durante peticiones aun no autenticadas (evita efectos colaterales en el login).
 * Este trait NO debe aplicarse al modelo User (provocaria recursion).
 */
trait BelongsToEmpresa
{
    public static function bootBelongsToEmpresa(): void
    {
        static::addGlobalScope('empresa', function (Builder $builder) {
            $user = auth()->hasUser() ? auth()->user() : null;
            if ($user && ! $user->es_super_admin && $user->empresa_id) {
                $builder->where($builder->getModel()->getTable().'.empresa_id', $user->empresa_id);
            }
        });

        static::creating(function ($model) {
            if (empty($model->empresa_id)) {
                $user = auth()->hasUser() ? auth()->user() : null;
                if ($user && $user->empresa_id) {
                    $model->empresa_id = $user->empresa_id;
                }
            }
        });
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
