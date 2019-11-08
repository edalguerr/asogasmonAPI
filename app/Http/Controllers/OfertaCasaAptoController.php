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
            => (
                $request->has('departamento') ? $request->input('departamento') : null
            ),
            'CIUDAD' 
            => ($request->has('ciudad') ? $request->input('ciudad') : null),
            'LOCALIDAD' 
            => (
                $request->has('localidad') ? $request->input('localidad') 
                : null
            ),
            'DIRECCION'  
            => (
                $request->has('direccion') ? $request->input('direccion') 
                : null
            ),
            'CODIGO_POSTAL'  
            => (
                $request->has('codigoPostal') ? $request->input('codigoPostal') : null
            ),
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
