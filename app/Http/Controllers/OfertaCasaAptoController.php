<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\OfertaCasaApto;
use App\models\UbicacionOfertaCasaApto;
use App\models\FotoCasaApto;
use App\models\ServicioEspecifico;
use App\models\ServicioEspecificoOfertaCasaApto;

class OfertaCasaAptoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $ofertas =  OfertaCasaApto::all();
        
        return response()->json([
            'ofertasCasaApto' => $ofertas
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
        //
        $oferta = OfertaCasaApto::create([
            'TIPO_INMUEBLE' => $request->input('tipoInmueble'),
            'NUM_HABITACIONES' => $request->input('numHabitaciones'),
            'NUM_BANIOS' => $request->input('numBanios'),
            'AREA_INMUEBLE' => $request->input('areaInmueble'),
            'ESTANCIA_MINIMA' => $request->input('estanciaMinima'),
            'SERVICIOS_PRINCIPALES' => $request->input('serviciosPrincipales'),
            'AMOBLADA' => $request->input('amoblada'),
            'PRECIO_MENSUAL' => $request->input('precio'),
            'NUMERO_CELULAR' => $request->input('celular'),
            'DESCRIPCION' => $request->input('descripcion'),
            'TITULO_AVISO' => $request->input('tituloAviso'),
            'USUARIO_ID' => $request->input('usuario')
        ]);

        UbicacionOfertaCasaApto::create([
            'PAIS' => $request->input('pais'),
            'DEPARTAMENTO' => $request->input('departamento'),
            'CIUDAD' => $request->input('ciudad'),
            'DIRECCION' => $request->input('direccion'),
            'CODIGO_POSTAL' => $request->input('codigoPostal'),
            'OFERTA_CASA_APTO_ID' => $oferta->id
        ]);

        //POR HACER: averiguar como almacenar fotos en php
        //POR HACER: verificar cuantas fotos agrego el usuario y meter esta accion en un ciclo
        FotoCasaApto::create([
            'FOTO' => 'fotos/casaApto/usuario/3/foto2.jpg',
            'OFERTA_CASA_APTO_ID' => $oferta->id
        ]);

        //POR HACER: verificar cuantos servicios especificios selecciono el usuario y 
        //meter esta accion en un ciclo para que relacione todos los servicios correspondientes a la oferta
        ServicioEspecificoOfertaCasaApto::create([
            'SERVICIO_ESPECIFICO_ID' => 1,
            'OFERTA_CASA_APTO_ID' => $oferta->id
        ]);

        $ofertaN =  OfertaCasaApto::find($oferta->id);
        $ofertaN->usuario;
        $ofertaN->ubicacion;
        $ofertaN->serviciosEspecificos;
        $ofertaN->fotos;

        return response()->json([
            'ofertasCasaApto' => $ofertaN
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
