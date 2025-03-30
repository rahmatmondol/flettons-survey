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
                <input type="text" name="market_value" placeholder="Market Value (£)" pattern="\d{6,}" title="Please enter at least 6 digits (e.g., 100000)" required>
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
                <label class="switch-label" for="extended">Extended?</label>
                <label class="switch">
                    <input type="checkbox" id="extended" name="extended" value="Yes">
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
                <input type="number" id="sqft_area" name="sqft_area" placeholder="Floor Area (sqft)" min="1651">
            </div>
        </div>

        <!-- Hidden fields -->
        <input type="hidden" name="level" value="2">
        <input type="hidden" name="total" id="total" value="0">
        <input type="hidden" name="action" value="process_quote_form">
        <input type="hidden" name="quote_form_nonce" value="<?php echo wp_create_nonce('flettons_quote_form_nonce'); ?>">

        <div class="buttons">
            <button type="submit" id="proceedBtn" style="padding: 15px 40px;background: #90be13;color: #fff;border: none;border-radius: 10px;">GET INSTANT QUOTE</button>
        </div>
    </form>

    <div class="footer">
        Powered by Flettons Group
    </div>
</div>

<script type="text/javascript">
    function initializeAutocomplete() {
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
    window.onload = initializeAutocomplete;
</script>
