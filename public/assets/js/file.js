$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const CHUNK_SIZE = window.CHUNK_SIZE;
    let originalFileName = "";

    $('#upload-form input[type="file"]').on("change", function () {
        originalFileName = this.files[0].name;
    });

    function uploadChunk(file, start, end, totalChunks, fileId, chunkIndex) {
        const chunk = file.slice(start, end);
        const formData = new FormData();
        formData.append("chunk", chunk);
        formData.append("fileId", fileId);
        formData.append("chunkIndex", chunkIndex);

        return $.ajax({
            url: "/upload-chunk",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            xhr: function () {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener(
                    "progress",
                    function (evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = Math.min(
                                (chunkIndex / totalChunks) * 100,
                                100
                            ).toFixed(2);
                            $("#progress-bar").css(
                                "width",
                                percentComplete + "%"
                            );
                            $("#progress-percentage").text(
                                percentComplete + "%"
                            );
                        }
                    },
                    false
                );
                return xhr;
            },
        });
    }

    function uploadFileInChunks(file) {
        const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
        const fileId = Date.now();

        let start = 0;
        let end = CHUNK_SIZE;

        $("#progress-bar-container").show();
        $("#upload-button").prop("disabled", true);
        $("#encrypt-button").prop("disabled", true);
        $("#decrypt-button").prop("disabled", true);    
        function uploadNextChunk(chunkIndex) {
            if (start < file.size) {
                uploadChunk(file, start, end, totalChunks, fileId, chunkIndex)
                    .done(function () {
                        start = end;
                        end = start + CHUNK_SIZE;
                        uploadNextChunk(chunkIndex + 1);
                    })
                    .fail(function () {
                        toastr.error("File upload failed.");
                        $("#progress-bar-container").hide();
                        $("#upload-button").prop("disabled", false);
                        $("#encrypt-button").prop("disabled", false);
                        $("#decrypt-button").prop("disabled", false);    
                    });
            } else {
                finalizeUpload(fileId, originalFileName);
            }
        }

        uploadNextChunk(0);
    }

    function finalizeUpload(fileId, originalFileName) {
        $.ajax({
            url: "/finalize-upload",
            type: "POST",
            data: {
                fileId: fileId,
                originalFileName: originalFileName,
            },
            success: function (response) {
                toastr.success("File uploaded successfully.");
                $(".file-details").show();
                $("#file-name").text(response.fileName);
                $("#file-size").text(response.fileSize);
                $("#file-extension").text(response.fileExtension);

                if (response.fileType === "enc") {
                    $("#file-path-dec").val(response.filePath);
                    $("#encrypt-form").hide();
                    $("#decrypt-form").show();
                } else {
                    $("#file-path-enc").val(response.filePath);
                    $("#encrypt-form").show();
                    $("#decrypt-form").hide();
                }

                $('#upload-form input[type="file"]').val("");
                $("#progress-bar-container").hide();
                $("#upload-button").prop("disabled", false);
                $("#encrypt-button").prop("disabled", false);
                $("#decrypt-button").prop("disabled", false);    
            },
            error: function () {
                toastr.error("File upload failed.");
                $("#progress-bar-container").hide();
                $("#upload-button").prop("disabled", false);
                $("#encrypt-button").prop("disabled", false);
                $("#decrypt-button").prop("disabled", false);   
            },
        });
    }

    $("#upload-form").on("submit", function (e) {
        e.preventDefault();
        const file = this.file.files[0];
        if (file) {
            uploadFileInChunks(file);
        }
    });
});

$(document).ready(function () {
    function trackProgress(evt, progressBarId, progressPercentageId) {
        if (evt.lengthComputable) {
            const percentComplete = ((evt.loaded / evt.total) * 100).toFixed(2);
            $(progressBarId).css("width", percentComplete + "%");
            $(progressPercentageId).text(percentComplete + "%");
        }
    }

    function encryptFile() {
        return $.ajax({
            url: "/encrypt",
            type: "POST",
            data: $("#encrypt-form").serialize(), 
            xhr: function () {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener(
                    "progress",
                    function (evt) {
                        trackProgress(
                            evt,
                            "#encrypt-progress-bar",
                            "#encrypt-progress-percentage"
                        );
                    },
                    false
                );
                return xhr;
            },
            success: function (response) {
                toastr.success("File encrypted successfully.");
                $("#encrypted-file-path").val(response.filePath);
                downloadFile(response.filePath, response.fileName);
                $('#encrypt-form input[type="text"]').val("");
                $("#encrypt-progress-bar-container").hide();
                $("#encrypt-button").prop("disabled", false);
                $("#upload-button").prop("disabled", false);
            },
            error: function () {
                toastr.error("File encryption failed.");
                $("#encrypt-progress-bar-container").hide();
                $("#encrypt-button").prop("disabled", false);
                $("#upload-button").prop("disabled", false);
            },
        });
    }

    function decryptFile() {
        return $.ajax({
            url: "/decrypt",
            type: "POST",
            data: $("#decrypt-form").serialize(), 
            xhr: function () {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener(
                    "progress",
                    function (evt) {
                        trackProgress(
                            evt,
                            "#decrypt-progress-bar",
                            "#decrypt-progress-percentage"
                        );
                    },
                    false
                );
                return xhr;
            },
            success: function (response) {
                toastr.success("File decrypted successfully.");
                $("#decrypted-file-path").val(response.filePath);
                downloadFile(response.filePath, response.fileName);
                $('#decrypt-form input[type="text"]').val("");
                $("#decrypt-progress-bar-container").hide();
                $("#decrypt-button").prop("disabled", false);
                $("#upload-button").prop("disabled", false);
            },
            error: function () {
                toastr.error("File decryption failed.");
                $("#decrypt-progress-bar-container").hide();
                $("#decrypt-button").prop("disabled", false);
                $("#upload-button").prop("disabled", false);
            },
        });
    }

    $("#encrypt-form").on("submit", function (e) {
        e.preventDefault();
        $("#encrypt-progress-bar-container").show();
        $("#encrypt-button").prop("disabled", true);
        $("#upload-button").prop("disabled", true);
        encryptFile();
    });

    $("#decrypt-form").on("submit", function (e) {
        e.preventDefault();
        $("#decrypt-progress-bar-container").show();
        $("#decrypt-button").prop("disabled", true);
        $("#upload-button").prop("disabled", true);
        decryptFile();
    });

    function downloadFile(filePath, fileName) {
        const link = document.createElement("a");
        link.href = "/download?filePath=" + encodeURIComponent(filePath);
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
});
