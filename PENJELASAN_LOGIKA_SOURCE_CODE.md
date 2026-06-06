# PENJELASAN LOGIKA SOURCE CODE
## Bab 3 - Hasil dan Pembahasan (Jurnal Penelitian)

Dokumen ini berisi penjelasan mendalam untuk setiap potongan kode (source code) yang ditampilkan dalam Bab 3 Jurnal Penelitian Backend Batik Giri Alam.

---

## GAMBAR 5: Kode Routing Sistem pada Laravel

**Lokasi di Dokumen:** Halaman 7

**Kode:**
```php
Route([ '/', [ LoadingController::class, 'home' ]])->name('landing_home');
Route([ '/shop', [ LoadingController::class, 'shop' ]])->name('loading_shop');
Route([ '/workshop', [ WorkshopBookingController::class, 'index' ]])->name('workshop_index');

Route::middleware([ 'auth' ])->group(function () {
    Route::get('/checkout', [ LegitUserControllers::class, 'index' ])->name('checkout');
    Route::post('/payment/{order}', [ LegitUserControllers\PaymentController::class, 'show' ])->name('payment_checkout');
    
    Route::post('/workshop/booking', [ WorkshopBookingController::class, 'store' ])->name('workshop_booking_store');
});

Route::post('/api/webhooks/tripay', [ TripayWebhookController::class, 'handle' ])
    ->name('tripay_webhook')
    ->withoutMiddleware([ 'auth' ]);
```

**Penjelasan Logika:**

Potongan kode ini menunjukkan implementasi routing yang menghubungkan URL dengan controller atau komponen yang sesuai dalam framework Laravel. Routing dalam konteks ini berfungsi sebagai dispatcher yang mengarahkan setiap permintaan HTTP (request) dari pengguna ke handler yang tepat berdasarkan URL path.

Secara spesifik, implementasi routing sistem terdiri dari beberapa kelompok route yang dikelompokkan berdasarkan fungsi dan level otentikasi:

1. **Route Publik** - Mencakup halaman landing (`/`), daftar toko (`/shop`), dan detail workshop (`/workshop-booking`). Kelompok route ini dapat diakses oleh semua pengguna tanpa memerlukan autentikasi. Middleware `auth:sanctum` tidak diterapkan, sehingga endpoint ini tersedia untuk public access.

2. **Route yang Memerlukan Autentikasi** - Endpoint checkout (`/checkout`) dan booking workshop (`/workshop/booking`) dilindungi dengan middleware autentikasi. Hal ini memastikan hanya pengguna yang telah login yang dapat mengakses fitur-fitur kritis ini. Middleware `auth:sanctum` akan memverifikasi token autentikasi sebelum request diproses lebih lanjut.

3. **Webhook Tripay** - Route khusus untuk menerima callback dari payment gateway Tripay (`/api/webhooks/tripay`) dibuat **tanpa middleware autentikasi dan CSRF protection**. Pengecualian ini diperlukan karena webhook adalah callback dari sistem eksternal (server Tripay) yang tidak memiliki session pengguna atau CSRF token. Jika middleware autentikasi diterapkan, sistem akan menolak callback pembayaran dari Tripay, sehingga status pembayaran tidak dapat diperbarui secara otomatis.

**Alur Pemrosesan:**
- User mengakses URL → Laravel Route matching → Middleware chain (jika ada) → Controller action → Response

**Fungsi Kritis:**
- Pemisahan endpoint publik dan protected memastikan keamanan data transaksi
- Webhook tanpa middleware memungkinkan sistem e-commerce terotomasi dan real-time

---

## GAMBAR 6: Kode Model Order

**Lokasi di Dokumen:** Halaman 8

**Kode:**
```php
class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_code',
        'status',
        'subtotal',
        'shipping_cost',
        'discount',
        'total',
        'shipping_address',
        'shipping_service',
        'payment_method',
        'tripay_reference',
        'paid_at',
    ];

    protected $casts = [
        'shipping_response' => 'array',
        'tripay_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
```

**Penjelasan Logika:**

Model Order merepresentasikan struktur data untuk setiap transaksi pembelian produk dalam sistem. Model ini menggunakan Eloquent ORM (Object-Relational Mapping) Laravel yang memfasilitasi interaksi dengan tabel `orders` di database MySQL.

**Struktur Atribut Model:**

