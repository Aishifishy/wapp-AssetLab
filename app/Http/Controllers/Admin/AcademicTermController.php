<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicTermController extends Controller
{
    public function index(AcademicYear $academicYear)
    {
        $terms = $academicYear->terms()->orderBy('term_number')->get();
        return view('admin.academic.terms.index', compact('academicYear', 'terms'));
    }

    public function create(AcademicYear $academicYear)
    {
        return view('admin.academic.terms.create', compact('academicYear'));
    }

    public function store(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'term_number' => 'required|integer|min:1|max:3',
            'start_date' => [
                'required',
                'date',
                'after_or_equal:' . $academicYear->start_date,
                'before:' . $academicYear->end_date,
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
                'before_or_equal:' . $academicYear->end_date,
            ],
            'is_current' => 'boolean',
        ]);

        // Set the term name based on the term number
        $termNames = [
            1 => 'First Term',
            2 => 'Second Term',
            3 => 'Third Term'
        ];
        $validated['name'] = $termNames[$validated['term_number']];

        $term = $academicYear->terms()->create($validated);

        if ($request->boolean('is_current')) {
            $term->markAsCurrent();
        }

        return redirect()->route('admin.academic.index')
            ->with('success', 'Term created successfully.');
    }

    public function edit(AcademicYear $academicYear, AcademicTerm $term)
    {
        return view('admin.academic.terms.edit', compact('academicYear', 'term'));
    }

    public function update(Request $request, AcademicYear $academicYear, AcademicTerm $term)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'start_date' => [
                'required',
                'date',
                'after_or_equal:' . $academicYear->start_date,
                'before:' . $academicYear->end_date,
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
                'before_or_equal:' . $academicYear->end_date,
            ],
        ]);

        $term->update($validated);

        return redirect()->route('admin.academic.terms.index', $academicYear)
            ->with('success', 'Term updated successfully.');
    }

    public function setCurrent(AcademicYear $academicYear, AcademicTerm $term)
    {
        $term->markAsCurrent();

        return redirect()->route('admin.academic.terms.index', $academicYear)
            ->with('success', 'Current term updated successfully.');
    }
} 