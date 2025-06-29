@extends('layouts.app')

@section('title', 'Home')
@push('styles')
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/1.6.0/Control.FullScreen.css" />

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

        @media (max-width: 576px) {
            .scroll-box {
                max-height: 250px;
            }
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
                            <button class="accordion-button collapsed bg-secondary text-white" type="button"
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
                        <div id="collapseKecamatan" class="accordion-collapse collapse" aria-labelledby="headingKecamatan"
                            data-bs-parent="#accordionSidebar">
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
                        <div id="map" style="height: 600px;"></div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/1.6.0/Control.FullScreen.js"></script>

    <script>
        let kelurahanFeatures = []; // dari kelurahan geojson
        let allFeatures = []; // dari kota geojson
        let kelurahanLayerByCode = {};
        let kecamatanNameMap = {};
        const kelurahanColorMap = {};

        function getColorFromString(str) {
            let hash = 0;
            for (let i = 0; i < str.length; i++) {
                hash = str.charCodeAt(i) + ((hash << 5) - hash);
            }
            const color = (hash & 0x00FFFFFF).toString(16).toUpperCase();
            return "#" + "00000".substring(0, 6 - color.length) + color;
        }

        function getConsistentColor(kd_kelurahan) {
            if (!kelurahanColorMap[kd_kelurahan]) {
                kelurahanColorMap[kd_kelurahan] = getColorFromString(kd_kelurahan);
            }
            return kelurahanColorMap[kd_kelurahan];
        }

        // Inisialisasi peta
        const map = L.map('map', {
            // fullscreenControl: true
        }).setView([-7.35, 108.2], 12);

        // Tambahkan kontrol fullscreen
        L.control.fullscreen({
            position: 'topright'
        }).addTo(map);


        // Tile layers
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

        // Default layer
        tileLayers.osm.addTo(map);

        // Tile layer control select
        const tileSelect = document.getElementById("tileLayerSelect");
        if (tileSelect) {
            tileSelect.addEventListener("change", function() {
                Object.values(tileLayers).forEach(layer => map.removeLayer(layer));
                tileLayers[this.value].addTo(map);
            });
        }

        const lokasi = @json($lokasi);

        lokasi.forEach(item => {
            if (item.latitude && item.longitude) {
                L.marker([item.latitude, item.longitude])
                    .addTo(map)
                    .bindPopup(`
                <div style="max-width: 200px;">
                    <img src="storage/${item.image}" alt="${item.name}" style="width: 100%; height: auto; border-radius: 8px;">
                    <strong>${item.name}</strong><br>
                    ${item.address || ''}<br>
                    ${item.description || ''}
                </div>
            `);
            }
        });

        // Setelah load kota geojson
        fetch('/geojson/KotaTasikmalaya1.geojson')
            .then(res => res.json())
            .then(kota => {
                allFeatures = kota.features;

                // Buat mapping kd_kecamatan => nm_kecamatan
                allFeatures.forEach(f => {
                    const props = f.properties;
                    if (props.kd_kecamatan && props.nm_kecamatan) {
                        kecamatanNameMap[props.kd_kecamatan] = props.nm_kecamatan;
                    }
                });

                // Load kelurahan setelah kota
                fetch('/geojson/kelurahanKotaTasikmalaya.geojson')
                    .then(res => res.json())
                    .then(kelurahan => {
                        kelurahanFeatures = kelurahan.features;
                        renderKecamatanCheckboxes();
                    });
            });

        function renderKecamatanCheckboxes() {
            const container = document.getElementById('checkboxList');
            container.innerHTML = '';
            const grouped = {};

            kelurahanFeatures.forEach(f => {
                const props = f.properties;
                const kd = props.kd_kecamatan;
                if (!grouped[kd]) {
                    grouped[kd] = {
                        nama: kecamatanNameMap[kd] || `Kecamatan ${kd}`,
                        kelurahans: []
                    };
                }
                grouped[kd].kelurahans.push(f);
            });

            for (const [kd_kecamatan, {
                    nama,
                    kelurahans
                }] of Object.entries(grouped)) {
                const kecWrapper = document.createElement('div');
                kecWrapper.className = 'border p-2 mb-2';

                const kecCb = document.createElement('input');
                kecCb.type = 'checkbox';
                kecCb.className = 'form-check-input me-1';
                kecCb.id = `cb-kec-${kd_kecamatan}`;

                const kecLabel = document.createElement('label');
                kecLabel.className = 'form-check-label fw-bold';
                kecLabel.htmlFor = kecCb.id;
                kecLabel.textContent = nama;

                const kecDiv = document.createElement('div');
                kecDiv.className = 'form-check mb-2';
                kecDiv.append(kecCb, kecLabel);
                kecWrapper.append(kecDiv);

                const kelurahanDiv = document.createElement('div');
                kelurahanDiv.className = 'ms-3';
                kelurahanDiv.style.display = 'none';

                kecCb.addEventListener('change', () => {
                    if (kecCb.checked) {
                        kelurahanDiv.style.display = 'block';
                        kelurahanCheckboxes.forEach(cb => {
                            cb.checked = true;
                            showSingleKelurahan(cb.feature);
                        });
                    } else {
                        kelurahanDiv.style.display = 'none';
                        kelurahanCheckboxes.forEach(cb => {
                            cb.checked = false;
                            removeSingleKelurahan(kd_kecamatan, cb.feature.properties.kd_kelurahan);
                        });
                    }
                });

                const kelurahanCheckboxes = [];
                kelurahans.forEach(f => {
                    const kd_kel = f.properties.kd_kelurahan;
                    const nm_kel = f.properties.nm_kelurahan;

                    const cb = document.createElement('input');
                    cb.type = 'checkbox';
                    cb.className = 'form-check-input me-1';
                    cb.id = `cb-${kd_kecamatan}-${kd_kel}`;
                    cb.feature = f;

                    const label = document.createElement('label');
                    label.className = 'form-check-label';
                    label.htmlFor = cb.id;
                    label.textContent = nm_kel;

                    const wrap = document.createElement('div');
                    wrap.className = 'form-check';
                    wrap.append(cb, label);

                    kelurahanDiv.append(wrap);
                    kelurahanCheckboxes.push(cb);

                    cb.addEventListener('change', () => {
                        if (cb.checked) {
                            showSingleKelurahan(cb.feature);
                        } else {
                            removeSingleKelurahan(kd_kecamatan, kd_kel);
                        }
                    });
                });

                kecWrapper.append(kelurahanDiv);
                container.append(kecWrapper);
            }
        }

        function showSingleKelurahan(feature) {
            const kd_kecamatan = feature.properties.kd_kecamatan;
            const kd_kelurahan = feature.properties.kd_kelurahan;
            const kode = `${kd_kecamatan}_${kd_kelurahan}`;
            if (kelurahanLayerByCode[kode]) return;

            const layer = L.geoJSON(feature, {
                style: {
                    color: getConsistentColor(kd_kelurahan),
                    weight: 1.5,
                    fillOpacity: 0.5
                },
                onEachFeature: (f, l) => {
                    l.bindPopup(
                        `Kelurahan: ${f.properties.nm_kelurahan}<br>Kecamatan: ${kecamatanNameMap[kd_kecamatan]}`
                    );
                }
            }).addTo(map);

            kelurahanLayerByCode[kode] = layer;
        }

        function removeSingleKelurahan(kd_kecamatan, kd_kelurahan) {
            const kode = `${kd_kecamatan}_${kd_kelurahan}`;
            if (kelurahanLayerByCode[kode]) {
                map.removeLayer(kelurahanLayerByCode[kode]);
                delete kelurahanLayerByCode[kode];
            }
        }
    </script>
@endpush