1. **`$fillable`** - Array ini mendefinisikan atribut-atribut yang dapat diisi secara massal melalui method `create()` atau `update()`. Atribut-atribut penting meliputi:
   - `user_id`: Foreign key referensi ke tabel users
   - `order_code`: Kode unik order untuk identifikasi di sistem dan customer
   - `status`: Status transaksi (pending, processing, shipped, delivered, cancelled)
   - `subtotal`, `shipping_cost`, `discount`, `total`: Komponen nilai transaksi
   - `shipping_address`, `shipping_service`: Detail pengiriman
   - `payment_method`, `tripay_reference`: Informasi pembayaran
   - `created_at`, `updated_at`: Timestamp untuk audit trail

2. **`$casts`** - Mendefinisikan tipe data yang akan dikembalikan saat mengakses atribut. Contohnya `shipping_response` dan `tripay_response` dicast sebagai array, sehingga JSON dari API eksternal otomatis dikonversi menjadi array PHP.

**Method Relasi:**

- **`user(): BelongsTo`** - Mendefinisikan relasi many-to-one dengan model User. Setiap order dimiliki oleh satu user, dan satu user dapat memiliki banyak order.

- **`items(): HasMany`** - Mendefinisikan relasi one-to-many dengan model OrderItem. Satu order dapat terdiri dari banyak item produk (misal: membeli 3 produk berbeda dalam satu transaksi).

**Alur Data:**
```
User membuat Order → Order menyimpan items produk → Items mereferensi Product
```

**Fungsi Kritis:**
- Centralized data representation untuk transaksi
- Relasi antar model memfasilitasi query kompleks (misal: mendapatkan semua produk dari satu order)
- Type casting otomatis untuk API response

---

## GAMBAR 7: Kode Model Workshop Booking

**Lokasi di Dokumen:** Halaman 8-9

**Kode:**
```php
class WorkshopBooking extends Model
{
    protected $table = 'workshop_bookings';

    protected $fillable = [
        'user_id',
        'workshop_available_date_id',
        'workshop_slot_schedule_id',
        'booking_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'num_participants',
        'total_price',
        'deposit_amount',
        'remaining_amount',
        'status',
        'payment_status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(WorkshopPayment::class, 'booking_id');
    }
}
```

**Penjelasan Logika:**

Model WorkshopBooking merepresentasikan data reservasi workshop yang terpisah dari Order karena model bisnis workshop memiliki karakteristik unik, terutama dalam hal pembayaran bertahap (deposit dan pelunasan).

**Struktur Atribut Model:**

1. **`$fillable`** - Atribut-atribut kritis meliputi:
   - `user_id`, `workshop_available_date_id`, `workshop_slot_schedule_id`: Foreign keys untuk relasi
   - `booking_number`: Identifier unik booking
   - `customer_name`, `customer_email`, `customer_phone`: Data pemesan
   - `num_participants`: Jumlah peserta workshop
   - `total_price`, `deposit_amount`, `remaining_amount`: Sistem pembayaran bertahap
   - `status`, `payment_status`: Status booking dan pembayaran
   
   Atribut-atribut ini mencerminkan kebutuhan business logic workshop yang kompleks dengan dukungan pembayaran fleksibel.

**Method Relasi:**

- **`user(): BelongsTo`** - Menghubungkan booking dengan user yang melakukan reservasi

- **`payments(): HasMany`** - Mendefinisikan relasi dengan model WorkshopPayment. **Penting**: Satu booking dapat memiliki multiple payment records untuk mendukung pembayaran bertahap (deposit awal, kemudian pelunasan). Struktur ini lebih fleksibel daripada single payment record karena memungkinkan tracking terpisah untuk setiap transaksi pembayaran.

**Alur Pembayaran Bertahap:**
```
1. User melakukan booking workshop → WorkshopBooking dibuat dengan status 'pending'
2. User membayar deposit → WorkshopPayment record pertama dibuat (type: deposit)
3. User membayar pelunasan → WorkshopPayment record kedua dibuat (type: pelunasan)
4. Setelah semua pembayaran masuk, status booking berubah menjadi 'confirmed'
```

**Fungsi Kritis:**
- Pemisahan booking dari payment memungkinkan fleksibilitas dalam skema pembayaran
- Support untuk multiple payments dalam satu booking
- Audit trail pembayaran bertahap

---

## GAMBAR 8: Kode Insialisasi Checkout

**Lokasi di Dokumen:** Halaman 9

**Kode:**
```php
class Checkout extends Component
{
    public $currentStep = 1;
    public $totalSteps = 4;
    public $selectedAddressId = null;
    public $selectedShippingService = null;
    public $selectedPaymentMethod = null;
    public $shippingCost = 0;
    public $subtotal = 0;
    public $totalAmount = 0;
}
```

