<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Lab | Frontend UI (Bukhari & Raudatul)</title>
    <!-- Silakan Tulis/Panggil file CSS milik Frontend di sini -->
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; color: #333; padding: 20px; }
        .container { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 900px; margin: auto; }
        h1, h3 { color: #2c3e50; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold;}
        input, select { width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #3498db; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #2980b9; }
        .card { border: 1px solid #e0e0e0; border-radius: 5px; padding: 15px; margin-bottom: 20px; background-color: #fafafa;}
        .alert { padding: 10px; border-radius: 4px; margin-bottom: 15px; display: none; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        ul { list-style: none; padding: 0; }
        li { background: #fff; border: 1px solid #eee; margin-bottom: 8px; padding: 10px; border-radius: 4px; display: flex; justify-content: space-between;}
    </style>
</head>
<body>

    <div class="container">
        <h1>Dashboard Pemantauan Kondisi Laboratorium</h1>
        
        <!-- BAGIAN INPUT (FORM) -->
        <div class="card">
            <h3>Input Data Suhu & Kelembaban</h3>
            <div id="alert-box" class="alert"></div>
            
            <form id="monitoring-form">
                <!-- Dropdown Ruangan -->
                <div class="form-group">
                    <label>Pilih Ruang Penyimpanan:</label>
                    <select id="storage_room_id" required>
                        <option value="">-- Memuat ruangan... --</option>
                    </select>
                </div>

                <!-- Input Suhu -->
                <div class="form-group">
                    <label>Suhu Saat Ini (°C):</label>
                    <input type="number" step="0.1" id="temperature" placeholder="Contoh: 25.5" required>
                </div>

                <!-- Input Kelembaban -->
                <div class="form-group">
                    <label>Kelembaban (%RH):</label>
                    <input type="number" step="0.1" id="humidity" placeholder="Contoh: 60.0" required>
                </div>

                <!-- Hardcoded ID Petugas Untuk Simulasi (Karena Belum Buat Fitur Login) -->
                <input type="hidden" id="inputted_by" value="1">

                <button type="submit">Simpan Data Kondisi</button>
            </form>
        </div>

        <!-- BAGIAN HASIL (RIWAYAT DATA) -->
        <div class="card">
            <h3>Riwayat Monitoring Historis</h3>
            <ul id="data-list">
                <li>Memuat data...</li>
            </ul>
        </div>
    </div>

    <script>
        // 1. FUNGSI UNTUK MENGAMBIL DAFTAR RUANGAN (GET) DAN MENGISI DROPDOWN
        function fetchRooms() {
            fetch('/api/storage-rooms')
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success') {
                        const selectObj = document.getElementById('storage_room_id');
                        selectObj.innerHTML = '<option value="">-- Pilih Ruangan --</option>';
                        res.data.forEach(room => {
                            selectObj.innerHTML += `<option value="${room.id}">${room.room_name} (Batas Max Suhu: ${room.temp_limit}°C)</option>`;
                        });
                    }
                });
        }

        // 2. FUNGSI UNTUK MENGAMBIL RIWAYAT DATA MONITORING (GET)
        function fetchHistory() {
            fetch('/api/condition-data')
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success') {
                        const listObj = document.getElementById('data-list');
                        listObj.innerHTML = '';
                        if(res.data.length === 0) {
                            listObj.innerHTML = '<li>Belum ada data terekam. Silakan input dulu di atas.</li>';
                            return;
                        }

                        res.data.forEach(item => {
                            let li = document.createElement('li');
                            let color = item.indicator_color === 'red' ? '#e74c3c' : item.indicator_color === 'yellow' ? '#f39c12' : '#2ecc71';
                            
                            li.innerHTML = `
                                <div>
                                    <strong>${item.room.room_name}</strong><br>
                                    <small>Oleh: ${item.user.name} | ${new Date(item.created_at).toLocaleString()}</small>
                                </div>
                                <div style="text-align:right;">
                                    Suhu: <b>${item.temperature}°C</b> | Kelembaban: <b>${item.humidity}%</b><br>
                                    <span style="color: white; background-color: ${color}; padding: 2px 8px; border-radius: 12px; font-size:12px; font-weight:bold;">
                                        STATUS: ${item.indicator_color.toUpperCase()}
                                    </span>
                                </div>
                            `;
                            listObj.appendChild(li);
                        });
                    }
                });
        }

        // 3. FUNGSI UNTUK MENGIRIM / MENGINPUT DATA BARU (POST)
        document.getElementById('monitoring-form').addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah halaman reload

            const bodyData = {
                storage_room_id: document.getElementById('storage_room_id').value,
                inputted_by: document.getElementById('inputted_by').value, // Dummy Petugas ID 1
                temperature: document.getElementById('temperature').value,
                humidity: document.getElementById('humidity').value,
            };

            fetch('/api/condition-data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(bodyData)
            })
            .then(response => response.json())
            .then(data => {
                const alertBox = document.getElementById('alert-box');
                alertBox.style.display = 'block';

                if (data.status === 'success') {
                    alertBox.className = 'alert alert-success';
                    alertBox.innerHTML = `Sukses! Data disimpan. Indikator Sistem: <strong>${data.data.indicator_color.toUpperCase()}</strong>`;
                    document.getElementById('monitoring-form').reset(); // Kosongkan form
                    fetchHistory(); // Refresh daftar data di bawahnya
                } else {
                    // Jika ada error validasi dari Backend
                    alertBox.className = 'alert alert-danger';
                    alertBox.innerHTML = 'Error: ' + JSON.stringify(data.messages || data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        // Panggil fungsi saat halaman pertama kali diload
        fetchRooms();
        fetchHistory();
    </script>
</body>
</html>
