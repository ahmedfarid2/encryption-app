@extends('layouts.master')

@section('title')
File Encryption App
@endsection

@push('file_styles')
<link rel="stylesheet" href="/assets/css/file.css">
@endpush

@section('file')
<div class="container">
    <h1>File Encryption App</h1>
    <form id="upload-form" method="post" enctype="multipart/form-data" style="display:grid;">
        @csrf
        <input type="file" name="file" required>
        <button type="submit" id="upload-button">Upload</button>
    </form>

    <div id="progress-bar-container">
        <div id="progress-bar"></div>
        <div id="progress-percentage">0%</div>
    </div>

    <div class="file-details" style="display:none;">
        <p><strong>File Name:</strong> <span id="file-name"></span></p>
        <p><strong>File Size:</strong> <span id="file-size"></span></p>
        <p><strong>File Extension:</strong> <span id="file-extension"></span></p>

         <form id="encrypt-form" method="post" style="display:grid; display:none;">
            @csrf
            <input type="hidden" name="filePath" id="file-path-enc">
            <label>Encrypt</label>
            <button type="submit" id="encrypt-button">Encrypt</button>
        </form>

         <div id="encrypt-progress-bar-container" style="display:none;">
            <div id="encrypt-progress-bar"></div>
            <div id="encrypt-progress-percentage">0%</div>
        </div>

        <form id="decrypt-form" method="post" style="display:grid; display:none;">
            @csrf
            <input type="hidden" name="filePath" id="file-path-dec">
            <label>Decrypt</label>
            <button type="submit" id="decrypt-button">Decrypt</button>
        </form>

        <div id="decrypt-progress-bar-container" style="display:none;">
            <div id="decrypt-progress-bar"></div>
        <div id="decrypt-progress-percentage">0%</div>
    </div>
</div>
@endsection

@push('file_scripts')
<script type="text/javascript">
    window.CHUNK_SIZE = @json(config('constants.CHUNK_SIZE'));
</script>
<script type="text/javascript" src="/assets/js/file.js"></script>
@endpush
