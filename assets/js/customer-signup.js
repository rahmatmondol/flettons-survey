/**
 * Customer Sign-up Form JavaScript
 */
jQuery(document).ready(function ($) {
    // Extract URL parameters to pre-fill form fields
    const urlParams = new URLSearchParams(window.location.search);

    // Pre-fill form fields if not already filled
    if (!$('#first_name').val()) {
        $('#first_name').val(urlParams.get('first_name') || '');
    }

    if (!$('#last_name').val()) {
        $('#last_name').val(urlParams.get('last_name') || '');
    }

    if (!$('#email').val()) {
        $('#email').val(urlParams.get('email') || '');
    }

    if (!$('#phone').val()) {
        $('#phone').val(urlParams.get('phone') || '');
    }

    if (!$('#current_address').val()) {
        $('#current_address').val(urlParams.get('address') || '');
    }

    // Get the survey address from URL parameters
    const survey_address = urlParams.get('address') || '';
    if (survey_address && !$('#survey_address').val()) {
        $('#survey_address').val(survey_address);
    }

    // Store quote data in hidden fields
    $('#quote_id').val(urlParams.get('quote_id') || '');
    $('#survey_level').val(urlParams.get('level') || '2');
    $('#survey_total').val(urlParams.get('total') || '0');

    // Optional: Pre-select property type based on URL parameter
    const property_type = urlParams.get('property_type');
    if (property_type && $('#property_type').length) {
        $('#property_type').val(property_type);
    }

    // Optional: Pre-select bedrooms based on URL parameter
    const bedrooms = urlParams.get('bedrooms');
    if (bedrooms && $('#bedrooms').length) {
        $('#bedrooms').val(bedrooms);
    }

    // Handle form submission
    $('#customer_signup_form').on('submit', function (e) {
        e.preventDefault();

        // Validate the form
        if (!this.checkValidity()) {
            this.reportValidity();
            return false;
        }

        // Check terms agreement
        if ($('#termsCheckbox').length && !$('#termsCheckbox').is(':checked')) {
            alert('Please confirm that you have read and understand the terms.');
            return false;
        }

        // Show loading state
        $('#submit_button').prop('disabled', true).text('Processing...');

        // Collect form data
        const formData = $(this).serialize();

        // Send AJAX request to save customer data to CRM
        $.ajax({
            type: 'POST',
            url: flettons_ajax.ajax_url,
            data: {
                action: 'save_customer_to_crm',
                form_data: formData,
                nonce: flettons_ajax.nonce
            },
            success: function (response) {

                console.log(response);

                if (response.success) {
                    // If successful, redirect to Stripe checkout
                    if (response.data && response.data.redirect_url) {
                        window.location.href = response.data.redirect_url;
                    } else {
                        // Fallback message if no redirect URL
                        alert('Your information has been saved. You will now be redirected to payment.');
                        window.location.reload();
                    }
                } else {
                    // Show error message
                    alert('Error: ' + (response.data ? response.data.message : 'An unknown error occurred.'));
                    $('#submit_button').prop('disabled', false).text('Confirm and Pay');
                }
            },
            error: function () {
                // Handle connection error
                alert('Error communicating with server. Please try again.');
                $('#submit_button').prop('disabled', false).text('Confirm and Pay');
            }
        });

        return false;
    });

    // Initialize Google Places Autocomplete if available
    function initializeAutocomplete() {
        // Map fields that should have autocomplete
        const addressFields = [
            document.getElementById('current_address'),
            document.getElementById('survey_address')
        ];

        // Only proceed if Google Places API is loaded
        if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
            console.log('Google Places API not loaded');
            return;
        }

        // Apply autocomplete to each address field
        addressFields.forEach(function (field) {
            if (field) {
                const autocomplete = new google.maps.places.Autocomplete(field, {
                    types: ['address'],
                    componentRestrictions: { country: 'gb' } // Restrict to UK
                });

                // Set the fields to get address components
                autocomplete.setFields(['formatted_address', 'address_components']);

                // Update postcode field when address is selected
                autocomplete.addListener('place_changed', function () {
                    const place = autocomplete.getPlace();
                    if (place.formatted_address) {
                        // Update address field with formatted address
                        field.value = place.formatted_address.replace(/,\s*United Kingdom$/, '').replace(/,\s*UK$/, '');

                        // Find related postcode field
                        let postcodeField = null;

                        if (field.id === 'current_address') {
                            postcodeField = document.getElementById('current_postcode');
                        } else if (field.id === 'survey_address') {
                            postcodeField = document.getElementById('survey_postcode');
                        }

                        // Extract postcode and update field
                        if (postcodeField) {
                            for (let i = 0; i < place.address_components.length; i++) {
                                for (let j = 0; j < place.address_components[i].types.length; j++) {
                                    if (place.address_components[i].types[j] === 'postal_code') {
                                        postcodeField.value = place.address_components[i].long_name;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    }

    // Initialize Google Places autocomplete
    if (typeof google !== 'undefined') {
        google.maps.event.addDomListener(window, 'load', initializeAutocomplete);
    } else {
        console.log('Google Maps API not loaded');
    }
});