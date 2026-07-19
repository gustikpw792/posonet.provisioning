<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemetaan ODP & Pelanggan WiFi Pendolo</title>
    <!-- CSS Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    
    <!-- <link rel="stylesheet" href="cloudflare.com" /> -->
    <!-- CSS FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- <link rel="stylesheet" href="cloudflare.com" /> -->
    <!-- CSS Leaflet.Awesome-Markers -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/Leaflet.awesome-markers/2.0.2/leaflet.awesome-markers.css">
    
        <!-- Marker Cluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css">
    <!-- Leaflet Search CSS -->
    <link rel="stylesheet" href="https://opengeo.tech/maps/leaflet-search/src/leaflet-search.css">

    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }

        #map {
            width: 100%;
            height: 100vh;
        }

        /* Compact search panel using icons to avoid covering the map */
        #search-panel {
            position: absolute;
            top: 12px;
            right: 12px;
            left: auto;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.16);
            padding: 4px;
            overflow: hidden;
            transition: width 0.18s ease;
            backdrop-filter: blur(4px);
        }

        #search-panel button.icon-btn {
            border: none;
            outline: none;
            background: transparent;
            color: #222;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 6px;
            font-size: 16px;
        }

        #search-panel button.icon-btn.active {
            background: rgba(0, 123, 255, 0.12);
            color: #007bff;
        }

        #search-panel input[type="search"] {
            border: none;
            outline: none;
            padding: 6px 8px;
            width: 0;
            font-size: 14px;
            border-radius: 4px;
            transition: width 0.18s ease, opacity 0.18s ease, margin 0.18s ease;
            opacity: 0;
            margin-left: 0;
        }

        #search-panel.expanded input[type="search"] {
            width: 180px;
            opacity: 1;
            margin-left: 6px;
        }

        #search-panel i.fa {
            pointer-events: none;
        }

        .legend {
            background: white;
            padding: 12px;
            line-height: 20px;
            color: #333;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            font-size: 13px;
        }

        .leaflet-interactive.active-connection {
            stroke: limegreen !important;
            stroke-width: 3 !important;
            stroke-linecap: round;
            stroke-dasharray: 10,10 !important;
            animation: dash-move 2.2s linear infinite;
        }

        .leaflet-interactive.los-connection {
            stroke: red !important;
            stroke-width: 3 !important;
            stroke-linecap: round;
            animation: blink-red 0.5s steps(2, start) infinite;
        }

        @keyframes dash-move {
            to {
                stroke-dashoffset: 100;
            }
        }

        @keyframes blink-red {
            0%, 100% {
                stroke-opacity: 1;
            }
            50% {
                stroke-opacity: 0.1;
            }
        }
    </style>
</head>

