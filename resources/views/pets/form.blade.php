@extends('layout')

@section('content')
<div class="container mt-5">
    <div><a href="{{ route('pets.index') }}">Powrót</a></div>
    <h1 class="mb-4">{{ $pet ? 'Edycja zwierzęcia' : 'Dodaj nowe zwierzę' }}</h1>

    <!-- Wyświetlanie błędów -->
    @if($errors->any())
        <div class="alert alert-danger">
            Formularz zawiera błędy. Prosimy i wypełnienie wymaganych danych.
        </div>
    @endif
    @if(Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    @if(Session::has('error'))
        <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <form action="{{ $pet ? route('pets.update') : route('pets.store') }}" method="POST" enctype="multipart/form-data" class="mb-5">
        @csrf
        @if($pet)
            @method('PUT')
        @endif

        @if($pet)
            <input type="hidden" value="{{ $pet['id'] }}" name="id" />
        @endif

        <!-- Nazwa -->
        <div class="mb-3">
            <label for="name" class="form-label">Nazwa</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ $pet && !old('_token') ? $pet['name'] : old('name')}}">
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

         <!-- Kategoria -->
         <div class="mb-3">
            <label for="category_id" class="form-label">Kategoria</label>
            <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror">
                <option value="">-- Wybierz kategorię --</option>
                @foreach($categories as $categoryId => $categoryName)
                    <option value="{{ $categoryId }}" {{ (isset($pet['category']['id']) && !old('_token') && $pet['category']['id'] == $categoryId) || (old('category_id') == $categoryId) ? 'selected' : '' }}>{{ $categoryName }}</option>
                @endforeach
            </select>
            @error('category_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Galeria zdjęć -->
        @if($pet)
            <div class="row mb-3">
                @foreach($pet['photoUrls'] as $photoUrl)
                    <div class="col-3">
                        @if($photoUrl != 'string')
                            <img class="w-100" src="{{ $photoUrl }}"  />
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Zdjęcie -->
        <div class="mb-3">
            <label for="image" class="form-label">Zdjęcie</label>
            <input type="file" class="form-control" id="image" name="image">
            @error('image')
                <span class="invalid-feedback  @error('image') d-block @enderror " role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <div style="font-size: .85rem" class="mt-2">Uploadować uploaduje, ale nie widzę aby obiekt PET zwracał wgrane zdjęcia.</div>

        </div>

        <!-- Linki do zdjęć -->
        <div class="mb-3">
            <label for="photoUrls" class="form-label">Linki do zdjęć (jeden link na linię)</label>
            <textarea name="photoUrls" id="photoUrls" class="form-control @error('photoUrls') is-invalid @enderror" rows="3" placeholder="Podaj linki do zdjęć, oddzielając je nową linią">{{ $pet && !old('_token') || old('photoUrls') ? trim(implode("\n", $pet['photoUrls'])) : old('photoUrls') }}</textarea>
            @error('photoUrls')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <!-- Tagi -->
        <div class="mb-3">
            <label class="form-label">Tagi</label>
            <div class="form-check">
                @foreach($tags as $tagId => $tagName)
                    <input type="checkbox" name="tags[]" id="tag_{{ $tagId }}" value="{{ $tagId }}" class="form-check-input" {{ ($pet && !old('_token') && array_search($tagId, array_column($pet['tags'], 'id')) !== false ) || (old('tags') && in_array($tagId, old('tags'))) ? 'checked' : '' }}>
                    <label for="tag_{{ $tagId }}" class="form-check-label">
                        {{ $tagName }}
                    </label>
                    <br>
                @endforeach
            </div>
            @error('tags')
                <span class="invalid-feedback  @error('tags') d-block @enderror " role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                @foreach($statuses as $statusId => $statusName)
                    <option value="{{ $statusId }}" {{ (isset($pet['status']) && !old('_token') && $pet['status'] == $statusId) || (old('status') == $statusId) ? 'selected' : '' }}>{{ $statusName }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Zapisz</button>
    </form>
</div>

@endsection
