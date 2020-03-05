<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\OfertaHabitacion;
use App\models\OfertaCasaApto;
use App\models\OfertaPension;
use App\models\FotoCasaApto;
use App\models\FotoHabitacion;
use App\models\FotoPension;

class UltimasOfertasController extends Controller
{
    //

    public function ofertas(Request $request)
    {

        $cantOfertasPorPagina = $request->input('cantOfertasPorPagina');
        $paginacionActual = $request->input('paginacionActual');
        $cantOfertasObtener = $cantOfertasPorPagina * $paginacionActual;

        $cantTotal = $this->contarOfertas($request);
        $ofertas = $this->filtrarOfertas($request);

        //si hay mas ofertas en la base de datos que las ofertas a obtener 
        //ofertas a obtener = ofertas por pagina * paginacion actual
        if ($cantTotal >= $cantOfertasObtener) {

            $ofertasFiltradas = array_slice($ofertas->toArray(), ($cantOfertasObtener - $cantOfertasPorPagina), $cantOfertasPorPagina);
        } else {

            //si las ofertas a obtener no completan el total de ofertas por pagina para una 
            //determinada paginacion. 
            //EJ: paginacion = 3, cantidad ofertas por paginas = 12; ofertas en la base de datos = 28
            //en este caso se devolveran solo las 4 ofertas restantes (25,26,27,28)
            if ($cantTotal > ($cantOfertasPorPagina * ($paginacionActual - 1))) {
                $ofertasFiltradas = array_slice($ofertas->toArray(), ($cantOfertasObtener - $cantOfertasPorPagina), $cantOfertasPorPagina);
            } else {
                //si no hay ofertas para esa paginacion
                //EJ: paginacion = 3, cantidad ofertas por paginas = 12; ofertas en la base de datos = 24
                ////en este caso no se devolveran ofertas
                $ofertasFiltradas = [];
            }
        }

        $fotos = [];
        for ($i = 0; $i < count($ofertasFiltradas); $i++) {
            $tipoInmueble = $ofertasFiltradas[$i]['INMUEBLE'];

            switch ($tipoInmueble) {
                case 'PENSION':

                    $fotoActual = FotoPension::where('OFERTA_PENSION_ID', $ofertasFiltradas[$i]['ID'])->select('FOTO')->take(1)->get();
                    $fotos[$i] = $fotoActual->first();
                    break;
                case 'HABITACION':

                    $fotoActual = FotoHabitacion::where('OFERTA_HABITACION_ID', $ofertasFiltradas[$i]['ID'])->select('FOTO')->take(1)->get();
                    $fotos[$i] = $fotoActual->first();
                    break;
                default:

                    $fotoActual = FotoCasaApto::where('OFERTA_CASA_APTO_ID', $ofertasFiltradas[$i]['ID'])->select('FOTO')->take(1)->get();
                    $fotos[$i] = $fotoActual->first();
                    break;
            }
        }

        return response()->json([
            'ofertas' => $ofertasFiltradas,
            'cantTotal' => $cantTotal,
            'fotos' => $fotos
        ]);
    }


