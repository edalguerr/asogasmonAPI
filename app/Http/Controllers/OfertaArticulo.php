<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\OfertaArticulo as OfertaArticuloModel;
use App\models\SubCategoria;
use App\models\Usuario;
use App\models\FotoArticulo;
use App\models\UbicacionOfertaArticulo;

class OfertaArticulo extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $ofertas = OfertaArticuloModel::all();
        
        return response()->json([
            'ofertasArticulo' => $ofertas
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $oferta = OfertaArticuloModel::create([
            'DESCRIPCION' => $request->input('descripcion'),
            'PRECIO' => $request->input('precio'),
            'NUMERO_CELULAR' => $request->input('celular'),
            'TITULO_AVISO' => $request->input('titulo'),
            'USUARIO_ID' => $request->input('usuario'),
            'SUB_CATEGORIA_ID' => $request->input('subCategoria')
        ]);
        
        //POR HACER: averiguar como almacenar fotos en php
        //POR HACER: verificar cuantas fotos agrego el usuario y meter esta accion en un ciclo
        FotoArticulo::create([
            'FOTO' => 'fotos/articulos/usuario/3/foto3.jpg',
            'OFERTA_ARTICULO_ID' => $oferta->id
        ]);

        UbicacionOfertaArticulo::create([
            'OFERTA_ARTICULO_ID' =>  $oferta->id,
            'PAIS' => $request->input('pais'),
            'DEPARTAMENTO' => $request->input('departamento'),
            'CIUDAD' => $request->input('ciudad')
        ]);

        $ofertaN = OfertaArticuloModel::find($oferta->id);
        $ofertaN->usuario;
        $ofertaN->subCategoria;
        $ofertaN->ubicacion;
        $ofertaN->fotos;

        return response()->json([
            'ofertasArticulo' => $ofertaN
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
}
