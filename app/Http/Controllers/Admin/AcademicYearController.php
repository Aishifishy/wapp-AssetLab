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
        
        $termStartDate = Carbon::parse($validated['start_date']);
        for ($i = 1; $i <= 3; $i++) {
            $termEndDate = (clone $termStartDate)->addDays($termLength);
            
            $academicYear->terms()->create([
                'name' => "Term {$i}",
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

        $academicYear->update($validated);

        return redirect()->route('admin.academic.index')
            ->with('success', 'Academic year updated successfully.');
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
} 