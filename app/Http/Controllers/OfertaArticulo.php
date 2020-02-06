<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\OfertaArticulo as OfertaArticuloModel;
use App\models\SubCategoria;
use App\models\Usuario;
use App\models\FotoArticulo;
use App\models\UbicacionOfertaArticulo;

use Image; //intervention image provider 

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

        //Guardando la ubicacion de la oferta    
        UbicacionOfertaArticulo::create([
            'OFERTA_ARTICULO_ID' =>  $oferta->id,
            'PAIS' => $request->input('pais'),
            'DEPARTAMENTO' => ($request->has('departamento') ? $request->input('departamento') : null),
            'CIUDAD' => ($request->has('ciudad') ? $request->input('ciudad') : null),
            'LOCALIDAD' => ($request->has('localidad') ? $request->input('localidad') : null),
            'CODIGO_POSTAL' => ($request->has('codigoPostal') ? $request->input('codigoPostal') : null)
        ]);


        //Guardando las fotos 
        $this->fotosArticulos($request, $oferta->id);


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
     * genera cadena string, de tamaño 10, con caracteres alfanuméricos aleatorios
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
     * Funcion para obtener imagenes de articulo y guardarlas
     */
    private function fotosArticulos(Request $request, $id_articulo)
    {

        //verificamos si la carpeta no existe, si no es asi creamos una
        $carpeta = public_path() . '/img/ofertasArticulo/' . $id_articulo;
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        // ruta de las imagenes guardadas
        $ruta = public_path() . '/img/ofertasArticulo/' . $id_articulo . '/';

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


            FotoArticulo::create([
                'FOTO' => $temp_name,
                'OFERTA_ARTICULO_ID' => $id_articulo
            ]);
        }
    }



    //Obtener ofertas de articulos
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
        for ($i = 0; $i < count($ofertasFiltradas); $i++) {
            $fotoActual = FotoArticulo::where('OFERTA_ARTICULO_ID', $ofertasFiltradas[$i]['ID'])->select('FOTO')->take(1)->get();
            $fotos[$i] = $fotoActual->first();
        }

        return response()->json([
            'ofertas' => $ofertasFiltradas,
            'cantTotal' => $cantTotal,
            'fotos' => $fotos
        ]);
    }


    private function especificidadUbicacion(Request $request)
    {
        //el pais siempre debe estar
        $especifidad = 1;

        ($request->has('departamento') ? $especifidad++ : null);
        ($request->has('ciudad') ? $especifidad++ : null);
        //($request->has('localidad') ? $especifidad++ : null);
        //($request->has('direccion') ? $especifidad++ : null);
        ($request->has('codigoPostal') ? $especifidad++ : null);

        return $especifidad;
    }

    /************************************************************************************************** */
    /*OBTENER OFERTAS POR UBICACIÓN Y PRECIO MAXIMO, ORDENANDOLAS POR FECHA DE ACTUALIZACIÓN (las más recientes)*/
    /************************************************************************************************** */
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
                $ofertas = OfertaArticuloModel::join(
                    'ubicacion_oferta_articulo',
                    'ubicacion_oferta_articulo.OFERTA_ARTICULO_ID',
                    '=',
                    'oferta_articulo.ID'
                )
                    ->select(
                        'oferta_articulo.ID',
                        'oferta_articulo.PRECIO',
                        'ubicacion_oferta_articulo.PAIS',
                        'ubicacion_oferta_articulo.DEPARTAMENTO',
                        'ubicacion_oferta_articulo.CIUDAD',
                        'ubicacion_oferta_articulo.LOCALIDAD'
                    )
                    ->where('oferta_articulo.ID', '>', 0)
                    ->where('ubicacion_oferta_articulo.PAIS', $pais)
                    ->where('oferta_articulo.PRECIO', '<=', $precioMaximo)
                    ->orderBy('oferta_articulo.ACTUALIZADO_EN', 'desc')
                    ->take($cantOfertasObtener)
                    ->get();
                break;
            case 2:
                //para busquedas de ubicacion por pais y departamento
                $ofertas = OfertaArticuloModel::join(
                    'ubicacion_oferta_articulo',
                    'ubicacion_oferta_articulo.OFERTA_ARTICULO_ID',
                    '=',
                    'oferta_articulo.ID'
                )
                    ->select(
                        'oferta_articulo.ID',
                        'oferta_articulo.PRECIO',
                        'ubicacion_oferta_articulo.PAIS',
                        'ubicacion_oferta_articulo.DEPARTAMENTO',
                        'ubicacion_oferta_articulo.CIUDAD',
                        'ubicacion_oferta_articulo.LOCALIDAD'
                    )
                    ->where('oferta_articulo.ID', '>', 0)
                    ->where('ubicacion_oferta_articulo.PAIS', $pais)
                    ->where('ubicacion_oferta_articulo.DEPARTAMENTO', $request->input('departamento'))
                    ->where('oferta_articulo.PRECIO', '<=', $precioMaximo)
                    ->orderBy('oferta_articulo.ACTUALIZADO_EN', 'desc')
                    ->take($cantOfertasObtener)
                    ->get();
                break;
            case 3:
                //para busquedas de ubicacion por pais, departamento y ciudad
                $ofertas = OfertaArticuloModel::join(
                    'ubicacion_oferta_articulo',
                    'ubicacion_oferta_articulo.OFERTA_ARTICULO_ID',
                    '=',
                    'oferta_articulo.ID'
                )
                    ->select(
                        'oferta_articulo.ID',
                        'oferta_articulo.PRECIO',
                        'ubicacion_oferta_articulo.PAIS',
                        'ubicacion_oferta_articulo.DEPARTAMENTO',
                        'ubicacion_oferta_articulo.CIUDAD',
                        'ubicacion_oferta_articulo.LOCALIDAD'
                    )
                    ->where('oferta_articulo.ID', '>', 0)
                    ->where('ubicacion_oferta_articulo.PAIS', $pais)
                    ->where('ubicacion_oferta_articulo.DEPARTAMENTO', $request->input('departamento'))
                    ->where('ubicacion_oferta_articulo.CIUDAD', $request->input('ciudad'))
                    ->where('oferta_articulo.PRECIO', '<=', $precioMaximo)
                    ->orderBy('oferta_articulo.ACTUALIZADO_EN', 'desc')
                    ->take($cantOfertasObtener)
                    ->get();
                break;
            default:
                //para busquedas de ubicacion por pais, departamento, ciudad y localidad(codigo postal)
                $ofertas = OfertaArticuloModel::join(
                    'ubicacion_oferta_articulo',
                    'ubicacion_oferta_articulo.OFERTA_ARTICULO_ID',
                    '=',
                    'oferta_articulo.ID'
                )
                    ->select(
                        'oferta_articulo.ID',
                        'oferta_articulo.PRECIO',
                        'ubicacion_oferta_articulo.PAIS',
                        'ubicacion_oferta_articulo.DEPARTAMENTO',
                        'ubicacion_oferta_articulo.CIUDAD',
                        'ubicacion_oferta_articulo.LOCALIDAD'
                    )
                    ->where('oferta_articulo.ID', '>', 0)
                    ->where('ubicacion_oferta_articulo.PAIS', $pais)
                    ->where('ubicacion_oferta_articulo.DEPARTAMENTO', $request->input('departamento'))
                    ->where('ubicacion_oferta_articulo.CIUDAD', $request->input('ciudad'))
                    ->where('ubicacion_oferta_articulo.CODIGO_POSTAL', $request->input('codigoPostal'))
                    ->where('oferta_articulo.PRECIO', '<=', $precioMaximo)
                    ->orderBy('oferta_articulo.ACTUALIZADO_EN', 'desc')
                    ->take($cantOfertasObtener)
                    ->get();
                break;
        }


        return $ofertas;
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
                $ofertas = OfertaArticuloModel::join(
                    'ubicacion_oferta_articulo',
                    'ubicacion_oferta_articulo.OFERTA_ARTICULO_ID',
                    '=',
                    'oferta_articulo.ID'
                )
                    ->where('oferta_articulo.ID', '>', 0)
                    ->where('ubicacion_oferta_articulo.PAIS', $pais)
                    ->where('oferta_articulo.PRECIO', '<=', $precioMaximo)
                    ->count();
                break;
            case 2:
                //para busquedas de ubicacion por pais y departamento
                $ofertas = OfertaArticuloModel::join(
                    'ubicacion_oferta_articulo',
                    'ubicacion_oferta_articulo.OFERTA_ARTICULO_ID',
                    '=',
                    'oferta_articulo.ID'
                )
                    ->where('oferta_articulo.ID', '>', 0)
                    ->where('ubicacion_oferta_articulo.PAIS', $pais)
                    ->where('ubicacion_oferta_articulo.DEPARTAMENTO', $request->input('departamento'))
                    ->where('oferta_articulo.PRECIO', '<=', $precioMaximo)
                    ->count();
                break;
            case 3:
                //para busquedas de ubicacion por pais, departamento y ciudad
                $ofertas = OfertaArticuloModel::join(
                    'ubicacion_oferta_articulo',
                    'ubicacion_oferta_articulo.OFERTA_ARTICULO_ID',
                    '=',
                    'oferta_articulo.ID'
                )
                    ->where('oferta_articulo.ID', '>', 0)
                    ->where('ubicacion_oferta_articulo.PAIS', $pais)
                    ->where('ubicacion_oferta_articulo.DEPARTAMENTO', $request->input('departamento'))
                    ->where('ubicacion_oferta_articulo.CIUDAD', $request->input('ciudad'))
                    ->where('oferta_articulo.PRECIO', '<=', $precioMaximo)
                    ->count();
                break;

            default:
                //para busquedas de ubicacion por pais, departamento, ciudad, localidad o/y direccion
                $ofertas = OfertaArticuloModel::join(
                    'ubicacion_oferta_articulo',
                    'ubicacion_oferta_articulo.OFERTA_ARTICULO_ID',
                    '=',
                    'oferta_articulo.ID'
                )
                    ->where('oferta_articulo.ID', '>', 0)
                    ->where('ubicacion_oferta_articulo.PAIS', $pais)
                    ->where('ubicacion_oferta_articulo.DEPARTAMENTO', $request->input('departamento'))
                    ->where('ubicacion_oferta_articulo.CIUDAD', $request->input('ciudad'))
                    ->where('ubicacion_oferta_articulo.CODIGO_POSTAL', $request->input('codigoPostal'))
                    ->where('oferta_articulo.PRECIO', '<=', $precioMaximo)
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
}
