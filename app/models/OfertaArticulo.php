<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\SubCategoria;
use App\models\Usuario;
use App\models\FotoArticulo;
use App\models\UbicacionOfertaArticulo;

class OfertaArticulo extends Model
{
    //
    protected $table = "oferta_articulo";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'DESCRIPCION',
        'PRECIO',
        'NUMERO_CELULAR',
        'TITULO_AVISO',
        'USUARIO_ID',
        'SUB_CATEGORIA_ID'
    ];

    public function subCategoria(){
        return $this->belongsTo(SubCategoria::class,'SUB_CATEGORIA_ID','ID');
    }

    public function usuario(){
        return $this->belongsTo(Usuario::class,'USUARIO_ID','ID');
    }

    public function fotos(){
        return $this->hasMany(FotoArticulo::class,'OFERTA_ARTICULO_ID','ID');
    }

    public function ubicacion(){
        return $this->hasOne(UbicacionOfertaArticulo::class,'OFERTA_ARTICULO_ID','ID');
    }

}
