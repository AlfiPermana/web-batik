<x-layouts.app>
<div class="workshop-payment-page bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen py-8 md:py-12">
    <div class="container mx-auto px-4 max-w-5xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">💳 Pembayaran Deposit Workshop</h1>
            <p class="text-gray-600">Booking ID: <span class="font-mono font-bold text-amber-700">{{ $booking->booking_number }}</span></p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Booking Summary Card -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-600 to-amber-700 px-6 py-4">
                        <h2 class="text-lg md:text-xl font-bold text-white">📋 Ringkasan Booking</h2>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <!-- Workshop Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Paket</p>
                                <p class="font-semibold text-gray-800">{{ $booking->workshopAvailableDate?->workshop?->title ?? $booking->workshop_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Tanggal</p>
                                <p class="font-semibold text-gray-800">{{ $booking->workshopAvailableDate?->date?->format('d M Y') ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Jam</p>
                                <p class="font-semibold text-gray-800">{{ $booking->workshopAvailableDate?->start_time ?? 'N/A' }} - {{ $booking->workshopAvailableDate?->end_time ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Jumlah Peserta</p>
                                <p class="font-semibold text-gray-800">{{ $booking->num_participants }} orang</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-600 mb-1">Nama Pelanggan</p>
                                <p class="font-semibold text-gray-800">{{ $booking->customer_name }}</p>
                            </div>
                        </div>

                        <!-- Payment Amount Section -->
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4 md:p-6 mt-6">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 font-semibold">Total Biaya Workshop:</span>
                                    <span class="text-2xl md:text-3xl font-bold text-blue-700">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                </div>
                                <div class="border-t border-blue-200 pt-3">
                                    <p class="text-xs text-gray-600">Pembayaran penuh harus diselesaikan sebelum workshop dimulai</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Action Section -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                        <h2 class="text-lg md:text-xl font-bold text-white">🔐 Pilih Metode Pembayaran</h2>
                    </div>
                    
                    <div class="p-6">
                        <p class="text-gray-600 mb-6 text-sm md:text-base">Klik tombol "Bayar Sekarang" di bawah untuk memilih metode pembayaran yang tersedia</p>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('workshop.my-bookings') }}" class="flex-1 px-4 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all font-bold text-center">
                                ← Kembali
                            </a>
                            <button 
                                type="button"
                                onclick="openPaymentModal()"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white rounded-lg transition-all font-bold shadow-md hover:shadow-lg">
                                💳 Bayar Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Important Notes -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-l-4 border-amber-500 rounded-lg p-5 shadow-sm">
                    <h3 class="font-bold text-amber-900 mb-3 flex items-center">
                        <span class="mr-2 text-lg">⚠️</span> Penting
                    </h3>
                    <ul class="text-sm text-amber-800 space-y-2">
                        <li class="flex items-start">
                            <span class="mr-2 font-bold">✓</span>
                            <span>Pembayaran penuh harus diselesaikan sebelum workshop dimulai</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 font-bold">✓</span>
                            <span>Konfirmasi pembayaran dalam 1-2 jam</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 font-bold">✓</span>
                            <span>Pembayaran valid selama 24 jam</span>
                        </li>
                    </ul>
                </div>

                <!-- Contact Support -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                        <span class="mr-2 text-lg">📞</span> Butuh Bantuan?
                    </h3>
                    <div class="space-y-3 text-sm">
                        <p class="flex items-center text-gray-700 hover:text-blue-600 transition-colors">
                            <span class="mr-3 text-lg">📞</span>
                            <a href="tel:+628123456789" class="hover:underline font-medium">+62 812-3456-789</a>
                        </p>
                        <p class="flex items-center text-gray-700 hover:text-blue-600 transition-colors">
                            <span class="mr-3 text-lg">📧</span>
                            <a href="mailto:info@batikal.com" class="hover:underline font-medium">info@batikal.com</a>
                        </p>
                        <p class="flex items-center text-gray-700 hover:text-green-600 transition-colors">
                            <span class="mr-3 text-lg">💬</span>
                            <a href="#" class="hover:underline font-medium">WhatsApp Chat</a>
                        </p>
                    </div>
                </div>

                <!-- Booking Status -->
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border-l-4 border-blue-500 rounded-lg p-5 shadow-sm">
                    <h3 class="font-bold text-blue-900 mb-3 flex items-center">
                        <span class="mr-2 text-lg">📋</span> Status Booking
                    </h3>
                    <div class="text-sm space-y-3">
                        <p class="flex justify-between">
                            <span class="text-gray-700">No. Booking:</span>
                            <span class="font-mono font-bold text-blue-700">{{ $booking->booking_number }}</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-gray-700">Status:</span>
                            <span class="font-bold text-yellow-600 bg-yellow-100 px-2 py-1 rounded">⏳ Menunggu</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-gray-700">Terdaftar:</span>
                            <span class="font-bold text-gray-800">{{ $booking->created_at->format('d M Y') }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Debug CSRF Token availability
console.log('Page loaded, checking CSRF token...');

let selectedMethod = '';
let selectedPaymentType = 'tripay'; // Default ke online payment

// ========== GLOBAL FUNCTIONS (defined before DOMContentLoaded) ==========
// These must be available immediately for inline onclick handlers

function openPaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (!modal) return;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    // Reset form
    const form = document.getElementById('modalPaymentForm');
    if (form) form.reset();
    const tripayDiv = document.getElementById('tripayMethodsDiv');
    if (tripayDiv) tripayDiv.classList.add('hidden');
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (!modal) return;
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function showTripayMethods() {
    const div = document.getElementById('tripayMethodsDiv');
    if (div) div.classList.remove('hidden');
}

function hideTripayMethods() {
    const div = document.getElementById('tripayMethodsDiv');
    if (div) div.classList.add('hidden');
    const select = document.getElementById('tripayMethods');
    if (select) select.value = '';
}

function selectPaymentMethod(method) {
    selectedMethod = method;
    const proofSection = document.getElementById('proofUploadSection');

    if (method === 'credit_card') {
        if (proofSection) proofSection.classList.add('hidden');
        const proofFile = document.getElementById('proofFile');
        if (proofFile) proofFile.removeAttribute('required');
    } else {
        if (proofSection) proofSection.classList.remove('hidden');
        const proofFile = document.getElementById('proofFile');
        if (proofFile) proofFile.setAttribute('required', 'required');
    }

    // Show/hide details
    document.querySelectorAll('[class$="-details"]').forEach(el => el.classList.add('hidden'));
    const detailsElement = document.querySelector('.' + method + '-details');
    if (detailsElement) {
        detailsElement.classList.remove('hidden');
    }

    updateSubmitButton();
}

function updateFileName() {
    const fileInput = document.getElementById('proofFile');
    const fileNameDisplay = document.getElementById('fileName');
    const fileNameText = document.getElementById('fileNameText');

    if (fileInput && fileInput.files.length > 0) {
        if (fileNameText) fileNameText.textContent = fileInput.files[0].name;
        if (fileNameDisplay) fileNameDisplay.classList.remove('hidden');
    } else if (fileNameDisplay) {
        fileNameDisplay.classList.add('hidden');
    }
}

function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });

    // Remove active state from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-amber-600', 'text-amber-600');
        btn.classList.add('border-gray-300', 'text-gray-600');
    });

    // Show selected tab
    const selectedTab = document.getElementById(tabName + '-tab');
    if (selectedTab) {
        selectedTab.classList.remove('hidden');
    }

    // Add active state to clicked button
    if (event && event.target) {
        event.target.classList.remove('border-gray-300', 'text-gray-600');
        event.target.classList.add('border-amber-600', 'text-amber-600');
    }
}

