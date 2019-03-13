<!----->
@extends('layouts.home')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Seleccione Un Modulo</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @can('plantillas.index')
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <a  href="{{ url('/plantillas') }}" ><img class="card-img-top" src="http://192.168.1.107/auditoria/public/gestiones.png" width="100px" height="100px" alt="Card image cap"></a>
                                    <h5 class="card-title"> <b> Gestiones</b>  </h5><br>
                                </div>
                            </div>
                        </div>
                        @endcan
                       
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <a  href="{{ url('/plantillas') }}" ><img class="card-img-top" src="http://192.168.1.107/auditoria/public/indicadores.png" width="100px" height="100px" alt="Card image cap"></a>
                                            <h5 class="card-title"> <b> Indicadores</b>  </h5><br>
                                </div>
                            </div>
                        </div>
                    </div>
               
                    @can('users.index')
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <a  href="{{ url('/users') }}" ><img class="card-img-top" src="http://192.168.1.107/auditoria/public/users.jpg" width="100px" height="100px" alt="Card image cap"></a>
                                    <h5 class="card-title"> <b> Usuarios</b>  </h5>
                                </div>
                            </div>
                        </div>
                        @endcan
                       
                    </div>  
                </div>
            </div>
        </div>
</div>
@endsection