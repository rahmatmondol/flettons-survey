<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="form-container">
    <h2>RICS Survey Quotes</h2>

    <!-- Message container for success/error messages -->
    <div id="quote-message" style="display:none; margin-bottom:15px; padding:10px;"></div>

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
                        <option value="">House or Flat</option>
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
                        <option value="">Number of Bedrooms</option>
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
                <input type="number" id="market_value" name="market_value" placeholder="Market Value (£)" pattern="\d{1,6}" title="Please enter between 1 and 6 digits (e.g., 100000)" required>
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
            <!-- <div class="switch-option">
                <label class="switch-label" for="extended">Extended?</label>
                <label class="switch">
                    <input type="checkbox" id="extended" name="extended" value="Yes">
                    <span class="slider"></span>
                </label>
            </div> -->
            <div class="switch-option">
                <label class="switch-label" for="over1650">Over 1650 sqft?</label>
                <label class="switch">
                    <input type="checkbox" id="over1650" name="over1650" onchange="toggleSqftAreaBox()">
                    <span class="slider"></span>
                </label>
            </div>
            <div id="sqftPriceBox" style="display: none; margin-top: 10px;">
                <input type="number" id="sqft_area" name="sqft_area" placeholder="Floor Area (sqft)" min="1651">
            </div>
        </div>

        <!-- Hidden fields -->
        <input type="hidden" name="level" value="2">
        <input type="hidden" name="total" id="total" value="0">
        <input type="hidden" name="action" value="process_quote_form">
        <input type="hidden" name="quote_form_nonce" value="<?php echo wp_create_nonce('flettons_quote_form_nonce'); ?>">

        <div class="buttons">
            <button type="submit" style="padding: 15px 40px;background: #90be13;color: #fff;border: none;border-radius: 10px;">GET INSTANT QUOTE</button>
        </div>
    </form>

    <div class="footer">
        Powered by Flettons Group
    </div>
</div>
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

        // Get settings from PHP - these need to be properly passed from your PHP settings
        const listing_fee = <?php echo (int)$this->get_setting('listing-fee') ?: 250; ?>;
        const extra_sqft = <?php echo (float)$this->get_setting('extra-sqft') ?: 0.25; ?>;
        const extra_rooms = <?php echo (int)$this->get_setting('extra-rooms') ?: 50; ?>;
        const base_price = <?php echo (int)$this->get_setting('base-price') ?: 499; ?>;

        // Calculate the quote
        function calculateQuote() {
            let basePrice = base_price; // Base price from settings
            let additionalCost = 0;

            // Add costs for options
            if ($("#listed").is(":checked")) additionalCost += listing_fee;

            // Square footage calculation
            if ($("#over1650").is(":checked")) {
                const sqft = parseFloat($("#sqft_area").val()) || 1651;
                if (sqft > 1650) {
                    additionalCost += Math.round((sqft - 1650) * extra_sqft);
                }
            }

            // Bedroom calculation
            const bedrooms = parseInt($("select[name='number_of_bedrooms']").val()) || 0;
            if (bedrooms > 3) {
                additionalCost += (bedrooms - 3) * extra_rooms;
            }

            const total = basePrice + additionalCost;

            // Store total in hidden field
            $("#total").val(total);

            return {
                base: basePrice,
                additional: additionalCost,
                total: total
            };
        }

        // Form submission handler
        $("#quoteForm").on("submit", function(e) {
            e.preventDefault();

            // Get market value input
            const marketValue = $("input[name='market_value']").val();

            // Check if market value is empty or not valid
            if (!marketValue) {
                $("#quote-message")
                    .show()
                    .html('<div style="color:#721c24; padding:10px; border-radius:4px;">Please enter a market value.</div>');
                return;
            }

            $(this).find("button[type='submit']").prop("disabled", true).text("Processing...");

            // Show loading message
            $("#quote-message")
                .show()
                .html('<div style="padding:10px; text-align:center;">Processing your request...</div>');

            // Calculate quote
            const quote = calculateQuote();

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
                    quote: quote
                },
                success: function(response) {
                    console.log("AJAX response:", response);
                    if (response.success) {
                        // Show success message
                        $("#quote-message")
                            .html('<div style="color:#155724; padding:10px; border-radius:4px;">Your quote has been submitted successfully!</div>');

                        // Redirect to listing page with quote data in URL (if needed)
                        const redirectUrl = '<?php echo esc_js(site_url('/flettons-listing-page/')); ?>' + '?' + buildQueryString({
                            first_name: $("input[name='first_name']").val(),
                            last_name: $("input[name='last_name']").val(),
                            email: $("input[name='email_address']").val(),
                            phone: $("input[name='telephone_number']").val(),
                            address: $("input[name='full_address']").val(),
                            bedrooms: $("select[name='number_of_bedrooms']").val(),
                            property_type: $("select[name='house_or_flat']").val(),
                            market_value: $("input[name='market_value']").val(),
                            total: $("#total").val(),
                            quote_id: response.data ? response.data.quote_id : ''
                        });

                        // console.log("Redirect URL:", redirectUrl);
                        // Uncomment to enable redirect
                        window.location.href = redirectUrl;
                    } else {
                        // Show error message
                        $("#quote-message")
                            .html('<div style="color:#721c24; padding:10px; border-radius:4px;">' +
                                (response.data && response.data.message ? response.data.message : "An error occurred.") + '</div>');

                        // Re-enable button
                        $("#quoteForm").find("button[type='submit']").prop("disabled", false).text("GET INSTANT QUOTE");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", error);
                    // Show error message
                    $("#quote-message")
                        .html('<div style="color:#721c24; padding:10px; border-radius:4px;">' +
                            "There was an error processing your request. Please try again." + '</div>');

                    // Re-enable button
                    $("#quoteForm").find("button[type='submit']").prop("disabled", false).text("GET INSTANT QUOTE");
                }
            });
        });

        // Helper function to build query string
        function buildQueryString(params) {
            return Object.keys(params).map(key =>
                encodeURIComponent(key) + '=' + encodeURIComponent(params[key])
            ).join('&');
        }

        // Add responsive handling for telephone inputs
        if ($(window).width() < 768) {
            $(".telephone-field").css("flex-direction", "column");
        }

        $(window).resize(function() {
            if ($(window).width() < 768) {
                $(".telephone-field").css("flex-direction", "column");
            } else {
                $(".telephone-field").css("flex-direction", "row");
            }
        });
    });
</script>