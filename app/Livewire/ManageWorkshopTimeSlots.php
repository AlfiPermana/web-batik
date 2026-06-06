<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Workshop;
use App\Models\WorkshopTimeSlot;
use Illuminate\Validation\Rule;

class ManageWorkshopTimeSlots extends Component
{
    public $workshopId;
    public $workshop;
    public $timeSlots = [];
    
    // Form state
    public $showCreateForm = false;
    public $showEditForm = false;
    public $editingSlotId = null;
    
    // Form fields
    public $name = '';
    public $start_time = '';
    public $end_time = '';



    public function mount($workshopId = null)
    {
        $this->workshopId = $workshopId;
        if ($this->workshopId) {
            $this->workshop = Workshop::findOrFail($this->workshopId);
            $this->loadTimeSlots();
        }
    }

    public function loadTimeSlots()
    {
        if ($this->workshop) {
            $this->timeSlots = $this->workshop->timeSlots()
                ->orderBy('order')
                ->get()
                ->toArray();
        }
    }

    public function selectWorkshop($id)
    {
        $this->workshopId = $id;
        $this->workshop = Workshop::findOrFail($id);
        $this->loadTimeSlots();
    }

    public function openCreateForm()
    {
        $this->resetForm();
        $this->showCreateForm = true;
        $this->showEditForm = false;
    }

    public function openEditForm($slotId)
    {
        $slot = WorkshopTimeSlot::findOrFail($slotId);
        $this->editingSlotId = $slotId;
        $this->name = $slot->name;
        $this->start_time = substr($slot->start_time, 0, 5);
        $this->end_time = substr($slot->end_time, 0, 5);
        $this->showEditForm = true;
        $this->showCreateForm = false;
    }

    public function resetForm()
    {
        $this->name = '';
        $this->start_time = '';
        $this->end_time = '';
        $this->editingSlotId = null;
        $this->showCreateForm = false;
        $this->showEditForm = false;
    }

    public function createSlot()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        try {
            $defaultMaxCapacity = 1000000;

            $newSlot = WorkshopTimeSlot::create([
                'workshop_id' => $this->workshopId,
                'name' => $this->name,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'max_capacity' => $defaultMaxCapacity,
                'is_active' => true,
            ]);


            // ✅ AUTO-CREATE SCHEDULES: Untuk setiap existing date, buat schedule untuk slot baru ini
            $existingDates = $this->workshop->availableDates()->get();
            
            foreach ($existingDates as $date) {
                \App\Models\WorkshopSlotSchedule::firstOrCreate(
                    [
                        'workshop_id' => $this->workshopId,
                        'available_date_id' => $date->id,
                        'time_slot_id' => $newSlot->id,
                    ],
                    [
                        'date' => $date->date,
                        'status' => 'available',
                        'booked_count' => 0,
                        'max_capacity' => $defaultMaxCapacity,
                    ]
                );
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Time slot created successfully! (' . $existingDates->count() . ' schedules auto-created)',
            ]);

            $this->loadTimeSlots();
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error creating time slot: ' . $e->getMessage(),
            ]);
        }
    }

    public function updateSlot()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        try {
            $slot = WorkshopTimeSlot::findOrFail($this->editingSlotId);
            $slot->update([
                'name' => $this->name,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Time slot updated successfully!',
            ]);

            $this->loadTimeSlots();
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error updating time slot: ' . $e->getMessage(),
            ]);
        }
    }

    public function deleteSlot($slotId)
    {
        try {
            $slot = WorkshopTimeSlot::findOrFail($slotId);
            $slot->delete();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Time slot deleted successfully!',
            ]);

            $this->loadTimeSlots();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error deleting time slot: ' . $e->getMessage(),
            ]);
        }
    }

    public function toggleSlotStatus($slotId)
    {
        try {
            $slot = WorkshopTimeSlot::findOrFail($slotId);
            $slot->update(['is_active' => !$slot->is_active]);

            $status = $slot->is_active ? 'activated' : 'deactivated';
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Time slot $status successfully!",
            ]);

            $this->loadTimeSlots();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error updating time slot: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $workshops = Workshop::active()->get();

        return view('livewire.manage-workshop-time-slots', [
            'workshops' => $workshops,
        ]);
    }
}