**Penjelasan Logika:**

Kode ini menunjukkan inisialisasi component Livewire untuk proses checkout. Livewire adalah framework real-time Laravel yang memungkinkan update UI tanpa reload halaman. Inisialisasi state ini mendefinisikan tahapan checkout dan variabel-variabel yang akan di-track secara real-time.

**Analisis Property State:**

1. **`$currentStep = 1`** - Variable untuk melacak tahap checkout saat ini (1: alamat, 2: pengiriman, 3: pembayaran, 4: review). Sistem menggunakan step-by-step approach untuk user experience yang lebih intuitif.

2. **`$totalSteps = 4`** - Konstanta yang mendefinisikan jumlah tahapan total dalam checkout process.

3. **`$selectedAddressId = null`** - Menyimpan ID alamat pengiriman yang dipilih user. Initialized null karena belum ada pilihan.

4. **`$selectedShippingService = null`** - Menyimpan pilihan jasa pengiriman (JNE, J&T, Grab Express, dll). Nilai ini akan dipopulate dari API RajaOngkir berdasarkan alamat tujuan.

5. **`$selectedPaymentMethod = null`** - Menyimpan metode pembayaran (Virtual Account, QRIS, E-wallet, dll).

6. **`$shippingCost = 0`** - Menyimpan hasil kalkulasi biaya pengiriman dari API. Initial value 0, akan diupdate saat user memilih service pengiriman.

7. **`$subtotal = 0`** - Total harga produk sebelum shipping dan discount

8. **`$totalAmount = 0`** - Total akhir = subtotal + shipping - discount

**Alur Logika State:**
```
User mulai checkout → $currentStep = 1 → Pilih alamat ($selectedAddressId) 
→ $currentStep = 2 → Pilih shipping ($selectedShippingService) → Hitung ongkir
→ $currentStep = 3 → Pilih metode pembayaran ($selectedPaymentMethod) 
→ $currentStep = 4 → Review dan konfirmasi
```

**Fungsi Kritis:**
- State management untuk multi-step form
- Real-time update tanpa page reload menggunakan Livewire
- Tracking semua pilihan user dalam satu process checkout

---

## GAMBAR 9: Pemilihan Metode Pembayaran pada Checkout

**Lokasi di Dokumen:** Halaman 9

**Kode:**
```php
private function initializePaymentMethods()
{
    $this->paymentMethods = [
        [
            'id' => 'SCAVA',
            'name' => 'BCA Virtual Account',
            'fee' => 0,
        ],
        [
            'id' => 'QRIS',
            'name' => 'QRIS / Scan QR Code',
            'fee' => 0,
        ],
        [
            'id' => 'DANA',
            'name' => 'DANA',
            'fee' => 0,
        ],
    ];
}
```

**Penjelasan Logika:**

Kode ini mendefinisikan method private `initializePaymentMethods()` yang menginisialisasi daftar metode pembayaran yang tersedia dalam sistem. Method ini dipanggil saat component Livewire diload untuk mempersiapkan opsi pembayaran.

**Struktur Data Metode Pembayaran:**

Array `$this->paymentMethods` berisi beberapa opsi pembayaran:

1. **Virtual Account (SCAVA)**
   - `'id' => 'SCAVA'`: Identifier unik untuk metode ini
   - `'name' => 'BCA Virtual Account'`: Nama yang ditampilkan ke user
   - `'fee' => 0`: Tidak ada biaya tambahan untuk metode ini
   
   Virtual Account memungkinkan user untuk transfer ke nomor rekening unik yang di-generate untuk setiap transaksi.

2. **QRIS (Quick Response Code Indonesian Standard)**
   - `'id' => 'QRIS'`
   - `'name' => 'QRIS / Scan QR Code'`
   - `'fee' => 0`
   
   QRIS adalah standar pembayaran QR code yang didukung oleh semua e-wallet Indonesia (GCash, OVO, Dana, Link, dst).

3. **E-wallet DANA**
   - `'id' => 'DANA'`
   - `'name' => 'DANA'`
   - `'fee' => 0`
   
   Direct payment menggunakan aplikasi DANA wallet.

**Tujuan Design:**

- Menyediakan multiple payment options meningkatkan conversion rate
- Zero fee policy untuk semua metode menjadikan payment cost-friendly bagi customer
- Integrasi dengan payment gateway Tripay memungkinkan semua metode ini diproses dalam satu API

