@extends('components.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-6">Test Workshop Booking Form Component</h1>
    
    <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-6">
        <p class="text-blue-800"><strong>Workshop:</strong> {{ $workshop->title }} (ID: {{ $workshop->id }})</p>
        <p class="text-blue-800"><strong>Price:</strong> Rp {{ number_format($workshop->amount, 0, ',', '.') }}</p>
    </div>

    <h2 class="text-2xl font-bold mb-4">Booking Form Component:</h2>
    
    <!-- Test Component Render -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        @livewire('workshop-booking-form', ['workshopId' => $workshop->id])
    </div>

    <hr class="my-8">

    <h2 class="text-2xl font-bold mb-4">Debug Information:</h2>
    <div class="bg-gray-100 p-4 rounded font-mono text-sm">
        <p>Workshop ID: {{ $workshop->id }}</p>
        <p>Workshop Has TimeSlots: {{ $workshop->timeSlots()->count() }}</p>
        <p>Workshop Has AvailableDates: {{ $workshop->availableDates()->count() }}</p>
        <p>Workshop Has Schedules: {{ $workshop->schedules()->count() }}</p>
    </div>
</div>
@endsection
