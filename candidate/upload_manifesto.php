<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Manifesto</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container" id="tab-upload">
    <h1>Upload Manifesto</h1>
    <form id="upload_form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        <div class="form-group">
            <label for="manifesto_file">Upload Manifesto (PDF only):</label>
            <input type="file" name="manifesto_file" id="manifesto_file" accept=".pdf" required>
        </div>
        <div class="form-group">
            <button class="btn" type="submit" name="upload_manifesto">Upload Manifesto</button>
        </div>
    </form>
    <div id="message"></div>
</div>

<script>
$(document).ready(function() {
    $('#upload_form').on('submit', function(event) {
        event.preventDefault();
        
        var formData = new FormData(this);
        
        $.ajax({
            url: 'ajax_upload_manifesto.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#message').html(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#message').html('<p>Error: ' + textStatus + '</p>');
            }
        });
    });
});
</script>
</body>
</html>
