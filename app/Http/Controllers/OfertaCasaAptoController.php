<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\OfertaCasaApto;
use App\models\UbicacionOfertaCasaApto;
use App\models\FotoCasaApto;
use App\models\ServicioEspecifico;
use App\models\ServicioEspecificoOfertaCasaApto;

use Image;

use function PHPSTORM_META\type;

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
            'DEPARTAMENTO'
            => ($request->has('departamento') ? $request->input('departamento') : null),
            'CIUDAD'
            => ($request->has('ciudad') ? $request->input('ciudad') : null),
            'LOCALIDAD'
            => ($request->has('localidad') ? $request->input('localidad')
                : null),
            'DIRECCION'
            => ($request->has('direccion') ? $request->input('direccion')
                : null),
            'CODIGO_POSTAL'
            => ($request->has('codigoPostal') ? $request->input('codigoPostal') : null),
            'OFERTA_CASA_APTO_ID' => $oferta->id
        ]);


        //Guardamos fotos
        $this->fotosCasaApto($request, $oferta->id);

        //almacenando los servicios especificos
        $cantServicios = $request->input('cantServicios');

        for ($i = 0; $i < $cantServicios; $i++) {

            ServicioEspecificoOfertaCasaApto::create([
                'SERVICIO_ESPECIFICO_ID'
                => $request->input('servicioEspecifico' . $i),
                'OFERTA_CASA_APTO_ID' => $oferta->id
            ]);
        }


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
     * genera cadena string, de tamaño 10, con caracteres alfanuméricos 
     * aleatorios      
     */
    protected function random_string()
    {
        $key = '';
        $keys = array_merge(range('a', 'z'), range(0, 9));

        for ($i = 0; $i < 10; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }


    /**
     * Funcion para obtener imagenes de la oferta y guardarlas
     */
    private function fotosCasaApto(Request $request, $id_oferta)
    {

        //verificamos si la carpeta no existe, si no es asi creamos una
        $carpeta = public_path() . '/img/ofertasCasaApto/' . $id_oferta;
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        // ruta de las imagenes guardadas
        $ruta = public_path() . '/img/ofertasCasaApto/' . $id_oferta . '/';

        // cantidad de imagenes recibidas
        $cantImg = $request->input('cantImg');

        // recogida de imagenes recibidas 
        for ($i = 0; $i < $cantImg; $i++) {

            $imagenOriginal = $request->file('foto' . $i);

            // crear instancia de imagen
            $imagen = Image::make($imagenOriginal)->encode('webp');

            // generar un nombre aleatorio para la imagen
            $temp_name = $this->random_string() . '.webp';

            // guardar imagen
            // save( [ruta], [calidad])
            $imagen->save($ruta . $temp_name, 30);

            FotoCasaApto::create([
                'FOTO' => $temp_name,
                'OFERTA_CASA_APTO_ID' => $id_oferta
            ]);
        }
    }

    //Obtener ofertas por ubicación, precio maximo
    //ordenandolas por fecha de actualizacion(las mas recientes)
    public function obtenerOfertas(Request $request)
    {

        $cantOfertasPorPagina = $request->input('cantOfertasPorPagina');
        $paginacionActual = $request->input('paginacionActual');
        $precioMaximo = $request->input('precioMaximo');
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
        for ($i=0; $i < count($ofertasFiltradas) ; $i++) { 
            $fotos[$i] = FotoCasaApto::where('OFERTA_CASA_APTO_ID',$ofertasFiltradas[$i]['ID'])->select('FOTO')->take(1)->get();
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

        $especificidad = $this->especificidadUbicacion($request);
        $pais = 'COLOMBIA';

        switch ($especificidad) {
            case 1:
                //para busquedas de ubicacion por pais
                $ofertas = OfertaCasaApto::join(
                    'ubicacion_oferta_casa_apto',
                    'ubicacion_oferta_casa_apto.OFERTA_CASA_APTO_ID',
                    '=',
                    'oferta_casa_apto.ID'
                )
                    ->select(
                        'oferta_casa_apto.ID',
                        'oferta_casa_apto.PRECIO_MENSUAL',
                        'ubicacion_oferta_casa_apto.PAIS',
                        'ubicacion_oferta_casa_apto.DEPARTAMENTO',
                        'ubicacion_oferta_casa_apto.CIUDAD',
                        'ubicacion_oferta_casa_apto.DIRECCION'
                    )
                    ->where('oferta_casa_apto.ID', '>', 0)
                    ->where('ubicacion_oferta_casa_apto.PAIS', $pais)
                    ->where('oferta_casa_apto.PRECIO_MENSUAL', '<=', $precioMaximo)
                    ->orderBy('oferta_casa_apto.ACTUALIZADO_EN', 'desc')
                    ->take($cantOfertasObtener)
                    ->get();
                break;
            case 2:
                //para busquedas de ubicacion por pais y departamento
                $ofertas = OfertaCasaApto::join(
                    'ubicacion_oferta_casa_apto',
                    'ubicacion_oferta_casa_apto.OFERTA_CASA_APTO_ID',
                    '=',
                    'oferta_casa_apto.ID'
                )
                    ->select(
                        'oferta_casa_apto.ID',
                        'oferta_casa_apto.PRECIO_MENSUAL',
                        'ubicacion_oferta_casa_apto.PAIS',
                        'ubicacion_oferta_casa_apto.DEPARTAMENTO',
                        'ubicacion_oferta_casa_apto.CIUDAD',
                        'ubicacion_oferta_casa_apto.DIRECCION'
                    )
                    ->where('oferta_casa_apto.ID', '>', 0)
                    ->where('ubicacion_oferta_casa_apto.PAIS', $pais)
                    ->where('ubicacion_oferta_casa_apto.DEPARTAMENTO', $request->input('departamento'))
                    ->where('oferta_casa_apto.PRECIO_MENSUAL', '<=', $precioMaximo)
                    ->orderBy('oferta_casa_apto.ACTUALIZADO_EN', 'desc')
                    ->take($cantOfertasObtener)
                    ->get();
                break;
            case 3:
                //para busquedas de ubicacion por pais, departamento y ciudad
                $ofertas = OfertaCasaApto::join(
                    'ubicacion_oferta_casa_apto',
                    'ubicacion_oferta_casa_apto.OFERTA_CASA_APTO_ID',
                    '=',
                    'oferta_casa_apto.ID'
                )
                    ->select(
                        'oferta_casa_apto.ID',
                        'oferta_casa_apto.PRECIO_MENSUAL',
                        'ubicacion_oferta_casa_apto.PAIS',
                        'ubicacion_oferta_casa_apto.DEPARTAMENTO',
                        'ubicacion_oferta_casa_apto.CIUDAD',
                        'ubicacion_oferta_casa_apto.DIRECCION'
                    )
                    ->where('oferta_casa_apto.ID', '>', 0)
                    ->where('ubicacion_oferta_casa_apto.PAIS', $pais)
                    ->where('ubicacion_oferta_casa_apto.DEPARTAMENTO', $request->input('departamento'))
                    ->where('ubicacion_oferta_casa_apto.CIUDAD', $request->input('ciudad'))
                    ->where('oferta_casa_apto.PRECIO_MENSUAL', '<=', $precioMaximo)
                    ->orderBy('oferta_casa_apto.ACTUALIZADO_EN', 'desc')
                    ->take($cantOfertasObtener)
                    ->get();
                break;
                    
        }

       

        return $ofertas;
    }

    private function especificidadUbicacion(Request $request)
    {
        //el pais siempre debe estar
        $especifidad = 1;

        ($request->has('departamento') ? $especifidad++ : null);
        ($request->has('ciudad') ? $especifidad++ : null);
        ($request->has('localidad') ? $especifidad++ : null);
        //($request->has('direccion') ? $especifidad++ : null);
        //($request->has('codigoPostal') ? $especifidad++ : null);
        
        return $especifidad;
    }

    private function contarOfertas(Request $request)
    {

        $cantOfertasPorPagina = $request->input('cantOfertasPorPagina');
        $paginacionActual = $request->input('paginacionActual');
        $precioMaximo = $request->input('precioMaximo');
        $cantOfertasObtener = $cantOfertasPorPagina * $paginacionActual;

        $especificidad = $this->especificidadUbicacion($request);
        $pais = 'COLOMBIA';

        switch ($especificidad) {
            case 1:
                //para busquedas de ubicacion por pais
                $ofertas = OfertaCasaApto::join(
                    'ubicacion_oferta_casa_apto',
                    'ubicacion_oferta_casa_apto.OFERTA_CASA_APTO_ID',
                    '=',
                    'oferta_casa_apto.ID'
                )
                    ->select(
                        'oferta_casa_apto.ID',
                        'oferta_casa_apto.PRECIO_MENSUAL',
                        'ubicacion_oferta_casa_apto.PAIS',
                        'ubicacion_oferta_casa_apto.DEPARTAMENTO',
                        'ubicacion_oferta_casa_apto.CIUDAD',
                        'ubicacion_oferta_casa_apto.DIRECCION'
                    )
                    ->where('oferta_casa_apto.ID', '>', 0)
                    ->where('ubicacion_oferta_casa_apto.PAIS', $pais)
                    ->where('oferta_casa_apto.PRECIO_MENSUAL', '<=', $precioMaximo)
                    ->count();
                break;
            case 2:
                //para busquedas de ubicacion por pais y departamento
                $ofertas = OfertaCasaApto::join(
                    'ubicacion_oferta_casa_apto',
                    'ubicacion_oferta_casa_apto.OFERTA_CASA_APTO_ID',
                    '=',
                    'oferta_casa_apto.ID'
                )
                    ->select(
                        'oferta_casa_apto.ID',
                        'oferta_casa_apto.PRECIO_MENSUAL',
                        'ubicacion_oferta_casa_apto.PAIS',
                        'ubicacion_oferta_casa_apto.DEPARTAMENTO',
                        'ubicacion_oferta_casa_apto.CIUDAD',
                        'ubicacion_oferta_casa_apto.DIRECCION'
                    )
                    ->where('oferta_casa_apto.ID', '>', 0)
                    ->where('ubicacion_oferta_casa_apto.PAIS', $pais)
                    ->where('ubicacion_oferta_casa_apto.DEPARTAMENTO', $request->input('departamento'))
                    ->where('oferta_casa_apto.PRECIO_MENSUAL', '<=', $precioMaximo)
                    ->count();
                break;
            case 3:
                //para busquedas de ubicacion por pais, departamento y ciudad
                $ofertas = OfertaCasaApto::join(
                    'ubicacion_oferta_casa_apto',
                    'ubicacion_oferta_casa_apto.OFERTA_CASA_APTO_ID',
                    '=',
                    'oferta_casa_apto.ID'
                )
                    ->select(
                        'oferta_casa_apto.ID',
                        'oferta_casa_apto.PRECIO_MENSUAL',
                        'ubicacion_oferta_casa_apto.PAIS',
                        'ubicacion_oferta_casa_apto.DEPARTAMENTO',
                        'ubicacion_oferta_casa_apto.CIUDAD',
                        'ubicacion_oferta_casa_apto.DIRECCION'
                    )
                    ->where('oferta_casa_apto.ID', '>', 0)
                    ->where('ubicacion_oferta_casa_apto.PAIS', $pais)
                    ->where('ubicacion_oferta_casa_apto.DEPARTAMENTO', $request->input('departamento'))
                    ->where('ubicacion_oferta_casa_apto.CIUDAD', $request->input('ciudad'))
                    ->where('oferta_casa_apto.PRECIO_MENSUAL', '<=', $precioMaximo)
                    ->count();
                break;

            default:
                //para busquedas de ubicacion por pais, departamento, ciudad, localidad o/y direccion
                $ofertas = OfertaCasaApto::join(
                    'ubicacion_oferta_casa_apto',
                    'ubicacion_oferta_casa_apto.OFERTA_CASA_APTO_ID',
                    '=',
                    'oferta_casa_apto.ID'
                )
                    ->select(
                        'oferta_casa_apto.ID',
                        'oferta_casa_apto.PRECIO_MENSUAL',
                        'ubicacion_oferta_casa_apto.PAIS',
                        'ubicacion_oferta_casa_apto.DEPARTAMENTO',
                        'ubicacion_oferta_casa_apto.CIUDAD',
                        'ubicacion_oferta_casa_apto.DIRECCION'
                    )
                    ->where('oferta_casa_apto.ID', '>', 0)
                    ->where('ubicacion_oferta_casa_apto.PAIS', $pais)
                    ->where('ubicacion_oferta_casa_apto.DEPARTAMENTO', $request->input('departamento'))
                    ->where('ubicacion_oferta_casa_apto.CIUDAD', $request->input('ciudad'))
                    ->where('ubicacion_oferta_casa_apto.CODIGO_POSTAL', $request->input('codigoPostal'))
                    ->where('oferta_casa_apto.PRECIO_MENSUAL', '<=', $precioMaximo)
                    ->count();
                break;
        }

        return $ofertas;
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
