/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.0
 * Author: SKYRHRG Technologies Systems
 *
 * Custom JavaScript File
 */

$(document).ready(function() {

    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    /**
     * Reusable SweetAlert2 confirmation dialog for links.
     */
    $(document).on('click', '[data-confirm]', function(e) {
        e.preventDefault();
        const $this = $(this);
        Swal.fire({
            title: $this.data('confirm-title') || 'Are you sure?',
            text: $this.data('confirm-text') || '',
            icon: $this.data('confirm-icon') || 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: $this.data('confirm-button') || 'Yes, proceed!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = $this.attr('href');
            }
        });
    });

    // --- NEW / UPDATED FUNCTIONS ---

    /**
     * UPDATED: Handles AJAX form submission for deposits, now with full SweetAlert feedback.
     */
    window.handleAjaxFormSubmit = function(e) {
        e.preventDefault();
        const form = $(this);
        const submitButton = form.find('button[type="submit"]');
        const originalButtonText = submitButton.html();

        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');

        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            dataType: 'json'
        })
        .done(function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Success!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Optional: reload the page to show updated history
                    location.reload(); 
                });
                form[0].reset();
                $('#qrCodeImage').hide();
                $('#qrPlaceholder').show();
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.message || 'An unknown error occurred.',
                    icon: 'error'
                });
            }
        })
        .fail(function() {
            Swal.fire({
                title: 'Request Failed!',
                text: 'Could not connect to the server. Please check your connection and try again.',
                icon: 'error'
            });
        })
        .always(function() {
            submitButton.prop('disabled', false).html(originalButtonText);
        });
    };

    /**
     * Generates a UPI QR Code and displays it.
     */
    window.generateUpiQrCode = function(upiId, amount, targetElementId) {
        const qrImage = document.getElementById(targetElementId);
        if (qrImage) {
            const upiUrl = `upi://pay?pa=${upiId}&am=${amount}&cu=INR`;
            const qrApiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(upiUrl)}`;
            qrImage.src = qrApiUrl;
            qrImage.style.display = 'block';
        }
    };

    /**
     * Copies text to the user's clipboard and shows a confirmation toast.
     */
    window.copyToClipboard = function(textToCopy) {
        navigator.clipboard.writeText(textToCopy).then(function() {
            showToast('Copied to clipboard!', 'success');
        }, function(err) {
            showToast('Failed to copy!', 'error');
        });
    };

    /**
     * Reusable Toast notification function (for minor alerts).
     */
    function showToast(message, type = 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        Toast.fire({ icon: type, title: message });
    }

});