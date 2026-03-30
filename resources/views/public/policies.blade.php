{{-- Privacy Policy Modal --}}
<div id="privacy-policy-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-2xl font-bold text-gray-900">Kebijakan Privasi</h3>
                <button type="button" id="close-privacy-modal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="max-h-96 overflow-y-auto text-sm text-gray-700 space-y-4">
                <div>
                    <h4 class="font-bold text-lg mb-2">Cara Kami Memproses Data Pribadi Anda</h4>
                    <p class="mb-3">Untuk melindungi hak Anda dalam rangkaian pemrosesan data pribadi, kami memperhatikan ketentuan berikut untuk kenyamanan Anda:</p>

                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li>Kami akan meminta persetujuan yang sah secara eksplisit untuk 1 (satu) atau beberapa tujuan tertentu yang telah kami ajukan kepada Anda;</li>
                        <li>Setelah Anda memberikan persetujuan kepada kami, kami akan memberitahu Anda tentang legalitas pemrosesan Data Pribadi; tujuan pemrosesan Data Pribadi; jenis dan relevansi Data Pribadi yang akan diproses; periode retensi dokumen yang berisi Data Pribadi; rincian informasi yang dikumpulkan; periode pemrosesan Data Pribadi; dan hak Anda sebagai subjek Data Pribadi;</li>
                        <li>Kami akan memproses Data Pribadi Anda secara terbatas dan spesifik, secara sah dan transparan;</li>
                        <li>Kami akan memastikan akurasi, kelengkapan, dan konsistensi Data Pribadi Anda sesuai dengan hukum dan peraturan yang berlaku;</li>
                        <li>Kami akan memberikan akses kepada Anda terhadap Data Pribadi yang diproses dan rekam jejak pemrosesan Data Pribadi sesuai dengan periode penyimpanan Data Pribadi;</li>
                        <li>Dalam memproses Data Pribadi Anda, kami menjamin kerahasiaan Data Pribadi yang Anda serahkan;</li>
                        <li>Kami akan menghapus atau mengembalikan Data Pribadi Anda setelah periode pemrosesan berakhir, kecuali diwajibkan oleh hukum untuk menyimpannya;</li>
                        <li>Kami akan memberitahu Anda tentang setiap pelanggaran keamanan Data Pribadi yang mengakibatkan risiko tinggi terhadap hak dan kebebasan Anda;</li>
                        <li>Kami akan mematuhi semua persyaratan hukum dan peraturan yang berlaku terkait perlindungan Data Pribadi.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-2">Hak Anda sebagai Subjek Data Pribadi</h4>
                    <p class="mb-3">Sebagai subjek Data Pribadi, Anda memiliki hak-hak berikut:</p>

                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li><strong>Hak untuk mengetahui:</strong> Anda berhak mendapatkan informasi mengenai pemrosesan Data Pribadi Anda;</li>
                        <li><strong>Hak untuk mengakses:</strong> Anda berhak mengakses Data Pribadi yang kami proses;</li>
                        <li><strong>Hak untuk memperbaiki:</strong> Anda berhak memperbaiki Data Pribadi yang tidak akurat atau tidak lengkap;</li>
                        <li><strong>Hak untuk menghapus:</strong> Anda berhak menghapus Data Pribadi Anda dalam kondisi tertentu;</li>
                        <li><strong>Hak untuk membatasi pemrosesan:</strong> Anda berhak membatasi pemrosesan Data Pribadi Anda;</li>
                        <li><strong>Hak portabilitas data:</strong> Anda berhak mendapatkan Data Pribadi dalam format yang dapat dibaca mesin;</li>
                        <li><strong>Hak untuk menolak:</strong> Anda berhak menolak pemrosesan Data Pribadi Anda untuk kepentingan tertentu;</li>
                        <li><strong>Hak untuk mengajukan keberatan:</strong> Anda berhak mengajukan keberatan terhadap pemrosesan Data Pribadi Anda.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-2">Keamanan Data Pribadi</h4>
                    <p class="mb-3">Kami berkomitmen untuk menjaga keamanan Data Pribadi Anda dengan:</p>

                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li>Menerapkan langkah-langkah teknis dan organisasi yang sesuai untuk melindungi Data Pribadi;</li>
                        <li>Melakukan audit keamanan secara berkala;</li>
                        <li>Melatih karyawan kami tentang praktik keamanan Data Pribadi;</li>
                        <li>Menggunakan enkripsi untuk data sensitif;</li>
                        <li>Membatasi akses Data Pribadi hanya kepada personel yang berwenang;</li>
                        <li>Menggunakan sistem pemantauan keamanan 24/7;</li>
                        <li>Mengikuti standar keamanan internasional untuk perlindungan data.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-2">Kontak Kami</h4>
                    <p>Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini atau ingin menggunakan hak Anda sebagai subjek Data Pribadi, silakan hubungi kami melalui:</p>
                    <ul class="list-disc list-inside space-y-1 ml-4 mt-2">
                        <li>Email: privacy@waparagon.com</li>
                        <li>Telepon: +62 812-3456-7890</li>
                        <li>Alamat: Jakarta, Indonesia</li>
                    </ul>
                </div>

                <div class="text-xs text-gray-500 mt-4 pt-4 border-t">
                    <p>Kebijakan Privasi ini terakhir diperbarui pada: {{ date('d F Y') }}</p>
                    <p>Dengan menggunakan layanan kami, Anda menyetujui pengumpulan dan penggunaan informasi sesuai dengan Kebijakan Privasi ini.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Terms & Conditions Modal --}}