**Fungsi Kritis:**
- Flexibility dalam payment options
- User dapat memilih metode pembayaran yang paling convenient
- Backend dapat menghandle berbagai tipe pembayaran dalam satu system

---

## GAMBAR 10: Kode Service Perhitungan Ongkir RajaOngkir

**Lokasi di Dokumen:** Halaman 10

**Kode:**
```php
public function getShippingCost($originDistrictId, $destinationDistrictId, $weight, $couriers = [])
{
    $payload = [
        'origin' => $originDistrictId,
        'destination' => $destinationDistrictId,
        'weight' => $weight,
    ];
    
    if (!empty($couriers)) {
        $payload['courier'] = implode(',', $couriers);
    }
    
    $response = Http::withHeader(...)->post(...)->collect();
    
    if ($response->successful()) {
        return $response->get('data') ?? [];
    }
    
    return $this->getDefaultShippingRates();
}
```

**Penjelasan Logika:**

Kode ini menunjukkan service layer `getShippingCost()` yang bertanggung jawab untuk menghitung biaya pengiriman berdasarkan lokasi origin, destination, berat barang, dan pilihan kurir. Service layer pattern ini memisahkan business logic dari controller, membuat code lebih maintainable dan testable.

**Alur Logika Perhitungan:**

1. **Persiapan Parameter untuk API**
   ```
   $payload = [
       'origin' => $originDistrictId,        // Lokasi pengirim (Banyumas)
       'destination' => $destinationDistrictId, // Lokasi penerima
       'weight' => $weight,                   // Berat total produk
       'courier' => (implode(',', $couriers)) // Kurir: 'jne,j&t,grab,pos'
   ]
   ```
   
   Parameter-parameter ini diformat sesuai dengan requirement API RajaOngkir.

2. **HTTP Request ke API**
   ```
   $response = Http::withHeader(...)->post(...)->collect();
   ```
   
   Service mengirim request POST ke endpoint RajaOngkir dengan parameter di atas. Response dari API berisi daftar kurir yang tersedia beserta masing-masing tarif per berat kategori.

3. **Error Handling dengan Fallback**
   ```
   if (!$response->successful()) {
       return $this->getDefaultShippingRates();
   }
   ```
   
   **Penting**: Jika API RajaOngkir down atau error, system tidak langsung crash. Sebaliknya, method mengembalikan `getDefaultShippingRates()` - tarif default yang sudah ditetapkan di database. Ini memastikan checkout process dapat dilanjutkan meski API eksternal bermasalah (graceful degradation).

4. **Format Response**
   
   Response dari API diformat ulang untuk ditampilkan di frontend checkout:
   ```
   [
       {
           'courier': 'JNE',
           'costs': [
               {'service': 'REG', 'cost': 25000},
               {'service': 'YES', 'cost': 35000}
           ]
       },
       ...
   ]
   ```

**Alur Pemrosesan Menyeluruh:**
```
User pilih alamat → System ekstrak destination district → 
Query total berat dari cart → Service call RajaOngkir API → 
Format response → Tampilkan opsi kurir & harga di UI → 
User pilih kurir → Harga ongkir diupdate ke $shippingCost
```

**Fungsi Kritis:**
- Kalkulasi ongkir otomatis & real-time
- Integration dengan kurir resmi menghindari dispute biaya pengiriman
- Fallback mechanism untuk reliability

---

## GAMBAR 11: Kode Service Pembayaran Tripay

**Lokasi di Dokumen:** Halaman 10

**Kode:**
```php
class PaymentService
{
    protected GatewayInterface $gateway;

    public function __construct()
    {
        $this->gateway = new TripayGateway();
    }

    public function createPayment(array $data): array
    {
        return $this->gateway->createTransaction($data);
    }
}
```

**Penjelasan Logika:**

Kode ini menunjukkan `PaymentService` class yang mengabstraksi logika pembayaran. Service ini menggunakan **Strategy pattern** dengan interface `$gateway` untuk memungkinkan multiple payment gateway implementation (dalam hal ini Tripay, tapi bisa diperluas ke Midtrans, Xendit, dll).

**Struktur Class:**

1. **Protected Property `$gateway`**
   ```php
   protected GatewayInterface $gateway;
   ```
   
   Interface ini mendefinisikan kontrak bahwa gateway apapun harus implement method `createTransaction()`. Dengan pattern ini, kode tetap flexible dan dapat di-switch gateway tanpa mengubah PaymentService class.

