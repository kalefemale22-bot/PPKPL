<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Lab | Frontend UI</title>
    <!-- RAUDATUL: Tulis/Panggil file CSS kamu di sini -->
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; color: #333; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 800px; margin: auto; }
        h1 { color: #2c3e50; font-size: 24px; }
        .note { background: #e8f4f8; border-left: 4px solid #3498db; padding: 15px; margin-bottom: 20px; line-height: 1.6; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Dashboard Pemantauan Kondisi Laboratorium</h1>
        
        <div class="note">
            <strong>Halo Tim Frontend (Raudatul / Bukhari)! 👋</strong><br>
            Ini adalah file UI <code>resources/views/monitoring.blade.php</code> yang sudah tersambung langsung ke rute <code>http://127.0.0.1:8000/monitoring</code>. <br>
            Silakan hapus teks ini dan rombak total struktur HTML halamannya sesuai desain kreasi Anda!<br><br>
            Btw, *Backend Application Interface* (API) buatan saya sudah 100% matang di belakang layar. Untuk mengambil seluruh riwayat data lab-nya dan merendernya di layar ini (Metode GET), serta untuk mengirim/menyimpan input dari *Forms* (Metode POST), Anda cukup me-request jalur ini via Javascript Fetch/Axios: 
            <br>🏹 <b><code>/api/condition-data</code></b>.
        </div>

        <!-- RAUDATUL: Mulai desain HTML form dan tabel kamu di bawah ini (Bisa juga pakai Tailwind/Bootstrap) -->
        <div id="app">
            <h3>Daftar Riwayat Data Suhu & Kelembaban Harian</h3>
            <p><i>Data otomatis dari API Backend:</i></p>
            <ul id="data-list"></ul>
        </div>
    </div>

    <!-- RAUDATUL: Tulis kodingan Javascript kamu di sini -->
    <script>
        // CONTOH DASAR MENGAMBIL DATA DARI BACKEND:
        fetch('/api/condition-data')
            .then(response => response.json())
            .then(res => {
                if(res.status === 'success') {
                    const listObj = document.getElementById('data-list');
                    res.data.forEach(item => {
                        let li = document.createElement('li');
                        li.style.marginBottom = '8px';
                        li.innerHTML = `Suhu: <b>${item.temperature}°C</b>, Kelembaban: <b>${item.humidity}%</b> <br>
                        Ruangan: ${item.room.room_name} | Tanggal: ${new Date(item.created_at).toLocaleString()} | 
                        <span style="color:${item.indicator_color === 'red' ? 'red' : item.indicator_color === 'yellow' ? 'orange' : 'green'}; font-weight:bold;">(Status: ${item.indicator_color.toUpperCase()})</span>`;
                        listObj.appendChild(li);
                    });
                }
            })
            .catch(err => console.error('Gagal mengambil API:', err));
    </script>
</body>
</html>
