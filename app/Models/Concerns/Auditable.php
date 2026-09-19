<?php

namespace App\Models\Concerns;

use App\Models\Auditoria;

/**
 * Registra automaticamente en la bitacora las acciones (crear/editar/eliminar)
 * realizadas por un usuario del staff autenticado. No registra acciones de
 * seeders, comandos ni del portal de clientes (no hay usuario web).
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn ($model) => static::registrarAuditoria('creo', $model));
        static::updated(fn ($model) => static::registrarAuditoria('edito', $model));
        static::deleted(fn ($model) => static::registrarAuditoria('elimino', $model));
    }

    protected static function registrarAuditoria(string $accion, $model): void
    {
        if (! auth()->hasUser()) {
            return; // solo auditamos acciones del staff autenticado
        }

        $user = auth()->user();

        Auditoria::create([
            'empresa_id' => $model->empresa_id ?? $user->empresa_id,
            'user_id' => $user->id,
            'accion' => $accion,
            'modelo' => class_basename($model),
            'modelo_id' => $model->getKey(),
            'descripcion' => $model->nombre ?? $model->motivo ?? $model->servicio ?? ('#'.$model->getKey()),
            'ip' => request()->ip(),
        ]);
    }
}
