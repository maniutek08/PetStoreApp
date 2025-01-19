<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'photoUrls' => 'required|string',
            'tags' => 'required|array',
            'status' => 'required|in:available,pending,sold',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Pole "Nazwa" jest wymagane',
            'name.max' => 'Pole "Nazwa" nie może przekraczać 255 znaków',
            'category_id.required' => 'Pole "Kategoria" jest wymagane',
            'image.nullable' => 'Plik nie może być pusty',
            'image.image' => 'Plik musi być obrazkiem',
            'image.mimes' => 'Zdjęcie jest niepoprawnego formatu',
            'photoUrls.required' => 'Pole "Linki do zdjęć" jest wymagane. Podaj przynajmniej jeden URL obrazka',
            'tags.required' => 'Wybierz przynajmniej jeden tag',
            'status.required' => 'Pole "Status" jest wymagane',
            'status.in' => 'Pole "Status" powinno być jednym z: dostępne, oczekujące, sprzedane.',
        ];
    }
}
