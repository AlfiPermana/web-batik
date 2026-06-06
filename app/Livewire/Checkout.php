<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Order;
use App\Models\ProductSize;
use App\Models\Address as AddressModel;
use App\Models\ShippingOrigin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Livewire\Component;
use App\View\Components\Icon;

class Checkout extends Component
{
    // Current step: 1 = address, 2 = shipping, 3 = payment, 4 = review
    public $currentStep = 1;
    public $totalSteps = 4;

    // User data
    public $user;
    public $cart;
    public $cartItems = [];
    public $userAddresses = [];

    // Step 1: Address Form & Selection
    public $selectedAddressId = null;  // Selected existing address
    public $useNewAddress = false;     // Toggle between existing and new address
    public $fullName;
    public $phoneNumber;
    public $province = '';
    public $provinceId = '';
    public $city = '';
    public $cityId = '';
    public $district = '';
    public $districtId = '';
    public $subdistrict = '';
    public $subdistrictId = '';
    public $address;
    public $postalCode;

    // Raja Ongkir Dropdowns
    public $provinces = [];
    public $cities = [];
    public $districts = [];
    public $subdistricts = [];
    public $loadingCities = false;
    public $loadingDistricts = false;
    public $loadingSubdistricts = false;

    // Step 2: Shipping (placeholder untuk RajaOngkir integration nanti)
    public $selectedShippingService = null;
    public $shippingCost = 0;
    public $shippingOptions = [];
    public $loadingShippingOptions = false;

    // Shipping debug/status (biar kelihatan kenapa cuma fallback)
    public $shippingOptionsSource = null; // 'api' | 'cache' | 'mock'
    public $shippingOptionsError = null;
    /**
     * Force reload shipping options from API
     */
    public function forceReloadShipping()
    {
        Log::info('forceReloadShipping called - retrying API calls', [
            'districtId' => $this->districtId,
            'district' => $this->district,
            'cityId' => $this->cityId,
            'city' => $this->city
        ]);
        $this->calculateShipping();
        $this->dispatch('notify', type: 'success', message: 'Opsi pengiriman diperbarui');
    }

    /**
     * Force load mock shipping options (immediate solution)
     */
    public function forceMockShipping()
    {
        Log::info('forceMockShipping called - using fallback options', [
            'error' => $this->shippingOptionsError,
        ]);

        $this->shippingOptionsSource = 'mock';
        $this->shippingOptions = $this->getMockShippingOptions();
        $this->loadingShippingOptions = false;

        // kalau fallback, notif jangan bilang sukses full
        if ($this->shippingOptionsError) {
            $this->dispatch('notify', type: 'warning', message: 'Pakai opsi fallback: ' . $this->shippingOptionsError);
        } else {
            $this->dispatch('notify', type: 'success', message: 'Opsi pengiriman berhasil dimuat');
        }
    }

    /**
     * Get mock shipping options as fallback
     */
    public function getMockShippingOptions(): array
    {
        return [
            [
                'courier' => 'jne',
                'name' => 'JNE',
                'service' => 'REG',
                'description' => 'Regular Package',
                'cost' => 15000,
                'etd' => '2-3 hari'
            ],
            [
                'courier' => 'jnt',
                'name' => 'J&T Express',
                'service' => 'EZ',
                'description' => 'Regular Package',
                'cost' => 12000,
                'etd' => '1-2 hari'
            ],
            [
                'courier' => 'sicepat',
                'name' => 'SiCepat',
                'service' => 'REG',
                'description' => 'Regular Package',
                'cost' => 13000,
                'etd' => '1-2 hari'
            ],
            [
                'courier' => 'pos',
                'name' => 'POS Indonesia',
                'service' => 'Paket Kilat Khusus',
                'description' => 'Express Package',
                'cost' => 18000,
                'etd' => '2-4 hari'
            ]
        ];
    }

    // Step 3: Payment Method
    public $selectedPaymentMethod = null;
    public $paymentMethods = [];
    public $paymentFee = 0;

    // Order Summary
    public $subtotal = 0;
    public $discount = 0;
    public $totalAmount = 0;

    // Tripay Modal
    public $showTripayModal = false;
    public $tripayPaymentUrl = null;
    public $tripayReferenceCode = null;
    public $currentOrderId = null;
    
    // Tripay Payment Details (from API response)
    public $tripayPaymentData = null;  // Full response data
    public $tripayPaymentMethod = null;  // Payment method info
    public $tripayMerchantCode = null;  // Merchant reference code
    public $tripayFee = 0;  // Payment fee from Tripay
    
    // Payment Status (from webhook or poll)
    public $paymentStatus = null;  // 'pending', 'paid', 'expired', 'failed'
    public $paymentPaidAt = null;  // DateTime when paid
    public $paymentStatusMessage = null;
    
    // Order Details for success display
    public $orderNumber = null;
    public $orderItems = [];
    public $orderShippingAddress = null;

    public function mount()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->user = Auth::user();
        $this->cart = Cart::where('user_id', $this->user->id)
            ->with(['items' => function ($query) {
                $query->with('product', 'size');
            }])
            ->first();

        // Check if cart is empty
        if (!$this->cart || $this->cart->items()->count() === 0) {
            return redirect()->route('landing.shop')->with('error', 'Keranjang Anda kosong');
        }

        // Load cart items
        $this->loadCartItems();

        // Pre-fill form with user data
        $this->preFillForm();

        // Calculate initial subtotal
        $this->calculateSubtotal();

        // Initialize payment methods
        $this->initializePaymentMethods();

        // Load provinces from Raja Ongkir
        $this->loadProvinces();
        
