<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\OfertaPension;

class UbicacionOfertaPension extends Model
{
    //
    protected $table = "ubicacion_oferta_pension";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'PAIS',
        'DEPARTAMENTO',
        'CIUDAD',
        'LOCALIDAD',
        'DIRECCION',
        'CODIGO_POSTAL',
        'LATITUD',
        'LONGITUD',
        'OFERTA_PENSION_ID'
    ];

    public function oferta(){
        return $this->belongsTo(OfertaPension::class,'OFERTA_PENSION_ID','ID');
    }

}
