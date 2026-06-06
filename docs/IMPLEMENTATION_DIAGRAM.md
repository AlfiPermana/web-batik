# 📊 Modal Pembayaran Tripay - Implementation Overview

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                    CHECKOUT PAGE                                │
│                   (4-Step Process)                              │
└─────────────────────────────────────────────────────────────────┘
                              ↓
    ┌─────────────────────────────────────────────────────────┐
    │ Step 1: Address         Step 2: Shipping                │
    │ ────────────────────────────────────────────────────    │
    │ - Nama                  - Courier Options              │
    │ - Telepon               - Service Type                 │
    │ - Alamat Lengkap        - Shipping Cost                │
    │ - Kode Pos                                              │
    └─────────────────────────────────────────────────────────┘
                              ↓
    ┌─────────────────────────────────────────────────────────┐
    │ Step 3: Payment Method                                   │
    │ ──────────────────────                                   │
    │ Select Dropdown:                                         │
    │ - BCA Virtual Account    - OVO E-Wallet                 │
    │ - BRI Virtual Account    - DANA E-Wallet                │
    │ - BNI Virtual Account    - LinkAja                       │
    │ - Mandiri VA             - Gopay                         │
    │ - QRIS / Scan QR Code    - etc.                          │
    └─────────────────────────────────────────────────────────┘
                              ↓
    ┌─────────────────────────────────────────────────────────┐
    │ Step 4: Order Review                                     │
    │ ──────────────────                                       │
    │ - Cart Items            - Subtotal                       │
    │ - Shipping Cost         - Discount                       │
    │ - Payment Method        - TOTAL AMOUNT                   │
    │                                                          │
    │         [← Kembali]  [Konfirmasi Order →]               │
    │                                                          │
    │              🔘 Button Click Event                       │
    └─────────────────────────────────────────────────────────┘
                              ↓
                   wire:click="confirmOrder"
                              ↓
┌──────────────────────────────────────────────────────────────┐
│              CHECKOUT.PHP (Livewire Component)               │
│                                                               │
│  public function confirmOrder()                              │
│  {                                                           │
│      Log::info('confirmOrder() called')                     │
│                                                               │
│      // ✅ VALIDATION LEVEL 1: Payment Method               │
│      if (!$this->selectedPaymentMethod) {                   │
│          → Error: "Pilih metode pembayaran"                │
│          return;                                             │
│      }                                                       │
│                                                               │
│      // ✅ VALIDATION LEVEL 2: Shipping Service             │
│      if (!$this->selectedShippingService) {                 │
│          → Error: "Pilih metode pengiriman"                │
│          return;                                             │
│      }                                                       │
│                                                               │
│      // ✅ VALIDATION LEVEL 3: Address Data                 │
│      if (!$this->fullName || !$this->address) {            │
│          → Error: "Lengkapi data alamat"                   │
│          return;                                             │
│      }                                                       │
│                                                               │
│      // ✅ VALIDATION LEVEL 4: Cart Items                   │
│      if (empty($this->cartItems)) {                         │
│          → Error: "Keranjang belanja kosong"               │
│          return;                                             │
│      }                                                       │
│                                                               │
│      // ✓ All Validation PASSED                             │
│      $order = Order::create([...]);  // Save to DB          │
│      OrderItem::create([...]);       // Save items          │
│                                                               │
│      $this->initializeTripayPayment($order);              │
│      $this->showTripayModal = true;                        │
│  }                                                           │
└──────────────────────────────────────────────────────────────┘
                              ↓
          Order Created ✓  Modal State = true
                              ↓
