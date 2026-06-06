# Narasi Penjelasan Logika Source Code Backend Bab 3

Dokumen ini berisi narasi penjelasan logika untuk setiap gambar potongan kode backend pada Bab 3, khususnya Gambar 5 sampai Gambar 17. Narasi ditulis dengan gaya ilmiah agar dapat disisipkan setelah gambar atau setelah paragraf pengantar masing-masing implementasi.

---

## Gambar 5. Kode Routing Sistem pada Laravel

Potongan kode routing pada Gambar 5 menunjukkan mekanisme Laravel dalam memetakan alamat URL ke controller atau komponen yang bertanggung jawab memproses permintaan pengguna. Route berperan sebagai lapisan awal yang menentukan alur request sebelum masuk ke logika bisnis. Pada sistem ini, route dibedakan menjadi beberapa kelompok, yaitu route publik, route pelanggan yang memerlukan autentikasi, route admin, route pembayaran, route booking workshop, dan route webhook Tripay.

Secara logis, route publik seperti halaman beranda, toko, detail produk, dan informasi workshop dapat diakses tanpa proses login karena hanya menampilkan informasi umum. Sebaliknya, fitur seperti checkout, riwayat pesanan, pembayaran, dan pemesanan workshop diletakkan di dalam middleware autentikasi agar hanya pengguna yang sudah terdaftar dan masuk ke sistem yang dapat mengakses proses transaksi. Pemisahan ini penting untuk menjaga keamanan data pengguna dan mencegah pihak tidak berwenang melakukan transaksi.

Route webhook Tripay dibuat dengan karakteristik khusus karena endpoint tersebut menerima callback dari sistem eksternal, bukan dari pengguna yang memiliki session login. Oleh karena itu, endpoint webhook tidak bergantung pada middleware autentikasi pengguna, tetapi menggunakan verifikasi signature dari Tripay. Dengan rancangan tersebut, sistem tetap dapat menerima pembaruan status pembayaran secara otomatis tanpa mengurangi aspek keamanan backend.

## Gambar 6. Kode Model Order

Gambar 6 menampilkan model `Order` yang berfungsi sebagai representasi data transaksi pembelian produk pada sistem e-commerce. Model ini menghubungkan kode program Laravel dengan tabel order di database melalui Eloquent ORM. Setiap objek `Order` merepresentasikan satu transaksi yang dibuat oleh pengguna, mulai dari informasi pemilik pesanan, nomor order, status transaksi, status pembayaran, subtotal, ongkos kirim, diskon, total pembayaran, alamat pengiriman, metode pengiriman, metode pembayaran, hingga referensi transaksi Tripay.

Penggunaan atribut `$fillable` menunjukkan bahwa sistem membatasi kolom mana saja yang dapat diisi secara massal. Pembatasan ini penting untuk mencegah perubahan data yang tidak seharusnya dilakukan melalui request pengguna. Selain itu, atribut `$casts` digunakan untuk mengubah tipe data tertentu secara otomatis, misalnya alamat pengiriman dan respons Tripay menjadi array, serta waktu pembayaran menjadi objek datetime. Dengan demikian, data yang berasal dari database dapat diproses lebih mudah dan konsisten oleh aplikasi.

Relasi `user()` menjelaskan bahwa satu order dimiliki oleh satu pengguna, sedangkan relasi `items()` menunjukkan bahwa satu order dapat memiliki banyak item pesanan. Relasi ini mendukung proses transaksi yang umum pada e-commerce, yaitu satu pengguna dapat melakukan banyak pesanan, dan satu pesanan dapat terdiri atas beberapa produk. Selain itu, model juga menyediakan mekanisme perubahan status melalui `updateOrderStatus()`, sehingga setiap perubahan status dapat dicatat ke histori order. Hal ini membuat pengelolaan transaksi menjadi lebih tertelusur dan mendukung kebutuhan audit data.

## Gambar 7. Kode Model Workshop Booking

