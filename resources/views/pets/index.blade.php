@extends('layout')

@section('content')
<div class="container mt-5">

    <!-- Wyświetlanie błędów -->
    @if(Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    @if(Session::has('error'))
        <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <h1 class="mb-4">Lista zwierząt</h1>
    <a href="{{ route('pets.create') }}" class="btn btn-primary mb-3">Dodaj nowe zwierzę</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nazwa</th>
                <th>Status</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pets as $pet)
            <tr>
                <td>{{ $pet['id'] }}</td>
                <td>{{ isset($pet['name']) && $pet['name'] ? $pet['name'] : 'Brak nazwy' }}</td>
                <td>{{ $statuses[$pet['status']] }}</td>
                <td>
                    <a href="{{ route('pets.edit', $pet['id']) }}" class="btn btn-warning btn-sm">Edytuj</a>
                    <form action="{{ route('pets.destroy', $pet['id']) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Usuń</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection