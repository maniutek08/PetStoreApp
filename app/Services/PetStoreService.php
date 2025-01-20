<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class PetStoreService
{
    protected $apiUrl = 'https://petstore.swagger.io/v2';

    // Dodanie zwierzaka.
    public function addPet(array $data): Response
    {
        return Http::post("{$this->apiUrl}/pet", $data);
    }

    // Wysłanie zdjęcia zwierzaka.
    public function uploadPetImage(int $petId, $file): Response
    {

        $image = $file;
        $uploadResponse = Http::attach('file', file_get_contents($image->getRealPath().'sdfdf'), $image->getClientOriginalName())->post("{$this->apiUrl}/pet/".$petId."/uploadImage");

        if($uploadResponse->getStatusCode() != 200) {
            session()->flash('error', 'Nie udało się wgrać zdjęcia: ' . $uploadResponse->body());
        }
        return $uploadResponse;
    }

    // Pobranie listy zwierząt według statusu.
    public function getPetsByStatus(string $status): Response
    {
        return Http::get("{$this->apiUrl}/pet/findByStatus", ['status' => $status]);
    }

    // Pobranie szczegółów zwierzaka po ID.
    public function getPet(int $petId): Response
    {
        return Http::get("{$this->apiUrl}/pet/{$petId}");
    }

    //Aktualizacja zwierzaka.
    public function updatePet(array $data): Response
    {
        return Http::put("{$this->apiUrl}/pet", $data);
    }

    // Usunięcie zwierzaka.
    public function deletePet(int $petId): Response
    {
        return Http::delete("{$this->apiUrl}/pet/{$petId}");
    }

}