Model `WorkshopBooking` pada Gambar 7 digunakan untuk merepresentasikan data reservasi workshop. Model ini dipisahkan dari model `Order` karena transaksi workshop memiliki karakteristik bisnis yang berbeda dari pembelian produk. Pembelian produk umumnya selesai dalam satu transaksi checkout, sedangkan booking workshop perlu menyimpan jadwal, jumlah peserta, data pelanggan, status booking, status pembayaran, deposit, dan sisa pembayaran.

Daftar atribut `$fillable` menunjukkan bahwa data booking disimpan secara terstruktur, mulai dari relasi ke pengguna, jadwal workshop, nomor booking, identitas pelanggan, jumlah peserta, total biaya, nominal deposit, sisa pembayaran, hingga status reservasi. Pemisahan antara `status` dan `payment_status` juga penting karena status kehadiran atau validitas booking tidak selalu sama dengan status pembayaran. Sebagai contoh, booking dapat berstatus pending meskipun pembayaran deposit belum dilakukan, atau booking dapat dikonfirmasi setelah pembayaran memenuhi syarat.

Relasi `payments()` menunjukkan bahwa satu booking dapat memiliki lebih dari satu data pembayaran. Rancangan ini mendukung skema pembayaran bertahap, misalnya pembayaran deposit terlebih dahulu kemudian pelunasan di tahap berikutnya. Dengan pendekatan tersebut, sistem backend lebih fleksibel dalam menyesuaikan proses bisnis workshop Batik Giri Alam, sekaligus tetap menjaga pencatatan pembayaran secara rinci.

## Gambar 8. Kode Inisialisasi Checkout

Gambar 8 menunjukkan proses inisialisasi fitur checkout yang dijalankan ketika pengguna masuk ke halaman pembayaran. Pada tahap ini, sistem menyiapkan data awal yang diperlukan untuk transaksi, seperti data pengguna, alamat pengiriman, daftar item pada keranjang, subtotal, total berat produk, pilihan pengiriman, dan pilihan metode pembayaran. Inisialisasi ini penting karena checkout merupakan proses bertahap yang bergantung pada kelengkapan data dari beberapa sumber.

Secara logis, backend terlebih dahulu memastikan bahwa pengguna memiliki data keranjang dan alamat yang valid. Data keranjang digunakan untuk menghitung subtotal dan berat total, sedangkan alamat digunakan untuk menentukan tujuan pengiriman. Setelah data dasar tersedia, sistem dapat melanjutkan ke proses perhitungan ongkos kirim dan pemilihan metode pembayaran. Dengan cara ini, proses checkout tidak langsung membuat order, tetapi memastikan semua prasyarat transaksi telah terpenuhi.

Penggunaan Livewire pada bagian checkout membuat perubahan data dapat diproses secara dinamis. Ketika pengguna mengganti alamat, jasa pengiriman, atau metode pembayaran, backend dapat memperbarui nilai ongkir dan total pembayaran tanpa memuat ulang seluruh halaman. Hal ini menunjukkan bahwa backend tidak hanya menyimpan transaksi, tetapi juga mengatur alur validasi dan perhitungan selama proses pembelian berlangsung.

## Gambar 9. Pemilihan Metode Pembayaran pada Checkout

Potongan kode pada Gambar 9 menjelaskan mekanisme pemilihan metode pembayaran saat checkout. Sistem menyediakan beberapa metode pembayaran yang terhubung dengan Tripay, seperti virtual account, QRIS, e-wallet, atau pembayaran melalui gerai tertentu. Ketika pengguna memilih salah satu metode, backend menyimpan kode metode tersebut sebagai bagian dari data transaksi yang akan dikirim ke payment gateway.

Logika pemilihan metode pembayaran tidak hanya berfungsi sebagai tampilan pilihan bagi pengguna, tetapi juga menentukan payload transaksi yang akan dibuat. Setiap metode pembayaran memiliki kode dan ketentuan yang berbeda, sehingga backend harus memastikan bahwa metode yang dipilih valid dan termasuk dalam daftar metode yang didukung. Validasi ini mencegah pengguna mengirim nilai pembayaran yang tidak dikenali oleh sistem atau tidak tersedia pada konfigurasi Tripay.

