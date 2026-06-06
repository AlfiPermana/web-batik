@extends('components.layouts.app')

@section('content')
<div class="workshop-listing-page">
    <!-- Header Section -->
    <div class="workshop-hero">
        <div class="container mx-auto px-4 py-12">
            <h1 class="text-4xl font-bold text-center mb-4">Workshop Batik Kami</h1>
            <p class="text-center text-amber-100 text-lg mb-8">
                Pilih paket workshop yang sesuai dengan kebutuhan Anda
            </p>
        </div>
    </div>

    <!-- Workshop Packages -->
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @php
                $packages = [
                    [
                        'name' => 'PAKET 1',
                        'price' => 250000,
                        'duration' => '5 Jam',
                        'features' => [
                            'Tour proses membatik',
                            'Penyampaian materi mengenai batik',
                            'Praktik Batik Tulis di kain 2,2 Meter Persegi'
                        ],
                        'workshop_id' => 1
                    ],
                    [
                        'name' => 'PAKET 2',
                        'price' => 150000,
                        'duration' => '4 Jam',
                        'features' => [
                            'Tour proses membatik',
                            'Penyampaian materi mengenai batik',
                            'Praktik Batik Tulis di kain 1 Meter Persegi'
                        ],
                        'workshop_id' => 2
                    ],
                    [
                        'name' => 'PAKET 3',
                        'price' => 35000,
                        'duration' => '2 Jam',
                        'features' => [
                            'Tour Proses Membatik',
                            'Penyampaian materi mengenai batik',
                            'Praktik Batik Tulis di Kain Batik 25cm Persegi'
                        ],
                        'workshop_id' => 3
                    ]
                ];
            @endphp

            @forelse($packages as $package)
                <div class="workshop-package-card bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <!-- Package Header -->
                    <div class="bg-gradient-to-r from-amber-700 to-amber-900 px-6 py-4">
                        <h2 class="text-2xl font-bold text-white">{{ $package['name'] }}</h2>
                        <p class="text-amber-100 text-sm">Durasi: {{ $package['duration'] }}</p>
                    </div>

                    <!-- Package Content -->
                    <div class="p-6">
                        <!-- Description -->
                        <div class="mb-6">
                            <h3 class="font-bold text-gray-800 mb-3">Apa yang Anda Pelajari:</h3>
                            <ul class="space-y-2">
                                @foreach($package['features'] as $feature)
                                    <li class="flex items-start">
                                        <span class="text-amber-600 mr-2">•</span>
                                        <span class="text-gray-700">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Price -->
                        <div class="mb-6 pt-6 border-t border-gray-200">
                            <p class="text-center text-gray-600 text-sm mb-2">Harga Workshop (Fix)</p>
                            <p class="text-center text-xs text-gray-500 -mt-1 mb-2">Tidak bergantung pada jumlah peserta</p>

                            <p class="text-center text-4xl font-bold text-amber-700">
                                Rp {{ number_format($package['price'], 0, ',', '.') }}
                            </p>
                        </div>

                        <!-- Book Button -->
                        <button 
                            type="button"
                            id="bookButton{{ $loop->index }}"
                            data-workshop-id="{{ $package['workshop_id'] }}"
                            data-workshop-name="{{ $package['name'] }}"
                            data-package-price="{{ $package['price'] }}"
                            class="w-full bg-amber-800 hover:bg-amber-900 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-200 book-now-btn">
                            BOOK NOW
                        </button>
                    </div>

                    <!-- Package Info Footer -->
                    <div class="bg-gray-50 px-6 py-3 text-center">
                        <p class="text-sm text-gray-600">
                            Jumlah peserta ditentukan saat booking
                        </p>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12">
                    <p class="text-gray-500 text-lg">Tidak ada paket workshop tersedia saat ini</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4 overflow-y-auto" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full my-8">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-amber-700 to-amber-900 px-6 py-4 sticky top-0 z-10">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-bold text-white">Booking Workshop</h3>
                <button 
                    type="button"
                    onclick="closeBookingModal()"
                    class="text-white hover:text-amber-200 text-2xl leading-none">
                    ×
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6 max-h-[70vh] overflow-y-auto">
            <form id="bookingForm" method="POST" action="{{ route('workshop.booking.store') }}">
                @csrf

                <!-- Hidden Fields -->
                <input type="hidden" id="workshopId" name="workshop_id" value="{{ $workshopId ?? '0' }}">
                <input type="hidden" id="workshopName" name="workshop_name">
                <input type="hidden" id="packagePrice" name="package_price">
                <input type="hidden" id="scheduleId" name="workshop_slot_schedule_id">

                <!-- Step 1: Schedule Selection (Button Grid) -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-3">
                        🗓️ Pilih Jadwal Workshop
                    </label>
                    <div id="scheduleContainer" class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                        <!-- Schedules will be loaded here -->
                        <div class="col-span-full text-center py-8 text-gray-500">
                            ⏳ Loading jadwal tersedia...
                        </div>
                    </div>
                    <div id="scheduleError" class="text-red-600 text-sm" style="display: none;"></div>
                </div>

                <!-- Number of Participants -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Jumlah Peserta
                    </label>
                    <input 
                        type="number" 
                        id="numberOfParticipants" 
                        name="number_of_participants"
                        min="1"

                        value="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500"
                        required>

                </div>

                <!-- Customer Data -->
                <div class="border-t pt-6 mb-6">
                    <h4 class="font-bold text-gray-800 mb-4">Data Diri Pemesan</h4>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Nama Lengkap
                        </label>
                        <input 
                            type="text" 
                            id="fullName" 
                            name="full_name"
                            value="{{ auth()->user()?->name ?? '' }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Email
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email"
                            value="{{ auth()->user()?->email ?? '' }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Nomor Telepon
                        </label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone"
                            value="{{ auth()->user()?->phone ?? '' }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Alamat
                        </label>
                        <textarea 
                            id="address" 
                            name="address"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500"
                            required></textarea>
                    </div>
                </div>

                <!-- Price Summary -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-700">Harga Workshop (Fix):</span>
                        <span class="font-bold text-amber-700" id="pricePerPerson">Rp 0</span>

                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-700">Jumlah Peserta:</span>
                        <span class="font-bold text-amber-700" id="participantCount">1</span>
                    </div>
                    <div class="border-t border-amber-200 pt-2 flex justify-between items-center mb-2">
                        <span class="font-bold text-gray-800">Total Harga:</span>
                        <span class="text-2xl font-bold text-amber-700" id="totalPrice">Rp 0</span>
                    </div>
                    <div class="border-t border-amber-200 pt-2 flex justify-between items-center">
                        <span class="font-bold text-gray-800">Deposit (50%):</span>
                        <span class="text-xl font-bold text-red-600" id="depositAmount">Rp 0</span>
                    </div>
                </div>

                <!-- Deposit Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="font-bold text-blue-900 mb-2">ℹ️ Sistem Pembayaran</h4>
                    <p class="text-sm text-blue-800 mb-2">
                        Anda akan membayar <span class="font-bold">50% sebagai deposit</span> di tahap ini.
                    </p>
                    <p class="text-sm text-blue-800">
                        Sisa 50% dapat dibayar sebelum workshop dimulai atau saat tiba di lokasi.
                    </p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button 
                        type="button"
                        onclick="closeBookingModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 font-bold">
                        Batal
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-4 py-2 bg-amber-700 hover:bg-amber-800 text-white rounded-lg transition-colors duration-200 font-bold">
                        Lanjut ke Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    updateTotalPrice();

    document.querySelectorAll('.book-now-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const workshopId = this.getAttribute('data-workshop-id');
            const workshopName = this.getAttribute('data-workshop-name');
            const packagePrice = this.getAttribute('data-package-price');
            
            openBookingModal(workshopId, workshopName, packagePrice);
        });
    });

    const modal = document.getElementById('bookingModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeBookingModal();
            }
        });
    }

    const form = document.getElementById('bookingForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }

    const participantsInput = document.getElementById('numberOfParticipants');
    if (participantsInput) {
        participantsInput.addEventListener('change', updateTotalPrice);
        participantsInput.addEventListener('input', updateTotalPrice);
    }
});

