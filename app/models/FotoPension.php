<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\OfertaPension;

class FotoPension extends Model
{
    //
    protected $table = "foto_pension";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'FOTO',
        'OFERTA_PENSION_ID'
    ];

    public function oferta(){
        return $this->belongsTo(OfertaPension::class,'OFERTA_PENSION_ID','ID');
    }

}
