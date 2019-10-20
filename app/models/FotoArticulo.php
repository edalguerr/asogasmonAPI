<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class FotoArticulo extends Model
{
    //
    protected $table = 'foto_articulo';
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';
    
    protected $fillable = [
        'FOTO',
        'OFERTA_ARTICULO_ID'
    ];

    public function oferta(){
        return $this->belongsTo(OfertaArticulo::class,'OFERTA_ARTICULO_ID','ID');
    }

}