function updateSubmitButton() {
    const method = document.querySelector('input[name="payment_method"]:checked');
    const agreeTerms = document.querySelector('input[name="agree_terms"]:checked');
    const hasFile = document.getElementById('proofFile')?.files?.length > 0;
    const submitBtn = document.getElementById('submitBtn');

    if (!submitBtn) return;

    let canSubmit = method && agreeTerms;

    if (method && (method.value === 'bank_transfer' || method.value === 'ewallet')) {
        canSubmit = canSubmit && hasFile;
    }

    submitBtn.disabled = !canSubmit;
}

function selectPaymentType(type) {
    selectedPaymentType = type;
}

function showSuccessMessage(message) {
    const alert = document.createElement('div');
    alert.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-[9999] animate-pulse';
    alert.textContent = message;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 3000);
}

function showErrorMessage(message) {
    const alert = document.createElement('div');
    alert.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-[9999]';
    alert.textContent = message;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 5000);
}

// ========== DOM-DEPENDENT CODE (must run after DOM is ready) ==========

document.addEventListener('DOMContentLoaded', function() {
    // Check CSRF token after DOM is ready
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfInput = document.querySelector('input[name="_token"]');
    console.log('CSRF Meta Tag:', csrfMeta ? csrfMeta.content.substring(0, 10) + '...' : 'NOT FOUND');
    console.log('CSRF Input Field:', csrfInput ? csrfInput.value.substring(0, 10) + '...' : 'NOT FOUND');
    // Tab switching
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
        });
    });

    // Payment method option click
    const methodOptions = document.querySelectorAll('.payment-method-option');
    methodOptions.forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.payment-method-option').forEach(o => {
                o.classList.remove('border-amber-400', 'bg-amber-50');
                o.classList.add('border-gray-200');
            });
            this.classList.remove('border-gray-200');
            this.classList.add('border-amber-400', 'bg-amber-50');
            updateSubmitButton();
        });
    });

    // Setup drag and drop - ONLY IF ELEMENT EXISTS
    const proofFileInput = document.getElementById('proofFile');
    if (proofFileInput && proofFileInput.parentElement) {
        const fileUploadArea = proofFileInput.parentElement.parentElement;

        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('border-amber-400', 'bg-amber-50');
        });

        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('border-amber-400', 'bg-amber-50');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('border-amber-400', 'bg-amber-50');
            if (e.dataTransfer.files.length) {
                proofFileInput.files = e.dataTransfer.files;
                updateFileName();
            }
        });
    }

    // Form submission for manual payment - ONLY IF ELEMENT EXISTS
    const manualForm = document.getElementById('paymentForm');
    if (manualForm) {
        manualForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const method = document.querySelector('input[name="payment_method"]:checked');
            if (!method) {
                Swal.fire({ title: 'Metode Pembayaran', text: 'Silakan pilih metode pembayaran', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                return;
            }

            const proofFileInput = document.getElementById('proofFile');
            if ((method.value === 'bank_transfer' || method.value === 'ewallet') && (!proofFileInput || !proofFileInput.files.length)) {
                Swal.fire({ title: 'Bukti Pembayaran', text: 'Silakan upload bukti pembayaran', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                return;
            }

            const agreeCheckbox = document.querySelector('input[name="agree_terms"]:checked');
            if (!agreeCheckbox) {
                Swal.fire({ title: 'Syarat & Ketentuan', text: 'Silakan setujui syarat dan ketentuan', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                return;
            }

            this.submit();
        });
    }

    // Form submission for Tripay (online payment) - ONLY IF ELEMENT EXISTS
    const tripayForm = document.getElementById('tripayForm');
    if (tripayForm) {
        tripayForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const paymentMethod = document.querySelector('select[name="payment_method"]');
            if (!paymentMethod || !paymentMethod.value) {
                Swal.fire({ title: 'Metode Pembayaran', text: 'Silakan pilih metode pembayaran', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                return;
            }

            const agreeCheckbox = document.querySelector('input[name="agree_terms"]:checked');
            if (!agreeCheckbox) {
                Swal.fire({ title: 'Syarat & Ketentuan', text: 'Silakan setujui syarat dan ketentuan', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                return;
            }

            this.submit();
        });
    }

    // Modal form submission - ONLY IF ELEMENT EXISTS
    const modalPaymentForm = document.getElementById('modalPaymentForm');
    if (modalPaymentForm) {
        modalPaymentForm.addEventListener('submit', handleModalPaymentSubmit);
    }

    // Setup agree terms checkbox change listener - ONLY IF ELEMENT EXISTS
    const agreeCheckbox = document.querySelector('input[name="agree_terms"]');
    if (agreeCheckbox) {
        agreeCheckbox.addEventListener('change', updateSubmitButton);
    }

    // Setup modal click outside close
    const paymentModal = document.getElementById('paymentModal');
    if (paymentModal) {
        paymentModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closePaymentModal();
            }
        });
    }
    
    // Setup Escape key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('paymentModal');
            if (modal && modal.style.display === 'flex') {
                closePaymentModal();
            }
        }
    });
});

