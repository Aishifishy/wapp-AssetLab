<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::with('terms')
            ->orderBy('start_date', 'desc')
            ->get();

        $calendarEvents = [];
        foreach ($academicYears as $year) {
            foreach ($year->terms as $term) {
                $calendarEvents[] = [
                    'title' => $term->name . ' (' . $year->name . ')',
                    'start' => $term->start_date->format('Y-m-d'),
                    'end' => $term->end_date->addDay()->format('Y-m-d'), // Add a day because FullCalendar end dates are exclusive
                    'className' => 'term-event' . ($term->is_current ? ' current-term' : ''),
                    'extendedProps' => [
                        'year_id' => $year->id,
                        'term_id' => $term->id,
                        'is_current' => $term->is_current
                    ]
                ];
            }
        }

        return view('admin.academic.index', compact('academicYears', 'calendarEvents'));
    }

    public function create()
    {
        return view('admin.academic.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:academic_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $academicYear = AcademicYear::create($validated);

        // Create three terms automatically
        $termLength = Carbon::parse($validated['end_date'])->diffInDays(Carbon::parse($validated['start_date'])) / 3;
        
        $termNames = [
            1 => 'First Term',
            2 => 'Second Term', 
            3 => 'Third Term'
        ];
        
        $termStartDate = Carbon::parse($validated['start_date']);
        for ($i = 1; $i <= 3; $i++) {
            $termEndDate = (clone $termStartDate)->addDays($termLength);
            
            $academicYear->terms()->create([
                'name' => $termNames[$i],
                'term_number' => $i,
                'start_date' => $termStartDate,
                'end_date' => $termEndDate,
                'is_current' => false,
            ]);
            
            $termStartDate = (clone $termEndDate)->addDay();
        }

        return redirect()->route('admin.academic.index')
            ->with('success', 'Academic year created successfully.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:academic_years,name,' . $academicYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $oldStartDate = $academicYear->start_date;
        $oldEndDate = $academicYear->end_date;
        
        $academicYear->update($validated);

        // If the academic year dates changed, update term dates proportionally
        if ($oldStartDate != $academicYear->start_date || $oldEndDate != $academicYear->end_date) {
            $this->updateTermDatesProportionally($academicYear, $oldStartDate, $oldEndDate);
        }

        return redirect()->route('admin.academic.index')
            ->with('success', 'Academic year updated successfully. Term dates have been adjusted proportionally.');
    }

    /**
     * Update term dates proportionally when academic year dates change
     */
    private function updateTermDatesProportionally(AcademicYear $academicYear, $oldStartDate, $oldEndDate)
    {
        $newStartDate = Carbon::parse($academicYear->start_date);
        $newEndDate = Carbon::parse($academicYear->end_date);
        $newTotalDays = $newEndDate->diffInDays($newStartDate);
        
        $termLength = $newTotalDays / 3;
        
        $terms = $academicYear->terms()->orderBy('term_number')->get();
        $currentTermStart = $newStartDate->copy();
        
        foreach ($terms as $index => $term) {
            $termEndDate = $currentTermStart->copy()->addDays($termLength);
            
            // Ensure the last term ends exactly on the academic year end date
            if ($index == 2) { // Third term (index 2)
                $termEndDate = $newEndDate->copy();
            }
            
            $term->update([
                'start_date' => $currentTermStart->format('Y-m-d'),
                'end_date' => $termEndDate->format('Y-m-d'),
            ]);
            
            $currentTermStart = $termEndDate->copy()->addDay();
        }
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return redirect()->route('admin.academic.index')
            ->with('success', 'Academic year deleted successfully.');
    }

    public function setCurrent(AcademicYear $academicYear)
    {
        $academicYear->markAsCurrent();

        return redirect()->route('admin.academic.index')
            ->with('success', 'Current academic year updated successfully.');
    }

    /**
     * Automatically set current academic year and term based on today's date
     */
    public function setCurrentByDate()
    {
        $service = new \App\Services\AcademicCalendarService();
        $result = $service->setCurrentByDate();
        
        if ($result['success']) {
            return redirect()->route('admin.academic.index')
                ->with('success', $result['message']);
        } else {
            return redirect()->route('admin.academic.index')
                ->with('error', $result['message']);
        }
    }

    /**
     * Get daily overview data for a specific date
     */
    public function getDailyOverview(Request $request)
    {
        $date = $request->get('date');
        
        if (!$date) {
            return response()->json(['error' => 'Date parameter is required'], 400);
        }

        $selectedDate = \Carbon\Carbon::parse($date);
        $dayOfWeek = $selectedDate->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.

        // Get all available equipment grouped by category
        $availableEquipment = \App\Models\Equipment::with('category')
            ->available()
            ->get()
            ->groupBy(function($equipment) {
                return $equipment->category ? $equipment->category->name : 'Uncategorized';
            })
            ->map(function($equipmentGroup, $categoryName) {
                return [
                    'category' => $categoryName,
                    'total_count' => $equipmentGroup->count(),
                    'equipment_ids' => $equipmentGroup->pluck('id')->toArray(),
                    'equipment_list' => $equipmentGroup->map(function($equipment) {
                        return [
                            'id' => $equipment->id,
                            'name' => $equipment->name,
                            'rfid_tag' => $equipment->rfid_tag
                        ];
                    })->toArray()
                ];
            })
            ->values();

        // Get equipment due for return on the selected date
        $dueEquipment = \App\Models\EquipmentRequest::with(['equipment', 'user'])
            ->approved()
            ->whereNull('returned_at')
            ->whereDate('requested_until', $selectedDate->format('Y-m-d'))
            ->get()
            ->map(function($request) {
                return [
                    'id' => $request->id,
                    'equipment_name' => $request->equipment->name,
                    'borrower_name' => $request->user->name,
                    'due_time' => $request->requested_until->format('h:i A'),
                    'borrowed_from' => $request->requested_from->format('M d, Y h:i A'),
                    'purpose' => $request->purpose,
                    'is_overdue' => $request->isOverdue()
                ];
            });

        // Get overdue equipment (past due as of selected date)
        $overdueEquipment = \App\Models\EquipmentRequest::with(['equipment', 'user'])
            ->approved()
            ->whereNull('returned_at')
            ->where('requested_until', '<', $selectedDate->format('Y-m-d'))
            ->get()
            ->map(function($request) {
                return [
                    'id' => $request->id,
                    'equipment_name' => $request->equipment->name,
                    'borrower_name' => $request->user->name,
                    'due_date' => $request->requested_until->format('M d, Y h:i A'),
                    'days_overdue' => $request->requested_until->diffInDays(\Carbon\Carbon::now()),
                    'purpose' => $request->purpose
                ];
            });

        // Get all computer laboratories for comprehensive data
        $laboratories = \App\Models\ComputerLaboratory::orderBy('building')->orderBy('room_number')->get();

        // Get computer lab schedules for the selected day
        $regularSchedules = \App\Models\LaboratorySchedule::with(['laboratory', 'academicTerm'])
            ->whereHas('academicTerm', function($query) {
                $query->where('is_current', true);
            })
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->get();

        // Get active schedule overrides for the selected date
        $scheduleOverrides = \App\Models\LaboratoryScheduleOverride::with(['laboratory', 'createdBy', 'requestedBy', 'originalSchedule'])
            ->whereDate('override_date', $selectedDate->format('Y-m-d'))
            ->where('is_active', true)
            ->where(function($query) use ($selectedDate) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', $selectedDate);
            })
            ->get();

        // Get laboratory reservations for the selected date
        $labReservations = \App\Models\LaboratoryReservation::with(['laboratory', 'user'])
            ->whereDate('reservation_date', $selectedDate->format('Y-m-d'))
            ->where('status', 'approved') // Only approved reservations
            ->get();

        // Build comprehensive lab data with new structure
        $labData = $laboratories->map(function($laboratory) use ($regularSchedules, $scheduleOverrides, $labReservations, $selectedDate, $dayOfWeek) {
            // Get regular schedules for this lab
            $labRegularSchedules = $regularSchedules->where('laboratory_id', $laboratory->id);
            
            // Get overrides for this lab
            $labOverrides = $scheduleOverrides->where('laboratory_id', $laboratory->id);
            
            // Get reservations for this lab
            $labReservationsData = $labReservations->where('laboratory_id', $laboratory->id);
            
            // Create a map of overridden schedule IDs to avoid duplicates
            $overriddenScheduleIds = $labOverrides->pluck('laboratory_schedule_id')->filter()->unique()->toArray();
            
            // Process schedules into unified format
            $schedules = collect();
            
            // Add regular schedules (only if not overridden)
            foreach ($labRegularSchedules as $schedule) {
                // Skip this regular schedule if it has been overridden
                if (in_array($schedule->id, $overriddenScheduleIds)) {
                    continue;
                }
                
                // Check if this schedule is for the current day
                if ($schedule->day_of_week !== $dayOfWeek) {
                    continue;
                }
                
                $schedules->push([
                    'id' => $schedule->id,
                    'type' => 'regular',
                    'schedule_type' => $schedule->type, // regular/special
                    'subject_code' => $schedule->subject_code,
                    'subject_name' => $schedule->subject_name,
                    'instructor' => $schedule->instructor_name,
                    'section' => $schedule->section,
                    'start_time' => $schedule->start_time->format('H:i'),
                    'end_time' => $schedule->end_time->format('H:i'),
                    'time_range' => $schedule->start_time->format('H:i') . ' - ' . $schedule->end_time->format('H:i'),
                    'is_override' => false,
                    'is_reservation' => false,
                    'notes' => $schedule->notes
                ]);
            }
            
            // Add override schedules
            foreach ($labOverrides as $override) {
                if ($override->override_type === 'cancel') {
                    // For cancellations, we don't add anything - cancelled slots become available
                    // The original schedule is already excluded above, so the time slot will show as available
                    continue;
                } else {
                    // For reschedule/replace, add the new schedule details
                    $schedules->push([
                        'id' => $override->id,
                        'type' => 'override',
                        'schedule_type' => $override->override_type, // reschedule/replace
                        'subject_code' => $override->new_subject_code ?? ($override->originalSchedule ? $override->originalSchedule->subject_code : 'OVERRIDE'),
                        'subject_name' => $override->new_subject_name ?? ($override->originalSchedule ? $override->originalSchedule->subject_name : $override->reason),
                        'instructor' => $override->new_instructor_name ?? ($override->originalSchedule ? $override->originalSchedule->instructor_name : $override->createdBy->name),
                        'section' => $override->new_section ?? ($override->originalSchedule ? $override->originalSchedule->section : 'Override'),
                        'start_time' => \Carbon\Carbon::parse($override->new_start_time)->format('H:i'),
                        'end_time' => \Carbon\Carbon::parse($override->new_end_time)->format('H:i'),
                        'time_range' => \Carbon\Carbon::parse($override->new_start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($override->new_end_time)->format('H:i'),
                        'is_override' => true,
                        'is_reservation' => false,
                        'override_reason' => $override->reason,
                        'override_id' => $override->id,
                        'notes' => $override->reason,
                        'original_schedule_id' => $override->laboratory_schedule_id // For debugging
                    ]);
                }
            }
            
            // Add reservations
            foreach ($labReservationsData as $reservation) {
                $schedules->push([
                    'id' => $reservation->id,
                    'type' => 'reservation',
                    'schedule_type' => 'reservation',
                    'subject_code' => $reservation->course_code ?? 'RESERVATION',
                    'subject_name' => $reservation->subject ?? $reservation->purpose,
                    'instructor' => $reservation->user->name,
                    'section' => $reservation->section ?? 'Reservation',
                    'start_time' => \Carbon\Carbon::parse($reservation->start_time)->format('H:i'),
                    'end_time' => \Carbon\Carbon::parse($reservation->end_time)->format('H:i'),
                    'time_range' => \Carbon\Carbon::parse($reservation->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($reservation->end_time)->format('H:i'),
                    'is_override' => false,
                    'is_reservation' => true,
                    'reservation_id' => $reservation->id,
                    'purpose' => $reservation->purpose,
                    'num_students' => $reservation->num_students,
                    'notes' => $reservation->purpose
                ]);
            }
            
            // Sort schedules by start time
            $schedules = $schedules->sortBy('start_time')->values();
            
            // Generate time slots from 7 AM to 9 PM
            $timeSlots = [];
            for ($hour = 7; $hour <= 21; $hour++) {
                $slotStart = sprintf('%02d:00', $hour);
                $slotEnd = sprintf('%02d:00', $hour + 1);
                
                // Find what occupies this time slot - now properly excluding overridden schedules
                $occupyingItem = null;
                $slotType = 'available';
                
                // Check all schedules/reservations for overlap
                $overlappingItems = $schedules->filter(function($item) use ($slotStart, $slotEnd) {
                    return ($item['start_time'] < $slotEnd && $item['end_time'] > $slotStart);
                });
                
                if ($overlappingItems->isNotEmpty()) {
                    // Priority order: 1. Overrides, 2. Reservations, 3. Regular schedules
                    $prioritizedItems = $overlappingItems->sortBy(function($item) {
                        if ($item['type'] === 'override') return 1; // Highest priority
                        if ($item['type'] === 'reservation') return 2;
                        return 3; // Regular schedules
                    });
                    
                    $occupyingItem = $prioritizedItems->first();
                    $slotType = $occupyingItem['type'];
                }
                
                $timeSlots[] = [
                    'time' => $slotStart . ' - ' . $slotEnd,
                    'hour' => $hour,
                    'available' => !$occupyingItem,
                    'type' => $slotType,
                    'item' => $occupyingItem,
                    'is_override' => $slotType === 'override' || ($occupyingItem && isset($occupyingItem['is_override']) && $occupyingItem['is_override'])
                ];
            }
            
            return [
                'lab_id' => $laboratory->id,
                'lab_name' => $laboratory->name,
                'lab_building' => $laboratory->building,
                'lab_room' => $laboratory->room_number,
                'capacity' => $laboratory->capacity,
                'computers' => $laboratory->number_of_computers,
                'status' => $laboratory->status,
                'schedules' => $schedules->toArray(),
                'time_slots' => $timeSlots
            ];
        })->values();

        // Temporary debugging - remove this after testing
        logger('Lab Data Debug:', [
            'selected_date' => $selectedDate->format('Y-m-d'),
            'total_labs' => $labData->count(),
            'sample_lab_data' => $labData->first()
        ]);

        // Get laboratory reservations for the selected date (separate for other tabs)
        $labReservationsDisplay = \App\Models\LaboratoryReservation::with(['laboratory', 'user'])
            ->whereDate('reservation_date', $selectedDate->format('Y-m-d'))
            ->get()
            ->map(function($reservation) {
                return [
                    'id' => $reservation->id,
                    'laboratory_name' => $reservation->laboratory->name,
                    'user_name' => $reservation->user->name,
                    'start_time' => $reservation->start_time ? \Carbon\Carbon::parse($reservation->start_time)->format('h:i A') : 'N/A',
                    'end_time' => $reservation->end_time ? \Carbon\Carbon::parse($reservation->end_time)->format('h:i A') : 'N/A',
                    'purpose' => $reservation->purpose ?? 'N/A',
                    'instructor' => $reservation->course_code ?? 'N/A', // Using course_code since instructor_name doesn't exist
                    'subject' => $reservation->subject ?? 'N/A',
                    'expected_attendees' => $reservation->num_students ?? 0,
                    'status' => $reservation->status
                ];
            });

        // Get equipment borrowing for the selected date
        $equipmentBorrowing = \App\Models\EquipmentRequest::with(['equipment.category', 'user'])
            ->whereDate('requested_from', $selectedDate->format('Y-m-d'))
            ->get()
            ->map(function($request) {
                return [
                    'id' => $request->id,
                    'equipment_name' => $request->equipment->name,
                    'user_name' => $request->user->name,
                    'borrow_time' => $request->requested_from->format('h:i A'),
                    'return_time' => $request->requested_until->format('h:i A'),
                    'purpose' => $request->purpose ?? 'N/A',
                    'quantity' => 1, // Default quantity since model doesn't have this field
                    'category' => $request->equipment->category ? $request->equipment->category->name : 'Uncategorized',
                    'rfid_tag' => $request->equipment->rfid_tag ?? 'N/A',
                    'status' => $request->status
                ];
            });

        return response()->json([
            'date' => $selectedDate->format('F d, Y'),
            'day_name' => $selectedDate->format('l'),
            'available_equipment' => $availableEquipment,
            'due_equipment' => $dueEquipment,
            'overdue_equipment' => $overdueEquipment,
            'lab_schedules' => $labData,
            'lab_reservations' => $labReservationsDisplay,
            'equipment_borrowing' => $equipmentBorrowing,
            'debug_info' => [
                'total_regular_schedules' => $regularSchedules->count(),
                'total_schedule_overrides' => $scheduleOverrides->count(),
                'total_lab_reservations' => $labReservations->count(),
                'selected_date' => $selectedDate->format('Y-m-d'),
                'day_of_week' => $dayOfWeek
            ]
        ]);
    }
} 