<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="form-container">
    <h2 class="form-title">RICS Survey Quote</h2>

    <form id="quoteForm">
        <div class="form-grid">
            <div class="form-row">
                <div>
                    <input type="text" name="first_name" placeholder="First Name" required>
                </div>
                <div>
                    <input type="text" name="last_name" placeholder="Last Name" required>
                </div>
            </div>
            <div class="form-row">
                <div>
                    <input type="email" name="email_address" placeholder="Email Address" required>
                </div>
                <div>
                    <div class="telephone-field">
                        <input type="hidden" name="countryCode" value="+44">
                        <input type="tel" name="telephone_number" placeholder="Telephone Number" required>
                    </div>
                </div>
            </div>
            <div>
                <input type="text" id="full_address" name="full_address" placeholder="Property Address" required>
            </div>
            <div class="form-row">
                <div>
                    <select name="house_or_flat" required>
                        <option value="">Property Type</option>
                        <option>House</option>
                        <option>Flat</option>
                        <option>Maisonette</option>
                        <option>Barn Conversion</option>
                        <option>Warehouse Conversion</option>
                        <option>Other</option>
                    </select>
                </div>
                <div>
                    <select name="number_of_bedrooms" required>
                        <option value="">Bedrooms</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                </div>
            </div>
            <div>
                <input type="number" id="market_value" name="market_value" min="100000" step="1" max="99999999" placeholder="Market Value (£)" pattern="\d{5}" title="Please enter a 5 digit number (e.g., 12345)" required>
            </div>
        </div>

        <div class="switch-group">
            <div class="switch-option">
                <label class="switch-label" for="listed">Listed Building?</label>
                <label class="switch">
                    <input type="checkbox" id="listed" name="listed_building" value="Yes">
                    <span class="slider"></span>
                </label>
            </div>

            <div class="switch-option">
                <label class="switch-label" for="over1650">Over 1650 sqft?</label>
                <label class="switch">
                    <input type="checkbox" id="over1650" name="over1650" onchange="toggleSqftAreaBox()">
                    <span class="slider"></span>
                </label>
            </div>
            <div id="sqftPriceBox" style="display: none; margin-top: 10px;">
                <input type="number" id="sqft_area" name="sqft_area" placeholder="Floor Area (sqft)" min="1651" step="1">
            </div>
        </div>

        <!-- Hidden fields -->
        <input type="hidden" name="action" value="process_quote_form">
        <input type="hidden" name="quote_form_nonce" value="<?php echo wp_create_nonce('flettons_quote_form_nonce'); ?>">

        <div class="buttons">
            <button type="submit" id="proceedBtn" style="padding: 15px 40px;background: #90be13;color: #fff;border: none;border-radius: 10px;">GET INSTANT QUOTE</button>
            <div style="display: none; margin: 15px auto; animation: rotate 2s linear infinite;" id="loading">
                <svg width="50" height="50" viewBox="0 0 50 50">
                    <circle cx="25" cy="25" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" stroke="#90be13" />
                </svg>
            </div>
        </div>
    </form>

    <div class="footer">
        <div class="footer-text">
            <input type="checkbox" id="agree_terms" name="agree_terms" required>
            <label for="agree_terms">Your details are never shared with third parties. 
                Powered by <b>Flettons Group LLC</b> </label>
        </div>
    </div>
</div>

<style>
    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>


<script type="text/javascript">
    function initializeAutocomplete() {
        // Check if Google Maps API is loaded
        if (typeof google === 'undefined' || typeof google.maps === 'undefined' ||
            typeof google.maps.places === 'undefined') {
            console.error('Google Maps API not loaded');
            return;
        }

        // Map address fields to their corresponding postcode fields
        const addressFields = [{
                address: document.getElementById('full_address'),
            }, // Your Current Address
        ];
        // Apply autocomplete with UK restriction and postcode extraction for each address field
        addressFields.forEach(fieldPair => {
            if (fieldPair.address) {
                const autocomplete = new
                google.maps.places.Autocomplete(fieldPair.address, {
                    types: ['address'],
                    componentRestrictions: {
                        country: 'uk'
                    } // Restrict to UK with correct lowercase code
                });
                // Set the fields to get address components
                autocomplete.setFields(['formatted_address']);
                // Update the input fields with the formatted address and postcode
                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (place.formatted_address) {
                        // Remove "United Kingdom" from the formatted address (fixed regex)
                        let formattedAddress = place.formatted_address.replace(/,\s*United Kingdom$/, '').replace(/,\s*UK$/, '');
                        fieldPair.address.value = formattedAddress;
                    }
                });
            }
        });
    }

    // Initialize autocomplete on page load
    window.onload = function() {
        // Check if Google Maps API is loaded
        if (typeof google !== 'undefined') {
            initializeAutocomplete();
        }
    };

    jQuery(document).ready(function($) {
        // Toggle sqft area box
        window.toggleSqftAreaBox = function() {
            $("#sqftPriceBox").toggle($("#over1650").is(":checked"));
        };

        // Form submission handler
        $("#quoteForm").on("submit", function(e) {
            e.preventDefault();

            // check if agree_terms is checked
            if (!$("#agree_terms").is(":checked")) {
                alert("Please agree to the terms and conditions.");
                return;
            }

            $(this).find("button[type='submit']").hide();
            $("#loading").show();

            // Show loading message
            // $("#quote-message")
            //     .show()
            //     .html('<div style="padding:10px; text-align:center;">Processing your request...</div>');

            // Get form data
            const formData = $(this).serialize();

            // Validate form data
            if (!formData) {
                $("#quote-message")
                    .html('<div style="color:#721c24; padding:10px; border-radius:4px;">Please fill in all required fields.</div>');
                $(this).find("button[type='submit']").prop("disabled", false).text("GET INSTANT QUOTE");
                return;
            }

            // Send AJAX request to store quote data
            $.ajax({
                type: "POST",
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    action: 'store_temp_quote_data',
                    formData: formData,
                },
                success: function(response) {
                    console.log("AJAX response:", response);
                    if (response.success) {
                        // Show success message

                        // Redirect to listing page with quote data in URL (if needed)
                        const redirectUrl = new URL('<?php echo esc_js(site_url('/flettons-listing-page/')); ?>');
                        if (response.data.contact_id) {
                            redirectUrl.searchParams.set('contact_id', response.data.contact_id);
                        }

                        // Uncomment to enable redirect
                        window.location.href = redirectUrl.toString();
                    } else {
                        // Show error message
                        // $("#quote-message")
                        //     .html('<div style="color:#721c24; padding:10px; border-radius:4px;">' +
                        //         (response.data && response.data.message ? response.data.message : "An error occurred.") + '</div>');

                        // Re-enable button
                        $("#quoteForm").find("button[type='submit']").prop("disabled", false).text("GET INSTANT QUOTE");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", error);
                    // Show error message
                    // $("#quote-message")
                    //     .html('<div style="color:#721c24; padding:10px; border-radius:4px;">' +
                    //         "There was an error processing your request. Please try again." + '</div>');

                    // Re-enable button
                    $("#quoteForm").find("button[type='submit']").prop("disabled", false).text("GET INSTANT QUOTE");
                }
            });
        });


    });
</script>