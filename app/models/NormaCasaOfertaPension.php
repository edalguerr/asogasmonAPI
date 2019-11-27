<?php

namespace App\models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class NormaCasaOfertaPension extends Pivot
{
    //
    public $incrementing = true;
    protected $table = "norma_casa_has_oferta_pension";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'NORMA_CASA_ID',
        'OFERTA_PENSION_ID'
    ];

}
