<?php

namespace App\Livewire\Admin;

use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RecycleBinStudents extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $intake = 'all';
    public $agentId = 'all';
    public $nationality = 'all';
    public $perPage = 20;

    protected $queryString = [
        'search' => ['except' => ''],
        'intake' => ['except' => 'all'],
        'agentId' => ['except' => 'all'],
        'nationality' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingIntake()
    {
        $this->resetPage();
    }

    public function updatingAgentId()
    {
        $this->resetPage();
    }

    public function updatingNationality()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function restoreStudent($studentId)
    {
        $student = Student::onlyTrashed()->find($studentId);

        if (!$student) {
            session()->flash('error', 'Deleted student not found.');
            return;
        }

        $student->restore();

        session()->flash('success', 'Student restored successfully.');
    }

    public function forceDeleteStudent($studentId)
    {
        $student = Student::onlyTrashed()->with([
            'applications',
        ])->find($studentId);

        if (!$student) {
            session()->flash('error', 'Deleted student not found.');
            return;
        }

        DB::transaction(function () use ($student) {
            // delete files if stored in student->photo
            if (!empty($student->photo)) {
                $photoPath = ltrim(str_replace('storage/', 'public/', $student->photo), '/');
                if (Storage::exists($photoPath)) {
                    Storage::delete($photoPath);
                }
            }

            // delete related rows if these relations exist in your project
            if (method_exists($student, 'applications')) {
                $student->applications()->delete();
            }

            if (method_exists($student, 'documents')) {
                $documents = $student->documents()->get();

                foreach ($documents as $document) {
                    if (!empty($document->file_path)) {
                        $docPath = ltrim(str_replace('storage/', 'public/', $document->file_path), '/');
                        if (Storage::exists($docPath)) {
                            Storage::delete($docPath);
                        }
                    }
                }

                $student->documents()->delete();
            }

            if (method_exists($student, 'comments')) {
                $student->comments()->delete();
            }

            if (method_exists($student, 'strength')) {
                optional($student->strength)->delete();
            }

            $student->forceDelete();
        });

        session()->flash('success', 'Student permanently deleted successfully.');

        $this->resetPage();
    }

    public function render()
    {
        $intakes = Student::onlyTrashed()
            ->whereNotNull('intake')
            ->where('intake', '!=', '')
            ->distinct()
            ->orderBy('intake')
            ->pluck('intake');

        $nationalities = Student::onlyTrashed()
            ->whereNotNull('nationality')
            ->where('nationality', '!=', '')
            ->distinct()
            ->orderBy('nationality')
            ->pluck('nationality');

        $agents = \App\Models\User::query()
            ->where('role', 'agent')
            ->orderBy('name')
            ->get(['id', 'name']);

        $query = Student::onlyTrashed()
            ->with(['creator'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('student_name', 'like', '%' . $this->search . '%')
                        ->orWhere('student_name_jp', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('passport_number', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->intake !== 'all', function ($q) {
                $q->where('intake', $this->intake);
            })
            ->when($this->nationality !== 'all', function ($q) {
                $q->where('nationality', $this->nationality);
            })
            ->when($this->agentId !== 'all', function ($q) {
                $q->where('created_by', $this->agentId);
            })
            ->latest('deleted_at');

        $students = $query->paginate($this->perPage);

        $rows = $students->getCollection()->map(function ($student) {
            $photoUrl = null;

            if (!empty($student->photo)) {
                $photoUrl = asset('storage/' . ltrim(str_replace(['storage/', 'public/'], '', $student->photo), '/'));
            }

            return [
                'student' => $student,
                'photo_url' => $photoUrl,
            ];
        });

        $students->setCollection($rows);

        $totalDeleted = Student::onlyTrashed()->count();
        $showingCount = $students->count();

        return view('livewire.admin.recycle-bin-students', [
            'students' => $students,
            'intakes' => $intakes,
            'nationalities' => $nationalities,
            'agents' => $agents,
            'totalDeleted' => $totalDeleted,
            'showingCount' => $showingCount,
        ]);
    }
}