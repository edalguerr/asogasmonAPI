<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\Usuario;
use App\models\UbicacionOfertaHabitacion;
use App\models\ServicioEspecifico;
use App\models\FotoHabitacion;
use App\models\NormaCasa;

class OfertaHabitacion extends Model
{
    // 
    protected $table = "oferta_habitacion";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'AMOBLADA',
        'TIPO_HABITACION',
        'BANIO_INTERNO',
        'GENERO_ADMITIDO',
        'ALIMENTACION_INCLUIDA',
        'AREA_INMUEBLE',
        'ESTANCIA_MINIMA',
        'NUM_HABITANTES',
        'NUMERO_CELULAR',
        'PRECIO_MENSUAL',
        'TITULO_AVISO',
        'DESCRIPCION',
        'USUARIO_ID'
    ];

    public function usuario(){
        return $this->belongsTo(Usuario::class,'USUARIO_ID','ID');
    }

    public function ubicacion(){
        return $this->hasOne(UbicacionOfertaHabitacion::class,'OFERTA_HABITACION_ID','ID');
    }

    public function serviciosEspecificos(){
        return $this->belongsToMany(ServicioEspecifico::class,'servicio_especifico_has_oferta_habitacion',
        'OFERTA_HABITACION_ID','SERVICIO_ESPECIFICO_ID','ID','ID')
        ->withPivot('CREADO_EN','ACTUALIZADO_EN');
    }

    public function fotos(){
        return $this->hasMany(FotoHabitacion::class,'OFERTA_HABITACION_ID','ID');
    }

    public function normasCasa(){
        return $this->belongsToMany(NormaCasa::class,'norma_casa_has_oferta_habitacion',
        'OFERTA_HABITACION_ID','NORMA_CASA_ID','ID','ID')
        ->withPivot('CREADO_EN','ACTUALIZADO_EN');
    }

}