┌──────────────────────────────────────────────────────────────┐
│            INITIALIZE TRIPAY PAYMENT PROCESS                │
│                                                               │
│  public function initializeTripayPayment($order)            │
│  {                                                           │
│      Log::info('initializeTripayPayment started')           │
│                                                               │
│      // Get Tripay Credentials from .env                    │
│      $merchantCode = config('services.tripay.merchant_code')│
│      $tripayApiKey = config('services.tripay.api_key')      │
│                                                               │
│      if (!$merchantCode || !$tripayApiKey) {               │
│          → Use Fallback Payment                             │
│          $this->createPaymentFallback($order);             │
│          return;                                             │
│      }                                                       │
│                                                               │
│      // Build Request Payload                               │
│      $payload = [                                            │
│          'method' => 'QRIS',    // Selected method          │
│          'merchant_ref' => 'ORD-12345',                    │
│          'amount' => 150000,                                │
│          'customer_name' => 'John Doe',                    │
│          'signature' => hash_hmac(...),  // HMAC-SHA256     │
│      ];                                                      │
│                                                               │
│      // Send HTTP POST to Tripay API                        │
│      $response = Http::withHeaders([                        │
│          'Authorization' => 'Bearer API_KEY',              │
│      ])->timeout(15)->post($tripayApiUrl, $payload);       │
│                                                               │
│      if ($response->successful()) {                         │
│          $data = $response->json();                         │
│          $this->tripayPaymentUrl = $data['checkout_url'];  │
│          $this->tripayReferenceCode = $data['reference'];  │
│          Log::info('Tripay payment initialized');          │
│      } else {                                                │
│          → Use Fallback Payment                             │
│          $this->createPaymentFallback($order);             │
│      }                                                       │
│  }                                                           │
└──────────────────────────────────────────────────────────────┘
                              ↓
          $tripayPaymentUrl = set ✓
          $showTripayModal = true ✓
                              ↓
┌──────────────────────────────────────────────────────────────┐
│            BLADE TEMPLATE - RENDER MODAL                    │
│                                                               │
│  @if($showTripayModal)                                      │
│      <div class="fixed inset-0 bg-black/50">  <!-- Overlay │
│                                                               │
│      <!-- Modal Container -->                               │
│      <div class="bg-white rounded-xl shadow-2xl">           │
│                                                               │
│          <!-- HEADER -->                                    │
│          <div class="px-6 py-4 border-b">                   │
│              <h2>💳 Pembayaran</h2>                         │
│              <p>Metode: {{ $selectedPaymentMethod['name'] }} │
│                  → "QRIS / Scan QR Code"                   │
│              [X] Close Button                               │
│          </div>                                              │
│                                                               │
│          <!-- CONTENT -->                                   │
│          @if($tripayPaymentUrl)                             │
│                                                               │
│              <!-- Reference Card (BLUE) -->                 │
│              <div class="bg-blue-50 border-blue-200">       │
│                  <p>Referensi Pembayaran</p>               │
│                  <p class="font-mono font-bold">            │
│                      {{ $tripayReferenceCode }}            │
│                      → "TRI-XXXXX-XXXXX"                  │
│                  </p>                                       │
│              </div>                                          │
│                                                               │
│              <!-- Details Card (GRAY) -->                   │
│              <div class="bg-gray-50">                       │
│                  <p>Metode: QRIS / Scan QR Code</p>        │
│                  <p>Nominal: Rp 150.000</p>                │
│              </div>                                          │
│                                                               │
│              <!-- Tripay Iframe -->                         │
│              <div style="height: 400px">                    │
│                  <iframe src="{{ $tripayPaymentUrl }}">     │
│                      Tripay Payment Form                    │
│                  </iframe>                                  │
│              </div>                                          │
│                                                               │
│          @else                                               │
│              <!-- Loading State -->                         │
│              <div class="text-center py-20">               │
│                  <svg class="animate-spin">...</svg>       │
│                  <p>Mempersiapkan Pembayaran...</p>        │
│              </div>                                          │
│          @endif                                              │
│                                                               │
│          <!-- FOOTER -->                                    │
│          <div class="border-t px-6 py-4">                   │
│              <p>Nominal: Rp 150.000</p>                    │
│              [Tutup] Button                                 │
│          </div>                                              │
│      </div>                                                  │
│      </div>                                                  │
│  @endif                                                      │
│                                                               │
│  wire:click="closeTripayModal"                             │
│      → Set $showTripayModal = false                        │
└──────────────────────────────────────────────────────────────┘
                              ↓
                      USER SEES MODAL:
                              ↓
            ╔══════════════════════════════════╗
            ║      💳 Pembayaran                ║
            ║   QRIS / Scan QR Code             ║
            ║                          [X]      ║
            ╠══════════════════════════════════╣
            ║ Referensi Pembayaran:             ║
            ║ TRI-XXXXX-XXXXX                   ║
            ║                                   ║
            ║ Metode: QRIS                      ║
            ║ Nominal: Rp 150.000               ║
            ║                                   ║
            ║ ┌─────────────────────────────┐   ║
            ║ │ Tripay Payment Form          │   ║
            ║ │ (Iframe - 400px height)      │   ║
            ║ │                             │   ║
            ║ │ Scan QR Code / Input         │   ║
            ║ │ Payment Details               │   ║
            ║ └─────────────────────────────┘   ║
            ║                                   ║
            ║ Rp 150.000              [Tutup]   ║
            ╚══════════════════════════════════╝
                              ↓
                    USER MELAKUKAN PEMBAYARAN
                    (melalui Tripay Iframe)
                              ↓
                    PAYMENT SUCCESS/FAILED
                              ↓
              (Webhook Handler - Phase 2)
              - Update Order Status
              - Clear Cart
              - Redirect to Success Page
```

---

## Component Relationship Diagram

```
┌─────────────────────────────────┐
│   checkout.blade.php            │
│   (Main Template)               │
├─────────────────────────────────┤
│ - Step 1-3 Form Logic           │
│ - Buttons (Lanjutkan, Konfirmasi)│
│                                 │
│ @include('...payment-modal')    │
│         ↓                       │
│ ┌──────────────────────────────┐│
│ │ payment-modal.blade.php      ││
│ │ (Modal Component)            ││
│ ├──────────────────────────────┤│
│ │ - Modal Overlay              ││
│ │ - Header                     ││
│ │ - Reference Card             ││
│ │ - Details Card               ││
│ │ - Iframe (Tripay)            ││
│ │ - Footer                     ││
│ │ - wire:click actions         ││
│ └──────────────────────────────┘│
│                                 │
└─────────────────────────────────┘
              ↑↓
    ┌──────────────────────────────┐
    │   Checkout.php (Livewire)    │
    ├──────────────────────────────┤
    │ Properties:                  │
    │ - $showTripayModal           │
    │ - $tripayPaymentUrl          │
    │ - $tripayReferenceCode       │
    │ - $selectedPaymentMethod     │
    │ - $cartItems, $totalAmount   │
    │ - dll...                     │
    │                              │
    │ Methods:                     │
    │ - confirmOrder()             │
    │ - initializeTripayPayment()  │
    │ - createPaymentFallback()    │
    │ - closeTripayModal()         │
    │ - selectPaymentMethod()      │
    │ - dll...                     │
    │                              │
    └──────────────────────────────┘
              ↑↓
    ┌──────────────────────────────┐
    │   services.php (Config)      │
    ├──────────────────────────────┤
    │ tripay:                      │
    │ - api_key                    │
    │ - merchant_key               │
    │ - merchant_code              │
    │ - sandbox_url                │
    │ - production_url             │
    │ - callback_url               │
    │ - payment_url                │
    └──────────────────────────────┘
              ↑↓
    ┌──────────────────────────────┐
    │   .env (Environment)         │
    ├──────────────────────────────┤
    │ TRIPAY_API_KEY=xxx           │
    │ TRIPAY_MERCHANT_KEY=xxx      │
    │ TRIPAY_MERCHANT_CODE=xxx     │
    │ TRIPAY_SANDBOX_URL=...       │
    └──────────────────────────────┘
              ↑↓
    ┌──────────────────────────────┐
    │   Database (Order, OrderItem)│
    ├──────────────────────────────┤
    │ Order:                       │
    │ - id, order_number           │
    │ - user_id                    │
    │ - status, payment_status     │
    │ - total, shipping_cost       │
    │ - payment_method             │
    │ - shipping_address           │
    │                              │
    │ OrderItem:                   │
    │ - id, order_id               │
    │ - product_id, quantity       │
    │ - size_id, price             │
    └──────────────────────────────┘
