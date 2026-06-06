<div class="max-w-2xl mx-auto">
    @if($bookingCreated)
        <!-- Success Message -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-8 text-center">
            <div class="mb-4">
                <svg class="w-16 h-16 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-green-900 mb-2">Booking Successful!</h3>
            <p class="text-green-800 mb-4">Your booking has been created successfully.</p>
            
            <div class="bg-white border border-green-200 rounded p-4 mb-6">
                <p class="text-sm text-gray-600">Booking Number:</p>
                <p class="text-2xl font-bold text-green-700">{{ $bookingNumber }}</p>
            </div>

            <p class="text-green-800 text-sm mb-6">
                Please check your email for booking confirmation and payment instructions.
            </p>

            <button 
                wire:click="resetForm"
                class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition">
                Create Another Booking
            </button>
        </div>
    @else
        <!-- Step 1: Date Selection -->
        @if($step === 1)
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Select Workshop Date</h2>
                
                <div class="text-center mb-6">
                    <p class="text-gray-600">
                        <strong class="text-lg">{{ $workshop->title }}</strong><br>
                        <span class="text-amber-600">Rp {{ number_format($workshop->amount, 0, ',', '.') }} (harga workshop)</span>

                    </p>
                </div>
                @if(count($availableDates) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($availableDates as $dateOption)
                            <button 
                                wire:click="selectDate('{{ $dateOption['date'] }}')"
                                class="p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-left">
                                <div class="font-semibold text-gray-800">{{ $dateOption['display'] }}</div>
                                <div class="text-sm text-gray-600 mt-2">
                                    📅 {{ $dateOption['slots'] }} slot waktu
                                    @if($dateOption['available_slots'] > 0)
                                        <span class="text-green-600 font-medium">({{ $dateOption['available_slots'] }} tersedia)</span>
                                    @endif
                                    @if($dateOption['on_book_slots'] > 0)
                                        <span class="text-amber-600 font-medium">({{ $dateOption['on_book_slots'] }} sedang booking)</span>
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                        <p class="text-yellow-800">Tidak ada tanggal tersedia 30 hari ke depan. Silakan cek kembali nanti.</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Step 2: Slot & Participants Selection -->
        @if($step === 2)
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="mb-6">
                    <button 
                        wire:click="goBack"
                        class="text-blue-500 hover:text-blue-700 flex items-center gap-2">
                        ← Back to Date Selection
                    </button>
                </div>

                <h2 class="text-2xl font-bold text-gray-800 mb-2">Select Time Slot</h2>
                <p class="text-gray-600 mb-6">
                    Date: <strong>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</strong>
                </p>

                @if(count($availableSlots) > 0)
                    <div class="space-y-3 mb-6">
                        @foreach($availableSlots as $slot)
                            <button 
                                wire:click="selectSlot({{ $slot['id'] }})"
                                {{ $slot['is_fully_booked'] ? 'disabled' : '' }}
                                class="w-full p-4 border-2 rounded-lg text-left transition {{ $slot['is_fully_booked'] ? 'border-red-200 bg-red-50 cursor-not-allowed opacity-60' : 'border-gray-200 hover:border-blue-500 hover:bg-blue-50 cursor-pointer' }}">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="font-semibold {{ $slot['is_fully_booked'] ? 'text-red-700' : 'text-gray-800' }}">{{ $slot['slot_name'] }}</div>
                                        <div class="text-sm {{ $slot['is_fully_booked'] ? 'text-red-600' : 'text-gray-600' }}">⏰ {{ $slot['start_time'] }} - {{ $slot['end_time'] }}</div>
                                    </div>
                                    <div class="text-right">
                                        @if($slot['is_fully_booked'])
                                            <div class="text-sm font-bold px-3 py-1.5 rounded-full bg-red-500 text-white">
                                                FULL BOOK
                                            </div>
                                        @elseif($slot['is_on_book'])
                                            <div class="text-sm font-medium px-3 py-1.5 rounded-full bg-amber-100 text-amber-800">
                                                ON BOOK
                                            </div>
                                        @else
                                            <div class="text-sm font-medium px-3 py-1.5 rounded-full bg-green-100 text-green-800">
                                                AVAILABLE
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if($slot['is_on_book'])
                                    <div class="text-xs text-amber-700 mt-2">📍 {{ $slot['booked_count'] }} peserta sudah booking di slot ini</div>
                                @endif
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                        <p class="text-yellow-800">Tidak ada slot tersedia untuk tanggal ini.</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Step 3: Booking Details & Confirmation -->
        @if($step === 3)
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="mb-6">
                    <button 
                        wire:click="goBack"
                        class="text-blue-500 hover:text-blue-700 flex items-center gap-2">
                        ← Back to Slot Selection
                    </button>
                </div>

                <h2 class="text-2xl font-bold text-gray-800 mb-6">Complete Your Booking</h2>

                <!-- Booking Summary -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-3">Booking Summary</h3>
                    <div class="space-y-2 text-sm text-blue-800">
                        <div class="flex justify-between">
                            <span>Workshop:</span>
                            <span class="font-semibold">{{ $workshop->title }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Date:</span>
                            <span class="font-semibold">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</span>
                        </div>
                        @php
                            $selectedSlot = collect($availableSlots)->firstWhere('id', $selectedScheduleId);
                        @endphp
                        @if($selectedSlot)
                            <div class="flex justify-between">
                                <span>Time:</span>
                                <span class="font-semibold">{{ $selectedSlot['start_time'] }} - {{ $selectedSlot['end_time'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Form -->
                <form wire:submit="submitBooking" class="space-y-4">
                    <!-- Customer Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input 
                            type="text"
                            wire:model="customer_name"
                            placeholder="Your full name"
                            class="w-full border border-gray-300 rounded-lg p-3 @error('customer_name') border-red-500 @enderror"
                        />
                        @error('customer_name') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Customer Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input 
                            type="email"
                            wire:model="customer_email"
                            placeholder="your.email@example.com"
                            class="w-full border border-gray-300 rounded-lg p-3 @error('customer_email') border-red-500 @enderror"
                        />
                        @error('customer_email') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Customer Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                        <input 
                            type="tel"
                            wire:model="customer_phone"
                            placeholder="08XXXXXXXXXX"
                            class="w-full border border-gray-300 rounded-lg p-3 @error('customer_phone') border-red-500 @enderror"
                        />
                        @error('customer_phone') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Number of Participants -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Number of Participants *</label>
                        <div class="flex items-center gap-3">
                            <button 
                                type="button"
                                wire:click="decrementParticipants"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
                                −
                            </button>

                            <input 
                                type="number"
                                wire:model="num_participants"
                                min="1"
                                class="w-20 border border-gray-300 rounded-lg p-2 text-center text-lg font-semibold"
                            />
                            <button 
                                type="button"
                                wire:click="incrementParticipants"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
                                +
                            </button>

                        </div>
                        @error('num_participants') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Special Requests -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests (Optional)</label>
                        <textarea 
                            wire:model="special_requests"
                            placeholder="Any special requests or notes..."
                            rows="4"
                            class="w-full border border-gray-300 rounded-lg p-3">
                        </textarea>
                    </div>

                    <!-- Ringkasan Harga -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Harga workshop (fix):</span>
                                <span class="font-semibold">Rp {{ number_format($workshop->amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Jumlah peserta:</span>
                                <span class="font-semibold">{{ $num_participants }}</span>
                            </div>
                            <div class="border-t border-gray-300 pt-2 flex justify-between text-base font-bold">
                                <span>Total:</span>
                                <span class="text-blue-600">Rp {{ number_format($workshop->amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-green-600">
                                <span>Deposit (50%):</span>
                                <span class="font-semibold">Rp {{ number_format((int) ceil($workshop->amount / 2), 0, ',', '.') }}</span>
                            </div>
                            <p class="text-xs text-gray-500 pt-1">Catatan: total harga tidak berubah walaupun jumlah peserta berbeda.</p>
                        </div>
                    </div>


                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full bg-amber-700 hover:bg-amber-800 text-white font-semibold py-3 rounded-lg transition">
                        Buat Booking
                    </button>

                </form>
            </div>
        @endif
    @endif
</div>

<style>
    [wire\:loading] {
        opacity: 0.5;
        pointer-events: none;
    }
</style>
