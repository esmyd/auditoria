<?php

namespace App\Http\Controllers;
use App\Gestion;
use App\Evaluacion;
use App\Plantilla;
use App\Pregunta;
use App\Respuesta;
use App\User;
use App\Tempgestione;
use App\Tarea;
use App\Vicidial_log;
use App\PreguntaRespuesta;
use App\Recording_log;
use Illuminate\Http\Request;
use lists;
use DB;
use Lang;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
class EvaluacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $agente = $request->get('agente');
        $supervisor = $request->get('supervisor');
        $nombres_cliente = $request->get('nombres_cliente');
      //  dd($request);
        $gestion=Gestion::orderBy('id', 'DESC')
        ->agente($agente)
        ->supervisor($supervisor)
        ->nombres_cliente($nombres_cliente)
        ->paginate(5);
        
        $plantilla = Plantilla::all();
      
        
        //dd($plantilla);
        return view('gestion.index', compact('gestion','plantilla','plantillas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    { 
      
        $plantilla = Plantilla::all(); //relacion entre plantilla y plantilla.

   //dd($plantilla);
        //$pregunta = Pregunta::find($idpreguntas)->respuestas; //relacion entre pregunta y respuesta.


       

        $namegestion = Gestion::findOrFail($id); //traigo los datos del la gestion  seleccionado por el id traido del controlador. de getion. 
        //dd($namegestion->agente);
      
       

        return view('evaluaciones.create', compact('namegestion','plantilla'));
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function procesarcreate($id,$idplantilla)
    { 
 
       

        return view('evaluaciones.create', compact('namegestion','pregunta'));
    }

    public function proce(Request $request,$id,$tarea,$seg)
    { 
      
       $seg=$seg;
        $gestion_id = $id;//id de la gestion
      //dd($gestion_id);
      //$plantillaALL = Tempgestione::where('gestions_id',$gestion_id)->get();///consultar datos de la gestion
      $plantillaALL = Tarea::where('id',$tarea)->get();///consultar datos de la gestion

  
      foreach ($plantillaALL as  $plantilla) {
    
            $plantillas = Plantilla::where('id',$plantilla->plantillas_id)->get();//obtener la plantilla
           
           
        }
        
        foreach ($plantillas as  $plantillaIDS) {//obtener las preguntas segun el id de la plantilla
          
           
            $plantilla_id = $plantillaIDS->id;//id de la plantilla

            $preguntas = Pregunta::where('plantillas_id',$plantilla_id)->get();//traigo las preguntas relacionadas con el id de la plantilla antes seleccionada
      
        }


        foreach ($preguntas as  $preguntasIDS) {//obtener las respuestas segun el id de la pregunta
          
           
            $preguntas_id = $plantillaIDS->id;//id de la plantilla

            $repuestas = Respuesta::where('preguntas_id',$preguntas_id)->get();//obtengo las respuesta de la preguntas
  
        }
  


   /**
    * traer los datos de la grabacion
    */

        $audios = DB::connection('asterisk')->select("SELECT uniqueid FROM vicidial_log WHERE lead_id='$gestion_id' and status='$plantilla->estados'  and length_in_sec='$seg'");
        foreach ($audios as  $audioss) {
            
            $location=$audioss->uniqueid;
            $audiox = DB::connection('asterisk')->select("SELECT location,start_time FROM recording_log WHERE vicidial_id='$location' and lead_id='$gestion_id'");
            
    }
   

      return view('evaluaciones.respuestas',compact('plantilla_id','gestion_id','tarea','preguntas_id','repuestas','preguntas','plantillas','audiox','seg'));
      
   


      
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        /*$validatedData = $request->validate([
            'comentario' => 'required',
        
        ]);*/
        $pregunta_id                = $request->input('pregunta_id');//al igual de la plantilla 
        $respuesta_id               = $request->input('respuesta_id');//guardo lo que me viene del array del select
        $plantilla_id               = $request->input('plantilla_id');//obtengo el id de la plantilla pasado desde el controlador creeate a la vista y mediante la misma la paso aqui con la etiqueta input <input name="plantilla_id" type="hidden" value="{{$plantilla_id}}">
        $gestion_id                 = $request->input('gestion_id');//al igual de la plantilla 
        $tareas_id                  = $request->input('tareas_id');
        $comentario                 = $request->input('comentario');
        $maxima_calificacion        = $request->input('maxima_calificacion');
        
        $seg = $request->input('seg');
        //dd($seg);
         $user = \Auth::user()->id;

       
         $evaluacion = new Evaluacion();
         $evaluacion->users_id = $user;
         $evaluacion->gestions_id = $gestion_id;
         $evaluacion->plantillas_id = $plantilla_id;
         $evaluacion->tarea_id = $tareas_id;
         $evaluacion->status = 1;

         $evaluacion->grabacion =$request->input('file');


/**
 * traer los datos del agente de recorren log
 */

        $plantillaALL = Tarea::where('id',$tareas_id)->get();///consultar datos de la gestion

        
        foreach ($plantillaALL as  $plantilla) {

            $plantillas = Plantilla::where('id',$plantilla->plantillas_id)->get();//obtener la plantilla
            
            
        }
        
        foreach ($plantillas as  $plantillaIDS) {//obtener las preguntas segun el id de la plantilla
            
            
            $plantilla_id = $plantillaIDS->id;//id de la plantilla

            $preguntas = Pregunta::where('plantillas_id',$plantilla_id)->get();//traigo las preguntas relacionadas con el id de la plantilla antes seleccionada

            foreach ($preguntas as  $preguntass) {
                $pregunta = $preguntass->pregunta;
                $descripcion = $preguntass->descripcion;
                $peso=$preguntass->peso;
                $tipo=$preguntass->tipo;
    
               }
        }

                    $respuestadetalle = Respuesta::where('preguntas_id',$pregunta_id)->get();//detalle respuestas
           // dd($respuestadetalle);
            foreach ($respuestadetalle as  $respuestadetalles) {
                $respuestax = $respuestadetalles->respuesta;
               // $comentario = $respuestadetalles->comentario;
                $valor_1=$respuestadetalles->valor_1;
                

            }

            /*** conseguir los datos del telefono */
                    $log = DB::connection('asterisk')->select("SELECT * 
                                                                    FROM vicidial_log 
                                                                        WHERE lead_id='$gestion_id' 
                                                                        and status='$plantilla->estados'  
                                                                        and length_in_sec='$seg'");

            //dd($log);

                    /*** recorrer y almacenar en varibles */                                                    
                    foreach ($log as  $logs) {
                        $telefono = $logs->phone_number;
                        $grupo = $logs->user_group;
                        $estado=$logs->status;
                        $location=$logs->uniqueid;

                    }

            /*** conseguir los datos del cliente */
                        $list = DB::connection('asterisk')->select("SELECT * 
                                                                        FROM vicidial_list
                                                                            WHERE lead_id='$gestion_id'"); 
            
            foreach ($list as  $lists) {

                $cedula = $lists->first_name;
                $nombre = $lists->last_name;
                $address1 = $lists->address1;
                $address2 = $lists->address2;
                $address3 = $lists->address3;
                $city = $lists->city;
                $province = $lists->province;
            }



            
             /*** buscar el agente que gestiono esa llamada */
             $userx = DB::connection('asterisk')->select("SELECT user
                                                            FROM recording_log 
                                                                WHERE vicidial_id='$location' 
                                                                and lead_id='$gestion_id'");
            /**recorrer para almacenar en una variable el agente que gestiono la llamda */  
                foreach ($userx as  $userxs) {
            
            $agente =  $userxs->user;
                }
            /*** guaramos en la bd el agente gestionado */
            $evaluacion->agente =   $agente;
/************************************************************************************************************************** */
                
            $evaluacion->save();




for ($i=0;$i<count($respuesta_id);$i++) //recorro el array que me viene del select de respuesta
            {  
                
                for ($i=0;$i<count($pregunta_id);$i++)   //recorro el array que me viene del select de pregunta 
                {     
                    for ($i=0;$i<count($comentario);$i++)   //recorro el array que me viene del select de pregunta 
                    {
                        $preguntarespuesta = new PreguntaRespuesta();//instancio la propiedad para almacenar los datos
                        
                        $preguntarespuesta->preguntas_id    =   $pregunta_id[$i];
                        $preguntarespuesta->respuestas_id   =   $respuesta_id[$i];    
                        $preguntarespuesta->evaluacions_id  =   $evaluacion->id;
                        $preguntarespuesta->comentario = $comentario[$i];
                        $valor = Pregunta::where('id',$pregunta_id[$i])->get();

                            /**
                            * si la respuesta viene en null no aplico el guardado con la operacion arismetica
                            */
                        if ($respuesta_id[$i]) 
                        {
                                foreach ( $valor  as   $valors ) 
                                {
                            
                                     $preguntarespuesta->calificacion    =  $valors->peso;
                                }
                        }
                        $preguntarespuesta->agente      =   $agente;
                        $preguntarespuesta->telefono    =   $telefono;
                        $preguntarespuesta->grupo       =   $grupo;
                        $preguntarespuesta->estado      =   $estado;
                        $preguntarespuesta->seg         =   $seg;

                        /**detalle del cliente del predictivo */
                        $preguntarespuesta->cedula      =   $cedula;
                        $preguntarespuesta->nombre      =   $nombre;
                        $preguntarespuesta->address1    =   $address1;
                        $preguntarespuesta->address2    =   $address2;
                        $preguntarespuesta->city        =   $city;
                        $preguntarespuesta->province    =   $province;
                        $preguntarespuesta->grabacion    =   $evaluacion->grabacion;

                        /**detalle de las preguntas */
                        $preguntarespuesta->pregunta    =   $pregunta;
                        $preguntarespuesta->descripcion  =   $descripcion;
                        $preguntarespuesta->peso      =   $peso;
                        $preguntarespuesta->tipo         =   $tipo;

                        /**detalle de las respuestas de esas preguntas  */
                        $preguntarespuesta->respuesta  =   $respuestax;
                      //  $preguntarespuesta->comentario      =   $comentario;
                        $preguntarespuesta->valor_1         =   $valor_1;
                       // dd($preguntarespuesta);
                        $preguntarespuesta->save();
                    }
                    
                }
            } 

           /** 
            * guardar el calculo de la de las respuestas seleccionadas en la columna calificacion de la tabla evaluacion
           */
            $calificacion = DB::table('pregunts_respuests')->where('evaluacions_id',$evaluacion->id)->sum('calificacion');
            
            DB::table('evaluacions')
                ->where('id', $evaluacion->id)
                ->update(['calificacion' => $maxima_calificacion  - $calificacion]);
            
            /**
             * cambiar estatus de la tabla temgestiones por el registro gestionado.
             */
           
            $gestiontm=Tempgestione::where('gestions_id',$evaluacion->gestions_id)
                                    ->where('tareas_id',$evaluacion->tarea_id)
                                    ->update(['status' => 'of']);


            /**
             * Cambiar el estatus de la tarea siempre y cuando se cumplan los registros solicitados para gestionar en dicha tarea
             */


            $canTemp    =   Tempgestione::Where('status','of')->Where('tareas_id',$evaluacion->tarea_id)->count();  //consulto la cantidad de registros q tengo con el estatus of
      
            $canTarea   =   Tarea::select('cantidad_registros')->Where('id',$evaluacion->tarea_id)->get();//consulto la cantidad de registro que fueron ingresado al crear la tarea
       
            foreach ($canTarea  as  $canTareax ) {//realizo el bucle para obtener el campo solicitado
                $cantidadregistros = $canTareax->cantidad_registros;

                if($cantidadregistros == $canTemp ){//valido si la cantidad de registros que esta actualmente en mi tarea es igual a la gestiones cerradas en mi tabla temporal
                    $gestiontm=Tarea::where('id',$evaluacion->tarea_id)
                                    ->update(['cerrada' => 'of']);//si se cumple realizo un update a mi campo cerrada de la tabla tareas.
                }


            }

            $cerrada   =   Tarea::select('cerrada')->Where('id',$evaluacion->tarea_id)->get();//consulto el estatus de la tarea si esta cerrada
           
            foreach ($cerrada  as  $cerradax ) {//realizo el bucle para obtener el campo solicitado
                $cerrada = $cerradax->cerrada;

                if($cerrada == 'of' ){//valido si la el campo cerrada esta en estatus of o no.
                    return redirect()->route('tarea')->with([ 
                        'info' => 'Tarea Culminada'
                    ]);//direcciono al usuario al index de tarea
                }


            }//si no se cumple solo lo dejo gestionar 

            return redirect()->route('temp.index', $evaluacion->tarea_id)->with([
                'info' => 'La Gestion fue guardada Correctamente!!'
            ]);
    }
   

   

    /**
     * Display the specified resource.
     *
     * @param  \App\Evaluacion  $evaluacion
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

      
        $gestiontm=Evaluacion::orderBy('id', 'DESC')->where('tarea_id',$id)->get();
     
     
       //  dd($gestiontm);
        return view('evaluaciones.index', compact('gestiontm'));

    }

        /**
     * Display the specified resource.
     *
     * @param  \App\Evaluacion  $evaluacion
     * @return \Illuminate\Http\Response
     */
    public function detalle($id)
    {

      
        $gestiontm=PreguntaRespuesta::where('evaluacions_id',$id)->get();
        $evaluacion=Evaluacion::where('id',$id)->get();
     
        // dd($evaluacion);
        return view('evaluaciones.detalle', compact('gestiontm','evaluacion'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Evaluacion  $evaluacion
     * @return \Illuminate\Http\Response
     */
    public function edit(Evaluacion $evaluacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Evaluacion  $evaluacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Evaluacion $evaluacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Evaluacion  $evaluacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Evaluacion $evaluacion)
    {
        //
    }


    
}
