<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\UbicacionOfertaCasaApto;
use App\models\FotoCasaApto;
use App\models\Usuario;
use App\models\ServicioEspecifico;

class OfertaCasaApto extends Model
{
    //
    protected $table = "oferta_casa_apto";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'TIPO_INMUEBLE',
        'NUM_HABITACIONES',
        'NUM_BANIOS',
        'AREA_INMUEBLE',
        'ESTANCIA_MINIMA',
        'SERVICIOS_PRINCIPALES',
        'AMOBLADA',
        'PRECIO_MENSUAL',
        'NUMERO_CELULAR',
        'DESCRIPCION',
        'TITULO_AVISO',
        'USUARIO_ID'
    ];

    public function ubicacion(){
        return $this->hasOne(UbicacionOfertaCasaApto::class,'OFERTA_CASA_APTO_ID','ID');
    }

    public function fotos(){
        return $this->hasMany(FotoCasaApto::class,'OFERTA_CASA_APTO_ID','ID');
    }

    public function usuario(){
        return $this->belongsTo(Usuario::class,'USUARIO_ID','ID');
    }

    public function serviciosEspecificos(){
        return $this->belongsToMany(ServicioEspecifico::class,'servicio_especifico_has_oferta_casa_apto',
        'OFERTA_CASA_APTO_ID','SERVICIO_ESPECIFICO_ID','ID','ID')
        ->withPivot('CREADO_EN','ACTUALIZADO_EN');
    }
}