function openBookingModal(workshopId, workshopName, price) {
    try {
        const workshopIdField = document.getElementById('workshopId');
        const workshopNameField = document.getElementById('workshopName');
        const packagePriceField = document.getElementById('packagePrice');
        
        workshopIdField.value = workshopId;
        workshopNameField.value = workshopName;
        packagePriceField.value = price;
        
        document.getElementById('pricePerPerson').textContent = 'Rp ' + formatCurrency(price);
        document.getElementById('bookingModal').style.display = 'flex';
        updateTotalPrice();
        document.body.style.overflow = 'hidden';
        
        loadAvailableSchedules(workshopId);
    } catch (error) {
        alert('Terjadi kesalahan saat membuka form booking. Silahkan refresh halaman.');
    }
}

function loadAvailableSchedules(workshopId) {
    const scheduleContainer = document.getElementById('scheduleContainer');
    const scheduleError = document.getElementById('scheduleError');
    
    scheduleContainer.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500">⏳ Loading jadwal tersedia...</div>';
    scheduleError.style.display = 'none';
    
    fetch(`/workshop/${workshopId}/available-schedules`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(schedules => {
            scheduleContainer.innerHTML = '';
            
            if (schedules.length === 0) {
                scheduleError.textContent = '❌ Tidak ada jadwal tersedia untuk workshop ini saat ini.';
                scheduleError.style.display = 'block';
                return;
            }
            
            schedules.forEach(schedule => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'schedule-btn p-4 border-2 border-gray-300 rounded-lg text-left hover:border-amber-500 hover:bg-amber-50 transition-all duration-200 cursor-pointer';
                button.dataset.scheduleId = schedule.id;
                button.dataset.scheduleData = JSON.stringify(schedule);
                
                button.innerHTML = `
                    <div class="font-bold text-gray-800">${schedule.date}</div>
                    <div class="text-sm text-amber-700">${schedule.slot_name} ${schedule.start_time}-${schedule.end_time}</div>
                    <div class="text-xs text-gray-600 mt-1">Sudah booking: ${schedule.booked_count ?? 0} peserta</div>
                `;
                
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectSchedule(this);
                });
                
                scheduleContainer.appendChild(button);
            });
        })
        .catch(error => {
            scheduleError.textContent = '❌ Error loading schedules. Please refresh the page.';
            scheduleError.style.display = 'block';
            scheduleContainer.innerHTML = '<div class="col-span-full text-center py-8 text-red-500">Error loading schedules</div>';
        });
}

