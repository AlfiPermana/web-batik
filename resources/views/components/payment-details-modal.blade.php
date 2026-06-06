<!-- 💰 PAYMENT DETAILS MODAL (VA CODE / QR CODE DISPLAY) -->
<div id="paymentDetailsModal" class="fixed inset-0 bg-black/60 -z-50 flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 md:p-6 border-b-2 bg-gradient-to-r from-green-600 to-emerald-600 sticky top-0 z-10">
            <h2 class="text-xl md:text-2xl font-bold text-white">✅ Instruksi Pembayaran</h2>
            <button onclick="closePaymentDetailsModal()" class="text-white hover:bg-green-700 hover:rounded-full p-1 transition-all">
                <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="p-4 md:p-6 space-y-6">
            <!-- Status Alert -->
            <div class="bg-green-50 border-l-4 border-green-600 p-4 rounded-lg">
                <p class="text-green-800 font-semibold">✓ Pesanan Anda telah disiapkan</p>
                <p class="text-sm text-green-700 mt-1">Silakan lakukan pembayaran sesuai instruksi di bawah ini</p>
            </div>

            <!-- Payment Amount -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-300 p-4 md:p-6 rounded-xl">
                <p class="text-xs md:text-sm text-gray-700 font-medium mb-2">Total Biaya Workshop</p>
                <div class="text-3xl md:text-4xl font-bold text-blue-700" id="paymentAmount"></div>
                <p class="text-xs text-gray-600 mt-2">
                    Batas waktu pembayaran: <span id="paymentDeadline" class="font-bold text-red-600"></span>
                </p>
            </div>

            <!-- VA CODE SECTION -->
            <div id="vaCodeSection" style="display: none;">
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-4 md:p-6 border-2 border-purple-300">
                    <h3 class="font-bold text-purple-900 mb-4">🏦 Nomor Virtual Account</h3>
                    
                    <div class="bg-white rounded-lg p-4 md:p-5 border-2 border-dashed border-purple-400">
                        <p class="text-xs text-gray-600 mb-2">Virtual Account Number (VA)</p>
                        <div class="flex items-center justify-between gap-2">
                            <p id="vaCode" class="text-2xl md:text-3xl font-mono font-bold text-purple-700 break-all"></p>
                            <button onclick="copyToClipboard('vaCode')" class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-lg transition-colors">
                                📋 Salin
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2">
                        <h4 class="font-semibold text-purple-900 text-sm">Cara Pembayaran:</h4>
                        <ol class="text-sm text-purple-800 space-y-2 ml-4">
                            <li>1. Buka aplikasi bank atau ATM BCA Anda</li>
                            <li>2. Pilih menu Transfer / Bayar Tagihan</li>
                            <li>3. Masukkan nomor VA di atas</li>
                            <li>4. Verifikasi jumlah pembayaran</li>
                            <li>5. Konfirmasi transaksi</li>
                            <li>6. Pembayaran selesai ✓</li>
                        </ol>
                    </div>

                    <div class="mt-4 bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                        <p class="text-xs md:text-sm text-yellow-800">
                            <span class="font-bold">💡 Tips:</span> Nomor VA Anda unik dan hanya berlaku untuk pesanan ini. Jangan bagikan ke orang lain.
                        </p>
                    </div>
                </div>
            </div>

            <!-- QR CODE SECTION -->
            <div id="qrcodeSection" style="display: none;">
                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-xl p-4 md:p-6 border-2 border-indigo-300">
                    <h3 class="font-bold text-indigo-900 mb-4">📱 QR Code QRIS</h3>
                    
                    <div class="bg-white rounded-lg p-4 md:p-6 flex justify-center">
                        <div id="qrcodeDisplay" class="w-64 h-64 md:w-72 md:h-72 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2">
                        <h4 class="font-semibold text-indigo-900 text-sm">Cara Pembayaran:</h4>
                        <ol class="text-sm text-indigo-800 space-y-2 ml-4">
                            <li>1. Buka aplikasi e-wallet Anda (OVO, Dana, GoPay, dll)</li>
                            <li>2. Pilih menu "Scan QR" atau "Bayar"</li>
                            <li>3. Arahkan kamera ke QR Code di atas</li>
                            <li>4. Verifikasi jumlah pembayaran</li>
                            <li>5. Masukkan PIN/password</li>
                            <li>6. Pembayaran selesai ✓</li>
                        </ol>
                    </div>

                    <div class="mt-4 bg-blue-50 border border-blue-300 rounded-lg p-3">
                        <p class="text-xs md:text-sm text-blue-800">
                            <span class="font-bold">ℹ️ Info:</span> QR Code ini dapat digunakan melalui berbagai aplikasi pembayaran digital.
                        </p>
                    </div>
                </div>
            </div>

            <!-- BANK TRANSFER SECTION -->
            <div id="bankTransferSection" style="display: none;">
                <div class="bg-gradient-to-r from-amber-50 to-orange-100 rounded-xl p-4 md:p-6 border-2 border-amber-300">
                    <h3 class="font-bold text-amber-900 mb-4">🏦 Data Transfer Bank</h3>
                    
                    <div class="bg-white rounded-lg p-4 md:p-5 space-y-3">
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Bank</p>
                            <p class="text-lg font-bold text-amber-700" id="bankName">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Nomor Rekening</p>
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-lg md:text-xl font-mono font-bold text-amber-700" id="bankAccount">-</p>
                                <button onclick="copyToClipboard('bankAccount')" class="px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg transition-colors">
                                    📋 Salin
                                </button>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Atas Nama</p>
                            <p class="font-bold text-amber-900" id="bankAccountName">-</p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2">
                        <h4 class="font-semibold text-amber-900 text-sm">Cara Pembayaran:</h4>
                        <ol class="text-sm text-amber-800 space-y-2 ml-4">
                            <li>1. Buka aplikasi/ATM bank Anda</li>
                            <li>2. Pilih menu Transfer</li>
                            <li>3. Masukkan data rekening di atas</li>
                            <li>4. Verifikasi jumlah pembayaran</li>
                            <li>5. Konfirmasi transaksi</li>
                            <li>6. Tunggu notifikasi konfirmasi (1-2 jam)</li>
                        </ol>
                    </div>

                    <div class="mt-4 bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                        <p class="text-xs md:text-sm text-yellow-800">
                            <span class="font-bold">⚠️ Penting:</span> Pembayaran akan diverifikasi oleh tim kami dalam 1-2 jam kerja. Pastikan nominal pembayaran sesuai.
                        </p>
                    </div>
                </div>
            </div>

            <!-- E-WALLET SECTION -->
            <div id="ewalletSection" style="display: none;">
                <div class="bg-gradient-to-r from-pink-50 to-rose-100 rounded-xl p-4 md:p-6 border-2 border-pink-300">
                    <h3 class="font-bold text-pink-900 mb-4">📱 Transfer E-Wallet</h3>
                    
                    <div class="bg-white rounded-lg p-4 md:p-5 space-y-3">
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Metode</p>
                            <p class="text-lg font-bold text-pink-700" id="ewalletMethod">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Nomor Tujuan</p>
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-lg md:text-xl font-mono font-bold text-pink-700" id="ewalletNumber">-</p>
                                <button onclick="copyToClipboard('ewalletNumber')" class="px-3 py-2 bg-pink-600 hover:bg-pink-700 text-white font-bold rounded-lg transition-colors">
                                    📋 Salin
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2">
                        <h4 class="font-semibold text-pink-900 text-sm">Cara Pembayaran:</h4>
                        <ol class="text-sm text-pink-800 space-y-2 ml-4">
                            <li>1. Buka aplikasi e-wallet Anda</li>
                            <li>2. Pilih menu Transfer</li>
                            <li>3. Masukkan nomor tujuan di atas</li>
                            <li>4. Verifikasi jumlah pembayaran</li>
                            <li>5. Masukkan PIN/password</li>
                            <li>6. Pembayaran selesai ✓</li>
                        </ol>
                    </div>

                    <div class="mt-4 bg-green-50 border border-green-300 rounded-lg p-3">
                        <p class="text-xs md:text-sm text-green-800">
                            <span class="font-bold">✓ Cepat:</span> Pembayaran via e-wallet langsung terverifikasi.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t-2">
                <a href="{{ route('workshop.my-bookings') }}" class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-lg transition-colors text-center">
                    ← Kembali ke Booking
                </a>
                <button onclick="closePaymentDetailsModal()" class="flex-1 px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold rounded-lg transition-all shadow-md hover:shadow-lg">
                    ✓ Saya Sudah Bayar
                </button>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-300 rounded-lg p-3 md:p-4 text-xs md:text-sm text-blue-800">
                <p><span class="font-bold">📌 Catatan:</span> Jika pembayaran Anda tidak terdeteksi, hubungi support kami melalui WhatsApp atau email dalam 24 jam.</p>
            </div>
        </div>
    </div>
