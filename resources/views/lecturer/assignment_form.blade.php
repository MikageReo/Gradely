@extends('lecturer.layout')

@section('title', ($mode === 'edit' ? 'Edit' : 'Create') . ' Assignment - ' . $course->course_code)

@push('styles')
<style>
    .form-shell {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        max-width: 820px;
        margin: 0 auto;
    }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .page-title {
        font-size: 22px;
        font-weight: 700;
        color: #111827;
    }
    .page-subtitle {
        font-size: 13px;
        color: #6b7280;
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }
    label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        color: #1f2937;
        font-size: 14px;
    }
    input[type="text"],
    input[type="datetime-local"],
    select,
    textarea,
    input[type="file"] {
        width: 100%;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 14px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    textarea {
        resize: vertical;
        min-height: 140px;
    }
    .actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 12px;
        flex-wrap: wrap;
    }
    .btn-primary {
        padding: 10px 20px;
        background: #1976D2;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-secondary {
        padding: 10px 20px;
        background: #6b7280;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-primary:hover { background: #1558a5; }
    .btn-secondary:hover { background: #575d69; }
    .field-hint {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }
    .back-link {
        color: #1976D2;
        text-decoration: none;
        font-weight: 600;
    }
    .back-link:hover { text-decoration: underline; }
    @media (max-width: 768px) {
        .form-shell { padding: 18px; }
        .actions { justify-content: center; }
    }
</style>
@endpush

@section('content')
    <div class="form-shell">
        <div class="page-header">
            <div>
                <div class="page-title">
                    {{ $mode === 'edit' ? 'Edit Assignment' : 'Create Assignment' }}
                </div>
                <div class="page-subtitle">
                    {{ $course->course_code }} · {{ $course->course_name }}
                </div>
            </div>
            <a href="{{ route('lecturer.course.show', $course->id) }}" class="back-link">← Back to course</a>
        </div>

        @if ($errors->any())
            <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;padding:12px;border-radius:8px;margin-bottom:12px;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ $mode === 'edit' ? route('lecturer.assignment.update', [$course->id, $assignment->id]) : route('lecturer.assignment.store', $course->id) }}"
              enctype="multipart/form-data">
            @csrf
            @if($mode === 'edit')
                @method('PUT')
            @endif

            <div class="form-grid">
                <div>
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" required
                           value="{{ old('title', $assignment->title ?? '') }}">
                </div>

                <div>
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5">{{ old('description', $assignment->description ?? '') }}</textarea>
                </div>

                <div style="display:grid;grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
                    <div>
                        <label for="due_date">Due Date</label>
                        <input type="datetime-local" id="due_date" name="due_date"
                               value="{{ old('due_date', isset($assignment) && $assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '') }}">
                    </div>
                    <div>
                        <label for="status">Status *</label>
                        <select id="status" name="status" required>
                            <option value="open" {{ old('status', $assignment->status ?? 'open') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="close" {{ old('status', $assignment->status ?? '') === 'close' ? 'selected' : '' }}>Close</option>
                        </select>
                    </div>
                    <div>
                        <label for="visibility">Visibility *</label>
                        <select id="visibility" name="visibility" required>
                            <option value="hidden" {{ old('visibility', $assignment->visibility ?? 'hidden') === 'hidden' ? 'selected' : '' }}>Hidden</option>
                            <option value="published" {{ old('visibility', $assignment->visibility ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="attachment">Attachment</label>
                    <input type="file" id="attachment" name="attachment" accept=".pdf,.doc,.docx,.txt">
                    <div class="field-hint">Upload supporting materials (PDF, DOC, DOCX, TXT). Max 10MB.</div>
                    @if($mode === 'edit' && ($assignment->attachment ?? false))
                        <div style="margin-top:8px;font-size:13px;color:#374151;">
                            Current: <a href="{{ url('/' . $assignment->attachment) }}" target="_blank" style="color:#1976D2;">{{ basename($assignment->attachment) }}</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="actions">
                <a href="{{ route('lecturer.course.show', $course->id) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    {{ $mode === 'edit' ? 'Update Assignment' : 'Create Assignment' }}
                </button>
            </div>
        </form>
    </div>
@endsection

