<!DOCTYPE html>

<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="File Encryption App">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="author" content="File Encryption App">
    <meta name="keywords" content="encryption, decryption">
    <!-- toastr CSS & SCRIPT -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @stack('file_styles')
</head>

<body>
    @yield('file')
    @stack('file_scripts')
</body>

</html>