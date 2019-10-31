<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\OfertaCasaApto;

class UbicacionOfertaCasaApto extends Model
{
    //
    protected $table = "ubicacion_oferta_casa_apto";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'PAIS',
        'DEPARTAMENTO',
        'CIUDAD',
        'DIRECCION',
        'CODIGO_POSTAL',
        'OFERTA_CASA_APTO_ID'
    ];

    public function oferta(){
        return $this->belongsTo(OfertaCasaApto::class,'OFERTA_CASA_APTO_ID','ID');
    }
}
