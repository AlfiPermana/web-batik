# 🔧 Perbaikan OVO dan DANA Payment Methods

## 📋 Masalah yang Ditemukan

### 1. OVO dan DANA tidak terdaftar di `config/payment.php`
- **Lokasi**: `config/payment.php`
- **Masalah**: Hanya QRIS, Virtual Account, dan Convenience Store yang terdaftar
- **Akibat**: TripayGateway menolak OVO dan DANA karena method tidak valid

### 2. Method ID tidak sesuai dengan Tripay API
- **Lokasi**: `app/Livewire/Checkout.php` line 285-297
- **Masalah**: Menggunakan `'DANA'` dan `'OVO'` sebagai ID
- **Tripay Ekspektasi**: `'DANACASH'` dan `'OVOBANK'`
- **Akibat**: Tripay API mengembalikan error "Invalid payment method"

## ✅ Solusi yang Diimplementasikan

### Fix #1: Tambah OVO dan DANA ke config/payment.php
```php
// Added to config/payment.php
'OVOBANK' => [
    'code' => 'OVOBANK',
    'name' => 'OVO',
    'group' => 'E-Wallet',
    'type' => 'redirect',
],
'DANACASH' => [
    'code' => 'DANACASH',
    'name' => 'Dana',
    'group' => 'E-Wallet',
    'type' => 'redirect',
],

// Updated grouped_methods
'E-Wallet' => [
    'QRIS', 'OVOBANK', 'DANACASH'
],
```

### Fix #2: Ubah Method ID di Checkout.php
```php
// Changed from:
'id' => 'DANA',    → 'id' => 'DANACASH'
'id' => 'OVO',     → 'id' => 'OVOBANK'
```

## 🧪 Cara Test

### Method 1: Via Checkout (Recommended)
1. Buka checkout page
2. Pilih metode pembayaran "DANA" atau "OVO"
3. Lanjutkan checkout
4. Verifikasi di laravel.log bahwa:
   - ✅ `initializeTripayPayment started` dengan order_id
   - ✅ `PaymentService instantiated, calling createPayment`
   - ✅ Response berisi `checkout_url` (tidak error)
   - ✅ `tripayPaymentUrl_set => true`

### Method 2: Via Artisan Tinker
```bash
php artisan tinker
```

```php
$service = new \App\Services\payment\PaymentService();

// Test DANA
$response = $service->createPayment([
    'merchant_ref' => 'TEST-DANA-' . time(),
    'amount' => 150000,
    'method' => 'DANACASH',
    'customer_name' => 'Test User',
    'customer_email' => 'test@example.com',
    'customer_phone' => '08123456789',
    'items' => [['name' => 'Test', 'quantity' => 1, 'price' => 150000]]
]);

dd($response);
```

### Method 3: Check Logs
```bash
tail -f storage/logs/laravel.log
```

Cari entries dengan:
- `PaymentService response received`
- `checkout_url_exists: true`
- `Tripay payment initialized successfully` ✅

## 🎯 Expected Results

### Sebelumnya (GAGAL)
```
PaymentService response received: {
  "success": false,
  "message": "Invalid payment method: OVO",
  "data": null
}
```

### Sesudah (BERHASIL)
```
PaymentService response received: {
  "success": true,
  "checkout_url": "https://checkout.tripay.co.id/...",
  "reference": "T...",
  ...
}
```

## 📝 Files Modified

1. ✅ `config/payment.php`
   - Added OVOBANK and DANACASH entries
   - Updated grouped_methods

2. ✅ `app/Livewire/Checkout.php` 
   - Line 285-297: Changed 'DANA' → 'DANACASH', 'OVO' → 'OVOBANK'

3. ✅ Cache cleared
   - `php artisan config:clear` executed

## 🚀 Next Steps

Jika masih error setelah fix ini, check:
1. Tripay API Key valid di `.env`
2. Merchant code valid
3. Private key correct
4. Tripay account sudah activated untuk OVO dan DANA

Cek di Tripay dashboard → Payment Methods apakah OVOBANK dan DANACASH sudah enabled.
