<?php

namespace App\Http\Controllers\Ruser;

use App\Http\Controllers\Controller;
use App\Models\ComputerLaboratory;
use App\Models\LaboratorySchedule;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaboratoryController extends Controller
{
    /**
     * Display the list of laboratories available for reservation
     */
    public function index()
    {
        // Get all available laboratories
        $laboratories = ComputerLaboratory::where('status', 'available')
            ->orderBy('building')
            ->orderBy('room_number')
            ->get();

        $currentTerm = AcademicTerm::where('is_current', true)->first();
        
        // Get existing schedules for these laboratories
        $schedules = [];
        if ($currentTerm) {
            $schedules = LaboratorySchedule::with(['laboratory', 'academicTerm'])
                ->where('academic_term_id', $currentTerm->id)
                ->get()
                ->groupBy('laboratory_id');
        }

        return view('ruser.laboratory.index', compact('laboratories', 'schedules', 'currentTerm'));
    }

    /**
     * Show the laboratory schedule and reservation form
     */
    public function show(ComputerLaboratory $laboratory)
    {
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        
        $schedules = collect([]);
        if ($currentTerm) {
            $schedules = LaboratorySchedule::where('laboratory_id', $laboratory->id)
                ->where('academic_term_id', $currentTerm->id)
                ->get();
        }

        return view('ruser.laboratory.show', compact('laboratory', 'schedules', 'currentTerm'));
    }

    /**
     * Make a reservation request for a laboratory
     */
    public function reserve(Request $request, ComputerLaboratory $laboratory)
    {
        $validated = $request->validate([
            'purpose' => 'required|string|max:500',
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'date' => 'required|date|after_or_equal:today',
        ]);
        
        // Here we would check for conflicts and create a reservation
        // For now this is a placeholder
        
        return redirect()->route('ruser.laboratory.index')
            ->with('success', 'Laboratory reservation request submitted successfully.');
    }
}