```

---

## Data Flow Sequence Diagram

```
User                 Browser           Livewire          Tripay API      Database
│                     │                  │                  │               │
├─ Fill Form (1-3) ──→│                  │                  │               │
│                     │                  │                  │               │
├─ Click "Konfirmasi" ─────────────────→│                  │               │
│                     │    POST          │                  │               │
│                     │   (wire:click)    │                  │               │
│                     │                  │                  │               │
│                     │              confirmOrder()          │               │
│                     │                  ├─ Validate All    │               │
│                     │                  │  - Payment ✓     │               │
│                     │                  │  - Shipping ✓    │               │
│                     │                  │  - Address ✓     │               │
│                     │                  │  - Cart ✓        │               │
│                     │                  │                  │               │
│                     │                  │                  │             Save Order
│                     │                  ├──────────────────────────────→│
│                     │                  │                  │             Save Items
│                     │                  │←────────────────────────────Order ID
│                     │                  │                  │               │
│                     │              initializeTripayPayment()
│                     │                  ├─ Build Payload   │               │
│                     │                  │                  │               │
│                     │                  │  POST /checkout ─────→│          │
│                     │                  │  (with signature) │    │          │
│                     │                  │                  │    │          │
│                     │                  │         ✓ Success    │
│                     │                  │←─────────────────│          │
│                     │                  │ checkout_url:... │
│                     │                  │ reference:...    │
│                     │                  │                  │
│                     │      $showTripayModal = true         │
│                     │      $tripayPaymentUrl set           │
│                     │      $tripayReferenceCode set        │
│                     │                  │
│                     │←─ Response ──────│
│                     │  (Modal Shown)   │
│                     │                  │
│←─ Modal Render ─────│ payment-modal.blade.php loaded
│   (Overlay)         │
│   (Header)          │
│   (Reference Card)  │
│   (Details Card)    │
│   (Tripay Iframe)   │
│   (Footer)          │
│                     │
├─ Scan QR / Input ──→│ Tripay Iframe Processing
│   Payment Info      │
│                     │
│                     │ Payment Server Processing
│                     │ (Outside Web App)
│                     │
│←─ Payment Status ───│ Success / Failed
│                     │
│  [Close Modal]      │ wire:click="closeTripayModal"
│  [Go Back]          │ $showTripayModal = false
│                     │ Modal closes
```

---

## File Structure & Dependencies

```
web-batik/
├── app/
│   ├── Livewire/
│   │   └── Checkout.php
│   │       ├── confirmOrder()
│   │       ├── initializeTripayPayment()
│   │       ├── createPaymentFallback()
│   │       └── closeTripayModal()
│   │
│   └── Models/
│       ├── Order.php (↔ Database)
│       └── OrderItem.php (↔ Database)
│
├── config/
│   └── services.php
│       └── 'tripay' => [...]
│
├── resources/
│   └── views/
│       └── livewire/
│           ├── checkout.blade.php
│           │   ├── Step 1-3 Forms
│           │   ├── Button (wire:click="confirmOrder")
│           │   └── @include('livewire.checkout.payment-modal')
│           │
│           └── checkout/
│               └── payment-modal.blade.php
│                   ├── Overlay
│                   ├── Header
│                   ├── Reference Card
│                   ├── Details Card
│                   ├── Tripay Iframe
│                   └── Footer
│
├── routes/
│   └── web.php
│       └── GET /checkout (Checkout::class)
│
├── .env
│   └── TRIPAY_* credentials
│
├── database/
│   ├── migrations/
│   │   ├── orders table
│   │   └── order_items table
│   │
│   └── seeders/
│       └── (optional sample data)
│
└── storage/
    └── logs/
        └── laravel.log (debugging)
```

---

## Summary

✅ **Frontend (Blade):** Modal component dengan responsive UI
✅ **Backend (Livewire):** Complete logic dengan validation & error handling
✅ **API Integration:** Full Tripay API dengan fallback system
✅ **Database:** Order persistence dengan user tracking
✅ **Logging:** Comprehensive debugging information
✅ **UX:** Visual feedback dengan spinner dan disable states

**Status: READY FOR TESTING & DEPLOYMENT** 🚀
