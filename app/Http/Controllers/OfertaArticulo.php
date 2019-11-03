<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\OfertaArticulo as OfertaArticuloModel;
use App\models\SubCategoria;
use App\models\Usuario;
use App\models\FotoArticulo;
use App\models\UbicacionOfertaArticulo;

use Image;

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
            'DEPARTAMENTO' => $request->input('departamento'),
            'CIUDAD' => $request->input('ciudad')
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

            //POR HACER:
            //ALMACENAR NOMBRE DE LAS FOTOS EN LA BASE DE DATOS
            FotoArticulo::create([
                'FOTO' => $temp_name,
                'OFERTA_ARTICULO_ID' => $id_articulo
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
}
