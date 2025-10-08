<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::with('terms')
            ->orderBy('start_date', 'desc')
            ->get();

        $calendarEvents = [];
        
        // Initialize empty activities - will be loaded dynamically
        $calendarActivities = [];
        
        foreach ($academicYears as $year) {
            foreach ($year->terms as $term) {
                $calendarEvents[] = [
                    'title' => $term->name . ' (' . $year->name . ')',
                    'start' => $term->start_date->format('Y-m-d'),
                    'end' => $term->end_date->addDay()->format('Y-m-d'), // Add a day because FullCalendar end dates are exclusive
                    'className' => 'term-event' . ($term->is_current ? ' current-term' : ''),
                    'display' => 'background',
                    'extendedProps' => [
                        'year_id' => $year->id,
                        'term_id' => $term->id,
                        'is_current' => $term->is_current,
                        'type' => 'term'
                    ]
                ];
            }
        }

        return view('admin.academic.index', compact('academicYears', 'calendarEvents', 'calendarActivities'));
    }

    /**
     * Get calendar activities for notification indicators
     */
    private function getCalendarActivities($startDate, $endDate)
    {
        $activities = [];
        
        // Get laboratory reservations within date range (only pending/approved)
        $labReservations = \App\Models\LaboratoryReservation::whereBetween('reservation_date', [$startDate, $endDate])
            ->whereIn('status', ['pending', 'approved'])
            ->get();
        
        // Get overdue equipment (for current date indicators)
        $overdueRequests = \App\Models\EquipmentRequest::where('requested_until', '<', Carbon::now())
            ->whereNull('returned_at')
            ->where('status', 'approved')
            ->get();
        
        // Initialize activities array for each date in the range
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            
            // Count equipment requests active on this specific date (only pending/approved)
            $equipmentCount = \App\Models\EquipmentRequest::where(function($query) use ($dateKey) {
                // Equipment is active on this date if the borrowing period includes this date
                // Use DATE() function to compare only the date part, ignoring time
                $query->whereRaw('DATE(requested_from) <= ?', [$dateKey])
                      ->whereRaw('DATE(requested_until) >= ?', [$dateKey]);
            })
            ->whereIn('status', ['pending', 'approved'])
            ->count();
            
            if ($equipmentCount > 0) {
                if (!isset($activities[$dateKey])) {
                    $activities[$dateKey] = [
                        'equipment_borrowing' => 0,
                        'lab_reservations' => 0,
                        'overdue_equipment' => 0
                    ];
                }
                
                $activities[$dateKey]['equipment_borrowing'] = $equipmentCount;
            }
        }
        
        // Process laboratory reservations
        foreach ($labReservations as $reservation) {
            $dateKey = $reservation->reservation_date->format('Y-m-d');
            
            if (!isset($activities[$dateKey])) {
                $activities[$dateKey] = [
                    'equipment_borrowing' => 0,
                    'lab_reservations' => 0,
                    'overdue_equipment' => 0
                ];
            }
            
            $activities[$dateKey]['lab_reservations']++;
        }
        
        // Process overdue equipment (show on current date)
        $currentDate = Carbon::now()->format('Y-m-d');
        if (count($overdueRequests) > 0) {
            if (!isset($activities[$currentDate])) {
                $activities[$currentDate] = [
                    'equipment_borrowing' => 0,
                    'lab_reservations' => 0,
                    'overdue_equipment' => 0
                ];
            }
            
            $activities[$currentDate]['overdue_equipment'] = count($overdueRequests);
        }
        
        return $activities;
    }

    /**
     * Get calendar activities for a specific month (AJAX endpoint)
     */
    public function getMonthActivities(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);
        
        // Create cache key for this month's data
        $cacheKey = "month_activities_{$year}_{$month}";
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($year, $month) {
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            
            // Extend range to include previous/next month edges for better UX
            $startDate->subDays(7);
            $endDate->addDays(7);
            
            $activities = $this->getCalendarActivities($startDate, $endDate);
            
            return response()->json($activities);
        });
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

        // Get laboratory reservations for the selected date (only approved for schedule display)
        $labReservations = \App\Models\LaboratoryReservation::with(['laboratory', 'user'])
            ->whereDate('reservation_date', $selectedDate->format('Y-m-d'))
            ->where('status', 'approved')
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
                        'original_schedule_id' => $override->laboratory_schedule_id
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

        // Get laboratory reservations for the selected date (separate for other tabs)
        $labReservationsDisplay = \App\Models\LaboratoryReservation::with(['laboratory', 'user'])
            ->whereDate('reservation_date', $selectedDate->format('Y-m-d'))
            ->whereIn('status', ['pending', 'approved'])
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

        // Get equipment borrowing for the selected date (active on this date)
        $equipmentBorrowing = \App\Models\EquipmentRequest::with(['equipment.category', 'user'])
            ->where(function($query) use ($selectedDate) {
                $dateStr = $selectedDate->format('Y-m-d');
                // Equipment is active on this date if the borrowing period includes this date
                // Use DATE() function to compare only the date part, ignoring time
                $query->whereRaw('DATE(requested_from) <= ?', [$dateStr])
                      ->whereRaw('DATE(requested_until) >= ?', [$dateStr]);
            })
            ->whereIn('status', ['pending', 'approved'])
            ->get()
            ->map(function($request) use ($selectedDate) {
                $selectedDateStr = $selectedDate->format('Y-m-d');
                $isStartingToday = $request->requested_from->format('Y-m-d') === $selectedDateStr;
                $isEndingToday = $request->requested_until->format('Y-m-d') === $selectedDateStr;
                
                return [
                    'id' => $request->id,
                    'equipment_name' => $request->equipment->name,
                    'user_name' => $request->user->name,
                    'borrow_time' => $request->requested_from->format('M d, Y h:i A'),
                    'return_time' => $request->requested_until->format('M d, Y h:i A'),
                    'purpose' => $request->purpose ?? 'N/A',
                    'quantity' => 1, // Default quantity since model doesn't have this field
                    'category' => $request->equipment->category ? $request->equipment->category->name : 'Uncategorized',
                    'rfid_tag' => $request->equipment->rfid_tag ?? 'N/A',
                    'status' => $request->status,
                    'borrowing_phase' => $isStartingToday ? 'starting' : ($isEndingToday ? 'ending' : 'active')
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
            'equipment_borrowing' => $equipmentBorrowing
        ]);
    }
} 