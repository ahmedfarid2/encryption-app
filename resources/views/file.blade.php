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
    <form id="upload-form" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Upload</button>
    </form>

    <div class="file-details" style="display:none;">
        <p><strong>File Name:</strong> <span id="file-name"></span></p>
        <p><strong>File Size:</strong> <span id="file-size"></span> bytes</p>
        <p><strong>File Extension:</strong> <span id="file-extension"></span></p>

        <form id="encrypt-form" method="post" style="display:inline;">
            @csrf
            <input type="hidden" name="filePath" id="file-path">
            <input type="text" name="outputFileName" placeholder="Enter output file name" required>
            <button type="submit">Encrypt</button>
        </form>

        <form id="decrypt-form" method="post" style="display:inline;">
            @csrf
            <input type="hidden" name="filePath" id="encrypted-file-path">
            <input type="text" name="outputFileName" placeholder="Enter output file name" required>
            <button type="submit">Decrypt</button>
        </form>

        <form id="validate-form" method="post" style="display:inline;">
            @csrf
            <input type="hidden" name="originalFilePath" id="original-file-path">
            <button type="submit">Validate</button>
        </form>
    </div>
</div>
@endsection

@push('file_scripts')
    <script type="text/javascript" src="/assets/js/file.js"></script>
@endpush