Setelah metode pembayaran dipilih, nilai total order digunakan sebagai dasar pembuatan transaksi pembayaran. Dengan demikian, data checkout yang dikirim ke Tripay telah berisi informasi penting seperti nomor referensi, jumlah pembayaran, identitas pelanggan, item transaksi, metode pembayaran, callback URL, dan return URL. Alur ini membuat proses pembayaran menjadi terintegrasi dan mengurangi kebutuhan pencatatan manual.

## Gambar 10. Kode Service Perhitungan Ongkir RajaOngkir

Gambar 10 menunjukkan penggunaan service khusus untuk menghitung ongkos kirim melalui API RajaOngkir. Service ini memisahkan logika integrasi eksternal dari controller atau komponen checkout, sehingga kode menjadi lebih modular dan mudah dipelihara. Parameter utama yang dikirim ke API meliputi lokasi asal, lokasi tujuan, berat total produk, dan kurir yang dipilih.

Pada prosesnya, backend membentuk payload request berdasarkan data transaksi pengguna, kemudian mengirimkan request ke endpoint RajaOngkir menggunakan API key yang disimpan pada konfigurasi sistem. Jika respons berhasil diterima, data layanan pengiriman akan dikembalikan dan diformat agar dapat ditampilkan pada halaman checkout. Data tersebut umumnya mencakup nama kurir, jenis layanan, deskripsi layanan, estimasi pengiriman, dan biaya ongkir.

Service ini juga dilengkapi penanganan kegagalan, seperti API tidak merespons, timeout, atau respons tidak sesuai. Ketika terjadi kendala, sistem mencatat log kesalahan dan menyediakan data fallback agar proses checkout tetap dapat berlanjut. Strategi ini menunjukkan bahwa backend dirancang untuk menjaga ketersediaan layanan dan mengurangi risiko transaksi gagal hanya karena gangguan pada API pihak ketiga.

## Gambar 11. Kode Service Pembayaran Tripay

Gambar 11 menjelaskan service pembayaran yang menghubungkan backend dengan payment gateway Tripay. Service ini bertugas membuat transaksi pembayaran berdasarkan data order atau booking yang sudah divalidasi oleh sistem. Data yang dikirim meliputi metode pembayaran, merchant reference, nominal pembayaran, identitas pelanggan, daftar item, callback URL, return URL, dan signature keamanan.

Bagian penting dari logika Tripay adalah pembentukan signature menggunakan algoritma HMAC SHA-256. Signature dibuat dari kombinasi merchant code, merchant reference, dan jumlah pembayaran, kemudian dienkripsi menggunakan private key. Signature ini berfungsi untuk memastikan bahwa request transaksi benar-benar berasal dari sistem yang sah dan tidak dimodifikasi oleh pihak lain sebelum diterima oleh Tripay.

Setelah request dikirim, respons dari Tripay dinormalisasi agar lebih mudah digunakan oleh sistem. Informasi seperti reference, checkout URL, payment code, QR URL, status pembayaran, biaya admin, dan waktu kedaluwarsa disimpan dalam format yang konsisten. Normalisasi ini penting karena setiap metode pembayaran dapat memiliki bentuk data yang berbeda, misalnya virtual account menggunakan `pay_code`, sedangkan QRIS menggunakan `qr_url`.

## Gambar 12. Kode Controller Pembayaran Order

Gambar 12 menunjukkan controller pembayaran order yang mengatur akses pengguna ke halaman pembayaran. Logika utama pada bagian ini adalah memastikan bahwa order yang dibuka benar-benar milik pengguna yang sedang login. Pemeriksaan kepemilikan order penting untuk mencegah pengguna melihat atau memproses pembayaran milik pengguna lain.