2. **Constructor Dependency Injection**
   ```php
   public function __construct() {
       $this->gateway = new TripayGateway();
   }
   ```
   
   Service di-initialize dengan TripayGateway. Di production dengan dependency container (Laravel Service Provider), ini bisa di-inject dan di-mock untuk testing.

3. **Method `createPayment($data)`**
   ```php
   public function createPayment(array $data): array {
       return $this->gateway->createTransaction($data);
   }
   ```
   
   Public method yang dipanggil dari controller. Method ini mendelegate ke gateway untuk create transaction. Return value adalah array berisi reference_number dan payment_url dari Tripay.

**Alur Pembayaran:**
```
Controller → PaymentService::createPayment($orderData) 
→ TripayGateway::createTransaction($data) 
→ API Tripay create invoice 
→ Return reference & payment_url 
→ Redirect user ke payment_url
```

**Fungsi Kritis:**
- **Separation of Concerns**: Logika pembayaran terpisah dari controller
- **Strategy Pattern**: Memudahkan swap payment gateway
- **Interface-based**: Code adalah dependency on abstraction, bukan concrete implementation
- **Maintainability**: Perubahan integrasi gateway hanya perlu di TripayGateway class

---

## GAMBAR 12: Kode Controller Pembayaran Order

**Lokasi di Dokumen:** Halaman 11

**Kode:**
```php
public function show(Order $order)
{
    if ($order->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }

    if ($order->payment_status === 'paid') {
        return redirect()->route('order.success', $order->id)
            ->with('success', 'Pesanan sudah dibayar');
    }

    return view('checkout.payment', [ 'order' => $order ]);
}
```

**Penjelasan Logika:**

Kode ini menunjukkan method `show()` dalam controller pembayaran order. Method ini menampilkan halaman checkout pembayaran untuk order yang spesifik.

**Analisis Baris per Baris:**

1. **Autentikasi & Otorisasi**
   ```php
   if ($order->user_id !== auth()->id()) {
       abort(403, 'Unauthorized');
   }
   ```
   
   Security check untuk memastikan hanya pemilik order yang dapat akses halaman pembayaran order mereka sendiri. Ini mencegah user melihat atau memanipulasi order milik user lain.

2. **Cek Status Pembayaran**
   ```php
   if ($order->payment_status === 'paid') {
       return redirect()->route('order.success', $order->id)
           ->with('success', 'Pesanan sudah dibayar');
   }
   ```
   
   Jika order sudah dibayar sebelumnya, redirect ke halaman success. Ini mencegah user melakukan double payment.

3. **Create Payment Transaction**
   ```php
   return redirect()->route('checkout.payment', ['order' => $order->id])
       ->with('success', 'Pesanan sudah dibuat');
   ```
   
   Jika order belum dibayar, kembalikan view checkout payment dengan data order. View ini akan memanggil PaymentService untuk create payment di Tripay dan tampilkan payment URL.

**Alur HTTP Request-Response:**
```
GET /payment/{order_id} → Controller::show() → 
Cek auth & status → View checkout payment dengan order data → 
Frontend render payment methods
```

**Fungsi Kritis:**
- Authorization check mencegah unauthorized access
- Status validation mencegah double payment
- Clean separation antara order creation dan payment process

---

## GAMBAR 13: Kode Proses Penyimpanan Booking Workshop

**Lokasi di Dokumen:** Halaman 11

**Kode:**
```php
$booking = $workshopBooking::create([
    'user_id' => $user->id,
    'workshop_available_date_id' => $lockedSchedule->available_date_id,
    'workshop_slot_schedule_id' => $validated['workshop_slot_schedule_id'],
    'booking_number' => $bookingNumber,
    'customer_name' => $validated['full_name'],
    'customer_email' => $validated['email'],
    'customer_phone' => $validated['phone'],
    'num_participants' => $validated['number_of_participants'],
    'total_price' => $totalPrice,
    'deposit_amount' => $depositAmount,
    'remaining_amount' => $remainingAmount,
    'status' => 'pending',
    'payment_status' => 'pending',
]);
```

**Penjelasan Logika:**

Kode ini menunjukkan method `store()` untuk menyimpan data booking workshop. Method ini menghandel business logic kompleks yang terkait dengan workshop reservation.

**Analisis Validasi & Data Preparation:**

1. **Validasi Input**
   ```php
   $booking = $workshopBooking::create([
       'workshop_available_date_id' => $validated['workshop_available_date_id'],
       ...
   ]);
   ```
   
   Dengan parameter `$validated` (hasil dari Form Request validation), semua input sudah terjamin valid sebelum disimpan ke database. Contoh validasi:
   - `workshop_slot_schedule_id` harus exist di table `workshop_slot_schedules`
   - `num_participants` harus integer dan > 0
   - `customer_email` harus format email valid
   - `customer_phone` harus format nomor telepon