</div>

<script>
function openPaymentDetailsModal() {
    document.getElementById('paymentDetailsModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePaymentDetailsModal() {
    document.getElementById('paymentDetailsModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const text = element.textContent;
    
    navigator.clipboard.writeText(text).then(() => {
        // Show copied feedback
        const btn = event.target.closest('button');
        const originalText = btn.textContent;
        btn.textContent = '✓ Tersalin!';
        btn.classList.add('bg-green-600');
        
        setTimeout(() => {
            btn.textContent = originalText;
            btn.classList.remove('bg-green-600');
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy:', err);
    });
}

function displayPaymentDetails(paymentData) {
    const amount = paymentData.amount || 0;
    const method = paymentData.method || '';
    const vatPaymentDetails = paymentData.payment_details || {};
    
    // Set amount
    document.getElementById('paymentAmount').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    
    // Set deadline (24 hours from now)
    const deadline = new Date();
    deadline.setHours(deadline.getHours() + 24);
    document.getElementById('paymentDeadline').textContent = deadline.toLocaleString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    // Hide all sections first
    document.getElementById('vaCodeSection').style.display = 'none';
    document.getElementById('qrcodeSection').style.display = 'none';
    document.getElementById('bankTransferSection').style.display = 'none';
    document.getElementById('ewalletSection').style.display = 'none';
    
    // Display based on method
    if (method === 'BRIVA') {
        document.getElementById('vaCodeSection').style.display = 'block';
        document.getElementById('vaCode').textContent = vatPaymentDetails.va_code || 'N/A';
    } 
    else if (method === 'QRIS') {
        document.getElementById('qrcodeSection').style.display = 'block';
        // Generate QR code using QR code library
        if (vatPaymentDetails.qr_image_url) {
            document.getElementById('qrcodeDisplay').innerHTML = `<img src="${vatPaymentDetails.qr_image_url}" alt="QRIS Code" class="w-full h-full object-contain">`;
        }
    }
    else if (['BCABANK', 'MANDIRIBANK'].includes(method)) {
        document.getElementById('bankTransferSection').style.display = 'block';
        document.getElementById('bankName').textContent = method === 'BCABANK' ? 'BCA' : 'Mandiri';
        document.getElementById('bankAccount').textContent = vatPaymentDetails.bank_account || 'N/A';
        document.getElementById('bankAccountName').textContent = vatPaymentDetails.bank_account_name || 'PT. Batik Giri Alam';
    }
    else if (['DANACASH', 'OVOBANK'].includes(method)) {
        document.getElementById('ewalletSection').style.display = 'block';
        document.getElementById('ewalletMethod').textContent = method === 'DANACASH' ? 'Dana' : 'OVO';
        document.getElementById('ewalletNumber').textContent = vatPaymentDetails.wallet_number || 'N/A';
    }
    
    // Open modal
    openPaymentDetailsModal();
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePaymentDetailsModal();
    }
});

// Close modal when clicking outside
document.getElementById('paymentDetailsModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentDetailsModal();
    }
});
</script>