Setelah validasi akses terpenuhi, controller mengambil data order beserta informasi pembayaran yang tersimpan, seperti referensi Tripay, metode pembayaran, status pembayaran, dan total nominal yang harus dibayar. Data tersebut kemudian dikirim ke tampilan pembayaran agar pengguna dapat menyelesaikan pembayaran sesuai instruksi dari payment gateway. Dengan demikian, controller berperan sebagai penghubung antara data transaksi di database dan halaman pembayaran yang digunakan oleh pelanggan.

Controller ini juga mendukung alur redirect setelah pembayaran, seperti halaman sukses atau gagal. Alur tersebut membantu pengguna memahami status transaksi setelah kembali dari payment gateway. Dari sisi backend, pola ini menjaga agar proses pembayaran tetap berada dalam alur yang terkendali dan sesuai dengan data order yang sudah dibuat sebelumnya.

## Gambar 13. Kode Proses Penyimpanan Booking Workshop

Gambar 13 menjelaskan proses penyimpanan booking workshop ketika pengguna memilih workshop, jadwal, dan jumlah peserta. Backend terlebih dahulu melakukan validasi terhadap data yang dikirim, seperti identitas pelanggan, pilihan workshop, jadwal tersedia, slot waktu, dan jumlah peserta. Validasi ini diperlukan untuk memastikan bahwa booking dibuat berdasarkan jadwal yang benar dan tidak melebihi kapasitas yang tersedia.

Setelah data dinyatakan valid, sistem menghitung total biaya berdasarkan harga workshop dan jumlah peserta. Dari total tersebut, sistem menentukan nilai deposit dan sisa pembayaran. Nilai ini kemudian disimpan ke dalam model `WorkshopBooking` bersama nomor booking yang dibuat secara unik. Nomor booking berfungsi sebagai identitas reservasi yang dapat digunakan pelanggan maupun admin untuk melacak transaksi.

Proses penyimpanan booking juga menunjukkan bahwa backend tidak hanya menerima input dari pengguna, tetapi menerapkan aturan bisnis reservasi. Aturan tersebut mencakup keterkaitan antara jadwal, kapasitas, jumlah peserta, status booking, dan status pembayaran. Dengan rancangan ini, data booking yang tersimpan menjadi lebih valid dan dapat digunakan untuk proses pembayaran serta pengelolaan workshop oleh admin.

## Gambar 14. Kode Proses Pembayaran Booking Workshop

Gambar 14 menampilkan logika pembayaran untuk booking workshop. Berbeda dengan pembayaran order produk, pembayaran workshop dapat dilakukan melalui beberapa skema, seperti deposit, pelunasan sisa pembayaran, atau pembayaran penuh. Oleh karena itu, backend membuat data pembayaran workshop secara terpisah dari data booking utama.

Secara logis, sistem menentukan jenis pembayaran yang dipilih pengguna, menghitung nominal yang harus dibayar, lalu membuat record `WorkshopPayment`. Setelah record pembayaran dibuat, backend mengirimkan data pembayaran tersebut ke Tripay untuk menghasilkan instruksi pembayaran. Data respons dari Tripay kemudian disimpan pada pembayaran terkait agar sistem dapat menampilkan kode bayar, QRIS, batas waktu pembayaran, dan status transaksi.

Pemisahan antara booking dan pembayaran memberikan keuntungan dari sisi fleksibilitas dan audit. Satu booking dapat memiliki beberapa pembayaran dengan status yang berbeda, misalnya deposit berhasil tetapi pelunasan belum dilakukan. Dengan demikian, backend mampu mendukung model bisnis workshop yang lebih realistis dan memudahkan admin dalam memantau perkembangan pembayaran setiap reservasi.

## Gambar 15. Kode Webhook Tripay

Gambar 15 menunjukkan mekanisme webhook yang digunakan untuk menerima notifikasi pembayaran dari Tripay secara otomatis. Ketika terjadi perubahan status pembayaran, Tripay mengirim callback ke endpoint webhook backend. Backend kemudian membaca payload request dan mengambil signature dari header callback.

