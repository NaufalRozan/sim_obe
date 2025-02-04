<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page Modal</title>
    <style>
        body {
            margin: 0;
            height: 100%;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
            position: relative;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .modal.hidden {
            display: none;
        }

        .modal-content h2 {
            text-align: center;
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .modal-content p {
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .modal-content .info-section {
            margin-bottom: 1.5rem;
        }

        .modal-content .info-section p {
            font-weight: bold;
        }

        .modal-content .info-section ul {
            list-style-type: disc;
            margin: 0;
            padding-left: 1.5rem;
            text-align: left;
        }

        .modal-content .info-section a {
            color: #007BFF;
            text-decoration: underline;
        }

        .modal-content button {
            cursor: pointer;
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 0.5rem;
            width: 100%;
            font-size: 1rem;
            font-weight: bold;
        }

        .modal-content button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <!-- Modal -->
    <div id="info-popup" class="modal hidden">
        <div class="modal-content">
            <!-- Tombol Tutup -->
            <span class="close" id="close-popup">&times;</span>

            <h2>Selamat Datang</h2>
            <p>Selamat datang di <strong>SIM OBE Demo</strong>. Pastikan Anda membaca panduan penggunaan sebelum login.
            </p>

            <!-- Informasi Prodi -->
            <div class="info-section">
                <p>Akses Prodi:</p>
                <p><a href="https://obe.jogjacode.id" target="_blank">obe.jogjacode.id</a></p>
                <ul>
                    <li>Email: <strong>prodi@demo.com</strong></li>
                    <li>Password: <strong>prodi1234</strong></li>
                </ul>
            </div>

            <!-- Informasi Pengajar -->
            <div class="info-section">
                <p>Akses Pengajar:</p>
                <p><a href="https://obe.jogjacode.id/pengajar/" target="_blank">obe.jogjacode.id/pengajar/</a></p>
                <ul>
                    <li>Email: <strong>pengajar@demo.com</strong></li>
                    <li>Password: <strong>pengajar1234</strong></li>
                </ul>
            </div>

            <!-- Informasi Mahasiswa -->
            <div class="info-section">
                <p>Akses Mahasiswa:</p>
                <p><a href="https://obe.jogjacode.id/mahasiswa/" target="_blank">obe.jogjacode.id/mahasiswa/</a></p>
                <ul>
                    <li>Email: <strong>mahasiswa@demo.com</strong></li>
                    <li>Password: <strong>pengajar1234</strong></li>
                </ul>
            </div>

            <!-- Tombol Konfirmasi -->
            <button id="confirm-popup">OK, Mengerti</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popup = document.getElementById('info-popup');
            const closeButton = document.getElementById('close-popup');
            const confirmButton = document.getElementById('confirm-popup');

            // Tampilkan modal saat halaman dimuat
            popup.classList.remove('hidden');

            // Fungsi untuk menutup modal
            const closeModal = () => {
                popup.classList.add('hidden');
            };

            // Event listener untuk tombol close dan confirm
            closeButton.addEventListener('click', closeModal);
            confirmButton.addEventListener('click', closeModal);
        });
    </script>
</body>

</html>