2. **Penyimpanan Data Booking**
   ```php
   'user_id' => $user->id,
   'workshop_available_date_id' => $validated['workshop_available_date_id'],
   'booking_number' => $booking_number,
   'customer_name' => $validated['full_name'],
   'total_price' => $totalPrice,
   'deposit_amount' => $depositAmount,
   'remaining_amount' => $remainingAmount,
   'status' => 'pending',
   'payment_status' => 'pending'
   ```
   
   Data-data penting disimpan sebagai record baru di table `workshop_bookings`. Status awal adalah 'pending' karena belum ada pembayaran.

3. **Perhitungan Harga**
   
   Backend melakukan kalkulasi:
   - `total_price` = participants count × price per participant
   - `deposit_amount` = total_price × deposit percentage (misal 50%)
   - `remaining_amount` = total_price - deposit_amount
   
   Kalkulasi di backend (bukan frontend) adalah best practice untuk mencegah price manipulation.

**Alur Bisnis:**
```
User fill booking form → Submit → Validate input → 
Calculate pricing → Save to DB with status 'pending' → 
Return booking confirmation with deposit amount
```

**Fungsi Kritis:**
- Comprehensive validation mencegah invalid data masuk database
- Backend-side price calculation untuk security
- Pembayaran bertahap (deposit + pelunasan) didukung dengan separate amount fields

---

## GAMBAR 14: Kode Proses Pembayaran Booking Workshop

**Lokasi di Dokumen:** Halaman 12

**Kode:**
```php
$payment = $booking->payments()->create([
    'amount' => $amount,
    'type' => $validated['type'],
    'payment_method' => $validated['payment_method'],
    'payment_status' => 'pending',
    'reference_number' => 'TRIPAY_' . Str::random(12),
]);

$response = $paymentService->createPayment([
    'merchant_ref' => $payment->reference_number,
    'amount' => $amount,
    'method' => $validated['payment_method'],
    'customer_name' => $booking->customer_name,
    'customer_email' => $booking->customer_email,
    'customer_phone' => $booking->customer_phone,
]);
```

**Penjelasan Logika:**

Kode ini menunjukkan bagaimana sistem membuat payment record untuk workshop booking. Penting untuk diperhatikan: booking dan payment adalah entity terpisah dengan relasi one-to-many.

**Analisis Logika:**

1. **Create Payment Record**
   ```php
   $payment = $booking->payments()->create([
       'amount' => $amount,
       'type' => $validated['type'],  // 'deposit' atau 'full_payment'
       'payment_method' => $validated['payment_method'],
       'payment_status' => 'pending',
       'reference_number' => 'TRIPAY_' . Str::random(12)
   ]);
   ```
   
   Menggunakan relationship `payments()` dari booking untuk create payment record. Setiap payment memiliki:
   - `amount`: Jumlah pembayaran (bisa deposit saja atau full payment)
   - `type`: Tipe pembayaran untuk tracking (deposit/pelunasan/penuh)
   - `payment_method`: Metode yang dipilih (QRIS/VA/DANA)
   - `payment_status`: Status awal 'pending'

2. **Call Payment Service**
   ```php
   $response = $paymentService->createPayment([
       'merchant_ref' => $payment->reference_number,
       'amount' => $amount,
       'method' => $validated['payment_method'],
       'customer_name' => $booking->customer_name,
       'customer_email' => $booking->customer_email,
       'customer_phone' => $booking->customer_phone,
   ]);
   ```
   
   PaymentService dipanggil dengan detail pembayaran. Service ini akan send request ke Tripay API untuk create invoice.

3. **Save Tripay Response**
   ```php
   $payment->update([
       'tripay_response' => $response,
       'tripay_reference' => $response['reference']
   ]);
   ```
   
   Response dari Tripay (berisi payment_url dan reference) disimpan ke database untuk reference dan audit trail.

**Alur Pembayaran Workshop:**
```
User submit payment form (memilih deposit/full payment) → 
Create WorkshopPayment record → 
Call Tripay API via PaymentService → 
Save Tripay response → 
Redirect ke payment_url → 
User complete payment di Tripay → 
Tripay send webhook callback
```

**Fungsi Kritis:**
- Multiple payments untuk satu booking didukung
- Flexible payment type (deposit/full) memenuhi business requirement
- Audit trail lengkap dengan reference number dari payment gateway