// ========== HANDLE MODAL FORM SUBMISSION ==========
function handleModalPaymentSubmit(e) {
    e.preventDefault();

    const paymentMethodType = document.querySelector('#paymentModal input[name="payment_method_type"]:checked');
    const tripayMethod = document.querySelector('#tripayMethods');
    const agreeTerms = document.querySelector('#paymentModal input[name="agree_terms_modal"]:checked');
    const submitBtn = document.getElementById('submitPaymentBtn');

    if (!paymentMethodType) {
        Swal.fire({ title: 'Metode Pembayaran', text: 'Pilih metode pembayaran terlebih dahulu', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
        return;
    }

    if (!agreeTerms) {
        Swal.fire({ title: 'Syarat & Ketentuan', text: 'Setujui syarat dan ketentuan terlebih dahulu', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
        return;
    }

    // Validate Tripay method selection
    if (paymentMethodType.value === 'tripay' && !tripayMethod.value) {
        Swal.fire({ title: 'Metode Pembayaran', text: 'Pilih metode pembayaran Tripay terlebih dahulu', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
        return;
    }

    // Disable submit button to prevent double submission
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ Memproses...';

    // Determine which endpoint to use
    const bookingId = {{ $booking->id }};
    let actionUrl = '';
    let formData = new FormData();
    
    // Get CSRF token - try multiple ways
    let token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!token) {
        token = document.querySelector('input[name="_token"]')?.value;
    }
    if (!token) {
        token = window.Laravel?.csrfToken;
    }
    
    // If still no token, try to extract from cookie
    if (!token) {
        const cookieValue = document.cookie
            .split('; ')
            .find(row => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1];
        if (cookieValue) {
            token = decodeURIComponent(cookieValue);
        }
    }

    console.log('CSRF Token:', token ? '✓ Found' : '✗ Not found');
    
    if (token) {
        formData.append('_token', token);
    }
    
    // CRITICAL: Always append 'type' parameter - default to 'full'
    formData.append('type', 'full');

    if (paymentMethodType.value === 'tripay') {
        actionUrl = `/workshop/booking/${bookingId}/process-payment`;
        formData.append('payment_method', tripayMethod.value);
    } else {
        actionUrl = `/workshop/booking/${bookingId}/store-payment`;
        formData.append('payment_method', 'bank_transfer');
    }

    console.log('Submit data before fetch:', {
        url: actionUrl,
        type: 'full',
        payment_method: paymentMethodType.value === 'tripay' ? tripayMethod.value : 'bank_transfer',
        token: token ? 'present' : 'missing'
    });

    // Log FormData contents
    console.log('FormData contents:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: ${value}`);
    }

    // Submit using fetch
    fetch(actionUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(token && { 'X-CSRF-TOKEN': token })
            // NOTE: DO NOT set Content-Type - let browser set it for FormData
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.checkout_url) {
            // Redirect ke Tripay checkout
            window.location.href = data.checkout_url;
        } else if (data.success) {
            showSuccessMessage('Pembayaran berhasil diproses!');
            setTimeout(() => {
                window.location.href = '/workshop/my-bookings';
            }, 2000);
        } else if (data.payment_details) {
            // Tampilkan payment details modal untuk instruksi pembayaran
            closePaymentModal();
            displayPaymentDetails({
                amount: data.amount,
                method: data.method,
                payment_details: data.payment_details
            });
        } else {
            showErrorMessage(data.message || 'Terjadi kesalahan saat memproses pembayaran');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage(error.message || 'Terjadi kesalahan saat memproses pembayaran');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Lanjut Ke Pembayaran →';
    });
}

// ===== ENHANCED MODAL ANIMATIONS =====
const paymentModal = document.getElementById('paymentModal');
if (paymentModal) {
    // Add smooth transitions
    const originalDisplay = paymentModal.style.display;
    
    const originalOpenModal = window.openPaymentModal;
    window.openPaymentModal = function() {
        originalOpenModal();
        paymentModal.classList.add('animate-in', 'fade-in', 'zoom-in', 'duration-300');
        paymentModal.querySelector('.bg-white')?.classList.add('animate-in', 'zoom-in', 'duration-300');
    };
    
    const originalCloseModal = window.closePaymentModal;
    window.closePaymentModal = function() {
        paymentModal.classList.remove('animate-in', 'fade-in', 'zoom-in');
        paymentModal.classList.add('animate-out', 'fade-out', 'zoom-out');
        setTimeout(() => {
            originalCloseModal();
            paymentModal.classList.remove('animate-out', 'fade-out', 'zoom-out');
        }, 200);
    };
}

// Add keyboard escape support
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const modal = document.getElementById('paymentModal');
        if (modal && modal.style.display === 'flex') {
            closePaymentModal();
        }
    }
});
</script>

<!-- ===== TAILWIND ANIMATIONS ===== -->
<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    
    @keyframes zoomIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    @keyframes zoomOut {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.95);
        }
    }
    
    .animate-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .zoom-in {
        animation: zoomIn 0.3s ease-in-out;
    }
    
    .animate-out {
        animation: fadeOut 0.2s ease-in-out;
    }
    
    .fade-out {
        animation: fadeOut 0.2s ease-in-out;
    }
    
    .zoom-out {
        animation: zoomOut 0.2s ease-in-out;
    }
    
    .duration-300 {
        animation-duration: 300ms;
    }
    
    /* Enhanced hover effects */
    .group:has(input:checked) {
        @apply border-amber-600 bg-gradient-to-r from-amber-50 to-orange-50;
    }
    
    /* Smooth transitions for all interactive elements */
    button, input, select, textarea {
        @apply transition-all duration-200;
    }
    
    /* Better focus states */
    input:focus, select:focus, textarea:focus {
        @apply ring-2 ring-amber-300 ring-offset-1;
    }