Langkah paling penting pada webhook adalah verifikasi signature. Sistem menghitung ulang signature menggunakan payload mentah dan private key, kemudian membandingkannya dengan signature yang dikirim oleh Tripay menggunakan `hash_equals()`. Perbandingan ini penting karena bersifat aman terhadap timing attack dan memastikan callback benar-benar berasal dari Tripay. Jika signature tidak valid, request ditolak dan status pembayaran tidak diperbarui.

Setelah signature valid, backend membaca data penting seperti merchant reference, reference Tripay, dan status pembayaran. Data tersebut digunakan untuk menemukan order atau pembayaran workshop yang sesuai di database. Dengan mekanisme webhook, perubahan status pembayaran dapat dilakukan secara otomatis tanpa menunggu konfirmasi manual dari admin.

## Gambar 16. Kode Update Status Pembayaran Order

Gambar 16 menjelaskan logika perubahan status order setelah pembayaran dikonfirmasi. Ketika webhook atau sinkronisasi status menunjukkan bahwa pembayaran telah berstatus paid, sistem memeriksa apakah order masih berada pada status pending. Jika kedua kondisi tersebut terpenuhi, status order diperbarui menjadi processing.

Pemeriksaan status awal diperlukan agar perubahan status tidak dilakukan berulang atau menimpa kondisi lain yang sudah terjadi. Misalnya, apabila order sudah dikirim atau dibatalkan, sistem tidak seharusnya mengembalikannya ke status processing hanya karena callback pembayaran diterima ulang. Hal ini membuat proses webhook menjadi lebih aman dan idempotent, yaitu tetap menghasilkan kondisi data yang konsisten meskipun callback diterima lebih dari satu kali.

Perubahan status dilakukan melalui method khusus pada model order agar setiap perubahan dapat dicatat pada histori status. Catatan ini berguna untuk pelacakan transaksi, audit internal, dan kebutuhan layanan pelanggan. Dengan demikian, backend tidak hanya memperbarui nilai status, tetapi juga menyimpan jejak perubahan yang menjelaskan kapan dan mengapa status berubah.

## Gambar 17. Kode Pengelolaan Order oleh Admin

Gambar 17 menunjukkan fitur pengelolaan order oleh admin. Pada bagian ini, admin dapat memperbarui status pesanan berdasarkan kondisi operasional, misalnya dari pending menjadi processing, shipped, delivered, atau cancelled. Sebelum status diperbarui, backend melakukan validasi agar nilai status yang dikirim hanya berasal dari daftar status yang diperbolehkan.

Validasi input diperlukan untuk menjaga konsistensi data order. Status pesanan tidak boleh diisi sembarang nilai karena status tersebut digunakan oleh banyak bagian sistem, seperti dashboard admin, riwayat pelanggan, dan histori transaksi. Selain status, admin juga dapat menambahkan catatan untuk menjelaskan alasan perubahan, misalnya informasi pengiriman atau alasan pembatalan.

Proses pembaruan status menggunakan method yang sama dengan mekanisme otomatis dari webhook, yaitu method yang mencatat histori perubahan. Dengan pendekatan ini, baik perubahan otomatis dari payment gateway maupun perubahan manual oleh admin tetap melewati jalur logika yang konsisten. Fitur ini penting karena tidak semua kondisi transaksi dapat diselesaikan secara otomatis; admin tetap memerlukan mekanisme pengendalian manual untuk menangani kasus khusus dalam operasional Batik Giri Alam.

---

## Penutup Narasi Bab 3

Berdasarkan potongan kode pada Gambar 5 sampai Gambar 17, backend sistem Batik Giri Alam telah dirancang menggunakan pola yang terstruktur. Route mengatur akses dan alur request, model merepresentasikan data serta relasi antar entitas, service mengelola integrasi dengan API eksternal, controller menangani proses transaksi, dan webhook menyinkronkan status pembayaran secara otomatis. Struktur tersebut menunjukkan bahwa backend tidak hanya berfungsi sebagai penghubung antara frontend dan database, tetapi juga sebagai pusat pengelolaan logika bisnis, validasi, keamanan transaksi, serta integrasi layanan e-commerce dan booking workshop.
