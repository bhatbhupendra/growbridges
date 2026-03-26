@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f4f6f9;
}

.page-container {
    max-width: 1400px;
    margin: 24px auto;
}

.small-ui,
.small-ui * {
    font-size: 12.5px;
}

.card-box {
    padding: 18px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    background: #fff;
    margin-bottom: 18px;
}

.side-box {
    position: sticky;
    top: 16px;
}

.category-title {
    font-weight: 800;
    margin-top: 14px;
    margin-bottom: 6px;
}

.hr-tight {
    margin: 12px 0;
}

.form-control,
.form-select {
    padding: .38rem .55rem;
    font-size: 12.5px;
}

.form-check {
    margin-bottom: 4px;
}

.badge-soft {
    background: #eef2ff;
    color: #2b3a67;
    border: 1px solid #d6ddff;
    font-weight: 800;
}

.doc-mini {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 6px 8px;
    border: 1px solid #eef2f7;
    border-radius: 10px;
    margin-bottom: 6px;
    background: #fff;
}

.doc-mini:hover {
    background: #fafcff;
}

.doc-left {
    min-width: 0;
}

.doc-name {
    font-weight: 800;
}

.doc-meta {
    font-size: 12px;
    color: #6c757d;
    white-space: nowrap;
}

.btn-icon {
    border: 1px solid #e5e7eb;
    background: #fff;
    border-radius: 10px;
    padding: 5px 8px;
    font-weight: 800;
}

.btn-icon:hover {
    background: #f8fafc;
}

.modal-mini .modal-dialog {
    position: fixed;
    right: 16px;
    bottom: 16px;
    margin: 0;
    width: 440px;
    max-width: calc(100vw - 32px);
}

.modal-mini .modal-content {
    border-radius: 14px;
    box-shadow: 0 14px 30px rgba(0, 0, 0, .2);
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
    display: none;
}
</style>