        // Don't pre-load shipping options here - let it load when address is selected
        // This ensures we use real API data instead of mock data
    }

    private function loadCartItems()
    {
        $this->cartItems = $this->cart->items->map(fn($item) => [
            'id' => $item->id,
            'product' => [
                'id' => $item->product->id,
                'title' => $item->product->title,
                'photo' => $item->product->photo,
            ],
            'size' => [
                'id' => $item->size->id,
                'size' => $item->size->size,
            ],
            'quantity' => $item->quantity,
            'price' => (float) $item->size->price,
            'subtotal' => $item->quantity * (float) $item->size->price,
        ])->toArray();
    }

    private function preFillForm()
    {
        // Load user addresses
        $this->userAddresses = $this->user->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($addr) => [
                'id' => $addr->id,
                'label' => $addr->label,
                'full_name' => $addr->full_name,
                'phone_number' => $addr->phone_number,
                'address' => $addr->address,
                'subdistrict' => $addr->subdistrict_display,
                'district' => $addr->district_display,
                'city' => $addr->city_display,
                'province' => $addr->province_display,
                'postal_code' => $addr->postal_code,
                'is_default' => $addr->is_default,
            ])
            ->toArray();

        // If user has addresses, pre-select the default one
        if (!empty($this->userAddresses)) {
            $defaultAddress = collect($this->userAddresses)->firstWhere('is_default', true);
            if ($defaultAddress) {
                $this->selectedAddressId = $defaultAddress['id'];
                $this->loadAddressData($defaultAddress['id']);
            } else {
                // Select first address if no default
                $this->selectedAddressId = $this->userAddresses[0]['id'];
                $this->loadAddressData($this->userAddresses[0]['id']);
            }
            $this->useNewAddress = false;
        } else {
            // No addresses, use new address form
            $this->useNewAddress = true;
            // Fill dari user data
            $this->fullName = $this->user->name ?? '';
            $this->phoneNumber = $this->user->phone ?? '';

            // Jika ada customer profile, ambil data dari sana
            if ($this->user->customerProfile) {
                $profile = $this->user->customerProfile;
                $this->province = $profile->province ?? '';
                $this->city = $profile->city ?? '';
                $this->address = $profile->address ?? '';
                $this->postalCode = $profile->postal_code ?? '';
            }
        }
    }

    /**
     * Load address data into form when selected
     */
    private function loadAddressData($addressId)
    {
        $address = AddressModel::find($addressId);
        if (!$address) return;

        // Load all properties at once
        $this->fullName = $address->full_name;
        $this->phoneNumber = $address->phone_number;
        $this->address = $address->address;
        $this->postalCode = $address->postal_code;

        // NOTE:
        // - DB menyimpan ID (province/city/district/subdistrict)
        // - UI tetap menampilkan nama (via *_display)
        if (is_numeric($address->province)) {
            $this->provinceId = (string) $address->province;
            $this->province = $address->province_display;
        } else {
            $this->province = $address->province;
        }

        if (is_numeric($address->city)) {
            $this->cityId = (string) $address->city;
            $this->city = $address->city_display;
        } else {
            $this->city = $address->city;
        }

        if (is_numeric($address->district)) {
            $this->districtId = (string) $address->district;
            $this->district = $address->district_display;
        } else {
            $this->district = $address->district;
        }

        if (is_numeric($address->subdistrict)) {
            $this->subdistrictId = (string) $address->subdistrict;
            $this->subdistrict = $address->subdistrict_display;
        } else {
            $this->subdistrict = $address->subdistrict;
        }

        // Kalau ID belum kebaca (alamat lama masih simpan nama), baru resolve via API
        if (!$this->districtId && $this->district) {
            $this->loadLocationIds();
        }

        Log::info('loadAddressData: Triggering calculateShipping', [
            'provinceId' => $this->provinceId,
            'cityId' => $this->cityId,
            'districtId' => $this->districtId,
            'district' => $this->district,
        ]);

        $this->calculateShipping();
    }

    /**
     * Fetch location IDs from Raja Ongkir API based on location names
     */
    private function loadLocationIds()
    {
        try {
            $apiKey = config('services.raja_ongkir.api_key');
            
            // Get province ID
            if ($this->province && !$this->provinceId) {
                $response = Http::withHeaders(['key' => $apiKey])->timeout(10)->get(config('services.raja_ongkir.province_url'));
                if ($response->successful()) {
                    $data = $response->json('data') ?? [];
                    foreach ($data as $prov) {
                        if (strtolower($prov['name']) === strtolower($this->province)) {
                            $this->provinceId = $prov['id'];
                            break;
                        }
                    }
                }
            }
            
            // Get city ID
            if ($this->city && $this->provinceId && !$this->cityId) {
                $baseUrl = config('services.raja_ongkir.city_url');
                $url = str_contains($baseUrl, '{province_id}')
                    ? str_replace('{province_id}', $this->provinceId, $baseUrl)
                    : ($baseUrl . '/' . $this->provinceId);

                $response = Http::withHeaders(['key' => $apiKey])->timeout(10)->get($url);
                if ($response->successful()) {
                    $data = $response->json('data') ?? [];
                    Log::info('City lookup', [
                        'city_name' => $this->city,
                        'province_id' => $this->provinceId,
                        'available_cities' => array_map(fn($c) => ['id' => $c['id'], 'name' => $c['name']], $data)
                    ]);
                    
                    foreach ($data as $city) {
                        // Try exact match first, then try matching by ID if city name is numeric
                        if (strtolower($city['name']) === strtolower($this->city) || 
                            $city['id'] === $this->city) {
                            $this->cityId = $city['id'];
                            Log::info('City ID found', ['name' => $this->city, 'id' => $this->cityId]);
                            break;
                        }
                    }
                }
            }
            
            // Get district ID - FIX: If district is already numeric, use it directly!
            if ($this->district && !$this->districtId) {
                // Check if district is already a numeric ID
                if (is_numeric($this->district)) {
                    $this->districtId = $this->district;
                    Log::info('District ID set directly from numeric district', ['district' => $this->district, 'districtId' => $this->districtId]);
                } else {
                    // Try to find district ID from API
                    if ($this->cityId) {
                        $baseUrl = config('services.raja_ongkir.district_url');
                        $url = str_contains($baseUrl, '{city_id}')
                            ? str_replace('{city_id}', $this->cityId, $baseUrl)
                            : ($baseUrl . '/' . $this->cityId);

                        $response = Http::withHeaders(['key' => $apiKey])->timeout(10)->get($url);
                        if ($response->successful()) {
                            $data = $response->json('data') ?? [];
                            Log::info('District lookup', [
                                'district_name' => $this->district,
                                'city_id' => $this->cityId,
                                'available_districts' => array_map(fn($d) => ['id' => $d['id'], 'name' => $d['name']], $data)
                            ]);
                            
                            foreach ($data as $dist) {
                                // Try exact match first, then try matching by ID if district name is numeric
                                if (strtolower($dist['name']) === strtolower($this->district) || 
                                    $dist['id'] === $this->district) {
                                    $this->districtId = $dist['id'];
                                    Log::info('District ID found', ['name' => $this->district, 'id' => $this->districtId]);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            
            Log::info('loadLocationIds: Complete', [
                'provinceId' => $this->provinceId,
                'cityId' => $this->cityId,
                'districtId' => $this->districtId,
            ]);
        } catch (\Exception $e) {
            Log::warning('Error loading location IDs: ' . $e->getMessage());
        }
    }

    /**
     * Handle when existing address is selected
     */
    public function updatedSelectedAddressId($value)
    {
        if ($value && !$this->useNewAddress) {
            $this->loadAddressData($value);
        }
    }

    /**
     * Toggle between existing address and new address form
     */
    public function toggleNewAddress()
    {
        $this->useNewAddress = !$this->useNewAddress;
        if ($this->useNewAddress) {
            // Clear form when switching to new address
            $this->fullName = $this->user->name ?? '';
            $this->phoneNumber = $this->user->phone ?? '';
            $this->province = '';
            $this->city = '';
            $this->district = '';
            $this->subdistrict = '';
            $this->address = '';
            $this->postalCode = '';
        } else {
            // Load default address when switching back
            if ($this->selectedAddressId) {
                $this->loadAddressData($this->selectedAddressId);
            }
        }
    }

    private function calculateSubtotal()
    {
        $this->subtotal = collect($this->cartItems)->sum('subtotal');
        $this->totalAmount = $this->subtotal + $this->shippingCost + $this->paymentFee - $this->discount;
    }

    /**
     * Initialize payment methods
     */
    private function initializePaymentMethods()
{
    $this->paymentMethods = [
        [
            'id' => 'BCAVA',
            'name' => 'BCA Virtual Account',
            'description' => 'Transfer ke Virtual Account BCA',
            'icon' => \App\View\Components\Icon::getUrl('BCAVA'), // ✅
            'fee' => 0,
        ],
        [
            'id' => 'BNIVA',
            'name' => 'BNI Virtual Account',
            'description' => 'Transfer ke Virtual Account BNI',
            'icon' => \App\View\Components\Icon::getUrl('BNIVA'), // ✅
            'fee' => 0,
        ],
        [
            'id' => 'MANDIRIVA',
            'name' => 'MANDIRI Virtual Account',
            'description' => 'Transfer ke Virtual Account MANDIRI',
            'icon' => \App\View\Components\Icon::getUrl('MANDIRIVA'), // ✅
            'fee' => 0,
        ],
        [
            'id' => 'BRIVA',
            'name' => 'BRI Virtual Account',
            'description' => 'Transfer ke Virtual Account BRI',
            'icon' => \App\View\Components\Icon::getUrl('BRIVA'), // ✅
            'fee' => 0,
        ],
        [
            'id' => 'QRIS',
            'name' => 'QRIS / Scan QR Code',
            'description' => 'Bayar dengan scan QRIS dari smartphone',
            'icon' => \App\View\Components\Icon::getUrl('QRIS'), // ✅
            'fee' => 0,
        ],
        [
            'id' => 'DANA',
            'name' => 'DANA',
            'description' => 'Transfer Menggunakan DANA',
            'icon' => \App\View\Components\Icon::getUrl('DANA'),
            'fee' => 0,
        ],
        [
            'id' => 'OVO',
            'name' => 'OVO',
            'description' => 'Transfer Menggunakan OVO',
            'icon' => \App\View\Components\Icon::getUrl('OVO'),
            'fee' => 0,
        ],
        ];

        Log::info('Payment methods initialized', ['count' => count($this->paymentMethods)]);
    }

    /**
     * Load provinces from Raja Ongkir
     */
    public function loadProvinces()
    {
        Log::info('loadProvinces called');
        try {
            $apiKey = config('services.raja_ongkir.api_key');
            $url = config('services.raja_ongkir.province_url');
            Log::info('Raja Ongkir request', ['key' => $apiKey, 'url' => $url]);
            
            $response = Http::withHeaders([
                'key' => $apiKey,
            ])->get($url);

            Log::info('Raja Ongkir response status', ['status' => $response->status()]);

            if ($response->successful()) {
                $jsonData = $response->json();
                // The API returns data in 'data' key, not 'results'
                $data = $jsonData['data'] ?? [];
                Log::info('Raja Ongkir provinces loaded', ['count' => count($data), 'data_sample' => array_slice($data, 0, 1)]);
                
                // API returns id and name directly
                $this->provinces = array_map(fn($item) => [
                    'id' => $item['id'],
                    'name' => $item['name'],
                ], $data);
                Log::info('Provinces stored', ['count' => count($this->provinces)]);
            } else {
                Log::error('Raja Ongkir API failed', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('Error loading provinces from Raja Ongkir: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Gagal memuat provinsi');
        }
    }

    /**
     * Update cities when province changes
     */
    public function updatedProvince($value)
    {
        Log::info('updatedProvince called', ['value' => $value, 'type' => gettype($value)]);
        
        if (!$value) {
            Log::info('Province value is empty, clearing cities');
            $this->cities = [];
            $this->city = '';
            $this->districts = [];
            $this->district = '';
            $this->subdistricts = [];
            $this->subdistrict = '';
            return;
        }

        $this->loadingCities = true;
        Log::info('Loading cities - loadingCities set to true');
        
        try {
            // Use path parameter: /city/{province_id}
            $baseUrl = config('services.raja_ongkir.city_url');
            $url = $baseUrl . '/' . $value;
            Log::info('Loading cities', ['url' => $url, 'province_id' => $value]);
            
            $response = Http::withHeaders([
                'key' => config('services.raja_ongkir.api_key'),
                'accept' => 'application/json',
            ])->timeout(10)->get($url);

            Log::info('City API response', ['status' => $response->status(), 'successful' => $response->successful()]);
            
            if ($response->successful()) {
                $jsonData = $response->json();
                $data = $jsonData['data'] ?? [];
                Log::info('Cities loaded', ['count' => count($data)]);
                $this->cities = array_map(fn($item) => [
                    'id' => $item['id'],
                    'name' => $item['name'],
                ], $data);
                $this->city = '';
                $this->districts = [];
                $this->district = '';
                $this->subdistricts = [];
                $this->subdistrict = '';
            } else {
                Log::error('City API failed', ['status' => $response->status()]);
                $this->cities = [];
            }
        } catch (\Exception $e) {
            Log::error('Error loading cities from Raja Ongkir: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Gagal memuat kota');
        } finally {
            $this->loadingCities = false;
        }
    }

    /**
     * Update districts when city changes
     */
    public function updatedCity($value)
    {
        if (!$value) {
            $this->districts = [];
            $this->district = '';
            $this->subdistricts = [];
            $this->subdistrict = '';
            return;
        }

        $this->loadingDistricts = true;

        try {
            // Use path parameter: /district/{city_id}
            $baseUrl = config('services.raja_ongkir.district_url');
            $url = $baseUrl . '/' . $value;
            Log::info('Loading districts', ['url' => $url, 'city_id' => $value]);
            
            $response = Http::withHeaders([
                'key' => config('services.raja_ongkir.api_key'),
                'accept' => 'application/json',
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                $jsonData = $response->json();
                $data = $jsonData['data'] ?? [];
                $this->districts = array_map(fn($item) => [
                    'id' => $item['id'],
                    'name' => $item['name'],
                ], $data);
                $this->district = '';
                $this->subdistricts = [];
                $this->subdistrict = '';
            }
        } catch (\Exception $e) {
            Log::error('Error loading districts from Raja Ongkir: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Gagal memuat kecamatan');
        } finally {
            $this->loadingDistricts = false;
        }
    }

    /**
     * Update subdistricts when district changes
     */
    public function updatedDistrict($value)
    {
        if (!$value) {
            $this->subdistricts = [];
            $this->subdistrict = '';
            $this->shippingOptions = [];
            return;
        }

        // If subdistrict is already set (e.g., from loading saved address), skip loading subdistricts
        if ($this->subdistrict) {
            Log::info('updatedDistrict: Subdistrict already set, skipping reload', ['district' => $value, 'subdistrict' => $this->subdistrict]);
            return;
        }

        $this->loadingSubdistricts = true;

        try {
            // Use path parameter: /sub-district/{district_id}
            $baseUrl = config('services.raja_ongkir.subdistrict_url');
            $url = $baseUrl . '/' . $value;
            Log::info('Loading subdistricts', ['url' => $url, 'district_id' => $value]);
            
            $response = Http::withHeaders([
                'key' => config('services.raja_ongkir.api_key'),
                'accept' => 'application/json',
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                $jsonData = $response->json();
                $data = $jsonData['data'] ?? [];
                $this->subdistricts = array_map(fn($item) => [
                    'id' => $item['id'],
                    'name' => $item['name'],
                ], $data);
                $this->subdistrict = '';
            }
        } catch (\Exception $e) {
            Log::error('Error loading subdistricts from Raja Ongkir: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Gagal memuat kelurahan');
        } finally {
            $this->loadingSubdistricts = false;
        }
        
        // DO NOT auto-calculate shipping here - wait for subdistrict selection
        // Shipping will be calculated in updatedSubdistrict() instead
    }

    /**
     * When subdistrict is selected, calculate shipping options
     */
    public function updatedSubdistrict($value)
    {
        // Only calculate shipping if we have a complete address (including subdistrict)
        if ($value && $this->district) {
            $this->calculateShipping();
        } else {
            $this->shippingOptions = [];
        }
    }

    public function validateStep1(): bool
    {
        $this->validate([
            'fullName' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:20',
            'province' => 'required|string',
            'city' => 'nullable|string',
            'district' => 'nullable|string',
            'subdistrict' => 'nullable|string',
            'address' => 'required|string|max:500',
            'postalCode' => 'nullable|string|max:10',
        ], [
            'fullName.required' => 'Nama lengkap diperlukan',
            'phoneNumber.required' => 'Nomor telepon diperlukan',
            'province.required' => 'Provinsi diperlukan',
            'address.required' => 'Alamat diperlukan',
            'postalCode.required' => 'Kode pos diperlukan',
        ]);

        return true;
    }

    /**
     * Move to next step
     */
    public function nextStep()
    {
        if ($this->currentStep == 1) {
            if (!$this->validateStep1()) {
                return;
            }
            // Auto-load shipping options when moving to step 2
            $this->calculateShipping();
        } elseif ($this->currentStep == 2) {
            if (!$this->selectedShippingService) {
                $this->dispatch('notify', type: 'error', message: 'Pilih metode pengiriman');
                return;
            }
        } elseif ($this->currentStep == 3) {
            if (!$this->selectedPaymentMethod) {
                $this->dispatch('notify', type: 'error', message: 'Pilih metode pembayaran');
                return;
            }
        }

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    /**
     * Move to previous step
     */
    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    /**
     * Confirm order and create it
     */
    public function confirmOrder()
    {
        Log::info('confirmOrder() called', [
            'currentStep' => $this->currentStep,
            'selectedPaymentMethod' => $this->selectedPaymentMethod ? $this->selectedPaymentMethod['id'] : null,
            'totalAmount' => $this->totalAmount,
        ]);

        try {
            // Validate all required data
            if (!$this->selectedPaymentMethod) {
                Log::warning('confirmOrder: No payment method selected');
                $this->dispatch('notify', type: 'error', message: 'Pilih metode pembayaran terlebih dahulu');
                return;
            }

            if (!$this->selectedShippingService) {
                Log::warning('confirmOrder: No shipping service selected');
                $this->dispatch('notify', type: 'error', message: 'Pilih metode pengiriman terlebih dahulu');
                return;
            }

            if (!$this->fullName || !$this->phoneNumber || !$this->address) {
                Log::warning('confirmOrder: Missing address data');
                $this->dispatch('notify', type: 'error', message: 'Lengkapi data alamat terlebih dahulu');
                return;
            }

            if (empty($this->cartItems)) {
                Log::warning('confirmOrder: Cart is empty');
                $this->dispatch('notify', type: 'error', message: 'Keranjang belanja kosong');
                return;
            }

            Log::info('Creating order...', ['user_id' => $this->user->id]);

            // ✅ Validasi stok per ukuran sebelum order dibuat
            $qtyBySize = collect($this->cartItems)
                ->groupBy(fn($i) => (int) ($i['size']['id'] ?? 0))
                ->map(fn($rows) => (int) collect($rows)->sum('quantity'))
                ->filter(fn($qty, $sizeId) => (int) $sizeId > 0);

            if ($qtyBySize->isNotEmpty()) {
                $sizes = ProductSize::query()
                    ->whereIn('id', $qtyBySize->keys()->all())
                    ->get(['id', 'stock'])
                    ->keyBy('id');

                foreach ($qtyBySize as $sizeId => $qty) {
                    $size = $sizes->get((int) $sizeId);
                    $stock = (int) ($size?->stock ?? 0);

                    if (!$size || $stock < (int) $qty) {
                        $this->dispatch('notify', type: 'error', message: 'Stok tidak cukup untuk salah satu ukuran yang dipilih. Silakan perbarui keranjang.');
                        return;
                    }
                }
            }

            // Use the location names from form fields (already populated from Address model or user input)
            $provinceName = $this->province ?? '';
            $cityName = $this->city ?? '';
            $districtName = $this->district ?? '';
            $subdistrictName = $this->subdistrict ?? '';

            Log::info('Location data at order creation', [
                'provinceName' => $provinceName,
                'cityName' => $cityName,
                'districtName' => $districtName,
                'subdistrictName' => $subdistrictName,
                'provinceId' => $this->provinceId,
                'cityId' => $this->cityId,
                'districtId' => $this->districtId,
                'subdistrictId' => $this->subdistrictId,
            ]);

            $order = DB::transaction(function () use ($provinceName, $cityName, $districtName, $subdistrictName) {
                $order = Order::create([
                    'user_id' => $this->user->id,
                    'order_number' => Order::generateOrderNumber(),
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'subtotal' => $this->subtotal,
                    'shipping_cost' => $this->shippingCost,
                    'discount' => $this->discount,
                    'total' => $this->totalAmount,
                    'shipping_address' => [
                        'full_name' => $this->fullName,
                        'phone_number' => $this->phoneNumber,
                        'address' => $this->address,
                        'province_id' => $this->provinceId ?: $this->province,
                        'province' => $provinceName,
                        'city_id' => $this->cityId ?: $this->city,
                        'city' => $cityName,
                        'district_id' => $this->districtId ?: $this->district,
                        'district' => $districtName,
                        'subdistrict_id' => $this->subdistrictId ?: $this->subdistrict,
                        'subdistrict' => $subdistrictName,
                        'postal_code' => $this->postalCode,
                    ],
                    'shipping_service' => $this->selectedShippingService['courier'] ?? null,
                    'shipping_service_type' => $this->selectedShippingService['service'] ?? null,
                    'payment_method' => $this->selectedPaymentMethod['id'] ?? null,
                ]);

                Log::info('Order created successfully', ['order_id' => $order->id, 'order_number' => $order->order_number]);

                $order->items()->createMany(collect($this->cartItems)->map(function ($item) {
                    return [
                        'product_id' => $item['product']['id'],
                        'product_size_id' => $item['size']['id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ];
                })->values()->all());

                $order->loadCount('items');
                if ((int) $order->items_count !== count($this->cartItems)) {
                    throw new \RuntimeException('Gagal menyimpan item pesanan secara lengkap.');
                }

                Log::info('Order items created', ['order_id' => $order->id, 'item_count' => count($this->cartItems)]);

                return $order->fresh(['items.product', 'items.productSize']);
            });

            // Store order ID and initialize Tripay payment
            $this->currentOrderId = $order->id;
            Log::info('Initializing Tripay payment', ['order_id' => $order->id, 'method' => $this->selectedPaymentMethod['id']]);
            
            $this->initializeTripayPayment($order);
            
            // Show Tripay modal AFTER initialization is complete
            $this->showTripayModal = true;
            
            // CRITICAL: Populate initial data immediately (before polling starts)
            $this->checkPaymentStatus();

            // Sync cart: setelah order dikonfirmasi, kosongkan cart user
            try {
                if ($this->cart) {
                    $this->cart->clear();
                }
                $this->dispatch('cart-updated');
            } catch (\Throwable $e) {
                Log::warning('Failed to clear cart after confirmOrder', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
            
            Log::info('Modal shown for order', ['order_id' => $order->id]);
            
            $this->dispatch('notify', type: 'success', message: 'Order berhasil dibuat! Silakan lanjutkan pembayaran.');
            
        } catch (\Exception $e) {
            Log::error('Error in confirmOrder: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            $this->dispatch('notify', type: 'error', message: 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Initialize Tripay payment request using PaymentService (same as Workshop)
     */
    public function initializeTripayPayment($order)
    {
        Log::info('initializeTripayPayment started', ['order_id' => $order->id]);

        try {
            // Prepare order items
            $orderItems = array_map(fn($item) => [
                'name' => $item['product']['title'],
                'quantity' => (int) $item['quantity'],
                'price' => (int) $item['price'],
            ], $this->cartItems);

            // Add shipping as line item
            if ((int) $this->shippingCost > 0) {
                $orderItems[] = [
                    'name' => 'Shipping Cost',
                    'quantity' => 1,
                    'price' => (int) $this->shippingCost,
                ];
            }

            // Use PaymentService just like Workshop does
            $paymentService = new \App\Services\payment\PaymentService();
            Log::info('PaymentService instantiated, calling createPayment');
            
            $response = $paymentService->createPayment([
                'merchant_ref' => $order->order_number,
                'amount' => (int) $this->totalAmount,
                'method' => $this->selectedPaymentMethod['id'],
                'customer_name' => $this->fullName,
                'customer_email' => $this->user->email,
                'customer_phone' => $this->phoneNumber,
                'items' => $orderItems,
            ]);

            Log::info('PaymentService response received', [
                'success' => $response['success'] ?? false,
                'has_checkout_url' => isset($response['data']['checkout_url']),
                'response_keys' => array_keys($response),
            ]);

            if (!($response['success'] ?? false) || empty($response['data']['checkout_url'])) {
                Log::warning('PaymentService failed or no checkout_url', [
                    'success' => $response['success'] ?? false,
                    'full_response' => $response,
                ]);
                $this->createPaymentFallback($order);
                return;
            }

            // Extract normalized response from PaymentService
            $paymentData = $response['data'];
            
            Log::info('Payment initialized from PaymentService', [
                'reference' => $paymentData['reference'] ?? null,
                'amount' => $paymentData['amount'] ?? null,
                'fee_merchant' => $paymentData['fee_merchant'] ?? null,
                'checkout_url_exists' => isset($paymentData['checkout_url']),
            ]);
            
            // Save complete Tripay response to database
            $order->update([
                'tripay_reference' => $paymentData['reference'] ?? null,
                'tripay_response' => $paymentData,
            ]);
            
            // Set modal properties with normalized data from PaymentService
            $this->tripayPaymentUrl = $paymentData['checkout_url'];
            $this->tripayReferenceCode = $paymentData['reference'];
            $this->tripayPaymentData = $paymentData;
            $this->tripayMerchantCode = $paymentData['merchant_ref'] ?? null;

            // Use amount from Tripay response
            $paymentAmount = (int) ($paymentData['amount'] ?? $this->totalAmount);
            $this->totalAmount = $paymentAmount;

            // Admin fee (Tripay):
            // - Prefer fee charged to customer (`fee_customer`) if > 0
            // - Otherwise fallback to `total_fee` (some channels put fee on merchant)
            // - Otherwise fallback to diff between Tripay amount and base total
            $feeCustomer = $paymentData['fee_customer'] ?? null;
            $totalFee = $paymentData['total_fee'] ?? null;
            $feeMerchant = $paymentData['fee_merchant'] ?? null;

            $feeCustomerInt = is_numeric($feeCustomer) ? (int) $feeCustomer : null;
            $totalFeeInt = is_numeric($totalFee) ? (int) $totalFee : null;
            $feeMerchantInt = is_numeric($feeMerchant) ? (int) $feeMerchant : null;

            if ($feeCustomerInt !== null && $feeCustomerInt > 0) {
                $this->tripayFee = $feeCustomerInt;
            } elseif ($totalFeeInt !== null && $totalFeeInt > 0) {
                $this->tripayFee = $totalFeeInt;
            } elseif ($feeMerchantInt !== null && $feeMerchantInt > 0) {
                $this->tripayFee = $feeMerchantInt;
            } else {
                $baseTotal = (int) $this->subtotal + (int) $this->shippingCost - (int) $this->discount;
                $this->tripayFee = max(0, $paymentAmount - $baseTotal);
            }
            
            Log::info('✅ Tripay payment initialized successfully', [
                'reference' => $this->tripayReferenceCode,
                'method' => $this->selectedPaymentMethod['id'],
                'amount' => $this->totalAmount,
                'fee' => $this->tripayFee,
                'tripayPaymentUrl_set' => !empty($this->tripayPaymentUrl),
                'tripayPaymentData_set' => !empty($this->tripayPaymentData),
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Error in initializeTripayPayment: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            $this->createPaymentFallback($order);
        }
    }

    /**
     * Create payment fallback - redirect ke halaman pembayaran dengan metode
     */
    private function createPaymentFallback($order)
    {
        // Generate payment URL based on payment method
        $methodId = $this->selectedPaymentMethod['id'] ?? 'QRIS';
        
        // Fallback payment URL - gunakan payment.checkout route yang sudah ada
        $this->tripayPaymentUrl = route('payment.checkout', [
            'order' => $order->id,
        ]);
        
        $this->tripayReferenceCode = $order->order_number;
        
        // Set fallback data so modal shows
        $this->tripayPaymentData = [
            'reference' => $this->tripayReferenceCode,
            'amount' => (int) $this->totalAmount,
            'checkout_url' => $this->tripayPaymentUrl,
            'method' => $methodId,
            'fallback' => true,
        ];
        
        Log::warning('⚠️ USING PAYMENT FALLBACK (PaymentService failed)', [
            'order_id' => $order->id,
            'method' => $methodId,
            'url' => $this->tripayPaymentUrl,
            'reference' => $this->tripayReferenceCode,
            'amount' => $this->totalAmount,
        ]);
    }

    /**
     * Close Tripay modal
     */
    public function closeTripayModal()
    {
        $this->showTripayModal = false;
    }

    /**
     * Check payment status from database (polls for webhook updates)
     * Called via polling from modal
     */
    public function checkPaymentStatus()
    {
        Log::info('===== checkPaymentStatus() CALLED =====');
        
        if (!$this->currentOrderId) {
            Log::warning('checkPaymentStatus: currentOrderId is null or empty!');
            return;
        }

        Log::info('checkPaymentStatus: currentOrderId = ' . $this->currentOrderId);

        try {
            $order = Order::with('items.product')->find($this->currentOrderId);
            
            if (!$order) {
                Log::warning('checkPaymentStatus: Order not found! ID: ' . $this->currentOrderId);
                return;
            }

            Log::info('checkPaymentStatus: Order found', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_status' => $order->payment_status,
                'tripay_response_exists' => !empty($order->tripay_response),
            ]);

            // Update payment status from order (using payment_status column)
            $this->paymentStatus = $order->payment_status;
            Log::info('Set paymentStatus to: ' . $this->paymentStatus);
            
            // IMPORTANT: Set ALL order details for modal display
            $this->orderNumber = $order->order_number;
            $this->orderShippingAddress = $order->shipping_address;
            $this->subtotal = (int) $order->subtotal;
            $this->shippingCost = (int) $order->shipping_cost;
            
            // Get Tripay response from database
            $tripayResponse = is_array($order->tripay_response)
                ? $order->tripay_response
                : (is_string($order->tripay_response) ? json_decode($order->tripay_response, true) : null);

            // Keep modal data synced
            if (is_array($tripayResponse)) {
                $this->tripayPaymentData = $tripayResponse;
                if (!empty($tripayResponse['checkout_url'])) {
                    $this->tripayPaymentUrl = $tripayResponse['checkout_url'];
                }
            }

            // Determine Tripay expiry
            $expiresAt = null;
            try {
                if (is_array($tripayResponse) && !empty($tripayResponse['expired_time']) && is_numeric($tripayResponse['expired_time'])) {
                    $expiresAt = Carbon::createFromTimestamp((int) $tripayResponse['expired_time']);
                } elseif (is_array($tripayResponse) && !empty($tripayResponse['expired_at']) && is_string($tripayResponse['expired_at'])) {
                    $expiresAt = Carbon::parse($tripayResponse['expired_at']);
                }
            } catch (\Throwable $e) {
                $expiresAt = null;
            }

            // Auto-expire (order) when already past deadline
            if (in_array($order->payment_status, ['unpaid', 'pending'], true) && $expiresAt && $expiresAt->isPast()) {
                $order->update(['payment_status' => 'expired']);
                $order->refresh();
                $this->paymentStatus = $order->payment_status;
            }

            Log::info('Tripay Response', [
                'is_array' => is_array($tripayResponse),
                'has_amount' => is_array($tripayResponse) && array_key_exists('amount', $tripayResponse),
                'amount' => is_array($tripayResponse) ? ($tripayResponse['amount'] ?? 'NOT SET') : 'NOT SET',
                'fee_merchant' => is_array($tripayResponse) ? ($tripayResponse['fee_merchant'] ?? 'NOT SET') : 'NOT SET',
                'expired_at' => $expiresAt?->format('d-m-Y H:i:s'),
            ]);

            // CRITICAL: Use Tripay amount as totalAmount, not order total
            if (is_array($tripayResponse) && isset($tripayResponse['amount'])) {
                // Use amount from Tripay response (what customer actually pays)
                $this->totalAmount = (int) $tripayResponse['amount'];

                $feeCustomer = $tripayResponse['fee_customer'] ?? null;
                $totalFee = $tripayResponse['total_fee'] ?? null;
                $feeMerchant = $tripayResponse['fee_merchant'] ?? null;

                $feeCustomerInt = is_numeric($feeCustomer) ? (int) $feeCustomer : null;
                $totalFeeInt = is_numeric($totalFee) ? (int) $totalFee : null;
                $feeMerchantInt = is_numeric($feeMerchant) ? (int) $feeMerchant : null;

                if ($feeCustomerInt !== null && $feeCustomerInt > 0) {
                    $this->tripayFee = $feeCustomerInt;
                } elseif ($totalFeeInt !== null && $totalFeeInt > 0) {
                    $this->tripayFee = $totalFeeInt;
                } elseif ($feeMerchantInt !== null && $feeMerchantInt > 0) {
                    $this->tripayFee = $feeMerchantInt;
                } else {
                    $discount = (int) ($order->discount ?? $this->discount ?? 0);
                    $baseTotal = (int) $order->subtotal + (int) $order->shipping_cost - $discount;
                    $this->tripayFee = max(0, $this->totalAmount - $baseTotal);
                }

                Log::info('Set from Tripay response', [
                    'totalAmount' => $this->totalAmount,
                    'tripayFee' => $this->tripayFee,
                    'fee_customer' => $feeCustomer,
                    'fee_merchant' => $feeMerchant,
                    'total_fee' => $totalFee,
                ]);
            } else {
                // Fallback: jangan overwrite nilai yang sudah ada dari inisialisasi Tripay
                if (empty($this->totalAmount)) {
                    $this->totalAmount = (int) $order->total;
                }
                if (empty($this->tripayFee)) {
                    $this->tripayFee = 0;
                }

                Log::warning('Tripay response missing, using fallback', [
                    'totalAmount' => $this->totalAmount,
                    'tripayFee' => $this->tripayFee,
                ]);
            }

            // Set order items details
            $this->orderItems = $order->display_items->map(fn($item) => [
                'product_title' => $item->product->title ?? 'Product',
                'quantity' => $item->quantity,
                'price' => $item->price,
            ])->toArray();

            // Set status message
            $expiresLabel = $expiresAt?->format('d-m-Y H:i:s');

            if ($order->payment_status === 'paid') {
                $paidAt = $order->paid_at ?? now();
                $this->paymentPaidAt = $paidAt->format('d F Y H:i');
                $this->paymentStatusMessage = "Transaksi ini sudah berhasil dibayar pada {$this->paymentPaidAt} WIB";

                Log::info('✅ Payment PAID - Success state set!', [
                    'order_id' => $this->currentOrderId,
                    'paid_at' => $this->paymentPaidAt,
                    'tripay_amount' => $this->totalAmount,
                    'tripay_fee' => $this->tripayFee,
                    'items_count' => count($this->orderItems),
                ]);
            } elseif ($order->payment_status === 'expired') {
                $this->paymentStatusMessage = 'Pembayaran kadaluarsa.';
            } elseif ($order->payment_status === 'failed') {
                $this->paymentStatusMessage = 'Pembayaran gagal. Silakan coba metode pembayaran lain.';
            } elseif ($order->payment_status === 'refunded') {
                $this->paymentStatusMessage = 'Pembayaran dikembalikan (refunded). Silakan hubungi admin jika ada pertanyaan.';
            } else {
                // Info batas pembayaran ditampilkan terpisah di UI (jangan duplikasi di message)
                $this->paymentStatusMessage = 'Menunggu pembayaran.';
            }

            Log::info('===== checkPaymentStatus() COMPLETE =====', [
                'status' => $this->paymentStatus,
                'totalAmount' => $this->totalAmount,
                'tripayFee' => $this->tripayFee,
                'subtotal' => $this->subtotal,
                'shippingCost' => $this->shippingCost,
            ]);

        } catch (\Exception $e) {
            Log::error('ERROR in checkPaymentStatus: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Redirect to payment success (called from Tripay callback)
     */
    public function completePayment($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            
            // Verify order belongs to current user
            if ($order->user_id !== $this->user->id) {
                throw new \Exception('Unauthorized access to order');
            }

            // Clear cart
            if ($this->cart) {
                $this->cart->items()->delete();
                $this->cart->delete();
            }

            // Close modal and redirect
            $this->showTripayModal = false;
            
            return redirect()->route('checkout.success', ['order_id' => $order->id])
                ->with('success', 'Pembayaran berhasil! Terima kasih atas pesanan Anda.');
        } catch (\Exception $e) {
            Log::error('Error completing payment: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Calculate shipping (RajaOngkir integration with multiple couriers)
     */
    public function calculateShipping()
    {
        $this->shippingOptionsError = null;
        $this->shippingOptionsSource = null;

        Log::info('calculateShipping() called', [
            'districtId' => $this->districtId,
            'district' => $this->district,
            'city' => $this->city,
            'province' => $this->province,
            'current_shipping_options_count' => count($this->shippingOptions)
        ]);

        // If we don't have district ID, try to get it from district name
        if (!$this->districtId && $this->district) {
            Log::info('District ID missing, trying to fetch from API');
            $this->loadLocationIds();
        }

        // Check if district is the same as origin
        $originDistrictId = ShippingOrigin::currentOriginDistrictId() ?: config('services.raja_ongkir.origin_district_id');
        if ($this->districtId == $originDistrictId) {
            $this->shippingOptionsError = 'Destination district sama dengan origin (fallback).';
            Log::warning('Destination district is the same as origin, using mock data', [
                'origin' => $originDistrictId,
                'destination' => $this->districtId
            ]);
            $this->forceMockShipping();
            return;
        }

        // Use mock shipping if destination districtId belum ada
        if (!$this->districtId) {
            $this->shippingOptionsError = 'District ID tujuan belum terbaca (fallback).';
            Log::warning('No district ID available, using mock shipping options', [
                'districtId' => $this->districtId,
                'district' => $this->district
            ]);
            $this->forceMockShipping();
            return;
        }

        $apiKey = config('services.raja_ongkir.api_key');
        $calculateUrl = config('services.raja_ongkir.calculate_cost_url');

        if (empty($apiKey) || empty($calculateUrl) || empty($originDistrictId)) {
            $this->shippingOptionsError = 'Konfigurasi shipping API belum lengkap (fallback).';
            Log::warning('Shipping API config missing, using mock', [
                'apiKey_set' => !empty($apiKey),
                'calculateUrl' => $calculateUrl,
                'originDistrictId' => $originDistrictId,
            ]);
            $this->forceMockShipping();
            return;
        }

        $this->loadingShippingOptions = true;

        try {
            // Get cart weight (assuming 500g per item)
            $totalWeight = collect($this->cartItems)->sum('quantity') * 500;
            if ($totalWeight < 1000) {
                $totalWeight = 1000; // Minimum 1kg
            }

            // IMPORTANT: Jangan loop 8x request (cepet kena limit).
            // Komerce RajaOngkir support multiple couriers via `courier` dengan format dipisah ':'
            // Jika admin tidak memilih jasa kirim, kita TIDAK kirim param `courier` => biarkan API return yang tersedia.
            $courierParam = ShippingOrigin::currentCourierParam(); // empty string = AUTO

            $cacheKey = 'shipping:cost:' . $originDistrictId . ':' . $this->districtId . ':' . $totalWeight . ':' . ($courierParam ?: 'auto');

            if (Cache::has($cacheKey)) {
                $data = Cache::get($cacheKey);
                $this->shippingOptionsSource = 'cache';
                Log::info('Shipping options loaded from cache', [
                    'cacheKey' => $cacheKey,
                    'count' => is_array($data) ? count($data) : 0,
                ]);
            } else {
                Log::info('Calling RajaOngkir shipping API (single request, multi-courier)', [
                    'origin_district_id' => $originDistrictId,
                    'destination_district_id' => $this->districtId,
                    'weight' => $totalWeight,
                    'url' => $calculateUrl,
                    'courier' => $courierParam,
                ]);

                $payload = [
                    'origin' => $originDistrictId,
                    'destination' => $this->districtId,
                    'weight' => $totalWeight,
                ];

                if (!empty($courierParam)) {
                    $payload['courier'] = $courierParam;
                }

                $response = Http::withHeaders([
                    'key' => $apiKey,
                ])->timeout(15)->asForm()->post($calculateUrl, $payload);

                if (!$response->successful()) {
                    $json = null;
                    try { $json = $response->json(); } catch (\Exception $e) { $json = null; }

                    $apiMsg = $json['meta']['message'] ?? $json['message'] ?? $response->body();
                    $this->shippingOptionsError = 'Shipping API gagal (' . $response->status() . '): ' . (is_string($apiMsg) ? $apiMsg : 'unknown');

                    Log::warning('Shipping API failed, using mock', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    $this->forceMockShipping();
                    return;
                }

                $data = $response->json();
                $this->shippingOptionsSource = 'api';

                // Cache only success response (biar hemat limit)
                Cache::put($cacheKey, $data, now()->addHours(6));
            }

            $allShippingOptions = $this->parseShippingOptions($data);

            if (!empty($allShippingOptions)) {
                $this->shippingOptions = $allShippingOptions;
                Log::info('All shipping options loaded', ['total_count' => count($this->shippingOptions), 'source' => $this->shippingOptionsSource]);
                $this->dispatch('notify', type: 'success', message: 'Opsi pengiriman berhasil dimuat');
            } else {
                $this->shippingOptionsError = 'Tidak ada opsi dari API (fallback).';
                Log::warning('No shipping options from API response, using mock data');
                $this->forceMockShipping();
            }

        } catch (\Exception $e) {
            $this->shippingOptionsError = 'Exception: ' . $e->getMessage();
            Log::error('Error in calculateShipping: ' . $e->getMessage());
            $this->forceMockShipping();
        } finally {
            $this->loadingShippingOptions = false;
        }
    }
    
    /**
     * Parse shipping options from RajaOngkir API response
     */
    private function parseShippingOptions($data): array
    {
        $options = [];
        
        Log::info('parseShippingOptions called', [
            'has_data_key' => isset($data['data']),
            'data_is_array' => isset($data['data']) && is_array($data['data']),
            'data_count' => isset($data['data']) && is_array($data['data']) ? count($data['data']) : 0
        ]);
        
        if (!isset($data['data']) || !is_array($data['data'])) {
            $this->shippingOptionsError = 'Struktur response shipping API tidak valid (fallback).';
            Log::warning('Invalid API response structure', [
                'response_keys' => is_array($data) ? array_keys($data) : null,
            ]);
            return [];
        }
        
        foreach ($data['data'] as $courierData) {
            $courierCode = $courierData['code'] ?? '';
            $courierName = $courierData['name'] ?? strtoupper($courierCode);
            $service = $courierData['service'] ?? '';
            $description = $courierData['description'] ?? '';
            $costValue = $courierData['cost'] ?? 0;
            $etd = $courierData['etd'] ?? '';
            
            // Skip invalid entries
            if (empty($courierCode) || empty($service) || $costValue <= 0) {
                continue;
            }
            
            // Skip specialized services that are not suitable for regular e-commerce
            $skipPatterns = ['jtr', 'motor', 'otopack']; // Skip trucking, motorcycle, automotive shipping
            $shouldSkip = false;
            foreach ($skipPatterns as $pattern) {
                if (str_contains(strtolower($service), $pattern)) {
                    $shouldSkip = true;
                    break;
                }
            }
            if ($shouldSkip) {
                continue;
            }
            
            // Format ETD
            if (is_array($etd)) {
                $etd = implode(' - ', $etd);
            }
            if ($etd && !str_contains(strtolower($etd), 'hari') && !str_contains(strtolower($etd), 'day')) {
                $etd .= ' hari';
            }
            
            $options[] = [
                'courier' => $courierCode,
                'name' => $courierName,
                'service' => $service,
                'description' => $description,
                'cost' => (int) $costValue,
                'etd' => $etd ?: 'Estimasi tersedia'
            ];
        }
        
        // If no options found, return mock data
        if (empty($options)) {
            Log::warning('No shipping options found from API, using mock data');
            return $this->getMockShippingOptions();
        }
        
        // Sort by cost (lowest first)
        usort($options, function($a, $b) {
            return $a['cost'] - $b['cost'];
        });
        
        return $options;
    }

    /**
     * Select shipping service
     */
    public function updatedSelectedShippingService($value)
    {
        if (empty($value)) {
            $this->selectedShippingService = null;
            $this->shippingCost = 0;
            $this->calculateSubtotal();
            return;
        }

        try {
            $service = json_decode($value, true);
            
            if (!$service) {
                Log::error('Failed to decode shipping service', ['value' => $value]);
                return;
            }

            // Find the full service option with all details
            $fullOption = collect($this->shippingOptions)->first(function($option) use ($service) {
                return $option['courier'] === $service['courier'] && 
                       $option['service'] === $service['service'] &&
                       $option['cost'] === $service['cost'];
            });

            if ($fullOption) {
                $this->selectedShippingService = $fullOption;
                $this->shippingCost = $fullOption['cost'];
                $this->calculateSubtotal();
                Log::info('Shipping service selected', [
                    'courier' => $fullOption['courier'],
                    'service' => $fullOption['service'],
                    'cost' => $fullOption['cost'],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating shipping service: ' . $e->getMessage());
        }
    }

    /**
     * Select payment method
     */
    public function selectPaymentMethod(string $methodId)
    {
        if (empty($methodId)) {
            $this->selectedPaymentMethod = null;
            $this->paymentFee = 0;
            $this->calculateSubtotal();
            return;
        }

        $method = collect($this->paymentMethods)->firstWhere('id', $methodId);
        
        if (!$method) {
            $this->dispatch('notify', type: 'error', message: 'Metode pembayaran tidak valid');
            return;
        }

        $this->selectedPaymentMethod = $method;
        $this->paymentFee = (int) ($method['fee'] ?? 0);
        $this->calculateSubtotal();
        $this->dispatch('notify', type: 'success', message: 'Metode pembayaran dipilih: ' . $method['name']);
    }

    /**
     * Update selected payment method from dropdown (wire:model binding)
     */
    public function updatedSelectedPaymentMethod($value)
    {
        if (empty($value)) {
            $this->selectedPaymentMethod = null;
            $this->paymentFee = 0;
            $this->calculateSubtotal();
            return;
        }

        // Value is JSON string from dropdown
        try {
            $method = json_decode($value, true);
            if (is_array($method)) {
                $this->selectedPaymentMethod = $method;
                $this->paymentFee = (int) ($method['fee'] ?? 0);
                $this->calculateSubtotal();
            }
        } catch (\Exception $e) {
            Log::error('Error updating payment method: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to get location name from ID
     */
    private function getLocationName($id, $locations): string
    {
        if (!$id || empty($locations)) {
            return '';
        }

        $location = collect($locations)->firstWhere('id', $id);
        return $location ? $location['name'] : '';
    }

    #[\Livewire\Attributes\Layout('layouts.checkout')]
    public function render()
    {
        return view('livewire.checkout');
    }
}
