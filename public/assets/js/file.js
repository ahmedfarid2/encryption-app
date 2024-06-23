 $(document).ready(function () {
            $('#upload-form').on('submit', function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: '/upload',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        toastr.success('File uploaded successfully.');
                        $('.file-details').show();
                        $('#file-name').text(response.fileName);
                        $('#file-size').text(response.fileSize);
                        $('#file-extension').text(response.fileExtension);
                        $('#file-path').val(response.filePath);
                        $('#original-file-path').val(response.filePath);

                        $('#upload-form input[type="file"]').val('');
                        $('#encrypt-form input[type="text"]').val('');
                        $('#decrypt-form input[type="text"]').val('');
                        $('#validate-form input[type="text"]').val('');
                    },
                    error: function (response) {
                        toastr.error('File upload failed.');
                    }
                });
            });

            $('#encrypt-form').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: '/encrypt',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        toastr.success('File encrypted successfully.');
                        $('#encrypted-file-path').val(response.filePath);
                        downloadFile(response.filePath, response.fileName);
                        $('#encrypt-form input[type="text"]').val('');
                    },
                    error: function (response) {
                        toastr.error('File encryption failed.');
                    }
                });
            });

            $('#decrypt-form').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: '/decrypt',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        toastr.success('File decrypted successfully.');
                        downloadFile(response.filePath, response.fileName);
                        $('#decrypt-form input[type="text"]').val('');
                    },
                    error: function (response) {
                        toastr.error('File decryption failed.');
                    }
                });
            });

            $('#validate-form').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: '/validate',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                        $('#validate-form input[type="text"]').val('');
                    },
                    error: function (response) {
                        toastr.error('Validation failed.');
                    }
                });
            });

            function downloadFile(filePath, fileName) {
                const link = document.createElement('a');
                link.href = '/download?filePath=' + encodeURIComponent(filePath);
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        });