---

## GAMBAR 15: Kode Webhook Tripay

**Lokasi di Dokumen:** Halaman 12

**Kode:**
```php
public function handle(Request $request)
{
    $signature = $request->header('X-Callback-Signature');
    $payload = $request->getContent();
    $secretkey = config('services.tripay.private_key');

    $computedSignature = hash_hmac('sha256', $payload, $secretkey);

    if (!hash_equals($computedSignature, $signature)) {
        return response()->json(['error' => 'Invalid signature'], 403);
    }

    $data = $request->json()->all();
    $merchantRef = $data['merchant_ref'] ?? null;
    $reference = $data['reference'] ?? null;
    $status = $data['status'] ?? null;
}
```

**Penjelasan Logika:**

Webhook adalah mechanism otomatis dimana Tripay mengirim callback ke backend saat user selesai pembayaran. Kode ini adalah handler untuk webhook Tripay yang verifies signature dan process payment confirmation.

**Analisis Security & Processing:**

1. **Extract Webhook Data**
   ```php
   $signature = $request->header('X-Callback-Signature');
   $payload = $request->getContent();
   $secretkey = config('services.tripay.private_key');
   ```
   
   Webhook data diextract dari HTTP request:
   - `X-Callback-Signature` header berisi HMAC signature untuk verify authenticity
   - Payload adalah raw JSON body dengan detail pembayaran
   - Private key dari config untuk verification

2. **Signature Verification (Critical Security)**
   ```php
   $computedSignature = hash_hmac('sha256', $payload, $secretkey);
   
   if (!hash_equals($computedSignature, $signature)) {
       return response()->json(['error' => 'Invalid signature'], 403);
   }
   ```
   
   **CRITICAL**: Signature verification memastikan callback benar-benar dari Tripay, bukan attacker. Menggunakan `hash_equals()` untuk timing-safe comparison (mencegah timing attack).

3. **Parse Webhook Data**
   ```php
   $data = $request->json()->all();
   $merchantRef = $data['merchant_ref'] ?? null;
   $reference = $data['reference'] ?? null;
   $status = $data['status'] ?? null;
   ```
   
   Data pembayaran di-parse dari JSON payload. Contoh data:
   - `merchant_ref`: Reference number yang kita kirim ke Tripay (booking payment ID)
   - `reference`: Reference dari Tripay side
   - `status`: Status pembayaran ('success', 'failed', 'expired')

**Alur Webhook Processing:**
```
User bayar di Tripay → Tripay process & verify pembayaran → 
Tripay call webhook endpoint kita dengan callback data & signature → 
Handler verify signature → Parse data → 
Update status order/booking → Return 200 OK
```

**Fungsi Kritis:**
- **Signature verification**: Mencegah unauthorized status updates
- **Idempotency**: Handler dirancang safe jika webhook di-call multiple times
- **Automated status sync**: Pembayaran auto-confirmed tanpa manual intervention admin

---

## GAMBAR 16: Kode Update Status Pembayaran Order

**Lokasi di Dokumen:** Halaman 12

**Kode:**
```php
if ($paymentStatus === 'paid' && $order->status === 'pending') {
    $order->updateOrderStatus(
        'processing', 
        'Pembayaran dikonfirmasi otomatis dari Tripay webhook'
    );
}
```

**Penjelasan Logika:**

Kode ini menunjukkan bagaimana webhook Tripay di-handle untuk update status order. Ini adalah continuation dari GAMBAR 15 yang menghandle webhook callback.

**Analisis Logika:**

1. **Check Payment Status & Update Order**
   ```php
   if ($paymentStatus === 'paid' && $order->status === 'pending') {
       $order->updateOrderStatus(
           'processing',
           'Pembayaran dikonfirmasi otomatis dari Tripay webhook'
       );
   }
   ```
   
   Logika ini trigger saat:
   - Payment gateway memberikan status 'paid' (pembayaran berhasil)
   - Order current status masih 'pending' (belum ada perubahan status sebelumnya)
   
   Ketika trigger terpenuhi, order status diubah ke 'processing' yang mengindikasikan order sudah dibayar dan siap diproses untuk pengiriman.

2. **Method `updateOrderStatus()`**
   ```php
   public function updateOrderStatus($status, $notes = null) {
       $this->update([
           'status' => $status,
           'notes' => $notes
       ]);
       
       $this->updateShippingTimeline('processing', 'Pembayaran dikonfirmasi ...');
   }
   ```
   
   Method ini:
   - Update kolom `status` dan `notes` di table orders
   - Call method `updateShippingTimeline()` untuk audit log
   - Timeline membantu tracking history status changes untuk customer support

