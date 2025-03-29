<script>
    function toggleGarageLocation() {
        const garage = document.getElementById('inf_custom_Garage');
        const field = document.getElementById('garageLocationField');
        if (garage && field) field.style.display = garage.checked ? 'block' : 'none';
    }

    function toggleSolicitorFields() {
        const exchange = document.getElementById('inf_custom_ExchangeDate');
        const solicitorFields = document.getElementById('solicitorFields');
        if (exchange && solicitorFields) solicitorFields.style.display =
            exchange.value ? 'block' : 'none';
    }

    function toggleExchangeDate() {
        const exchangeToggle =
            document.getElementById('inf_custom_KnowExchangeDate');
        const exchangeDateField =
            document.getElementById('inf_custom_ExchangeDate').parentElement;
        if (exchangeToggle && exchangeDateField) {
            exchangeDateField.style.display = exchangeToggle.checked ? 'block' :
                'none';
        }
    }

    function handleTermsScroll() {
        const iframe = document.getElementById('termsIframe');
        const checkbox = document.getElementById('termsCheckboxInput');
        if (iframe && checkbox) {
            iframe.contentWindow.onscroll = function() {
                const scrollHeight =
                    iframe.contentDocument.documentElement.scrollHeight;
                const scrollTop = iframe.contentWindow.scrollY;
                const clientHeight =
                    iframe.contentDocument.documentElement.clientHeight;
                if (scrollTop + clientHeight >= scrollHeight - 10) {
                    checkbox.disabled = false;
                }
            };
        }
    }
</script>


