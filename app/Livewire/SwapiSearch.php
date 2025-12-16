<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class SwapiSearch extends Component
{
    public $category = 'people';
    public $query = '';
    public $resultados = [];
    public $resources = [];
    public $loading = false;

    public function mount()
    {
        // Endpoints base (swapi.tech)
        $this->resources = [
            'people'   => 'https://www.swapi.tech/api/people',
            'films'    => 'https://www.swapi.tech/api/films',
            'planets'  => 'https://www.swapi.tech/api/planets',
            'species'  => 'https://www.swapi.tech/api/species',
            'starships'=> 'https://www.swapi.tech/api/starships',
            'vehicles' => 'https://www.swapi.tech/api/vehicles',
        ];
    }

    // Si quieres búsqueda automática mientras escribes:
    public function updatedQuery()
    {
        // evita llamadas para textos muy cortos
        if (strlen(trim($this->query)) < 2) {
            $this->resultados = [];
            return;
        }
        $this->search();
    }

    public function updatedCategory()
    {
        // limpiar resultados al cambiar categoría
        $this->resultados = [];
        if (trim($this->query) !== '') {
            $this->search();
        }
    }

    // Método público que llama la vista (botón usa wire:click="search")
    public function search()
    {
        $this->resultados = [];
        $q = trim($this->query);

        if ($q === '') {
            return;
        }

        $this->loading = true;

        try {
            // 1) Obtener lista completa de la categoría (la API no filtra por query en URL)
            $resp = Http::withOptions(['verify' => false])
                        ->get($this->resources[$this->category]);

            // swapi.tech puede usar "result" o "results"
            $items = $resp->json('result') ?? $resp->json('results') ?? [];

            // 2) Filtrar localmente (name o title)
            $matches = [];
            foreach ($items as $it) {
                $name = $it['name'] ?? $it['title'] ?? '';
                if ($name !== '' && stripos($name, $q) !== false) {
                    $matches[] = $it;
                }
            }

            // 3) Por cada match, pedir detalles completos (url -> /:id/)
            $detailed = [];
            foreach ($matches as $m) {
                // asegúrate de que exista 'url'
                if (!empty($m['url'])) {
                    $detailResp = Http::withOptions(['verify' => false])
                                      ->get($m['url']);
                    $detail = $detailResp->json('result') ?? $detailResp->json();
                    if ($detail) {
                        $detailed[] = $detail;
                    }
                }
            }

            $this->resultados = $detailed;

        } catch (\Throwable $e) {
            // opcional: puedes loguearlo
            $this->resultados = [];
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        return view('livewire.swapi-search');
    }
}