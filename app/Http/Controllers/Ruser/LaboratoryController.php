<?php

namespace App\Http\Controllers\Ruser;

use App\Http\Controllers\Controller;
use App\Models\ComputerLaboratory;
use App\Models\LaboratorySchedule;
use App\Models\AcademicTerm;
use App\Services\UserLaboratoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaboratoryController extends Controller
{
    protected $userLaboratoryService;

    public function __construct(UserLaboratoryService $userLaboratoryService)
    {
        $this->userLaboratoryService = $userLaboratoryService;
    }

    /**
     * Display the list of laboratories available for reservation
     */
    public function index()
    {
        $laboratories = $this->userLaboratoryService->getAvailableLaboratories();
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
        // Redirect to the new reservation form
        return redirect()->route('ruser.laboratory.reservations.create', $laboratory);
    }
}