function selectSchedule(button) {
    document.querySelectorAll('.schedule-btn').forEach(btn => {
        btn.classList.remove('border-amber-600', 'bg-amber-50', 'border-2');
        btn.classList.add('border-gray-300');
    });
    
    button.classList.remove('border-gray-300');
    button.classList.add('border-amber-600', 'bg-amber-50', 'border-2');
    
    const scheduleData = JSON.parse(button.dataset.scheduleData);
    document.getElementById('scheduleId').value = scheduleData.id;
}

function closeBookingModal() {
    document.getElementById('bookingModal').style.display = 'none';
    document.getElementById('bookingForm').reset();
    document.getElementById('scheduleContainer').innerHTML = '<div class="col-span-full text-center py-8 text-gray-500">⏳ Loading jadwal tersedia...</div>';
    document.getElementById('scheduleError').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

function updateTotalPrice() {
    try {
        const price = parseInt(document.getElementById('packagePrice').value) || 0;
        const participants = parseInt(document.getElementById('numberOfParticipants').value) || 0;
        const total = price;
        const deposit = Math.ceil(total / 2);


        document.getElementById('participantCount').textContent = participants;
        document.getElementById('totalPrice').textContent = 'Rp ' + formatCurrency(total);
        document.getElementById('depositAmount').textContent = 'Rp ' + formatCurrency(deposit);
    } catch (error) {
        // Silent error
    }
}

function handleFormSubmit(e) {
    e.preventDefault();
    
    const scheduleId = document.getElementById('scheduleId').value;
    
    if (!scheduleId) {
        alert('Silahkan pilih jadwal workshop terlebih dahulu!');
        return;
    }
    
    const workshopName = document.getElementById('workshopName').value;
    const packagePrice = document.getElementById('packagePrice').value;
    const numberOfParticipants = document.getElementById('numberOfParticipants').value;
    
    const workshopData = {
        paketName: workshopName || 'PAKET 1',
        title: workshopName || 'Workshop Batik',
        price: parseInt(packagePrice) || 0,
        scheduleId: scheduleId,
        numberOfParticipants: parseInt(numberOfParticipants) || 1
    };
    
    sessionStorage.setItem('workshopData', JSON.stringify(workshopData));
    document.getElementById('bookingForm').submit();
}
</script>
@endpush

@push('styles')
<style>
    .workshop-listing-page {
        background-color: #fafaf9;
        min-height: 100vh;
    }

    .workshop-hero {
        background: linear-gradient(135deg, #92400e 0%, #b45309 100%);
        color: white;
    }

    .workshop-package-card {
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease;
    }

    .workshop-package-card:hover {
        transform: translateY(-5px);
    }

    .workshop-package-card .p-6 {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .workshop-package-card button {
        margin-top: auto;
    }

    @media (max-width: 768px) {
        .workshop-listing-page .grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
@endsection
