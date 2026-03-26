<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSchoolRequest;
use App\Models\School;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View{
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);

        $agents = User::query()
            ->whereIn('role', ['agent'])
            ->latest('id')
            ->get(['id', 'name', 'email', 'role']);

        $students = User::query()
            ->whereIn('role', ['student'])
            ->latest('id')
            ->get(['id', 'name', 'email', 'role']);

        $schools = School::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.dashboard', compact('agents', 'schools','students'));
    }

    public function storeSchool(StoreSchoolRequest $request): RedirectResponse {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);

        $schoolName = trim($request->school_name);

        $exists = School::whereRaw('LOWER(name) = ?', [strtolower($schoolName)])->exists();

        if ($exists) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'School already exists.');
        }

        School::create([
            'name' => $schoolName,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'School added successfully!');
    }
}