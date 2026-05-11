<div>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        background: #f4f6f9;
    }
    .page-container {
        max-width: 1520px;
        margin: 22px auto;
    }
    .small-ui,
    .small-ui * {
        font-size: 12.5px;
    }
    .card-box {
        padding: 16px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, .08);
        background: #fff;
        margin-bottom: 16px;
    }
    .side-box {
        position: sticky;
        top: 16px;
    }
    .table thead th {
        white-space: nowrap;
        vertical-align: middle;
    }
    .student-name {
        font-weight: 800;
        font-size: 13px;
    }
    .student-meta {
        color: #6c757d;
        font-size: 12px;
    }
    .thumb {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #ddd;
        background: #fff;
    }
    .badge-soft {
        background: #eef2ff;
        color: #2b3a67;
        border: 1px solid #d6ddff;
        font-weight: 700;
    }
    .toast-pop {
        position: fixed;
        right: 16px;
        bottom: 16px;
        z-index: 1080;
        min-width: 280px;
        max-width: 420px;
        border-radius: 12px;
        padding: 12px 14px;
        box-shadow: 0 14px 30px rgba(0, 0, 0, .18);
    }
    .filter-chip {
        text-decoration: none;
        border-radius: 999px;
        padding: 7px 12px;
        font-weight: 700;
        border: 1px solid #dbe1ea;
        color: #334155;
        background: #fff;
    }
    .meta-list {
        color: #6b7280;
        line-height: 1.45;
    }
    .deleted-chip {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 800;
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    .loading-box {
        opacity: .65;
        pointer-events: none;
    }
    </style>

    <div class="container page-container small-ui" wire:loading.class="loading-box">
        <div class="row g-3">
            <div class="col-lg-10">

                <div class="card-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="m-0">Recycle Bin</h5>
                            <div class="text-muted" style="font-size:12px;">
                                Restore deleted students or permanently remove them
                            </div>
                        </div>
                        <div>
                            <span class="badge badge-soft">Admin View</span>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">← Dashboard</a>
                        </div>
                    </div>
                </div>

                <div class="card-box">
                    <div><b>Deleted Students:</b> {{ $totalDeleted }}</div>
                    <div class="text-muted mt-1">Students moved here are soft deleted. You can restore them or delete them permanently.</div>
                </div>

                <div class="card-box">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="m-0" style="font-weight:800;">Deleted Students</h6>
                        <span class="badge badge-soft">{{ $showingCount }} Showing</span>
                    </div>

                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Search</label>
                            <input type="text" wire:model.live.debounce.400ms="search" class="form-control" placeholder="Name, email, passport...">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Intake</label>
                            <select wire:model.live="intake" class="form-select">
                                <option value="all">All intake</option>
                                @foreach($intakes as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Nationality</label>
                            <select wire:model.live="nationality" class="form-select">
                                <option value="all">All nationality</option>
                                @foreach($nationalities as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Agent</label>
                            <select wire:model.live="agentId" class="form-select">
                                <option value="all">All agents</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label class="form-label fw-bold">Show</label>
                            <select wire:model.live="perPage" class="form-select">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width:55px;">#</th>
                                    <th style="min-width:180px;">Student</th>
                                    <th style="width:140px;">Deleted Status</th>
                                    <th style="width:160px;">Photo</th>
                                    <th style="width:170px;">Deleted At</th>
                                    <th style="width:130px;">Deleted By Agent</th>
                                    <th style="width:220px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $index => $row)
                                    @php
                                        $st = $row['student'];
                                        $name = $st?->student_name ?? '';
                                        $jp = $st?->student_name_jp ?? '';
                                        $agent = $st?->creator?->name ?? '-';
                                        $gender = $st?->gender ?? '';
                                        $nat = $st?->nationality ?? '';
                                        $age = $st?->age ?? '';
                                    @endphp

                                    <tr wire:key="recycle-student-{{ $st->id }}">
                                        <td>{{ $students->firstItem() + $index }}</td>

                                        <td>
                                            <div class="student-name">{{ $name }}</div>
                                            @if($jp)
                                                <div class="student-meta">{{ $jp }}</div>
                                            @endif

                                            <div class="meta-list mt-1">
                                                {{ !empty($agent) ? 'Agent: '.$agent.' • ' : '' }}
                                                {{ $gender ? $gender . ' • ' : '' }}
                                                {{ $nat ? $nat . ' • ' : '' }}
                                                {{ $age ? 'Age: ' . $age . ' • ' : '' }}
                                                {{ !empty($st?->intake) ? 'Intake: ' . $st->intake : '' }}
                                            </div>

                                            @if(!empty($st->email))
                                                <div class="text-muted mt-1">{{ $st->email }}</div>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="deleted-chip">Deleted</span>
                                        </td>

                                        <td>
                                            @if(!empty($row['photo_url']))
                                                <img src="{{ $row['photo_url'] }}" alt="Student Photo" class="thumb">
                                            @else
                                                <span class="text-muted">No photo</span>
                                            @endif
                                        </td>

                                        <td>
                                            {{ optional($st->deleted_at)->format('Y-m-d H:i') ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $agent }}
                                        </td>

                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-success w-100"
                                                    onclick="return confirm('Restore this student?')"
                                                    wire:click="restoreStudent({{ $st->id }})"
                                                    wire:loading.attr="disabled"
                                                >
                                                    Restore
                                                </button>

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger w-100"
                                                    onclick="return confirm('This will permanently delete the student and related data. Continue?')"
                                                    wire:click="forceDeleteStudent({{ $st->id }})"
                                                    wire:loading.attr="disabled"
                                                >
                                                    Delete Permanently
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No deleted students found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($students->hasPages())
                        <div class="mt-3">
                            {{ $students->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-2">
                <div class="card-box side-box">
                    <h6 class="mb-2" style="font-weight:800;">About this page</h6>

                    <div class="text-muted" style="font-size:12px; line-height:1.5;">
                        Deleted students are kept here temporarily.

                        <div class="mt-2">
                            • <b>Restore</b> → Bring the student back<br>
                            • <b>Delete Permanently</b> → Remove forever<br>
                        </div>

                        <div class="mt-2">
                            Use restore when a student was deleted by mistake. Use permanent delete only when you are sure.
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="mb-2" style="font-weight:800;">Quick actions</div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="toast-pop" style="background:#198754; color:#fff;">
                <div style="font-weight:900;">Success</div>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="toast-pop" style="background:#dc3545; color:#fff;">
                <div style="font-weight:900;">Error</div>
                <div>{{ session('error') }}</div>
            </div>
        @endif
    </div>

    <script>
    document.addEventListener('livewire:init', () => {
        setTimeout(() => {
            document.querySelectorAll('.toast-pop').forEach(el => {
                setTimeout(() => el.remove(), 3500);
            });
        }, 100);
    });
    </script>
</div>