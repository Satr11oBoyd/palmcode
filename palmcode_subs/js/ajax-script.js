jQuery(document).ready(function ($) {

    // Handle form submission
    $('#contact-form').on('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData(this); // Gather form data
        formData.append('action', 'handle_form_submission'); // Append action for AJAX handler
        formData.append('nonce', ajax_object.nonce); // Add nonce for security

        $.ajax({
            url: ajax_object.ajax_url, // Use the correct AJAX URL
            method: 'POST',
            data: formData,
            contentType: false,  // Let FormData handle content type
            processData: false,  // Let FormData handle data processing
            success: function (response) {
                if (response.success) {
                    // Display success message
                    $('#response').html('<div class="alert alert-success">' + response.data + '</div>');
                    $('#contact-form')[0].reset(); // Reset the form after successful submission
                } else {
                    // Display error message
                    $('#response').html('<div class="alert alert-danger">' + response.data + '</div>');
                }
            },
            error: function (xhr, status, error) {
                // Display generic error message
                $('#response').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                console.error('AJAX Error:', xhr.responseText, status, error);
            }
        });
    });

    // Handle image preview functionality
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#imageResult')  // Assuming #imageResult is the element to display image preview
                    .attr('src', e.target.result); // Set the image preview
            };

            reader.readAsDataURL(input.files[0]); // Read the file as a data URL
        }
    }

    // Bind the change event for image input field
    $('#upload-image').on('change', function () {
        readURL(this); // Pass the input element to the readURL function
    });

    // Script to show the file name in the UI
    var input = document.getElementById('upload-image');
    var infoArea = document.getElementById('image-label'); // Assuming this is where the file name should appear

    input.addEventListener('change', showFileName);

    // Function to show the selected file name
    function showFileName(event) {
        var fileName = event.target.files[0].name; // Get the file name
        infoArea.innerHTML = `<div class="text-center text-black"> File name: ${fileName} <p class="afterUpload">Click here again if you want to change the file</p></div>`;
    }

});