<form accept-charset="UTF-8" action="https://yh388.infusionsoft.com/app/form/process/59721e1c56b9dcc11498a070f37a2b12" class="infusion-form"
    id="inf_form_59721e1c56b9dcc11498a070f37a2b12" method="POST">
    <input name="inf_form_xid" type="hidden"
        value="59721e1c56b9dcc11498a070f37a2b12" />
    <input name="inf_form_name" type="hidden" value="CUSTOMER SIGN UP FORM 2" />
    <input name="infusionsoft_version" type="hidden" value="1.70.0.789938" />
    <!--<h2>Property Survey Booking</h2>-->
    <!--<h2>Client Details</h2>-->
    <div class="infusion-field">
        <label for="inf_field_Title">Title</label>
        <select id="inf_field_Title" name="inf_field_Title">
            <option value="">Please select one</option>
            <option value="Mr.">Mr.</option>
            <option value="Mrs.">Mrs.</option>
            <option value="Ms.">Ms.</option>
            <option value="Dr.">Dr.</option>
        </select>
    </div>
    <div class="infusion-field">
        <label for="inf_field_FirstName">First Name *</label>
        <input type="text" id="inf_field_FirstName" name="inf_field_FirstName"
            required value="<?php echo esc_attr($first_name); ?>" />
    </div>
    <div class="infusion-field">
        <label for="inf_field_LastName">Last Name *</label>
        <input type="text" id="inf_field_LastName" name="inf_field_LastName"
            required value="<?php echo esc_attr($last_name); ?>" />
    </div>
    <div class="infusion-field">
        <label for="inf_field_Email">Email *</label>
        <input type="email" id="inf_field_Email" name="inf_field_Email" required value="<?php echo esc_attr($email); ?>" />
    </div>
    <div class="infusion-field">
        <label for="inf_field_Phone1">Phone</label>
        <input type="tel" id="inf_field_Phone1" name="inf_field_Phone1" value="<?php echo esc_attr($phone); ?>" />
    </div>
    <div class="infusion-field">
        <label for="inf_field_StreetAddress1">Your Home Address *</label>
        <input type="text" id="inf_field_StreetAddress1"
            name="inf_field_StreetAddress1" required value="<?php echo esc_attr($address); ?>" />
    </div>
    <div class="infusion-field">
        <label for="inf_field_PostalCode">Your Postcode *</label>
        <input type="text" id="inf_field_PostalCode" name="inf_field_PostalCode"
            required />
    </div>
    <h2>Survey Property Details</h2>
    <div class="infusion-field">
        <label for="inf_field_Address2Street1">Survey Street Address *</label>
        <input type="text" id="inf_field_Address2Street1"
            name="inf_field_Address2Street1" required />
    </div>
    <div class="infusion-field">
        <label for="inf_field_PostalCode2">Survey Postal Code *</label>
        <input type="text" id="inf_field_PostalCode2"
            name="inf_field_PostalCode2" required />
    </div>
    <div class="infusion-field">
        <label for="inf_custom_PropertyType">Property Type *</label>
        <select id="inf_custom_PropertyType" name="inf_custom_PropertyType"
            required>
            <option value="">Please select one</option>
            <option <?php if ($property_type === 'Detached') echo 'selected'; ?> value="Detached">Detached</option>
            <option <?php if ($property_type === 'Semi Detached') echo 'selected'; ?> value="Semi Detached">Semi Detached</option>
            <option <?php if ($property_type === 'Terraced') echo 'selected'; ?> value="Terraced">Terraced</option>
            <option <?php if ($property_type === 'Flat') echo 'selected'; ?> value="Flat">Flat</option>
        </select>
    </div>
    <div class="infusion-field">
        <label for="inf_custom_NumberofBedrooms">Number of Bedrooms *</
                label>
            <select id="inf_custom_NumberofBedrooms"
                name="inf_custom_NumberofBedrooms" required>
                <option value="">Please select one</option>
                <option <?php if ($bedrooms === '1') echo 'selected'; ?> value="1">1</option>
                <option  <?php if ($bedrooms === '2') echo 'selected'; ?> value="2">2</option>
                <option <?php if ($bedrooms === '3') echo 'selected'; ?> value="3">3</option>
                <option <?php if ($bedrooms === '4') echo 'selected'; ?> value="4">4</option>
                <option <?php if ($bedrooms === '5') echo 'selected'; ?> value="5">5</option>
            </select>
    </div>
    <div class="infusion-field">
        <label for="inf_custom_PropertyLink">Rightmove/Zoopla/Agents' Link *</
                label>
            <input type="url" id="inf_custom_PropertyLink"
                name="inf_custom_PropertyLink" required />
    </div>
    <div class="infusion-field">
        <label for="inf_custom_SpecificConcerns">Tell us about your specific
            concerns *</label>
        <textarea id="inf_custom_SpecificConcerns"
            name="inf_custom_SpecificConcerns" rows="5" required></textarea>
    </div>
    <style>
        /* Flexbox container for the row */
        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            /* Space between items */
            margin-bottom: 20px;
        }

        /* Style for each field container */
        .row .infusion-field {
            flex: 1;
            /* Allow fields to grow and fill the row */
            min-width: 150px;
            /* Minimum width for each field */
        }

        /* Adjust switch styling */
        .row .switch {
            margin-top: 10px;
            /* Align switches vertically */
        }
    </style>
    <!-- Row for Vacant or Occupied, Any Extensions, and Garage -->
    <div class="row">
        <!-- Vacant or Occupied -->
        <div class="infusion-field">
            <label for="inf_custom_VacantorOccupied">Vacant or Occupied *</label>
            <label class="switch">
                <input type="checkbox" id="inf_custom_VacantorOccupied"
                    name="inf_custom_VacantorOccupied" value="Vacant">
                <span class="slider"></span>
            </label>
        </div>
        <!-- Any Extensions? -->
        <div class="infusion-field">
            <label for="inf_custom_AnyExtensions">Any Extensions? *</label>
            <label class="switch">
                <input type="checkbox" id="inf_custom_AnyExtensions"
                    name="inf_custom_AnyExtensions" value="1">
                <span class="slider"></span>
            </label>
        </div>
        <!-- Garage? -->
        <div class="infusion-field">
            <label for="inf_custom_Garage">Garage? *</label>
            <label class="switch">
                <input type="checkbox" id="inf_custom_Garage"
                    name="inf_custom_Garage" value="1" onchange="toggleGarageLocation()">
                <span class="slider"></span>
            </label>
        </div>
    </div>
    <!-- Garage Location Field (hidden by default) -->
    <div class="infusion-field" id="garageLocationField" style="display:none;">
        <label for="inf_custom_GarageLocation">Garage Location:</label>
        <input type="text" id="inf_custom_GarageLocation"
            name="inf_custom_GarageLocation" />
    </div>
    <div class="infusion-field" id="garageLocationField" style="display:none;">
        <label for="inf_custom_GarageLocation">Garage Location:</label>
        <input type="text" id="inf_custom_GarageLocation"
            name="inf_custom_GarageLocation" />
    </div>
    <h2>Exchange Date & Solicitor Details</h2>
    <div class="infusion-field">
        <label for="inf_custom_KnowExchangeDate">Do you know your exchange
            date?</label>
        <label class="switch">
            <input type="checkbox" id="inf_custom_KnowExchangeDate"
                name="inf_custom_KnowExchangeDate" onchange="toggleExchangeDate()">
            <span class="slider"></span>
        </label>
    </div>
    <div class="infusion-field" id="inf_custom_ExchangeDateField"
        style="display:none;">
        <label for="inf_custom_ExchangeDate">Exchange Date (if known)</label>
        <input type="date" id="inf_custom_ExchangeDate"
            name="inf_custom_ExchangeDate" onchange="toggleSolicitorFields()" />
    </div>
    <div id="solicitorFields" style="display:none;">
        <div class="infusion-field">
            <label for="inf_custom_SolicitorFirm">Solicitor Firm</label>
            <input type="text" id="inf_custom_SolicitorFirm"
                name="inf_custom_SolicitorFirm" />
        </div>
        <div class="infusion-field">
            <label for="inf_custom_SolicitorName">Conveyancer Name</label>
            <input type="text" id="inf_custom_SolicitorName"
                name="inf_custom_SolicitorName" />
        </div>
        <div class="infusion-field">
            <label for="inf_custom_SolicitorPhone">Solicitor Phone</label>
            <input type="text" id="inf_custom_SolicitorPhone"
                name="inf_custom_SolicitorPhone" />
        </div>
        <div class="infusion-field">
            <label for="inf_custom_SolicitorEmail">Solicitor Email</label>
            <input type="email" id="inf_custom_SolicitorEmail"
                name="inf_custom_SolicitorEmail" />
        </div>
    </div>
    <h2>Estate Agent or Vendor Details</h2>
    <div class="infusion-field">
        <label for="inf_custom_AgentCompanyName">Agent Company Name *</
                label>
            <input type="text" id="inf_custom_AgentCompanyName"
                name="inf_custom_AgentCompanyName" required />
    </div>
    <div class="infusion-field">
        <label for="inf_custom_AgentName">Agent Name *</label>
        <input type="text" id="inf_custom_AgentName"
            name="inf_custom_AgentName" required />
    </div>
    <div class="infusion-field">
        <label for="inf_custom_AgentPhoneNumber">Agent Phone Number *</
                label>
            <input type="text" id="inf_custom_AgentPhoneNumber"
                name="inf_custom_AgentPhoneNumber" required />
    </div>
    <div class="infusion-field">
        <label for="inf_custom_AgentsEmail">Agent Email *</label>
        <input type="email" id="inf_custom_AgentsEmail"
            name="inf_custom_AgentsEmail" required />
    </div>
    <div class="infusion-field">
        <label for="inf_field_Address3Street1">Agent Address *</label>
        <input type="text" id="inf_field_Address3Street1"
            name="inf_field_Address3Street1" required />
    </div>
    <div class="infusion-field">
        <label for="inf_field_PostalCode3">Agent Postal Code *</label>
        <input type="text" id="inf_field_PostalCode3"
            name="inf_field_PostalCode3" required />
    </div>
    <!-- Terms and Conditions Iframe -->
    <iframe
        id="termsIframe"
        src="https://flettons.group/wp-content/uploads/2025/03/Flettons-Group-Terms.pdf"
        width="100%"
        height="400"
        style="border: 0.5px solid #1C1B37; margin-top: 40px; margin-bottom: 40px;border-radius: 6px;"></iframe>
    <!-- Terms Checkbox -->
    <div id="termsCheckbox" style="margin-top: 20px;">
        <input type="checkbox" id="termsCheckboxInput"
            name="termsCheckboxInput" required />
        <label for="termsCheckboxInput">I have read and agree to the terms and conditions. *</label>
    </div>
    <!-- Typed Name Field -->
    <div class="infusion-field">
        <label for="typedName">Type Your Name *</label>
        <input
            type="text"
            id="typedName"
            name="typedName"
            placeholder="Please type your full name"
            style="width: 100%; padding: 10px; font-size: 16px; border: 1px solid #1C1B37; border-radius: 4px;"
            required />
    </div>
    <!-- Submit Button -->
    <div class="infusion-submit">
        <button type="submit">Confirm and Pay</button>
    </div>
    <!-- JavaScript to Control the Popup -->
    <script type="text/javascript">
        // Get the modal and link elements
        const termsModal = document.getElementById('termsModal');
        const viewTermsLink = document.getElementById('viewTermsLink');
        const closeTermsModal = document.getElementById('closeTermsModal');
        // Open the modal when the link is clicked
        viewTermsLink.addEventListener('click', (event) => {
            event.preventDefault(); // Prevent the link from navigating
            termsModal.style.display = 'flex'; // Show the modal
        });
        // Close the modal when the close button is clicked
        closeTermsModal.addEventListener('click', () => {
            termsModal.style.display = 'none'; // Hide the modal
        });
        // Close the modal when clicking outside the modal content
        window.addEventListener('click', (event) => {
            if (event.target === termsModal) {
                termsModal.style.display = 'none'; // Hide the modal
            }
        });
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA7xLp13hLBGIDOt4BIJZrJF99ItTsya0g&libraries=places"></script>
    </script>
    <script type="text/javascript">
        function initializeAutocomplete() {
            // Map address fields to their corresponding postcode fields
            const addressFields = [{
                    address: document.getElementById('inf_field_StreetAddress1'),
                    postcode: document.getElementById('inf_field_PostalCode')
                }, // Your Current Address
                {
                    address: document.getElementById('inf_field_Address2Street1'),
                    postcode: document.getElementById('inf_field_PostalCode2')
                }, // Survey Street Address 
                {
                    address: document.getElementById('inf_field_Address3Street1'),
                    postcode: document.getElementById('inf_field_PostalCode3')
                } // Agent Address
            ];
            // Apply autocomplete with UK restriction and postcode extraction for each address field
            addressFields.forEach(fieldPair => {
                if (fieldPair.address) {
                    const autocomplete = new
                    google.maps.places.Autocomplete(fieldPair.address, {
                        types: ['address'],
                        componentRestrictions: {
                            country: 'gb'
                        } // Restrict to UK
                    });
                    // Set the fields to get address components
                    autocomplete.setFields(['formatted_address', 'address_components']);
                    // Update the input fields with the formatted address and postcode
                    autocomplete.addListener('place_changed', function() {
                        const place = autocomplete.getPlace();
                        if (place.formatted_address) {
                            // Remove "United Kingdom" from the formatted address
                            let formattedAddress = place.formatted_address.replace(/,\s*United Kingdom$ /, '').replace(/,\s*UK$/, '');
                            fieldPair.address.value = formattedAddress;
                            // Extract postcode from address components
                            const postcodeComponent =
                                place.address_components.find(component =>
                                    component.types.includes("postal_code"));
                            if (postcodeComponent && fieldPair.postcode) {
                                fieldPair.postcode.value = postcodeComponent.long_name;
                            }
                        }
                    });
                }
            });
        }
        // Initialize autocomplete on page load
        window.onload = initializeAutocomplete;
    </script>
    <script type="text/javascript">
        // Function to format telephone numbers
        function formatTelephoneNumber(input) {
            let value = input.value.trim(); // Get the trimmed value of the input
            // Check if the number starts with a zero
            if (value.startsWith('0')) {
                // Replace the first zero with '+44'
                value = '+44' + value.slice(1);
            }
            // Update the input value
            input.value = value;
        }
        // Attach the formatting function to telephone fields
        function attachTelephoneFormatting() {
            const telephoneFields = [
                document.getElementById('inf_field_Phone1'), // Client Phone
                document.getElementById('inf_custom_SolicitorPhone'), // Solicitor Phone
                document.getElementById('inf_custom_AgentPhoneNumber') // Agent Phone
            ];
            telephoneFields.forEach(field => {
                if (field) {
                    field.addEventListener('blur', () => formatTelephoneNumber(field));
                }
            });
        }
        // Initialize telephone formatting on page load
        window.onload = function() {
            initializeAutocomplete(); // Initialize address autocomplete
            attachTelephoneFormatting(); // Attach telephone formatting
        };
    </script>

    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1C1B37;
            background-color: #ffffff;
        }

        form {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #fff;
        }

        h2 {
            color: #1C1B37;
            font-size: 20px;
            margin-top: 40px;
            margin-bottom: 20px;
            border-bottom: 1px solid #1C1B37;
            padding-bottom: 8px;
        }

        .infusion-field {
            margin-bottom: 20px;
        }

        .infusion-field label {
            display: block;
            font-weight: 600;
            color: #1C1B37;
            margin-bottom: 16px;
        }

        .infusion-field input[type="text"],
        .infusion-field input[type="email"],
        .infusion-field input[type="url"],
        .infusion-field input[type="tel"],
        .infusion-field input[type="date"],
        .infusion-field select,
        .infusion-field textarea {
            width: 100%;
            padding: 12px 14px;
            font-size: 16px;
            border: 0.5px solid #1C1B37;
            border-radius: 4px;
            color: #1C1B37;
            background-color: #fff;
            box-sizing: border-box;
        }

        .infusion-field textarea {
            resize: vertical;
            min-height: 100px;
        }

        .infusion-submit {
            text-align: center;
            margin-top: 40px;
        }

        .infusion-submit button {
            background-color: #1C1B37;
            color: #ffffff;
            padding: 14px 30px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .infusion-submit button:hover {
            background-color: #13122a;
        }

        /* Toggle switches */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            display: none;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #1C1B37;
            transition: 0.4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #91be10;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        /* Terms and Conditions Checkbox */
        #termsCheckbox {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        #termsCheckbox input {
            margin-right: 10px;
        }
    </style>