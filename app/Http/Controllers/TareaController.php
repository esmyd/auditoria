<?php

namespace App\Http\Controllers;

use App\Tarea;
use App\Departamento;
use Illuminate\Http\Request;
use App\Tempgestione;
use App\Evaluacion;
use App\Estado;
use App\Gestion;
use App\Plantilla;
use App\Asterisk;
use App\PreguntaRespuesta;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use DB;
use App\Temporal;
class TareaController extends Controller
{
    
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tarea = $request->get('tarea');
        $departamento = $request->get('departamentos');
        $status = $request->get('status');
    
        $tarea=Tarea::orderBy('id', 'DESC')
        ->tarea($tarea)
        ->departamento($departamento)
        ->status($status)
        ->paginate(5);

        /**
         * Lllamar datos de otro servidor
         */

        //Instancio el modelo de mi base de datos
        $death = new Asterisk;
        //seteo mi conecion, ya con mi peticion del modelo
        $death->setConnection('asterisk');
        //defino el rango de mi consulta 
        $joffrey = $death->find(1);
        //dd($joffrey);
      

        $gestionestem = DB::table('evaluacions')
                     ->select(DB::raw('count(gestions_id) as gestion, tarea_id'))
                     ->groupBy('tarea_id')
                     ->get();
      
      //  dd($gestionestem);
        $cantidad = Tempgestione::where('status', '=', 'on')->count();
       
        


        return view('tareas.index', compact('tarea','gestionestem','cantidad'));
     
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $departamento = Departamento::all();
        $estado = Estado::distinct('status')->get();

        $campana = DB::connection('asterisk')->select("SELECT campaign_id,campaign_name,campaign_description FROM vicidial_campaigns order by campaign_id asc");
      
        //dd($campana);
        $plantilla = Plantilla::all();
        return view('tareas.create',compact('departamento','estado','plantilla','campana'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         //dd($request);
      //recuperar el id del usuario logueado
      $usuario = auth()->id();
      $now=Carbon::now();


      $tarea = new Tarea;
      $tarea->users_id = $usuario;
    
      $tarea->plantillas_id = $request->plantillas_id;
      $tarea->nombre = $request->nombre;
      $tarea->descripcion = $request->descripcion;
      $tarea->departamentos_id = $request->departamentos_id;
      $tarea->estados = $request->estados;
      $tarea->campaign_id = $request->campaign_id;
      $tarea->cantidad_registros = $request->cantidad_registros;
      $tarea->registros_agentes = $request->registros_agentes;
      $tarea->status = $request->status;
      $tarea->fecha = $now->format('Y-m-d');
      $tarea->fechadesde = $request->fechadesde;
      $tarea->fechahasta = $request->fechahasta;
      $tarea->cerrada = 'on';
        
      
      //obtengo los registros que cumplen con el id del departamento y el del id del estatus. 
      $gestionss =   DB::table('gestions')->where('estados', $tarea->estados) 
      ->Where('departamentos_id', $tarea->departamentos_id)
      ->Where('fecha','>=', $tarea->fechadesde)
      ->Where('fecha','<=', $tarea->fechahasta )
       ->get();

        //tengo la cantidad encontrada en la tabla gestiones, segun el flitro suministrado ------
       /* $gestionx =   DB::table('gestions')->where('estados', $tarea->estados) 
        ->Where('departamentos_id', $tarea->departamentos_id)
        ->Where('fecha','>=', $tarea->fechadesde)
        ->Where('fecha','<=', $tarea->fechahasta )->count();  */
      
        /*if($tarea->cantidad_registros <= $gestionx)
        {*/
            $tarea->save();

            $temporal = new Temporal();
            $temporal->gestion_id = 123456;
            $temporal->tarea_id = $tarea->id;
            $temporal->save();
           
               ///validar que solo se guarde una cedula por agente
              
               
                foreach($gestionss as $gestion){
                    $cantidagestion = DB::table('tempgestiones')->where('tareas_id',$tarea->id)->count();//obtengo la cantidad almacenada de gestiones en mi temporal de acuerdo a mi tarea
                
                    if($cantidagestion < $tarea->cantidad_registros){
                      
                    $temgestion = new  Tempgestione;
                    $temgestion->tareas_id = $tarea->id;

                    /**
                     * validar que solo se guarnden la cantidad de registros por agente.
                     *  $agentes = Gestion::select('agente')->Where('estados',$tarea->estados )
                     *  ->Where('id',$gestion->id )
                     *  ->get();
                     * 
                     */
                   

                    $temps = Tempgestione::select('gestions_id')->Where('tareas_id',$tarea->id )->get();


                    $temgestion->gestions_id = $gestion->id;
                    $temgestion->departamentos_id = $request->departamentos_id;
                    $temgestion->plantillas_id = $tarea->plantillas_id;
                    $temgestion->status = 'on';

                        $temgestion->save();
                          /**ingresar id de la gestion para el temporal  */

        
                         
                }
            }
              return redirect()->route('tarea')
                ->with('info', 'Tarea Guardada con Éxito'); 
        /*}else {

            return redirect()->route('tarea.create')
                ->with('info', 'No cuenta con la cantidad De gestiones Solicitadas'); 
        }   */



      /* proceso para almacenar los datos en la tabla temporal del gestiones */
     

    
    }

    



    /**
     * Display the specified resource.
     *
     * @param  \App\Tarea  $tarea
     * @return \Illuminate\Http\Response
     */
    public function show(Tarea $tarea)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tarea  $tarea
     * @return \Illuminate\Http\Response
     */
    public function edit(Tarea $tarea)
    {
        $departamento = Departamento::all();
        $estado = Estado::distinct('status')->get();

        $campana = DB::connection('asterisk')->select("SELECT campaign_id,campaign_name,campaign_description FROM vicidial_campaigns order by campaign_id asc");
      
        //dd($campana);
        $plantilla = Plantilla::all();
        return view('tareas.edit', compact('tarea','departamento','estado','plantilla','campana'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tarea  $tarea
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tarea $tarea)
    {
        $tarea->update($request->all());
        //dd($request);

        return redirect()->route('tarea', $tarea->id)
        ->with('info', 'TAREA ACTUALIZADA');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tarea  $tarea
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tarea $tarea)
    {
        $tarea->delete();
        return redirect()->route('tarea')->with('info', 'Eliminado Correctamente');
    
    }

    public function live()
    {
        $campana = DB::connection('asterisk')
        ->select("SELECT COUNT(a.user) AS cantidad,a.campaign_id,a.extension, b.user_group 
                    FROM vicidial_live_agents AS a, vicidial_users AS b
                    WHERE a.USER=b.USER 
                    GROUP BY a.campaign_id, b.user_group ORDER BY user_group desc ");
      return response()->json($campana);
    
    }
    

}
