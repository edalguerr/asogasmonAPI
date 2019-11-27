<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\Usuario;
use App\models\UbicacionOfertaPension;
use App\models\FotoPension;
use App\models\ServicioEspecifico;
use App\models\NormaCasa;

class OfertaPension extends Model
{
    //
    protected $table = "oferta_pension";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'HABITACIONES_DISPONIBLES',
        'HABITACIONES_INDIVIDUALES',
        'HABITACIONES_COMPARTIDAS',
        'NUM_HABITANTES',
        'HABITACIONES_BANIO_INTERNO',
        'GENERO_ADMITIDO',
        'ALIMENTACION_INCLUIDA',
        'PRECIO_MENSUAL',
        'NUM_CELULAR',
        'TITULO_AVISO',
        'DESCRIPCION_AVISO',
        'USUARIO_ID'
    ];

    public function usuario(){
        return $this->belongsTo(Usuario::class,'USUARIO_ID','ID');
    }

    public function ubicacion(){
        return $this->hasOne(UbicacionOfertaPension::class,'OFERTA_PENSION_ID','ID');
    }

    public function fotos(){
        return $this->hasMany(FotoPension::class,'OFERTA_PENSION_ID','ID');
    }

    public function serviciosEspecificos(){
        return $this->belongsToMany(ServicioEspecifico::class,'servicio_especifico_has_oferta_pension',
        'OFERTA_PENSION_ID','SERVICIO_ESPECIFICO_ID','ID','ID')
        ->withPivot('CREADO_EN','ACTUALIZADO_EN');
    }

    public function normasCasa(){
        return $this->belongsToMany(NormaCasa::class,'norma_casa_has_oferta_pension',
        'OFERTA_PENSION_ID','NORMA_CASA_ID','ID','ID')
        ->withPivot('CREADO_EN','ACTUALIZADO_EN');
    }


}
