@extends('layouts.app')

@section('title', 'Configuraciones de IVA')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Sale Taxes Settings</h1>
        <p class="text-muted">Manage VAT rates by country and service configurations</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card-group">
        <div class="col-sm-6 mb-3 mb-sm-0">    
        <!-- Configuración de País por Defecto -->
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <span class="fw-semibold">Default Country</span>
                    <span class="ms-2 text-muted" data-bs-toggle="tooltip" title="El país seleccionado será el predeterminado para facturación.">
                        <i class="bi bi-info-circle"></i>
                    </span>
                </div>
                <div class="card-body 6">
                <form action="{{ route('tax-settings.default-country') }}" method="POST" class="row g-2 align-items-center">
                    @csrf
                    <div class="col-auto flex-grow-1">
                        <select name="country_code" class="form-select">
                            @foreach($countries as $code => $name)
                                <option value="{{ $code }}" {{ $code === $defaultCountry ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            Set Default Country
                        </button>
                    </div>
                </form>
                </div>
            </div>
            <!-- Configuraciones de Servicios -->
            <div class="card" >
                <div class="card-header d-flex align-items-center">
                    <span class="fw-semibold">Service Configurations</span>
                    <span class="ms-2 text-muted" data-bs-toggle="tooltip" title="Define si los servicios y el transporte público están sujetos a IVA.">
                        <i class="bi bi-info-circle"></i>
                    </span>
                </div>
                <div class="card-body">
                    <form action="{{ route('tax-settings.service-settings') }}" method="POST">
                        @csrf
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="tax_includes_services" id="tax_includes_services" value="1" {{ $serviceSettings['tax_includes_services'] === 'true' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tax_includes_services" data-bs-toggle="tooltip" title="Si está marcado, los servicios pagarán IVA.">
                                Services pay VAT
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="tax_includes_transport" id="tax_includes_transport" value="0" {{ $serviceSettings['tax_includes_transport'] === 'true' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tax_includes_transport" data-bs-toggle="tooltip" title="Si está marcado, el transporte público pagará IVA.">
                                Transport services pay VAT
                            </label>
                        </div>
                        <button type="submit" class="btn btn-success">
                            Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    
    <!-- Tasas de IVA por País -->
    
        @foreach($taxSettings as $countryCode => $settings)
            <div class="col-6 col-md-6 col-lg-12">
                <div class="card h-100 position-relative">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span class="fw-semibold">{{ $settings['name'] }} <span class="badge bg-light text-secondary ms-1" data-bs-toggle="tooltip" title="Código de país">{{ $countryCode }}</span></span>
                        @if($settings['is_default'])
                            <span class="badge bg-primary">By Default</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tax-settings.update-rates', $countryCode) }}" method="POST" onsubmit="return validateRates('{{ $countryCode }}')">
                            @csrf
                            <div id="rates-{{ $countryCode }}">
                                @foreach($settings['rates'] as $index => $rate)
                                    <div class="row g-2 mb-2 rate-row">
                                        <div class="col-3">
                                            <input type="text" name="rates[{{ $index }}][name]" value="{{ $rate['name'] }}" placeholder="Nombre" required class="form-control form-control-sm">
                                        </div>
                                        <div class="col-1">
                                            <input type="number" name="rates[{{ $index }}][rate]" value="{{ $rate['rate'] }}" placeholder="%" step="0.01" min="0" max="100" required class="form-control form-control-sm">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="rates[{{ $index }}][description]" value="{{ $rate['description'] ?? '' }}" placeholder="Descripción" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-1 text-end">
                                            <button type="button" class="btn btn-outline-danger btn-sm p-0" onclick="removeRate(this)" title="Eliminar tasa">
                                                <i class="bi bi-x-circle"></i>Remove
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex mt-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addRate('{{ $countryCode }}')" title="Agregar nueva tasa">
                                    <i class="bi bi-plus-circle"></i> Add Tax
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    Save Taxes
                                </button>
                            
                        </form>
                        <button type="button" onclick="restoreDefaultRates('{{ $countryCode }}')" class="btn btn-warning btn-sm " title="Restaurar tasas por defecto (no guarda automáticamente)">
                            <i class="bi bi-arrow-clockwise"></i> Restores Default Rates
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    
</div>

<script>
function addRate(countryCode) {
    const container = document.getElementById(`rates-${countryCode}`);
    const index = container.querySelectorAll('.rate-row').length;
    const row = document.createElement('div');
    row.className = 'row g-2 align-items-center mb-2 rate-row';
    row.innerHTML = `
        <div class="col-5">
            <input type="text" name="rates[${index}][name]" placeholder="Nombre" required class="form-control form-control-sm">
        </div>
        <div class="col-3">
            <input type="number" name="rates[${index}][rate]" placeholder="%" step="0.01" min="0" max="100" required class="form-control form-control-sm">
        </div>
        <div class="col-3">
            <input type="text" name="rates[${index}][description]" placeholder="Descripción" class="form-control form-control-sm">
        </div>
        <div class="col-1 text-end">
            <button type="button" class="btn btn-outline-danger btn-sm p-0" onclick="removeRate(this)" title="Eliminar tasa">
                <i class="bi bi-x-circle"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
}

function removeRate(button) {
    button.closest('.rate-row').remove();
}

function validateRates(countryCode) {
    const container = document.getElementById(`rates-${countryCode}`);
    let valid = true;
    container.querySelectorAll('input[required]').forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            valid = false;
        } else {
            input.classList.remove('is-invalid');
        }
        if (input.type === 'number' && input.value) {
            const val = parseFloat(input.value);
            if (val < 0 || val > 100) {
                input.classList.add('is-invalid');
                valid = false;
            }
        }
    });
    if (!valid) {
        alert('Por favor, completa correctamente todos los campos requeridos.');
    }
    return valid;
}

function restoreDefaultRates(countryCode) {
    if (!confirm('¿Estás seguro de que deseas restaurar las tasas por defecto para este país? Se perderán los cambios no guardados.')) return;
    fetch(`/tax-settings/${countryCode}/restore-defaults`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Tasas restauradas correctamente.');
            location.reload();
        } else {
            alert(data.message || 'No se pudieron restaurar las tasas.');
        }
    })
    .catch(() => alert('Error al restaurar las tasas por defecto.'));
}

document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection 