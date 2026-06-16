<?php
namespace App\Livewire\Admin;

use App\Models\Student;
use App\Models\User;
use App\Models\StudentInspectorAssignment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AssignStudentToInspector extends Component
{
    public string $search = '';
    public string $inspectorId = '';
    public array $selectedStudents = [];

    public function mount(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);
    }

    public function assign(): void
    {
        $this->validate([
            'inspectorId' => ['required', 'exists:users,id'],
            'selectedStudents' => ['required', 'array'],
        ]);

        $inspector = User::where('role', 'inspector')->findOrFail($this->inspectorId);

        foreach ($this->selectedStudents as $studentId) {
            StudentInspectorAssignment::firstOrCreate(
                [
                    'student_id' => $studentId,
                    'inspector_id' => $inspector->id,
                ],
                [
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                ]
            );
        }

        $this->selectedStudents = [];

        session()->flash('success', 'Students assigned to inspector successfully.');
    }

    public function removeAssignment(int $assignmentId): void
    {
        StudentInspectorAssignment::findOrFail($assignmentId)->delete();

        session()->flash('success', 'Assignment removed successfully.');
    }

    public function render()
    {
        $inspectors = User::where('role', 'inspector')
            ->orderBy('name')
            ->get();

        $students = Student::query()
            ->whereNull('deleted_at')
            ->when($this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('student_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('passport_number', 'like', "%{$this->search}%")
                        ->orWhere('intake', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->limit(100)
            ->get();

        $assignments = StudentInspectorAssignment::with(['student', 'inspector'])
        ->latest()
        ->get()
        ->groupBy('inspector_id');

        return view('livewire.admin.assign-student-to-inspector', [
            'inspectors' => $inspectors,
            'students' => $students,
            'assignments' => $assignments,
        ]);
    }
}