<body>

    <div id="map"></div>
    <div id="search-panel">
        <button id="search-toggle" class="icon-btn" title="Cari"><i class="fa fa-search"></i></button>
        <input id="search-input" type="search" placeholder="ID/NAMA/ODP Pelanggan" />
        <button id="search-button" class="icon-btn" type="button" title="Jalankan pencarian"><i class="fa fa-arrow-right"></i></button>
        <button id="toggle-non-customer-odp" class="icon-btn" title="Tampilkan ODP tanpa pelanggan"><i class="fa fa-eye-slash"></i></button>
    </div>

    <!-- JS Leaflet & Awesome Markers -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Leaflet.awesome-markers/2.0.2/leaflet.awesome-markers.js"></script>
    <!-- 3. Marker Cluster JS (Plugin Pengelompokan Titik) -->
     <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
     <script src="https://opengeo.tech/maps/leaflet-search/dist/leaflet-search.src.js"></script>



    <script>
        // 1. Setup Peta & Pilihan Mode Lapisan
        const satelliteLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            attribution: '&copy; Google Maps Satellite'
        });

        const hybridLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            attribution: '&copy; Google Maps Hybrid'
        });

        const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        });

        const baseLayers = {
            'Satelit': satelliteLayer,
            'Hybrid': hybridLayer,
            'Jalan (Street)': streetLayer
        };

        const map = L.map('map', {
            // center: [-2.1460, 120.7348],
            center: <?= json_encode($lat_long) ?>,
            zoom: 16,
            layers: [hybridLayer],
            preferCanvas: true
        });

        L.control.layers(baseLayers, null, { position: 'bottomright', collapsed: false }).addTo(map);

        // 2. Icon Config
        const clientIconDefault = L.AwesomeMarkers.icon({ icon: 'wifi', prefix: 'fa', markerColor: 'orange' });
            const clientIconWorking = L.icon({
                iconUrl: '<?php echo base_url('assets/posonet/img/working2.png') ?>', // Path ke file gambar Anda
                iconSize: [24, 24],                // Ukuran ikon [lebar, tinggi] dalam pixel
                iconAnchor: [10, 20],              // Titik tumpu ikon (tengah bawah)
                popupAnchor: [0, -24]              // Posisi munculnya popup relatif terhadap iconAnchor
            });
            const clientIconLos = L.icon({
                iconUrl: '<?php echo base_url('assets/posonet/img/los2.png') ?>', // Path ke file gambar Anda
                iconSize: [24, 24],                // Ukuran ikon [lebar, tinggi] dalam pixel
                iconAnchor: [10, 20],              // Titik tumpu ikon (tengah bawah)
                popupAnchor: [0, -24]              // Posisi munculnya popup relatif terhadap iconAnchor
            });
            const clientIconDyingGasp = L.icon({
                iconUrl: '<?php echo base_url('assets/posonet/img/DyingGasp2.png') ?>', // Path ke file gambar Anda
                iconSize: [24, 24],                // Ukuran ikon [lebar, tinggi] dalam pixel
                iconAnchor: [10, 20],              // Titik tumpu ikon (tengah bawah)
                popupAnchor: [0, -24]              // Posisi munculnya popup relatif terhadap iconAnchor
            });
        // 2. custom ODP
            const odpIcon = L.icon({
                iconUrl: '<?php echo base_url('assets/posonet/img/odp.png') ?>', // Path ke file gambar Anda
                iconSize: [24, 24],                // Ukuran ikon [lebar, tinggi] dalam pixel
                iconAnchor: [10, 20],              // Titik tumpu ikon (tengah bawah)
                popupAnchor: [0, -24]              // Posisi munculnya popup relatif terhadap iconAnchor
            });
        // Definisi Icon ODP menggunakan File Gambar
            const odpIconCustom = L.icon({
                iconUrl: '<?php echo base_url('assets/posonet/img/odp.png') ?>', // Path ke file gambar Anda
                iconSize: [24, 24],                // Ukuran ikon [lebar, tinggi] dalam pixel
                iconAnchor: [10, 20],              // Titik tumpu ikon (tengah bawah)
                popupAnchor: [0, -24]              // Posisi munculnya popup relatif terhadap iconAnchor
            });

            const jointIcon = L.icon({
                iconUrl: '<?php echo base_url('assets/posonet/img/joint-box.png') ?>', // Path ke file gambar Anda
                iconSize: [24, 24],                // Ukuran ikon [lebar, tinggi] dalam pixel
                iconAnchor: [10, 20],              // Titik tumpu ikon (tengah bawah)
                popupAnchor: [0, -24]              // Posisi munculnya popup relatif terhadap iconAnchor
            });

            const oltIcon = L.icon({
                iconUrl: '<?php echo base_url('assets/posonet/img/olt.png') ?>', // Path ke file gambar Anda
                iconSize: [24, 24],                // Ukuran ikon [lebar, tinggi] dalam pixel
                iconAnchor: [10, 20],              // Titik tumpu ikon (tengah bawah)
                popupAnchor: [0, -24]              // Posisi munculnya popup relatif terhadap iconAnchor
            });

            const odpFullIconCustom = L.icon({
                iconUrl: '<?php echo base_url('assets/posonet/img/odp.png') ?>',   // File berbeda untuk status penuh
                iconSize: [24, 24],
                iconAnchor: [10, 20],
                popupAnchor: [0, -24]
            });

        // 3. Cluster Group & Cache ODP
        const markerCluster = L.markerClusterGroup({ chunkedLoading: true });
        const searchableLayer = new L.LayerGroup();
        const odpLayer = L.layerGroup();
        const nonCustomerOdpLayer = L.layerGroup();
        const connectionLines = L.layerGroup();
        const losConnectionLines = L.layerGroup();
        const searchMarkers = [];
        const renderedODPs = {};
        const odpHasCustomer = {};

        function highlightSearchResult(marker) {
            if (!marker) return;
            const latLng = marker.getLatLng();
            if (markerCluster.hasLayer(marker)) {
                markerCluster.zoomToShowLayer(marker, () => {
                    marker.openPopup();
                    map.setView(latLng, 19);
                });
            } else {
                map.setView(latLng, 19);
                marker.openPopup();
            }
        }

        function doSearch(query) {
            if (!query) return false;
            const needle = query.trim().toLowerCase();
            if (!needle) return false;
            const found = searchMarkers.find(marker => marker.searchText && marker.searchText.includes(needle));
            if (found) {
                highlightSearchResult(found);
                return true;
            }
            return false;
        }

        function setNonCustomerOdpMarkersVisible(visible) {
            console.log('[ODP-Toggle] setNonCustomerOdpMarkersVisible ->', visible, 'markers:', nonCustomerOdpLayer.getLayers().length);
            if (visible) {
                if (!map.hasLayer(nonCustomerOdpLayer)) map.addLayer(nonCustomerOdpLayer);
            } else {
                if (map.hasLayer(nonCustomerOdpLayer)) map.removeLayer(nonCustomerOdpLayer);
            }
        }

        function bindSearchControls() {
            const input = document.getElementById('search-input');
            const button = document.getElementById('search-button');
            const searchToggle = document.getElementById('search-toggle');
            const toggleBtn = document.getElementById('toggle-non-customer-odp');
            const panel = document.getElementById('search-panel');

            // Toggle expand/collapse search input
            if (searchToggle) {
                searchToggle.addEventListener('click', () => {
                    const expanded = panel.classList.toggle('expanded');
                    if (expanded) input.focus();
                });
            }

            button.addEventListener('click', () => {
                if (!doSearch(input.value)) {
                    alert('Pencarian tidak ditemukan. Coba masukkan no_pelanggan, nama_pelanggan, atau odp_name.');
                }
            });

            input.addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (!doSearch(input.value)) {
                        alert('Pencarian tidak ditemukan. Coba masukkan no_pelanggan, nama_pelanggan, atau odp_name.');
                    }
                }
            });

            // Toggle non-customer ODP visibility via icon button
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    const active = toggleBtn.classList.toggle('active');
                    const icon = toggleBtn.querySelector('i');
                    if (icon) icon.className = active ? 'fa fa-eye' : 'fa fa-eye-slash';
                    setNonCustomerOdpMarkersVisible(active);
                });
            }
        }

        // Fungsi pemecah string koordinat "lat, long"
        function splitCoord(str) {
            const p = str.split(',');
            return [parseFloat(p[0]), parseFloat(p[1])];
        }

        // 4. Ambil Data JSON
        // fetch('client.json')
        // fetch('http://192.168.55.251/primahome/api/map_odp') // Ganti dengan URL API Anda
        fetch('<?=$url?>') // Ganti dengan URL API Anda
            .then(res => res.json())
            .then(data => {
                const odpById = {};
                const hasChild = {};

                // 1. Render semua data pelanggan dan kumpulkan info ODP
                data.rows.forEach(item => {
                    const cCoord = item.cust_latlong ? splitCoord(item.cust_latlong) : null;
                    const oCoord = item.odp_latlong ? splitCoord(item.odp_latlong) : null;
                    const odpId = item.id_odp?.toString();
                    const parentId = item.id_odp_parent?.toString();
                    const description = item.description || null;

                    if (odpId && oCoord && !odpById[odpId]) {
                        odpById[odpId] = {
                            coord: oCoord,
                            name: item.odp_name,
                            id: odpId,
                            parentId,
                            description,
                            type: item.type
                        };
                    } else if (odpId && parentId && odpById[odpId] && !odpById[odpId].parentId) {
                        odpById[odpId].parentId = parentId;
                        if (!odpById[odpId].description && description) odpById[odpId].description = description;
                    }

                    if (odpId) {
                        odpHasCustomer[odpId] = true;
                    }

                    if (parentId) {
                        hasChild[parentId] = true;
                    }

                    // B. Gambar Pelanggan & Masukkan ke Cluster (hanya jika ada koordinat)
                    if (cCoord) {
                        const status = (item.ont_phase_state || '').toString().trim().toLowerCase();
                        let markerIcon = clientIconDefault;
                        if (status === 'dyinggasp' || status === 'offline') markerIcon = clientIconDyingGasp;
                        else if (status === 'los' || status === 'syncMib' || status === 'logging') markerIcon = clientIconLos;
                        else if (status === 'working') markerIcon = clientIconWorking;

                        const searchText = `${item.no_pelanggan || ''} ${item.nama_pelanggan || ''} ${item.odp_name || ''}`.trim().toLowerCase();
                        const mPel = L.marker(cCoord, {
                            icon: markerIcon,
                            title: `${item.no_pelanggan} ${item.nama_pelanggan} ${item.odp_name}` // Digunakan untuk Search
                        }).bindPopup(`
                        <table style="width:100%;border-collapse:collapse;line-height:1.4;">
                            <tr><th style="text-align:left;padding:4px 6px;">Pelanggan</th><td style="text-align:left;padding:4px 6px;">${item.no_pelanggan}. ${item.nama_pelanggan}</td></tr>
                            <tr><th style="text-align:left;padding:4px 6px;">IP</th><td style="text-align:left;padding:4px 6px;">${item.ip_address}</td></tr>
                            <tr><th style="text-align:left;padding:4px 6px;">Package</th><td style="text-align:left;padding:4px 6px;">${item.nama_paket}</td></tr>
                            <tr><th style="text-align:left;padding:4px 6px;">WhatsApp</th><td style="text-align:left;padding:4px 6px;">${item.telp}</td></tr>
                            <tr><th style="text-align:left;padding:4px 6px;">ONT Phase</th><td style="text-align:left;padding:4px 6px;">${item.ont_phase_state || 'Unknown'}</td></tr>
                            <tr><th style="text-align:left;padding:4px 6px;">ODP</th><td style="text-align:left;padding:4px 6px;">${item.odp_name}</td></tr>
                            <tr><th style="text-align:left;padding:4px 6px;">Interface</th><td style="text-align:left;padding:4px 6px;">${item.gpon_olt}</td></tr>
                            <tr><th style="text-align:left;padding:4px 6px;">ONU Type</th><td style="text-align:left;padding:4px 6px;">${item.onu_type}</td></tr>
                            <tr><th style="text-align:left;padding:4px 6px;">dB</th><td style="text-align:left;padding:4px 6px;">${item.onu_db}</td></tr>
                        </table>
                        `);

                        markerCluster.addLayer(mPel);
                        searchableLayer.addLayer(mPel);
                        searchMarkers.push(Object.assign(mPel, { searchText }));

                        // C. Tarik Kabel (Polyline) (hanya jika ada kedua koordinat)
                        if (oCoord && (status === 'los' || status === 'working' || status === 'syncMib' || status === 'logging' || status === 'dyinggasp' || status === 'offline')) {
                            const lineOptions = {
                                weight: 3,
                                opacity: 0.9,
                                dashArray: '10,10'
                            };

                            if (status === 'los' || status === 'syncMib' || status === 'logging' || status === 'dyinggasp' || status === 'offline') {
                                lineOptions.color = 'red';
                                lineOptions.className = 'los-connection';
                            } else if (status === 'working') {
                                lineOptions.color = 'limegreen';
                                lineOptions.className = 'active-connection';
                            }

                            const line = L.polyline([cCoord, oCoord], Object.assign({ renderer: L.svg() }, lineOptions));
                            if (status === 'los' || status === 'syncMib' || status === 'logging' || status === 'dyinggasp' || status === 'offline') {
                                losConnectionLines.addLayer(line);
                            } else {
                                connectionLines.addLayer(line);
                            }
                        }
                    }
                });

                // 1b. Render ODP yang tidak memiliki pelanggan (dari data.odp)
                if (data.odp && Array.isArray(data.odp)) {
                    data.odp.forEach(odpItem => {
                        const odpId = odpItem.id_odp?.toString();
                        const parentId = odpItem.id_odp_parent?.toString();
                        const oCoord = odpItem.odp_latlong ? splitCoord(odpItem.odp_latlong) : null;
                        const description = odpItem.description || null;
                        const odpType = odpItem.type || null;

                        if (odpId && oCoord && !odpById[odpId]) {
                            odpById[odpId] = {
                                coord: oCoord,
                                name: odpItem.odp_name,
                                id: odpId,
                                parentId,
                                description,
                                type: odpType
                            };
                        } else if (odpId && parentId && odpById[odpId] && !odpById[odpId].parentId) {
                            odpById[odpId].parentId = parentId;
                            if (!odpById[odpId].description && description) odpById[odpId].description = description;
                        }

                        if (parentId) {
                            hasChild[parentId] = true;
                        }
                    });
                }

                // 2. Gambar semua ODP unik, termasuk yang tidak punya relasi
                Object.values(odpById).forEach(odp => {
                    const isIsolated = !odp.parentId && !hasChild[odp.id];
                    const formattedDescription = odp.description
                        ? odp.description.replace(/\r\n/g, '<br>').replace(/\n/g, '<br>')
                        : 'Tidak ada';
                    let markerIcon = odpIcon;
                    if (odp.type === 'joint') {
                        markerIcon = jointIcon;
                    } else if (odp.type === 'odp') {
                        markerIcon = odpIcon;
                    } else if (odp.type === 'olt') {
                        markerIcon = oltIcon;
                    }
                    const marker = L.marker(odp.coord, {
                        icon: markerIcon,
                        title: odp.name
                    }).bindPopup(`
                        <b>ODP: ${odp.name}</b><br>
                        Desc: ${formattedDescription}<br>
                        Parent ID: ${odp.parentId ? odp.parentId : 'Tidak ada'}
                    `);
                    const odpSearchText = `${odp.name || ''}`.trim().toLowerCase();
                    searchMarkers.push(Object.assign(marker, { searchText: odpSearchText }));

                    if (!odpHasCustomer[odp.id]) {
                        marker.isNonCustomerOdp = true;
                        nonCustomerOdpLayer.addLayer(marker);
                        console.log('[ODP-Toggle] added non-customer ODP marker:', odp.id, odp.name);
                    } else {
                        marker.isNonCustomerOdp = false;
                        odpLayer.addLayer(marker);
                    }
                });

                // Aktifkan layer ODP utama dulu, ODP tanpa pelanggan akan tampil saat tombol toggle aktif
                odpLayer.addTo(map);
                // LOS connection lines selalu tampil (urgent)
                losConnectionLines.addTo(map);
                const toggleBtnEl = document.getElementById('toggle-non-customer-odp');
                if (toggleBtnEl) {
                    toggleBtnEl.classList.remove('active');
                    const icon = toggleBtnEl.querySelector('i');
                    if (icon) icon.className = 'fa fa-eye-slash';
                }
                setNonCustomerOdpMarkersVisible(false);

                // Tampilkan connection lines hanya saat zoom tinggi untuk mengurangi render
                function updateConnectionLineVisibility() {
                    if (map.getZoom() >= 17) {
                        if (!map.hasLayer(connectionLines)) map.addLayer(connectionLines);
                    } else if (map.hasLayer(connectionLines)) {
                        map.removeLayer(connectionLines);
                    }
                }
                map.on('zoomend', updateConnectionLineVisibility);
                updateConnectionLineVisibility();

                // 3. Tarik garis antar ODP ke parent ODP berdasarkan id_odp_parent
                Object.values(odpById).forEach(odp => {
                    if (!odp.parentId) return;
                    const parent = odpById[odp.parentId];
                    if (parent) {
                        L.polyline([odp.coord, parent.coord], {
                            renderer: L.svg(),
                            color: 'red',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '8,4'
                        }).addTo(map);
                    }
                });

                map.addLayer(markerCluster);

                // 5. Fitur Search bawaan Leaflet Search
                const search = new L.Control.Search({
                    layer: searchableLayer,
                    propertyName: 'title',
                    moveToLocation: function (latlng, title, map) {
                        map.setView(latlng, 19);
                    }
                });
                map.addControl(search);

                // 6. Pastikan non-customer ODP tetap tersembunyi saat load awal
                map.removeLayer(nonCustomerOdpLayer);

                // 7. Bind kontrol pencarian manual
                bindSearchControls();

                // 8. FITUR SEARCH VIA URL (?cari=NAMA)
                const urlParams = new URLSearchParams(window.location.search);
                const queryNama = urlParams.get('cari');

                if (queryNama) {
                    document.getElementById('search-input').value = queryNama;
                    if (!doSearch(queryNama)) {
                        console.warn('Pencarian URL tidak ditemukan:', queryNama);
                    }
                }
            })
            .catch(err => console.error("Gagal memuat data:", err));

    </script>
</body>

</html>