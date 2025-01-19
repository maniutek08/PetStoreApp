<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\PetRequest;

class PetController extends Controller
{
    private $apiUrl = 'https://petstore.swagger.io/v2';

    private $statuses = [
        'available' => 'Dostępny',
        'pending' => 'Oczekujące',
        'sold' => 'Sprzedane'
    ];

    private $categories = [
        1 => 'Psy',
        2 => 'Koty',
        3 => 'Ptaki',
        4 => 'Ryby',
        5 => 'Gady'
    ];

    private $tags = [
        1 => 'Szczepione',
        2 => 'Do adopcji',
        3 => 'Małe',
        4 => 'Duże'
    ];


    // Lista zwierzaków
    public function index()
    {
        $response = Http::get("{$this->apiUrl}/pet/findByStatus", [
            'status' => 'available, pending, sold'
        ]);
        $pets = $response->json();

        return view('pets.index', [
            'pets' => $pets,
            'statuses' => $this->statuses
        ]);
    }

    // Formularz dodawania edycji zwierzaka
    public function showForm($petId = null)
    {
        if($petId) {
            $response = Http::get("{$this->apiUrl}/pet/{$petId}");
            $pet = $response->json();

        }

        return view('pets.form', [
            'categories' => $this->categories,
            'tags' => $this->tags,
            'statuses' => $this->statuses,
            'pet' => isset($pet) ? $pet : array(),
        ]);
    }

    //Dodawanie nowego zwierzaka
    public function store(PetRequest $request)
    {
        $validatedData = $request->validated();

        $tags = $request->has('tags') ? $this->mapTags($request->tags) : [];

        try {

            $response = Http::post("{$this->apiUrl}/pet", [
                'id' => 558,
                'name' => $request->name,
                'category' => [
                    'id' => $request->category_id,
                    'name' => $this->categories[$request->category_id]
                ],
                'photoUrls' => explode("\n", trim($request->photoUrls)),
                'tags' => $tags,
                'status' => $request->status,
            ]);


            if($response->getStatusCode() == 200) {

                // Jeśli przesłano obraz, wysyłamy go osobno
                $this->uploadImage($request, $response->json()['id']);

                session()->flash('success', 'Zwierzak został dodany');
            }
            else session()->flash('error', 'Nie udało się dodać zwierzaka: ' . $response->body());

        } catch (\Exception $e) {
            session()->flash('error', 'Wystąpił problem podczas dodawania zwierzaka: ' . $e->getMessage());
        }

        return redirect()->route('pets.index');
    }

    //Edycjazwierzaka
    public function update(PetRequest $request)
    {
        $validatedData = $request->validated();

        $tags = $request->has('tags') ? $this->mapTags($request->tags) : [];

        try {

            $response = Http::post("{$this->apiUrl}/pet", [
                'id' => $request->id,
                'name' => $request->name,
                'category' => [
                    'id' => $request->category_id,
                    'name' => $this->categories[$request->category_id]
                ],
                'photoUrls' => explode("\n", trim($request->photoUrls)),
                'tags' => $tags,
                'status' => $request->status,
            ]);

            if($response->getStatusCode() == 200) {

                // Jeśli przesłano obraz, wysyłamy go osobno
                $this->uploadImage($request, $request->id);

                session()->flash('success', 'Zmiany zostały zapisane');
            }
            else session()->flash('error', 'Nie udało się zapisać zmian: ' . $response->body());

        } catch (\Exception $e) {
            session()->flash('error', 'Wystapił problem podczas edycji zwierzaka: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    // Usuwanie zwierzaka
    public function destroy($id)
    {
        try {
            $response = Http::delete("{$this->apiUrl}/pet/{$id}");

            if($response->getStatusCode() == 200) session()->flash('success', 'Zwierzak został usunięty');
            else session()->flash('error', 'Nie udało się usunąć zwierzaka' . $response -> body());
        } catch (\Exception $e) {
            session()->flash('error', 'Wystąpił problem podczas usuwania zwierzaka: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    // Metoda pomocnica do generowania tablicy tagów
    private function mapTags(array $tagsIds)
    {
        $tags = [];
        foreach($tagsIds as $tagId) {
            if (isset($this->tags[$tagId])) {
                $tags[] = [
                    'id' => (int) $tagId,
                    'name' => $this->tags[$tagId],
                ];
            }
        }

        return $tags;
    }

    // Metosa pomocnicza do przesyłania obrazka
    private function uploadImage(Request $request, $petId)
    {
        if($request->image) {
            $image = $request->file('image');
            $uploadResponse = Http::attach('file', file_get_contents($image->getRealPath()), $image->getClientOriginalName())->post("{$this->apiUrl}/pet/".$petId."/uploadImage");

            if($uploadResponse->getStatusCode() != 200) {
                session()->flash('error', 'Nie udało się wgrać zdjęcia: ' . $uploadResponse->body());
            }
        }
    }
}