    private function filtrarOfertas(Request $request)
    {

        $cantOfertasPorPagina = $request->input('cantOfertasPorPagina');
        $paginacionActual = $request->input('paginacionActual');
        $precioMaximo = $request->input('precioMaximo');
        $cantOfertasObtener = $cantOfertasPorPagina * $paginacionActual;

        $pais = 'COLOMBIA';

        $ofertasH = OfertaHabitacion::join(
            'ubicacion_oferta_habitacion',
            'ubicacion_oferta_habitacion.OFERTA_HABITACION_ID',
            '=',
            'oferta_habitacion.ID'
        )
            ->select(
                'oferta_habitacion.ID',
                'oferta_habitacion.INMUEBLE',
                'oferta_habitacion.TITULO_AVISO',
                'oferta_habitacion.PRECIO_MENSUAL',
                'ubicacion_oferta_habitacion.PAIS',
                'ubicacion_oferta_habitacion.DEPARTAMENTO',
                'ubicacion_oferta_habitacion.CIUDAD',
                'ubicacion_oferta_habitacion.DIRECCION',
                'oferta_habitacion.ACTUALIZADO_EN'
            )
            ->where('oferta_habitacion.ID', '>', 0)
            ->where('ubicacion_oferta_habitacion.PAIS', $pais)
            ->where('oferta_habitacion.PRECIO_MENSUAL', '<=', $precioMaximo)
            ->orderBy('oferta_habitacion.ACTUALIZADO_EN', 'desc')
            ->take($cantOfertasObtener)
            ->get();



        $ofertasCA = OfertaCasaApto::join(
            'ubicacion_oferta_casa_apto',
            'ubicacion_oferta_casa_apto.OFERTA_CASA_APTO_ID',
            '=',
            'oferta_casa_apto.ID'
        )
            ->select(
                'oferta_casa_apto.ID',
                'oferta_casa_apto.INMUEBLE',
                'oferta_casa_apto.TITULO_AVISO',
                'oferta_casa_apto.PRECIO_MENSUAL',
                'ubicacion_oferta_casa_apto.PAIS',
                'ubicacion_oferta_casa_apto.DEPARTAMENTO',
                'ubicacion_oferta_casa_apto.CIUDAD',
                'ubicacion_oferta_casa_apto.DIRECCION',
                'oferta_casa_apto.ACTUALIZADO_EN'
            )
            ->where('oferta_casa_apto.ID', '>', 0)
            ->where('ubicacion_oferta_casa_apto.PAIS', $pais)
            ->where('oferta_casa_apto.PRECIO_MENSUAL', '<=', $precioMaximo)
            ->orderBy('oferta_casa_apto.ACTUALIZADO_EN', 'desc')
            ->take($cantOfertasObtener)
            ->get();

        $ofertasP = OfertaPension::join(
            'ubicacion_oferta_pension',
            'ubicacion_oferta_pension.OFERTA_PENSION_ID',
            '=',
            'oferta_pension.ID'
        )
            ->select(
                'oferta_pension.ID',
                'oferta_pension.INMUEBLE',
                'oferta_pension.TITULO_AVISO',
                'oferta_pension.PRECIO_MENSUAL',
                'ubicacion_oferta_pension.PAIS',
                'ubicacion_oferta_pension.DEPARTAMENTO',
                'ubicacion_oferta_pension.CIUDAD',
                'ubicacion_oferta_pension.DIRECCION',
                'oferta_pension.ACTUALIZADO_EN'
            )
            ->where('oferta_pension.ID', '>', 0)
            ->where('ubicacion_oferta_pension.PAIS', $pais)
            ->where('oferta_pension.PRECIO_MENSUAL', '<=', $precioMaximo)
            ->orderBy('oferta_pension.ACTUALIZADO_EN', 'desc')
            ->take($cantOfertasObtener)
            ->get();


        $ofertas = $ofertasCA->concat($ofertasH);
        $ofertas = $ofertas->concat($ofertasP);
        $ofertas = $ofertas->sortByDesc('ACTUALIZADO_EN')->take($cantOfertasObtener);

        return $ofertas;
    }

    private function contarOfertas(Request $request)
    {

        $precioMaximo = $request->input('precioMaximo');
        $pais = 'COLOMBIA';

        $ofertasH = OfertaHabitacion::join(
            'ubicacion_oferta_habitacion',
            'ubicacion_oferta_habitacion.OFERTA_HABITACION_ID',
            '=',
            'oferta_habitacion.ID'
        )
            ->where('oferta_habitacion.ID', '>', 0)
            ->where('ubicacion_oferta_habitacion.PAIS', $pais)
            ->where('oferta_habitacion.PRECIO_MENSUAL', '<=', $precioMaximo)
            ->orderBy('oferta_habitacion.ACTUALIZADO_EN', 'desc')
            ->get();


        $ofertasCA = OfertaCasaApto::join(
            'ubicacion_oferta_casa_apto',
            'ubicacion_oferta_casa_apto.OFERTA_CASA_APTO_ID',
            '=',
            'oferta_casa_apto.ID'
        )
            ->where('oferta_casa_apto.ID', '>', 0)
            ->where('ubicacion_oferta_casa_apto.PAIS', $pais)
            ->where('oferta_casa_apto.PRECIO_MENSUAL', '<=', $precioMaximo)
            ->orderBy('oferta_casa_apto.ACTUALIZADO_EN', 'desc')
            ->get();

        $ofertasP = OfertaPension::join(
            'ubicacion_oferta_pension',
            'ubicacion_oferta_pension.OFERTA_PENSION_ID',
            '=',
            'oferta_pension.ID'
        )
            ->where('oferta_pension.ID', '>', 0)
            ->where('ubicacion_oferta_pension.PAIS', $pais)
            ->where('oferta_pension.PRECIO_MENSUAL', '<=', $precioMaximo)
            ->orderBy('oferta_pension.ACTUALIZADO_EN', 'desc')
            ->get();


        $ofertas = $ofertasCA->concat($ofertasH);
        $ofertas = $ofertas->concat($ofertasP);

        return $ofertas->count();
    }
}