<div id="terms-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-2xl font-bold text-gray-900">Syarat dan Ketentuan</h3>
                <button type="button" id="close-terms-modal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="max-h-96 overflow-y-auto text-sm text-gray-700 space-y-4">
                <div>
                    <h4 class="font-bold text-lg mb-2">1. Penerimaan Syarat</h4>
                    <p>Dengan mengakses dan menggunakan layanan WA Paragon, Anda menerima dan menyetujui untuk terikat oleh Syarat dan Ketentuan yang ditetapkan di sini. Jika Anda tidak menyetujui syarat-syarat ini, mohon untuk tidak menggunakan layanan kami.</p>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-2">2. Deskripsi Layanan</h4>
                    <p>WA Paragon adalah platform manajemen formulir yang memungkinkan pengguna untuk membuat, mengelola, dan mengumpulkan respons formulir melalui berbagai saluran komunikasi termasuk WhatsApp dan email.</p>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-2">3. Penggunaan Layanan</h4>
                    <p>Anda setuju untuk menggunakan layanan kami hanya untuk tujuan yang sah dan tidak melanggar hukum. Anda tidak diperbolehkan:</p>
                    <ul class="list-disc list-inside space-y-1 ml-4">
                        <li>Menggunakan layanan untuk mengirim spam atau konten yang tidak diinginkan</li>
                        <li>Mengunggah konten yang melanggar hak cipta atau hak kekayaan intelektual orang lain</li>
                        <li>Mencoba mengakses sistem kami tanpa izin</li>
                        <li>Menggunakan layanan untuk tujuan yang melanggar hukum atau tidak etis</li>
                        <li>Membagikan kredensial akun Anda dengan pihak lain</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-2">4. Akun Pengguna</h4>
                    <p>Untuk menggunakan layanan kami, Anda mungkin perlu membuat akun. Anda bertanggung jawab untuk:</p>
                    <ul class="list-disc list-inside space-y-1 ml-4">
                        <li>Memberikan informasi yang akurat dan terkini</li>
                        <li>Menjaga kerahasiaan kata sandi Anda</li>
                        <li>Memberitahu kami segera jika ada penggunaan yang tidak sah dari akun Anda</li>
                        <li>Bertanggung jawab atas semua aktivitas yang terjadi di akun Anda</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-2">5. Privasi dan Data</h4>
                    <p>Kami menghormati privasi Anda. Pengumpulan dan penggunaan data pribadi Anda diatur oleh Kebijakan Privasi kami. Dengan menggunakan layanan kami, Anda menyetujui pengumpulan dan penggunaan data sesuai dengan kebijakan tersebut.</p>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-2">6. Konten Pengguna</h4>
                    <p>Anda bertanggung jawab atas konten yang Anda unggah atau kirim melalui platform kami. Anda menjamin bahwa:</p>
                    <ul class="list-disc list-inside space-y-1 ml-4">
                        <li>Anda memiliki hak untuk menggunakan dan membagikan konten tersebut</li>
                        <li>Konten tidak melanggar hak orang lain</li>
                        <li>Konten tidak mengandung materi yang ilegal, menghina, atau berbahaya</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-2">7. Ketersediaan Layanan</h4>
                    <p>Kami berusaha untuk menjaga layanan kami tersedia 24/7, namun kami tidak menjamin ketersediaan tanpa gangguan. Layanan dapat dihentikan untuk pemeliharaan atau pembaruan.</p>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-2">8. Pembatasan Tanggung Jawab</h4>
                    <p>Dalam batas maksimal yang diizinkan oleh hukum, kami tidak bertanggung jawab atas:</p>
                    <ul class="list-disc list-inside space-y-1 ml-4">
                        <li>Keusakan tidak langsung, khusus, atau konsekuensial</li>
                        <li>Keusakan data atau kehilangan keuntungan</li>
                        <li>Gangguan layanan karena faktor di luar kendali kami</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-2">9. Perubahan Syarat</h4>
                    <p>Kami berhak mengubah Syarat dan Ketentuan ini kapan saja. Perubahan akan diberitahu melalui email atau pemberitahuan di platform kami. Penggunaan berkelanjutan berarti Anda menerima perubahan tersebut.</p>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-2">10. Hukum yang Berlaku</h4>
                    <p>Syarat dan Ketentuan ini diatur oleh hukum Republik Indonesia. Setiap perselisihan akan diselesaikan melalui pengadilan yang berwenang di Indonesia.</p>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-2">11. Kontak</h4>
                    <p>Jika Anda memiliki pertanyaan tentang Syarat dan Ketentuan ini, silakan hubungi kami di support@waparagon.com</p>
                </div>

                <div class="text-xs text-gray-500 mt-4 pt-4 border-t">
                    <p>Syarat dan Ketentuan ini terakhir diperbarui pada: {{ date('d F Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Modal functionality
document.addEventListener('DOMContentLoaded', function() {
    // Privacy Policy Modal
    const privacyModal = document.getElementById('privacy-policy-modal');
    const privacyLinks = document.querySelectorAll('a[href="#privacy-policy"]');
    const closePrivacyBtn = document.getElementById('close-privacy-modal');

    privacyLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            privacyModal.classList.remove('hidden');
        });
    });

    closePrivacyBtn.addEventListener('click', function() {
        privacyModal.classList.add('hidden');
    });

    // Terms Modal
    const termsModal = document.getElementById('terms-modal');
    const termsLinks = document.querySelectorAll('a[href="#terms"]');
    const closeTermsBtn = document.getElementById('close-terms-modal');

    termsLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            termsModal.classList.remove('hidden');
        });
    });

    closeTermsBtn.addEventListener('click', function() {
        termsModal.classList.add('hidden');
    });

    // Global functions for onclick handlers
    window.openPrivacyModal = function() {
        privacyModal.classList.remove('hidden');
    };

    window.openTermsModal = function() {
        termsModal.classList.remove('hidden');
    };

    // Close modals when clicking outside
    [privacyModal, termsModal].forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });
});
</script>