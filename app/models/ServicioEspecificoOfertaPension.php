<?php

namespace App\models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ServicioEspecificoOfertaPension extends Pivot
{
    //
    public $incrementing = true;
    protected $table = "servicio_especifico_has_oferta_pension";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'SERVICIO_ESPECIFICO_ID',
        'OFERTA_PENSION_ID'
    ];

}
