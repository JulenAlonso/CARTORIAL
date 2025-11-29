// VEHICLE_DATA se cargará desde vehiculos.json
let VEHICLE_DATA = {};

// Helpers para modelos y datos
function getModelsByBrand(brand) {
    const brandObj = VEHICLE_DATA[brand];
    if (!brandObj || !Array.isArray(brandObj.modelos)) return [];
    const set = new Set();
    brandObj.modelos.forEach(m => set.add(m.modelo));
    return Array.from(set).sort();
}

function findModelInfo(brand, modelName) {
    const brandObj = VEHICLE_DATA[brand];
    if (!brandObj || !Array.isArray(brandObj.modelos)) return null;
    for (const m of brandObj.modelos) {
        if (m.modelo === modelName) {
            return m;
        }
    }
    return null;
}

document.addEventListener('DOMContentLoaded', () => {
    // Inputs para marca y modelo
    const marcaSelect = document.getElementById('marca');
    const modeloSelect = document.getElementById('modelo');
    const anioFabInput = document.getElementById('anio_fabricacion');
    const precioInput = document.getElementById('precio');

    // Inputs para etiqueta
    const combustibleSelect = document.getElementById('combustible');
    const anioMatInput = document.getElementById('anio_matriculacion');
    const etiquetaSelect = document.getElementById('etiqueta');

    // Valores old() de Blade: los metemos vía atributos data-* si quieres
    const oldMarca = marcaSelect?.dataset.old || "";
    const oldModelo = modeloSelect?.dataset.old || "";

    async function loadVehicleData() {
        try {
            const response = await fetch(window.VEHICULOS_JSON_URL);
            VEHICLE_DATA = await response.json();
            initBrandSelect();
        } catch (err) {
            console.error('Error cargando vehiculos.json', err);
        }
    }

    function initBrandSelect() {
        if (!marcaSelect) return;

        marcaSelect.innerHTML = '<option value="">Selecciona marca</option>';

        const brands = Object.keys(VEHICLE_DATA).sort();
        brands.forEach(brand => {
            const opt = document.createElement('option');
            opt.value = brand;
            opt.textContent = brand;
            if (oldMarca === brand) {
                opt.selected = true;
            }
            marcaSelect.appendChild(opt);
        });

        if (oldMarca) {
            updateModels();
        }
    }

    function updateModels() {
        if (!marcaSelect || !modeloSelect) return;

        const brand = marcaSelect.value;
        modeloSelect.innerHTML = '<option value="">Selecciona modelo</option>';

        if (!brand) return;

        const models = getModelsByBrand(brand);
        models.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m;
            opt.textContent = m;
            if (oldModelo === m) {
                opt.selected = true;
            }
            modeloSelect.appendChild(opt);
        });
    }

    // Eventos marca → modelos
    if (marcaSelect) {
        marcaSelect.addEventListener('change', () => {
            updateModels();
            if (anioFabInput) anioFabInput.value = "";
            if (precioInput) precioInput.value = "";
        });
    }

    // Autocomplete año fabricación + precio desde JSON
    if (modeloSelect) {
        modeloSelect.addEventListener('change', () => {
            const brand = marcaSelect.value;
            const model = modeloSelect.value;
            const info = findModelInfo(brand, model);
            if (!info) return;

            if (anioFabInput && !anioFabInput.value && info.anio_fabricacion) {
                anioFabInput.value = info.anio_fabricacion;
            }

            if (precioInput && !precioInput.value && info.precio_original_eur) {
                precioInput.value = info.precio_original_eur;
            }
        });
    }

    // Inicializar lógica de etiqueta (usa lo que definimos en etiqueta.js)
    if (typeof initEtiquetaAuto === "function") {
        initEtiquetaAuto(combustibleSelect, anioMatInput, etiquetaSelect);
    }

    // URL del JSON (puedes inyectarla desde Blade si quieres)
    if (!window.VEHICULOS_JSON_URL) {
        window.VEHICULOS_JSON_URL = document
            .querySelector('meta[name="vehiculos-json-url"]')
            ?.content || '/assets/data/vehiculos.json';
    }

    // Cargar datos de vehículos desde JSON
    loadVehicleData();
});