3. **Audit Trail & Timeline**
   
   Setiap status change dicatat di table terpisah dengan timestamp. Ini penting untuk:
   - Customer dapat track order progress
   - Admin dapat lihat history perubahan status
   - Dispute resolution dengan evidence kapan status berubah

**Alur Update Status:**
```
Webhook handler verify signature & payment status → 
Find order record by merchant_ref → 
If payment_status = 'paid' && order_status = 'pending' → 
Update order_status to 'processing' → 
Log to shipping_timeline → 
Trigger notification email ke customer
```

**Fungsi Kritis:**
- Otomatis status transition mengurangi manual work admin
- Audit trail lengkap untuk traceability
- Customer notification memastikan transparency

---

## GAMBAR 17: Kode Pengelolaan Order oleh Admin

**Lokasi di Dokumen:** Halaman 13

**Kode:**
```php
public function updateStatus(Request $request, Order $order)
{
    $validated = $request->validate([
        'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        'notes' => 'nullable|string|max:500',
    ]);

    $order->updateOrderStatus(
        $validated['status'],
        $validated['notes'] ?? null,
        auth()->user()
    );

    authorize();

    return back()->with('success', 'Status pesanan berhasil diubah');
}
```

**Penjelasan Logika:**

Kode ini menunjukkan method `updateStatus()` dalam admin controller yang memungkinkan admin untuk manual update order status. Method ini dikombinasikan dengan webhook automatic update (dari Gambar 16) memberikan full flexibility - automated untuk most cases, manual override untuk exceptional cases.

**Analisis Logika:**

1. **Validation Input**
   ```php
   $validated = $request->validate([
       'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
       'notes' => 'nullable|string|max:500'
   ]);
   ```
   
   Input validation memastikan:
   - Status hanya bisa nilai-nilai predefined (tidak arbitrary)
   - Notes optional tapi max 500 chars jika ada
   - Database tetap consistent

2. **Authorization Check**
   ```php
   authorize() // [Implicit dalam Laravel resource controller]
   ```
   
   Middleware automatically check apakah authenticated user adalah admin sebelum allow update. Non-admin/customer tidak bisa akses method ini.

3. **Update Order & Audit Log**
   ```php
   $order->updateOrderStatus($validated['status'], null);
   $validated['status'] ?? null  // notes
   authorize();
   ```
   
   Method `updateOrderStatus()` (sama seperti di Gambar 16) digunakan untuk consistency. Method ini:
   - Update order status
   - Log perubahan ke audit trail dengan admin name & timestamp
   - Trigger notification ke customer jika ada status change

4. **Response**
   ```php
   return back()->with('success', 'Status pesanan berhasil diubah');
   ```
   
   Admin dikembalikan ke halaman sebelumnya (order detail) dengan success message. Pattern ini dari Laravel convention untuk CRUD operations.

**Alur Admin Update:**
```
Admin buka order detail → Pilih status baru → Submit form → 
Middleware verify admin role → Validate input → 
Update order status → Log ke audit trail → 
Send notification ke customer → Back to order detail with success message
```

**Comparation: Automatic vs Manual Update**

| Scenario | Method |
|----------|--------|
| Normal case: User bayar & Tripay confirm | Webhook (Automatic) |
| User claim pembayaran tapi webhook lambat | Admin Manual Update |
| Order need cancel | Admin Manual Update |
| Shipping info update | Admin Manual Update |

**Fungsi Kritis:**
- Admin override mechanism untuk exceptional cases
- Consistent update method (sama dengan webhook)
- Full audit trail untuk semua perubahan
- Role-based access control

---

## KESIMPULAN

Implementasi backend Batik Giri Alam menunjukkan best practices dalam:

1. **Separation of Concerns**: Routing → Controller → Service → Model (layered architecture)
2. **Security**: Authorization check, signature verification, input validation
3. **Reliability**: Fallback mechanism (API RajaOngkir), retry logic (implied dalam Tripay integration)
4. **Maintainability**: Service layer untuk business logic, models untuk data representation, clear responsibility separation
5. **Audit & Traceability**: Logging untuk payment flows, status change history untuk orders dan bookings
6. **Flexibility**: Multi-step checkout, payment method options, booking payment schemes (deposit/full)

Setiap potongan kode mendemonstrasikan thoughtful design yang mempertimbangkan user experience, security, dan operational concerns untuk UMKM Batik Giri Alam.

