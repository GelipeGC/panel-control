@extends('layout')

@section('title', 'Profesiones')

@section('content')
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h1 class="pb-1">Listado de profesiones</h1>
    </div>

    <table class="table">
        <thead class="thead-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Título</th>
            <th scope="col">Perfiles</th>
            <th scope="col">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach($professions as $profession)
            <tr>
                <th scope="row">{{ $profession->id }}</th>
                <td>{{ $profession->title }}</td>
                <td>{{ $profession->profiles_count }}</td>
                <td>
                    <form action="{{ url("profesiones/{$profession->id}") }}" method="POST">
                        @csrf
                        @method('DELETE')
                        @if ($profession->profiles_count == 0)
                            <button type="submit" class="btn btn-link"><span class="oi oi-trash"></span></button>
                        @else
                            <button type="submit" class="btn btn-link" disabled><span class="oi oi-trash"></span></button>
                        @endif
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@section('sidebar')
    @parent
@endsection