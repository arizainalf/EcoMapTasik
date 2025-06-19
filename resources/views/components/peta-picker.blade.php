<div wire:ignore class="space-y-2">
    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Style Peta</label>
    <select id="tileSelector"
        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500 text-sm">
        <option value="osm" selected>OpenStreetMap</option>
        <option value="carto">Carto Light</option>
        <option value="satellite">Esri Satellite</option>
    </select>

    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Lokasi Bank Sampah</label>
    <div id="peta" class="w-full rounded-md border border-gray-300 dark:border-gray-700" style="height: 350px;">
    </div>
</div>


<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const baseLayers = {
        'osm': L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }),
        'carto': L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; CartoDB',
            maxZoom: 19
        }),
        'satellite': L.tileLayer(
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye',
                maxZoom: 19
            })
    };

    function waitForCoordinatesAndInitMap(attempt = 0) {
        const latInput = document.getElementById('latitude-input');
        const lngInput = document.getElementById('longitude-input');

        const lat = parseFloat(latInput?.value);
        const lng = parseFloat(lngInput?.value);

        if (!latInput || !lngInput || isNaN(lat) || isNaN(lng)) {
            if (attempt < 10) {
                console.log(`⏳ Menunggu koordinat edit... (${attempt + 1}/10)`);
                setTimeout(() => waitForCoordinatesAndInitMap(attempt + 1), 500);
            } else {
                console.warn("⚠️ Koordinat tidak tersedia, inisialisasi dengan default.");
                initPetaPicker(); // Lanjutkan pakai default
            }
        } else {
            console.log("✅ Koordinat tersedia:", lat, lng);
            initPetaPicker(); // Sekarang bisa inisialisasi dengan nilai yang benar
        }
    }

    // Jalankan saat halaman selesai render
    window.addEventListener('load', () => {
        setTimeout(() => waitForCoordinatesAndInitMap(), 500);
    });

    // Juga tambahkan hook untuk Livewire update (edit form biasanya async)
    document.addEventListener("livewire:load", () => {
        window.Livewire.hook('message.processed', (message, component) => {
            waitForCoordinatesAndInitMap();
        });
    });

    // Fallback jika window.onload tidak trigger
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(initPetaPicker, 2000);
    });

    let mapInitialized = false; // Prevent double initialization

    function initPetaPicker() {
        if (mapInitialized) {
            console.log('Peta sudah diinisialisasi');
            return;
        }

        console.log('Memulai inisialisasi peta...');

        // Cek container peta
        const petaContainer = document.getElementById('peta');
        if (!petaContainer) {
            console.error('Container #peta tidak ditemukan');
            return;
        }

        // Cek dimensi container
        const containerRect = petaContainer.getBoundingClientRect();
        console.log('Container dimensions:', containerRect);

        if (containerRect.width === 0 || containerRect.height === 0) {
            console.warn('Container belum memiliki dimensi, mencoba lagi dalam 1 detik...');
            setTimeout(initPetaPicker, 1000);
            return;
        }

        // Koordinat default
        let defaultLat = -7.35;
        let defaultLng = 108.2;

        // Cari input fields
        const latInput = document.getElementById('latitude-input') ||
            document.querySelector('input[name="latitude"]') ||
            document.querySelector('[wire\\:model*="latitude"]');
        const lngInput = document.getElementById('longitude-input') ||
            document.querySelector('input[name="longitude"]') ||
            document.querySelector('[wire\\:model*="longitude"]');

        console.log('Input fields found:', {
            latInput,
            lngInput
        });

        // Ambil nilai dari input jika ada
        if (latInput?.value && !isNaN(parseFloat(latInput.value))) {
            defaultLat = parseFloat(latInput.value);
        }
        if (lngInput?.value && !isNaN(parseFloat(lngInput.value))) {
            defaultLng = parseFloat(lngInput.value);
        }

        console.log('Default coordinates:', {
            defaultLat,
            defaultLng
        });

        // Inisialisasi peta
        let map, marker;

        try {
            // Clear any existing map
            if (petaContainer._leaflet_id) {
                petaContainer._leaflet.remove();
            }

            map = L.map('peta', {
                center: [defaultLat, defaultLng],
                zoom: 13,
                zoomControl: true,
                attributionControl: true
            });

            // Tambahkan tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Force resize map setelah tiles loaded
            map.whenReady(function() {
                setTimeout(() => {
                    map.invalidateSize();
                    console.log('Peta ready dan ukuran diupdate');
                }, 100);
            });

            // Tambahkan marker
            marker = L.marker([defaultLat, defaultLng], {
                draggable: true,
                title: 'Drag untuk mengubah lokasi atau klik peta'
            }).addTo(map);

            console.log('Peta dan marker berhasil dibuat');

        } catch (error) {
            console.error('Error membuat peta:', error);
            return;
        }

        // Fungsi update koordinat
        function updateCoordinates(lat, lng) {
            const formattedLat = lat.toFixed(7);
            const formattedLng = lng.toFixed(7);

            console.log(`Mengupdate koordinat: ${formattedLat}, ${formattedLng}`);

            if (latInput && lngInput) {
                // Set nilai
                latInput.value = formattedLat;
                lngInput.value = formattedLng;

                // Trigger events untuk Filament
                const events = ['input', 'change'];
                events.forEach(eventName => {
                    const event = new Event(eventName, {
                        bubbles: true,
                        cancelable: true
                    });

                    latInput.dispatchEvent(event);
                    lngInput.dispatchEvent(event);
                });

                // Livewire update jika tersedia
                if (window.Livewire) {
                    try {
                        const livewireEl = latInput.closest('[wire\\:id]');
                        if (livewireEl) {
                            const componentId = livewireEl.getAttribute('wire:id');
                            const component = window.Livewire.find(componentId);
                            if (component) {
                                component.set('data.latitude', formattedLat);
                                component.set('data.longitude', formattedLng);
                                console.log('Livewire component updated');
                            }
                        }
                    } catch (e) {
                        console.log('Livewire update failed:', e);
                    }
                }

                console.log('Form values updated:', latInput.value, lngInput.value);
            } else {
                console.warn('Input fields tidak ditemukan untuk update');
            }
        }

        // Event listeners
        if (marker && map) {
            marker.on('dragstart', function() {
                console.log('Marker drag started');
            });

            marker.on('dragend', function(e) {
                console.log('Marker drag ended');
                const position = marker.getLatLng();
                updateCoordinates(position.lat, position.lng);
            });

            map.on('click', function(e) {
                console.log('Map clicked at:', e.latlng);
                marker.setLatLng(e.latlng);
                updateCoordinates(e.latlng.lat, e.latlng.lng);
            });

            // Set nilai awal
            updateCoordinates(defaultLat, defaultLng);

            mapInitialized = true;
            console.log('✅ Peta picker berhasil diinisialisasi!');
        }
    }
</script>
