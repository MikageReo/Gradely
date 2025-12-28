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
        border: 2px solid #999;
        font-size: 14px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    input[type="text"]:focus,
    input[type="datetime-local"]:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #1976D2;
        border-width: 2px;
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
    /* File Upload Styles (matching student submission interface) */
    .upload-section {
        border: 2px dashed #BBDEFB;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        background: #F5F5F5;
        margin-top: 8px;
    }
    .upload-section.has-files {
        border-color: var(--color-primary);
        background: #E3F2FD;
    }
    .file-input-wrapper {
        margin-bottom: 15px;
    }
    .file-input {
        display: none;
    }
    .file-input-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: #1976D2;
        color: #fff;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.2s;
        font-size: 14px;
    }
    .file-input-label:hover {
        background: #1565C0;
    }
    .file-list {
        margin-top: 15px;
        text-align: left;
    }
    .file-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        background: #fff;
        border-radius: 4px;
        margin-bottom: 8px;
    }
    .file-name {
        font-size: 14px;
        color: #222;
    }
    .file-remove {
        background: #E53935;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 4px 8px;
        cursor: pointer;
        font-size: 12px;
    }
    .file-remove:hover {
        background: #C62828;
    }
    .current-file {
        margin-top: 12px;
        padding: 10px;
        background: #E8F5E9;
        border-radius: 6px;
        font-size: 13px;
    }
    .current-file-label {
        font-weight: 600;
        color: #2E7D32;
        margin-bottom: 6px;
    }
    .current-file-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #1B5E20;
    }
    .current-file-item a {
        margin-left: auto;
        color: #1976D2;
        text-decoration: none;
        font-size: 12px;
        font-weight: 500;
    }
    .current-file-item a:hover {
        text-decoration: underline;
    }
    @media (max-width: 768px) {
        .form-shell { padding: 18px; }
        .actions { justify-content: center; }
        .upload-section {
            padding: 20px;
        }
    }
</style>
@push('scripts')
<script>
    // Character counter for description field (Functional Appropriateness)
    function updateCharCount() {
        const textarea = document.getElementById('description');
        const charCountText = document.getElementById('char-count-text');
        const charCount = textarea.value.length;
        charCountText.textContent = charCount;
        
        // Change color when approaching limit
        const charCountDiv = document.getElementById('char-count');
        if (charCount > 4500) {
            charCountDiv.style.color = '#dc2626';
        } else if (charCount > 4000) {
            charCountDiv.style.color = '#ea580c';
        } else {
            charCountDiv.style.color = '#6b7280';
        }
    }
    
    // Initialize character count on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateCharCount();
    });

    // File upload handling (matching student submission interface - multiple files)
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    const uploadSection = document.getElementById('uploadSection');
    const selectedFiles = [];
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            const invalidFiles = [];
            const validFiles = [];

            // Validate file sizes
            files.forEach(file => {
                if (file.size > maxSize) {
                    invalidFiles.push(file.name + ' (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB)');
                } else {
                    validFiles.push(file);
                }
            });

            // Show error for files that are too large
            if (invalidFiles.length > 0) {
                alert('The following files exceed the 10MB limit:\n' + invalidFiles.join('\n') + '\n\nPlease select smaller files. Only files under 10MB will be uploaded.');
            }

            // Update selectedFiles with valid files only
            selectedFiles.length = 0;
            selectedFiles.push(...validFiles);

            // Update the file input with valid files only
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;

            updateFileList();
        });

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        function updateFileList() {
            fileList.innerHTML = '';
            if (selectedFiles.length > 0) {
                uploadSection.classList.add('has-files');

                selectedFiles.forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item';
                    fileItem.innerHTML = `
                        <span class="file-name">${file.name} <span style="color: #6b7280; font-size: 12px;">(${formatFileSize(file.size)})</span></span>
                        <button type="button" class="file-remove" onclick="removeFile(${index})">Remove</button>
                    `;
                    fileList.appendChild(fileItem);
                });
            } else {
                uploadSection.classList.remove('has-files');
            }
        }

        window.removeFile = function(index) {
            selectedFiles.splice(index, 1);
            updateFileList();
            
            // Update the file input
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;
        };
    }
</script>
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

        @if (session('success'))
            <div style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;padding:12px;border-radius:8px;margin-bottom:12px;">
                {{ session('success') }}
            </div>
        @endif

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
                    <textarea id="description" name="description" rows="5" maxlength="5000" 
                              onkeyup="updateCharCount()" 
                              oninput="updateCharCount()">{{ old('description', $assignment->description ?? '') }}</textarea>
                    <div id="char-count" class="field-hint">
                        <span id="char-count-text">0</span>/5000 characters
                    </div>
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
                    <label for="files">Attachments</label>
                    @error('files')
                        <div style="color: #dc2626; font-size: 13px; margin-bottom: 6px;">{{ $message }}</div>
                    @enderror
                    @error('files.*')
                        <div style="color: #dc2626; font-size: 13px; margin-bottom: 6px;">{{ $message }}</div>
                    @enderror
                    <div class="upload-section" id="uploadSection">
                        <div class="file-input-wrapper">
                            <input type="file" name="files[]" id="fileInput" class="file-input" multiple accept=".pdf,.doc,.docx,.txt">
                            <label for="fileInput" class="file-input-label">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"/>
                                    <path d="M9 13h2v5a1 1 0 11-2 0v-5z"/>
                                </svg>
                                Choose files to upload
                            </label>
                        </div>
                        <div class="field-hint" style="margin-top: 10px;">Upload supporting materials (PDF, DOC, DOCX, TXT). Max 10MB per file.</div>
                        <div class="file-list" id="fileList"></div>
                        @if($mode === 'edit' && $assignment->assignmentFiles && $assignment->assignmentFiles->count() > 0)
                            <div class="current-file">
                                <div class="current-file-label">Current Attachments:</div>
                                @foreach($assignment->assignmentFiles as $file)
                                    <div class="current-file-item">
                                        <span>📎</span>
                                        <span>{{ $file->original_filename }}</span>
                                        <a href="{{ url('/' . $file->file_path) }}" target="_blank">View/Download</a>
                                    </div>
                                @endforeach
                        </div>
                    @endif
                    </div>
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

