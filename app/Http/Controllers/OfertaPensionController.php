<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\OfertaPension;
use App\models\UbicacionOfertaPension;
use App\models\Usuario;
use App\models\FotoPension;
use App\models\ServicioEspecificoOfertaPension;
use App\models\NormaCasaOfertaPension;


use Image;

class OfertaPensionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $oferta = OfertaPension::find(7);
        $oferta->usuario;
        $oferta->ubicacion;
        $oferta->fotos;
        $oferta->serviciosEspecificos;
        $oferta->normasCasa;
        //$oferta->oferta;

        return response()->json([
            'ofertasPension' => $oferta
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
        $oferta = OfertaPension::create([
            'HABITACIONES_DISPONIBLES' => $request->input('habitacionesDisponibles'),
            'HABITACIONES_INDIVIDUALES' => $request->input('habitacionesIndividuales'),
            'HABITACIONES_COMPARTIDAS' => $request->input('habitacionesCompartidas'),
            'NUM_HABITANTES' => $request->input('numHabitantes'),
            'HABITACIONES_BANIO_INTERNO' => $request->input('habitacionesBanioInterno'),
            'GENERO_ADMITIDO' => $request->input('genero'),
            'ALIMENTACION_INCLUIDA' => $request->input('alimentacion'),
            'PRECIO_MENSUAL' => $request->input('precio'),
            'NUM_CELULAR' => $request->input('celular'),
            'TITULO_AVISO' => $request->input('tituloAviso'),
            'DESCRIPCION_AVISO' => $request->input('descripcion'),
            'USUARIO_ID' => $request->input('usuario')
        ]);

        UbicacionOfertaPension::create([
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
            'OFERTA_PENSION_ID' => $oferta->id
        ]);

        //Guardamos fotos
        $this->fotosPension($request, $oferta->id);

        //almacenando los servicios especificos
        $cantServicios = $request->input('cantServicios');

        for ($i = 0; $i < $cantServicios; $i++) {

            ServicioEspecificoOfertaPension::create([
                'SERVICIO_ESPECIFICO_ID'
                => $request->input('servicioEspecifico' . $i),
                'OFERTA_PENSION_ID' => $oferta->id
            ]);
        }

        //almacenamos normas de la casa
        $cantNormas = $request->input('cantNormas');

        for ($i = 0; $i < $cantNormas; $i++) {

            NormaCasaOfertaPension::create([
                'NORMA_CASA_ID'
                => $request->input('normaCasa' . $i),
                'OFERTA_PENSION_ID' => $oferta->id
            ]);
        }
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
    private function fotosPension(Request $request, $id_oferta)
    {

        //verificamos si la carpeta no existe, si no es asi creamos una
        $carpeta = public_path() . '/img/ofertasPension/' . $id_oferta;
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        // ruta de las imagenes guardadas
        $ruta = public_path() . '/img/ofertasPension/' . $id_oferta . '/';

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

            FotoPension::create([
                'FOTO' => $temp_name,
                'OFERTA_PENSION_ID' => $id_oferta
            ]);
        }
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