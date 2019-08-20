@extends('layout')

@section('title', 'Usuarios')

@section('content')
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h1 class="pb-1">
            {{ trans("users.title.{$view}")}}
        </h1>
        <p>
            @if ($view == 'index')
                <a href="{{ route('users.trashed')}}" class="btn btn-outline-dark">Ver Papelera</a>
                <a href="{{ route('users.create') }}" class="btn btn-dark">Nuevo usuario</a>
            @elseif($view == 'trash') 
                <a href="{{ route('users.index')}}" class="btn btn-outline-dark">Regresar al listado de usuarios</a>

            @endif
        </p>
    </div>
    @includeWhen($view == 'index','users._filters')

    @if ($users->isNotEmpty())


    <div class="table-responsive-lg">
        <table class="table table-sm">
            <thead class="thead-dark">
                <tr>
                    <th scope="col"># <span class="oi oi-caret-bottom"></span><span class="oi oi-caret-top"></span></th>
                <th scope="col"><a href="{{ $sortable->url('name') }}" class="{{ $sortable->classes('name') }}">Nombre</a></th>
                    <th scope="col"><a href="{{ $sortable->url('email') }}" class="{{ $sortable->classes('email') }}">Correo</a></th>
                    <th scope="col"><a href="{{ $sortable->url('date') }}" class="{{ $sortable->classes('date') }}">Registrado el</a></th>
                    <th scope="col" class="text-right th-actions">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @each('users._row', $users, 'user')
            </tbody>
        </table>

        {{ $users->links() }}
    </div>
    @else
        <p>No hay usuarios registrados.</p>
    @endif
@endsection

@section('sidebar')
    @parent
@endsection