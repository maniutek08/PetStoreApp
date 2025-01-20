<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetRequest;
use App\Services\PetStoreService;

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

    protected $petStoreService;

    public function __construct(PetStoreService $petStoreService)
    {
        $this->petStoreService = $petStoreService;
    }


    // Lista zwierzaków
    public function index()
    {
        $response = $this->petStoreService->getPetsByStatus('available, pending, sold');
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
            $response = $this->petStoreService->getPet($petId);
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

            $data = [
                'id' => 0,
                'name' => $request->name,
                'category' => [
                    'id' => $request->category_id,
                    'name' => $this->categories[$request->category_id]
                ],
                'photoUrls' => explode("\n", trim($request->photoUrls)),
                'tags' => $tags,
                'status' => $request->status,
            ];

            $response = $this->petStoreService->addPet($data);

            // Jeśli dodawanie informacji o zwierzaku się powiodło to wtedy wysyłamy zdjęcie
            if($response->getStatusCode() == 200) {

                // Jeśli przesłano obraz, wysyłamy go osobno
                if($request->image) {
                    $this->petStoreService->uploadPetImage($response->json()['id'], $request->image);
                }

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

            $data = [
                'id' => $request->id,
                'name' => $request->name,
                'category' => [
                    'id' => $request->category_id,
                    'name' => $this->categories[$request->category_id]
                ],
                'photoUrls' => explode("\n", trim($request->photoUrls)),
                'tags' => $tags,
                'status' => $request->status,
            ];

            $response = $this->petStoreService->updatePet($data);


            // Jeśli update informacji o zwierzaku się powiodło to wtedy wysyłamy zdjęcie
            if($response->getStatusCode() == 200) {

                // Jeśli przesłano obraz, wysyłamy go osobno
                if($request->image) {
                    $this->petStoreService->uploadPetImage($response->json()['id'], $request->image);
                }

                session()->flash('success', 'Zwierzak został dodany');
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
            $response = $this->petStoreService->deletePet($id);

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
}
