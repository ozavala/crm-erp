@extends('layouts.app')

@section('title', 'Configuraciones de IVA')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Configuraciones de IVA</h1>
        <p class="text-gray-600">Gestiona las tasas de IVA por país y configuraciones de servicios</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Configuración de País por Defecto -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">País por Defecto</h2>
        <form action="{{ route('tax-settings.default-country') }}" method="POST" class="flex items-center gap-4">
            @csrf
            <select name="country_code" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach($countries as $code => $name)
                    <option value="{{ $code }}" {{ $code === $defaultCountry ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                Establecer País por Defecto
            </button>
        </form>
    </div>

    <!-- Configuraciones de Servicios -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Configuraciones de Servicios</h2>
        <form action="{{ route('tax-settings.service-settings') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" name="tax_includes_services" id="tax_includes_services" 
                           value="1" {{ $serviceSettings['tax_includes_services'] === 'true' ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="tax_includes_services" class="ml-2 text-gray-700">
                        Los servicios pagan IVA
                    </label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="tax_includes_transport" id="tax_includes_transport" 
                           value="1" {{ $serviceSettings['tax_includes_transport'] === 'true' ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="tax_includes_transport" class="ml-2 text-gray-700">
                        El transporte público paga IVA
                    </label>
                </div>
            </div>
            <button type="submit" class="mt-4 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                Guardar Configuraciones
            </button>
        </form>
    </div>

    <!-- Tasas de IVA por País -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @foreach($taxSettings as $countryCode => $settings)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $settings['name'] }}</h3>
                    @if($settings['is_default'])
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                            Por Defecto
                        </span>
                    @endif
                </div>

                <form action="{{ route('tax-settings.update-rates', $countryCode) }}" method="POST" class="space-y-4">
                    @csrf
                    <div id="rates-{{ $countryCode }}" class="space-y-3">
                        @foreach($settings['rates'] as $index => $rate)
                            <div class="flex gap-2 items-center">
                                <input type="text" name="rates[{{ $index }}][name]" 
                                       value="{{ $rate['name'] }}" placeholder="Nombre"
                                       class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <input type="number" name="rates[{{ $index }}][rate]" 
                                       value="{{ $rate['rate'] }}" placeholder="%" step="0.01" min="0" max="100"
                                       class="w-20 border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <input type="text" name="rates[{{ $index }}][description]" 
                                       value="{{ $rate['description'] ?? '' }}" placeholder="Descripción"
                                       class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <button type="button" onclick="removeRate(this)" 
                                        class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                    
                    <button type="button" onclick="addRate('{{ $countryCode }}')" 
                            class="text-blue-600 hover:text-blue-800 text-sm">
                        + Agregar Tasa
                    </button>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Guardar Tasas
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</div>

<script>
function addRate(countryCode) {
    const container = document.getElementById(`rates-${countryCode}`);
    const index = container.children.length;
    
    const rateDiv = document.createElement('div');
    rateDiv.className = 'flex gap-2 items-center';
    rateDiv.innerHTML = `
        <input type="text" name="rates[${index}][name]" placeholder="Nombre"
               class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm">
        <input type="number" name="rates[${index}][rate]" placeholder="%" step="0.01" min="0" max="100"
               class="w-20 border border-gray-300 rounded-md px-3 py-2 text-sm">
        <input type="text" name="rates[${index}][description]" placeholder="Descripción"
               class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm">
        <button type="button" onclick="removeRate(this)" class="text-red-600 hover:text-red-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    container.appendChild(rateDiv);
}

function removeRate(button) {
    button.closest('div').remove();
}
</script>
@endsection 