@extends('layouts.app')

@section('title', 'Home')
@push('styles')
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 500px;
            width: 100%;
        }

        .leaflet-container {
            z-index: 0;
        }

        .card {
            margin-bottom: 1rem;
        }

        .scroll-box {
            max-height: 450px;
            overflow-y: auto;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid py-1">
        <div class="row">

            <!-- Card 1: Pilih Kecamatan -->
            <div class="col-md-3">
                <div class="accordion" id="accordionSidebar">

                    <!-- Accordion: Pilih Tampilan Peta -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingPeta">
                            <button class="accordion-button collapsed bg-danger text-white" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapsePeta" aria-expanded="false"
                                aria-controls="collapsePeta">
                                Pilih Tampilan Peta
                            </button>
                        </h2>
                        <div id="collapsePeta" class="accordion-collapse collapse show" aria-labelledby="headingPeta"
                            data-bs-parent="#accordionSidebar">
                            <div class="accordion-body">
                                <select id="tileLayerSelect" class="form-select mb-3">
                                    <option value="osm">OpenStreetMap</option>
                                    <option value="satellite">Satellite (Esri)</option>
                                    <option value="topo">Topographic (Esri)</option>
                                    <option value="dark">Dark Mode</option>
                                </select>
                                <p class="small text-muted">Ubah tampilan latar belakang peta.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Accordion: Pilih Kecamatan -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingKecamatan">
                            <button class="accordion-button bg-success text-white" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseKecamatan" aria-expanded="true" aria-controls="collapseKecamatan">
                                Pilih Kecamatan
                            </button>
                        </h2>
                        <div id="collapseKecamatan" class="accordion-collapse collapse"
                            aria-labelledby="headingKecamatan" data-bs-parent="#accordionSidebar">
                            <div class="accordion-body scroll-box" id="checkboxList">
                                <!-- Checkbox akan diisi dinamis -->
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <!-- Card 2: Peta -->
            <div class="col-md-9">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        Peta Bank Sampah kota tasikmalaya
                    </div>
                    <div class="card-body p-0">
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const map = L.map('map').setView([-7.35, 108.2], 12);

        const lokasi = @json($lokasi);

        lokasi.forEach(item => {
            if (item.latitude && item.longitude) {
                L.marker([item.latitude, item.longitude])
                    .addTo(map)
                    .bindPopup(
                        `<img src="storage/${item.image}" alt="${item.name}" width="100%"><strong>${item.name}</strong><br>${item.address || ''}<br>${item.description || ''}`
                    );
            }
        });

        const tileLayers = {
            osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }),
            satellite: L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles &copy; Esri'
                }),
            topo: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenTopoMap'
            }),
            dark: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; CartoDB'
            })
        };

        tileLayers.osm.addTo(map);

        document.getElementById("tileLayerSelect").addEventListener("change", function() {
            const selected = this.value;
            Object.values(tileLayers).forEach(layer => map.removeLayer(layer));
            tileLayers[selected].addTo(map);
        });

        const kotaStyle = {
            color: "#ffc107",
            weight: 2,
            fillOpacity: 0.3
        };
        const highlightStyle = {
            color: "#198754",
            weight: 3,
            fillOpacity: 0.5
        };

        let allFeatures = [];
        const layersByName = {};

        function getKecamatanName(props) {
            return props.nm_kecamatan || "Tanpa Nama";
        }

        function addCheckboxes(features) {
            const list = document.getElementById('checkboxList');
            list.innerHTML = ''; // Bersihkan isi sebelumnya

            const seen = new Set();
            const checkboxes = [];

            // Buat checkbox untuk "Pilih Semua"
            const selectAllCheckbox = document.createElement('input');
            selectAllCheckbox.type = 'checkbox';
            selectAllCheckbox.className = 'form-check-input me-1';
            selectAllCheckbox.id = 'cb-select-all';

            const selectAllLabel = document.createElement('label');
            selectAllLabel.className = 'form-check-label fw-bold';
            selectAllLabel.htmlFor = selectAllCheckbox.id;
            selectAllLabel.appendChild(selectAllCheckbox);
            selectAllLabel.append(" Pilih Semua");

            const selectAllWrapper = document.createElement('div');
            selectAllWrapper.className = 'form-check mb-2';
            selectAllWrapper.appendChild(selectAllLabel);
            list.appendChild(selectAllWrapper);

            // Generate semua checkbox per kecamatan
            features.forEach((feature, i) => {
                const name = getKecamatanName(feature.properties);
                if (seen.has(name)) return;
                seen.add(name);

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'form-check-input me-1';
                checkbox.id = `cb-${i}`;
                checkbox.value = name;

                const label = document.createElement('label');
                label.className = 'form-check-label';
                label.htmlFor = checkbox.id;
                label.appendChild(checkbox);
                label.append(" " + name);

                const wrapper = document.createElement('div');
                wrapper.className = 'form-check';
                wrapper.appendChild(label);

                checkbox.addEventListener('change', function() {
                    wrapper.classList.toggle('bg-warning-subtle', this.checked);
                    if (this.checked) {
                        showKecamatan(name);
                    } else {
                        removeKecamatan(name);
                    }

                    // Perbarui status "Pilih Semua"
                    const allChecked = checkboxes.every(cb => cb.checked);
                    selectAllCheckbox.checked = allChecked;
                });

                checkboxes.push(checkbox);
                list.appendChild(wrapper);
            });

            // Logic untuk checkbox "Pilih Semua"
            selectAllCheckbox.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    // Jangan trigger event lagi, cukup set checked + trigger manual function
                    cb.checked = selectAllCheckbox.checked;

                    const wrapper = cb.closest('.form-check');
                    wrapper.classList.toggle('bg-warning-subtle', cb.checked);

                    const name = cb.value;
                    if (cb.checked) {
                        showKecamatan(name);
                    } else {
                        removeKecamatan(name);
                    }
                });
            });
        }

        function showKecamatan(name) {
            const filtered = allFeatures.filter(f => getKecamatanName(f.properties) === name);
            const layer = L.geoJSON(filtered, {
                style: highlightStyle,
                onEachFeature: (feature, layer) => {
                    layer.bindPopup("Kecamatan: " + getKecamatanName(feature.properties));
                }
            }).addTo(map);
            layersByName[name] = layer;
        }

        function removeKecamatan(name) {
            if (layersByName[name]) {
                map.removeLayer(layersByName[name]);
                delete layersByName[name];
            }
        }

        // Load GeoJSON Kota Saja
        fetch('/geojson/KotaTasikmalaya1.geojson')
            .then(res => res.json())
            .then(kota => {
                allFeatures = kota.features;

                // Tambahkan batas wilayah umum (style dasar)
                // L.geoJSON(kota, {
                //     style: kotaStyle
                // }).addTo(map);

                addCheckboxes(allFeatures);
            });
    </script>
@endpush