<div class="container page-container small-ui">
    <div class="row g-3">

        <div class="col-lg-8">

            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="m-0">School Document Requirements</h5>
                    <a href="{{ url('/dashboard') }}" class="btn btn-sm btn-secondary">Back</a>
                </div>
                <hr class="hr-tight">
                <div class="text-muted" style="font-size:12px;">
                    Add document types (global), then select a school and tick required docs.
                </div>
            </div>

            <div class="card-box">
                <h6 class="mb-3 fw-bold">Add New Document Type</h6>

                <form method="POST" action="{{ route('admin.document-types.store') }}" class="row g-2">
                    @csrf
                    <input type="hidden" name="current_school_id" value="{{ (int) $schoolId }}">

                    <div class="col-md-5">
                        <label class="form-label fw-bold">Document Name</label>
                        <input type="text" name="doc_name" class="form-control" placeholder="e.g. Tax Document"
                            required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">File Type</label>
                        <select name="file_type" class="form-select">
                            <option value="pdf" selected>PDF</option>
                            <option value="jpeg">JPEG</option>
                            <option value="doc">DOC</option>
                            <option value="docx">DOCX</option>
                            <option value="xls">XLS</option>
                            <option value="xlsx">XLSX</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Category</label>
                        <select name="category" id="categorySelect" class="form-select">
                            @foreach($categories as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                            <option value="__custom__">Custom…</option>
                        </select>
                        <input type="text" name="category_custom" id="categoryCustom" class="form-control mt-2"
                            placeholder="Type new category" style="display:none;">
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-dark px-4">Add Document</button>
                    </div>
                </form>
            </div>

            <div class="card-box">
                <h6 class="mb-3 fw-bold">Select School</h6>

                <form method="GET" action="{{ route('admin.school-requirements.index') }}"
                    class="row g-2 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">School</label>
                        <select name="school_id" class="form-select" required>
                            <option value="">-- Choose School --</option>
                            @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary w-100" type="submit">Load Requirements</button>
                    </div>
                </form>
            </div>

            @if($schoolId > 0)
            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="m-0 fw-bold">Set Required Documents</h6>
                    <span class="badge badge-soft">School ID: {{ (int) $schoolId }}</span>
                </div>

                <form method="POST" action="{{ route('admin.school-requirements.save') }}">
                    @csrf
                    <input type="hidden" name="school_id" value="{{ (int) $schoolId }}">

                    @foreach($docTypes as $category => $docs)
                    <div class="category-title">{{ $category }}</div>

                    <div class="row g-2">
                        @foreach($docs as $doc)
                        @php
                        $did = (int) $doc->id;
                        $checked = array_key_exists($did, $requiredMap);
                        $ftShow = strtoupper($doc->file_type ?? 'PDF');
                        @endphp

                        <div class="col-md-6">
                            <div class="doc-mini">
                                <div class="doc-left">
                                    <div class="form-check m-0">
                                        <input class="form-check-input" type="checkbox" name="doc_type_ids[]"
                                            value="{{ $did }}" {{ $checked ? 'checked' : '' }} id="doc_{{ $did }}">
                                        <label class="form-check-label" for="doc_{{ $did }}">
                                            <span class="doc-name">{{ $doc->doc_name }}</span>
                                        </label>
                                    </div>
                                    <div class="doc-meta">
                                        Type: <b>{{ $ftShow }}</b>
                                    </div>
                                </div>

                                <button type="button" class="btn-icon btnEditDoc" data-id="{{ $doc->id }}"
                                    data-name="{{ $doc->doc_name }}" data-cat="{{ $category }}"
                                    data-ft="{{ $doc->file_type }}">
                                    Edit
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <hr class="hr-tight">
                    @endforeach

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success px-4">
                            Save Requirements
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card-box side-box">
                <h6 class="fw-bold mb-2">About This Page</h6>
                <div class="text-muted" style="font-size:12px; line-height:1.55;">
                    Configure which documents are required for each school. Students only see and upload selected items.
                </div>

                <hr>

                <h6 class="fw-bold mb-2">What you can do</h6>
                <ul style="padding-left:16px; line-height:1.65; margin:0;">
                    <li>Add a document type with file type (PDF/JPEG/Word/Excel).</li>
                    <li>Reuse existing categories from dropdown.</li>
                    <li>Edit any document type from the list (bottom-right popup).</li>
                    <li>Tick required docs per school.</li>
                </ul>

                <hr>

                <div class="d-grid">
                    <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary btn-sm">Return to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-mini" id="editDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header py-2">
                <h6 class="modal-title" style="font-weight:900;">Edit Document Type</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" id="editDocForm">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <input type="hidden" name="keep_school_id" value="{{ (int) $schoolId }}">

                    <div class="mb-2">
                        <label class="form-label fw-bold">Document Name</label>
                        <input type="text" name="doc_name" id="editDocName" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">File Type</label>
                        <select name="file_type" id="editFileType" class="form-select">
                            <option value="pdf">PDF</option>
                            <option value="jpeg">JPEG</option>
                            <option value="doc">DOC</option>
                            <option value="docx">DOCX</option>
                            <option value="xls">XLS</option>
                            <option value="xlsx">XLSX</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Category</label>
                        <select name="category" id="editCategorySelect" class="form-select">
                            @foreach($categories as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                            <option value="__custom__">Custom…</option>
                        </select>
                        <input type="text" name="category_custom" id="editCategoryCustom" class="form-control mt-2"
                            placeholder="Type new category" style="display:none;">
                    </div>

                    <div class="text-muted" style="font-size:12px;">
                        Editing file type will affect what students can upload for this document.
                    </div>
                </div>

                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-dark">Save Changes</button>
                </div>
            </form>

        </div>
    </div>
</div>

@if(session('success'))
<div id="toastMsg" class="toast-pop" style="background:#198754;color:#fff;">
    <div style="font-weight:900;">Success</div>
    <div>{{ session('success') }}</div>
</div>
@endif

@if(session('error'))
<div id="toastMsg" class="toast-pop" style="background:#dc3545;color:#fff;">
    <div style="font-weight:900;">Error</div>
    <div>{{ session('error') }}</div>
</div>
@endif

@if($errors->any())
<div id="toastMsg" class="toast-pop" style="background:#dc3545;color:#fff;">
    <div style="font-weight:900;">Validation Error</div>
    <div>Please check the form inputs.</div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const t = document.getElementById('toastMsg');
    if (t) {
        t.style.display = 'block';
        setTimeout(() => t.style.display = 'none', 3500);
    }

    const catSel = document.getElementById('categorySelect');
    const catCustom = document.getElementById('categoryCustom');
    if (catSel && catCustom) {
        catSel.addEventListener('change', () => {
            catCustom.style.display = (catSel.value === '__custom__') ? 'block' : 'none';
            if (catSel.value !== '__custom__') catCustom.value = '';
        });
    }

    const editCatSel = document.getElementById('editCategorySelect');
    const editCatCustom = document.getElementById('editCategoryCustom');
    if (editCatSel && editCatCustom) {
        editCatSel.addEventListener('change', () => {
            editCatCustom.style.display = (editCatSel.value === '__custom__') ? 'block' : 'none';
            if (editCatSel.value !== '__custom__') editCatCustom.value = '';
        });
    }

    const editModalEl = document.getElementById('editDocModal');
    const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
    const editForm = document.getElementById('editDocForm');

    document.querySelectorAll('.btnEditDoc').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id || '';
            const name = btn.dataset.name || '';
            const cat = btn.dataset.cat || 'Other';
            const ft = (btn.dataset.ft || 'pdf').toLowerCase();

            document.getElementById('editDocName').value = name;

            if (editForm) {
                editForm.action = "{{ url('/admin/document-types') }}/" + id;
            }

            const ftSel = document.getElementById('editFileType');
            if (ftSel) {
                ftSel.value = (ft === 'jpg') ? 'jpeg' : ft;
            }

            const catSel2 = document.getElementById('editCategorySelect');
            const catCustom2 = document.getElementById('editCategoryCustom');

            let found = false;
            if (catSel2) {
                for (const opt of catSel2.options) {
                    if (opt.value === cat) {
                        found = true;
                        break;
                    }
                }

                if (found) {
                    catSel2.value = cat;
                    catCustom2.style.display = 'none';
                    catCustom2.value = '';
                } else {
                    catSel2.value = '__custom__';
                    catCustom2.style.display = 'block';
                    catCustom2.value = cat;
                }
            }

            editModal?.show();
        });
    });
});
</script>
@endsection