</style>
@endpush

@push('styles')
<style>
    .payment-method-option {
        transition: all 0.3s ease;
    }

    .payment-method-option:hover {
        background-color: #faf5f0;
    }

    .payment-method-option input[type="radio"]:checked ~ * {
        font-weight: 600;
    }
</style>
@endpush

        </div>
    </div>
</div>

<!-- Include Payment Details Modal Component -->
<x-payment-details-modal />

</div>
</x-layouts.app>

<!-- ===== ENHANCED PAYMENT MODAL ===== -->
<div id="paymentModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 backdrop-blur-sm" style="display: none;">
    <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[95vh] overflow-hidden flex flex-col animate-in fade-in zoom-in duration-300">
        <!-- ===== MODAL HEADER ===== -->
        <div class="bg-gradient-to-r from-amber-600 via-amber-500 to-orange-600 px-6 md:px-8 py-6 md:py-8 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <span class="text-2xl">💳</span>
                </div>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-white">Selesaikan Pembayaran</h2>
                    <p class="text-amber-100 text-sm">Pilih metode pembayaran terpercaya</p>
                </div>
            </div>
            <button onclick="closePaymentModal()" class="text-white hover:bg-white/20 p-2 rounded-full transition-all hover:scale-110">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- ===== MODAL BODY ===== -->
        <div class="overflow-y-auto flex-1 p-6 md:p-8 space-y-6">
            <!-- Progress Indicator -->
            <div class="flex items-center justify-center gap-3">
                <div class="flex-1 h-2 bg-amber-600 rounded-full"></div>
                <span class="text-sm font-bold text-gray-700 bg-amber-100 px-4 py-2 rounded-full">Langkah 1: Pilih Metode</span>
                <div class="flex-1 h-2 bg-gray-300 rounded-full"></div>
            </div>

            <!-- SUMMARY CARD - More Eye-Catching -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 p-6 md:p-8 text-white shadow-lg">
                <!-- Background pattern -->
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full -ml-16 -mb-16"></div>
                
                <!-- Content -->
                <div class="relative z-10">
                    <p class="text-blue-100 text-sm font-medium mb-2">📋 Total Pembayaran</p>
                    <div class="flex items-baseline gap-2 mb-4">
                        <span class="text-4xl md:text-5xl font-bold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="bg-white/20 h-px mb-4"></div>
                    <div class="grid grid-cols-3 gap-3 text-sm">
                        <div>
                            <p class="text-blue-100">Workshop</p>
                            <p class="font-semibold">{{ $booking->workshop_name ?? 'PAKET 1' }}</p>
                        </div>
                        <div>
                            <p class="text-blue-100">Peserta</p>
                            <p class="font-semibold">{{ $booking->num_participants }} orang</p>
                        </div>
                        <div>
                            <p class="text-blue-100">Status</p>
                            <p class="font-semibold">Menunggu</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods Form -->
            <form id="modalPaymentForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="full">

                <div class="space-y-4">
                    <!-- TRIPAY METHOD - Primary -->
                    <div class="group">
                        <label class="block p-5 md:p-6 border-2 border-gray-200 rounded-2xl cursor-pointer hover:border-amber-400 hover:bg-amber-50 transition-all duration-200 group-has-[:checked]:border-amber-600 group-has-[:checked]:bg-gradient-to-r group-has-[:checked]:from-amber-50 group-has-[:checked]:to-orange-50">
                            <div class="flex items-start gap-4">
                                <input type="radio" name="payment_method_type" value="tripay" class="mt-1 w-5 h-5 cursor-pointer accent-amber-600" onchange="showTripayMethods()">
                                <div class="flex-grow">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="font-bold text-gray-800 text-base md:text-lg">💳 Pembayaran Online</h3>
                                        <span class="bg-green-500 text-white text-xs px-3 py-1 rounded-full font-semibold">Rekomendasi</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-4">Transfer bank, e-wallet, atau QRIS - Proses otomatis 1-5 menit</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="inline-flex items-center gap-1 text-xs bg-green-100 text-green-800 px-3 py-1.5 rounded-lg font-medium">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Aman
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-xs bg-blue-100 text-blue-800 px-3 py-1.5 rounded-lg font-medium">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Cepat
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-xs bg-purple-100 text-purple-800 px-3 py-1.5 rounded-lg font-medium">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> 6+ Metode
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </label>
                        
                        <!-- Tripay Methods Dropdown - Enhanced -->
                        <div class="border-2 border-t-0 border-gray-200 bg-gradient-to-b from-gray-50 to-white p-6 rounded-b-2xl hidden" id="tripayMethodsDiv">
                            <label class="block mb-4">
                                <span class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                                    <span class="w-6 h-6 bg-amber-600 text-white rounded-full flex items-center justify-center text-xs">✓</span>
                                    Pilih Metode Pembayaran
                                </span>
                                <select name="payment_method" id="tripayMethods" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl bg-white font-medium text-gray-700 focus:border-amber-600 focus:outline-none transition-colors cursor-pointer">
                                    <option value="">-- Pilih Metode Pembayaran --</option>
                                    <optgroup label="💳 Transfer Bank"
                                        <option value="BCAVA">💳 BCA Virtual Account (BCAVA)</option>
                                        <option value="BRIVA">💳 BRI Virtual Account (BRIVA)</option>
                                        <option value="BNIVA">💳 BNI Virtual Account (BNIVA)</option>
                                        <option value="MANDIRIVA">💳 Mandiri Virtual Account (MANDIRIVA)</option>
                                        <option value="PERMATAVA">💳 Permata Virtual Account (PERMATAVA)</option>
                                        <option value="MYBVA">💳 Maybank Virtual Account (MYBVA)</option>
                                        <option value="CIMBVA">💳 CIMB Virtual Account (CIMBVA)</option>
                                    </optgroup>
                                    <optgroup label="Convenience Store">
                                        <option value="ALFAMART">� Alfamart / Alfacart</option>
                                        <option value="INDOMARET">� Indomaret</option>
                                    </optgroup>
                                    <optgroup label="Scan & Pay">
                                        <option value="QRIS">📲 QRIS (Scan QR Code)</option>
                                    </optgroup>
                                </select>
                            </label>
                        </div>
                    </div>

                    <!-- Manual Transfer Method (Fallback) -->
                    <div class="border-2 border-gray-200 rounded-xl overflow-hidden hover:border-amber-400 transition-all">
                        <label class="p-4 md:p-5 cursor-pointer flex items-start gap-4 hover:bg-gradient-to-r hover:from-amber-50 hover:to-orange-50 transition-colors">
                            <input type="radio" name="payment_method_type" value="manual" class="mt-1 w-5 h-5 cursor-pointer" onchange="hideTripayMethods()">
                            <div class="flex-grow">
                                <h3 class="font-bold text-gray-800 mb-1 text-base md:text-lg">📝 Transfer Manual</h3>
                                <p class="text-sm text-gray-600">Transfer ke rekening bank kami (Verifikasi 2-4 jam)</p>
                            </div>
                        </label>
                        
                        <div class="border-t border-gray-200 bg-blue-50 p-4 md:p-5">
                            <p class="text-xs font-semibold text-gray-700 mb-3">📍 Rekening Tujuan:</p>
                            <div class="bg-white rounded-lg p-3 space-y-2 border border-blue-200">
                                <div>
                                    <p class="text-xs text-gray-600">Bank</p>
                                    <p class="font-bold text-gray-800">🏦 BCA</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Nomor Rekening</p>
                                    <p class="font-mono font-bold text-lg text-amber-700">1234567890</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Atas Nama</p>
                                    <p class="font-bold text-gray-800">PT. Batik Giri Alam</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="bg-amber-50 border-2 border-amber-200 rounded-xl p-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="agree_terms_modal" class="mt-1.5 w-5 h-5 cursor-pointer accent-amber-600" required>
                            <span class="text-sm text-gray-700">
                                Saya memahami dan setuju bahwa:
                                <ul class="text-xs text-gray-600 mt-2 ml-3 space-y-1">
                                    <li>✓ Pembayaran harus diselesaikan dalam 24 jam</li>
                                    <li>✓ Konfirmasi pembayaran dalam 1-2 jam kerja</li>
                                    <li>✓ Tidak ada pengembalian dana setelah pembayaran</li>
                                </ul>
                            </span>
                        </label>
                    </div>

                    <!-- MODAL FOOTER -->
                    <div class="flex gap-3 pt-4 border-t-2 border-gray-200">
                        <button type="button" onclick="closePaymentModal()" class="flex-1 px-4 py-4 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-all">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 px-4 py-4 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed" id="submitPaymentBtn">
                            <span class="flex items-center justify-center gap-2">
                                <span>💳 Lanjut ke Pembayaran</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Include Payment Details Modal Component -->
<x-payment-details-modal />
