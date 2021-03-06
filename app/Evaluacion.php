<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    protected $table = 'evaluacions';

    
    protected $fillable = ['users_id', 'gestions_id','plantillas_id','respuesta_id','tarea_id' ,'hora','fecha','grabacion', 'comentario', 'cantidad_evaluar','calificacion','agente','vicidial_id',];
   

  
    /**
     * Get the evaluacion that owns the gestion.
     */
    public function gestion()
    {
        return $this->belongsTo('App\Gestion');
    }

    /**
     * Get the plantilla that owns the users.
     */
    public function users()
    {
        return $this->belongsTo('App\User','users_id');
    }

    public function plantillas()
    {
        return $this->belongsTo('App\Plantilla','plantillas_id');
    }

    public function tarea()
    {
        return $this->belongsTo('App\Tarea','tarea_id');
    }


 
    

    public function audiox()
    {
        return $this->belongsTo('App\Recording_log','lead_id');
    }


  
}
