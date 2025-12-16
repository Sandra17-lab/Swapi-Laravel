<div class="bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Buscador SWAPI</h2>

    <div class="flex gap-3 mb-4">
        <select wire:model="category" class="border rounded px-3 py-2">
            @foreach($resources as $key => $url)
                <option value="{{ $key }}">{{ ucfirst($key) }}</option>
            @endforeach
        </select>

        <input
            type="text"
            wire:model.debounce.500ms="query"
            placeholder="Buscar... (mínimo 2 caracteres)"
            class="flex-1 border rounded px-3 py-2"
        />

        <button
            wire:click="search"
            class="bg-blue-600 text-black px-4 py-2 rounded hover:bg-blue-700"
            type="button"
        >
            Buscar
        </button>

        {{-- indicador de carga --}}
        <div wire:loading class="flex items-center px-3">
            <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity="0.25"/></svg>
            
        </div>
    </div>

    {{-- Resultados --}}
    <div class="mt-4">
        @if(empty($resultados))
            <p class="text-gray-500">No hay resultados.</p>
        @else
            <div class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                @foreach($resultados as $item)
                    @php $p = $item['properties'] ?? $item; @endphp
                    <div class="border rounded p-4 bg-gray-50">
                        {{-- Título / nombre --}}
                        <h3 class="text-lg font-bold mb-2">
                            {{ $p['name'] ?? $p['title'] ?? 'Sin nombre' }}
                        </h3>

                        {{-- Campos por categoría --}}
                        @if($category === 'people')
                            <p><strong>Birth year:</strong> {{ $p['birth_year'] ?? 'N/A' }}</p>
                            <p><strong>Gender:</strong> {{ $p['gender'] ?? 'N/A' }}</p>
                            <p><strong>Height:</strong> {{ $p['height'] ?? 'N/A' }}</p>
                            <p><strong>Mass:</strong> {{ $p['mass'] ?? 'N/A' }}</p>
                        @elseif($category === 'films')
                            <p><strong>Director:</strong> {{ $p['director'] ?? 'N/A' }}</p>
                            <p><strong>Producer:</strong> {{ $p['producer'] ?? 'N/A' }}</p>
                            <p><strong>Release:</strong> {{ $p['release_date'] ?? 'N/A' }}</p>
                            <p class="mt-2 italic text-sm">{{ \Illuminate\Support\Str::limit($p['opening_crawl'] ?? '', 200) }}</p>
                        @elseif(in_array($category, ['starships','vehicles']))
                            <p><strong>Model:</strong> {{ $p['model'] ?? 'N/A' }}</p>
                            <p><strong>Manufacturer:</strong> {{ $p['manufacturer'] ?? 'N/A' }}</p>
                            <p><strong>Crew:</strong> {{ $p['crew'] ?? 'N/A' }}</p>
                            <p><strong>Passengers:</strong> {{ $p['passengers'] ?? 'N/A' }}</p>
                        @elseif($category === 'species')
                            <p><strong>Classification:</strong> {{ $p['classification'] ?? 'N/A' }}</p>
                            <p><strong>Average height:</strong> {{ $p['average_height'] ?? 'N/A' }}</p>
                            <p><strong>Language:</strong> {{ $p['language'] ?? 'N/A' }}</p>
                        @elseif($category === 'planets')
                            <p><strong>Climate:</strong> {{ $p['climate'] ?? 'N/A' }}</p>
                            <p><strong>Terrain:</strong> {{ $p['terrain'] ?? 'N/A' }}</p>
                            <p><strong>Population:</strong> {{ $p['population'] ?? 'N/A' }}</p>
                        @endif

                        {{-- Lists (mostrar URLs o cantidad) --}}
                        <div class="mt-3 text-sm text-gray-700">
                            @foreach (['films','starships','vehicles','people','residents','characters','planets','species','pilots'] as $arr)
                                @if(!empty($p[$arr]))
                                    <p><strong>{{ ucfirst($arr) }}:</strong> {{ is_array($p[$arr]) ? count($p[$arr]).' item(s)' : $p[$arr] }}</p>
                                    <p>Conectado a: https://www.swapi.tech</p>

                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
