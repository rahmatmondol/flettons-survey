/**
 * Flettons Survey - Listing Page JavaScript
 * Handles the listing page functionality, survey selection, and add-on calculations
 */
jQuery(document).ready(function ($) {
    // Extract URL parameters to pre-fill form data
    const urlParams = new URLSearchParams(window.location.search);
    const queryData = {
        first_name: urlParams.get('first_name') || '',
        last_name: urlParams.get('last_name') || '',
        email: urlParams.get('email') || '',
        phone: urlParams.get('phone') || '',
        address: urlParams.get('address') || '',
        bedrooms: urlParams.get('bedrooms') || '',
        property_type: urlParams.get('property_type') || '',
        market_value: urlParams.get('market_value') || '',
        total: urlParams.get('total') || '',
        quote_id: urlParams.get('quote_id') || ''
    };

    console.log('Query Data:', queryData);

    // Populate form fields with data from URL if available
    if (queryData.first_name) $('.first_name').val(queryData.first_name);
    if (queryData.last_name) $('.last_name').val(queryData.last_name);
    if (queryData.email) $('.email_address').val(queryData.email);
    if (queryData.phone) $('.telephone_number').val(queryData.phone);
    if (queryData.address) $('.full_address').val(queryData.address);
    if (queryData.bedrooms) $('.number_of_bedrooms').val(queryData.bedrooms);
    if (queryData.property_type) $('.house_or_flat').val(queryData.property_type);
    if (queryData.market_value) $('.market_value').val(queryData.market_value);

    // Calculate prices for all survey levels
    calculateAllPrices();

    // Function to calculate prices for all survey levels
    function calculateAllPrices() {
        // Get base values from the form
        const marketValue = parseFloat($('.market_value').val()) || 0;
        const bedrooms = parseInt($('.number_of_bedrooms').val()) || 0;
        const propertyType = $('.house_or_flat').val();

        // Get pricing configuration from data attributes
        const marketValuePercentage1 = parseFloat($('#pricing-data').data('mv-percent-1') || 0);
        const marketValuePercentage2 = parseFloat($('#pricing-data').data('mv-percent-2') || 0.0004);
        const marketValuePercentage3 = parseFloat($('#pricing-data').data('mv-percent-3') || 0.0005);
        const marketValuePercentage4 = parseFloat($('#pricing-data').data('mv-percent-4') || 0.0006);

        const basePrice1 = parseFloat($('#pricing-data').data('base-1') || 349);
        const basePrice2 = parseFloat($('#pricing-data').data('base-2') || 499.38);
        const basePrice3 = parseFloat($('#pricing-data').data('base-3') || 611.73);
        const basePrice4 = parseFloat($('#pricing-data').data('base-4') || 1024.07);

        const bedroomCost = parseFloat($('#pricing-data').data('bedroom-cost') || 50);
        const listedBuildingCost = parseFloat($('#pricing-data').data('listed-cost') || 300);
        const extendedCost = parseFloat($('#pricing-data').data('extended-cost') || 150);

        // Calculate Level 1 price
        let price1 = basePrice1;
        if (marketValue >= 100000) {
            price1 += marketValue * marketValuePercentage1;
        }
        if (bedrooms > 4) {
            price1 += (bedrooms - 4) * bedroomCost;
        }
        $('.total1').val(price1.toFixed(2));
        $('.level1-price').text('£' + price1.toFixed(2));

        // Calculate Level 2 price
        let price2 = basePrice2;
        if (marketValue >= 100000) {
            price2 += marketValue * marketValuePercentage2;
        }
        if (bedrooms > 4) {
            price2 += (bedrooms - 4) * bedroomCost;
        }
        $('.total2').val(price2.toFixed(2));
        $('.level2-price').text('£' + price2.toFixed(2));

        // Calculate Level 3 price
        let price3 = basePrice3;
        if (marketValue >= 100000) {
            price3 += marketValue * marketValuePercentage3;
        }
        if (bedrooms > 4) {
            price3 += (bedrooms - 4) * bedroomCost;
        }

        // Add addon costs if selected
        if ($('.breakdown_of_estimated_repair_costs').val() == '1') {
            price3 += parseFloat($('.breakdown_of_estimated_repair_costs').data('cost') || 300);
        }
        if ($('.aerial_roof_and_chimney').val() == '1') {
            price3 += parseFloat($('.aerial_roof_and_chimney').data('cost') || 200);
        }
        if ($('.insurance_reinstatement_valuation').val() == '1') {
            price3 += parseFloat($('.insurance_reinstatement_valuation').data('cost') || 200);
        }
        if ($('.thermal_images').val() == '1') {
            price3 += parseFloat($('.thermal_images').data('cost') || 250);
        }

        $('.total3').val(price3.toFixed(2));
        $('.level3-price').text('£' + price3.toFixed(2));

        // Calculate Level 4 price
        let price4 = basePrice4;
        if (marketValue >= 100000) {
            price4 += marketValue * marketValuePercentage4;
        }
        if (bedrooms > 4) {
            price4 += (bedrooms - 4) * bedroomCost;
        }
        $('.total4').val(price4.toFixed(2));
        $('.level4-price').text('£' + price4.toFixed(2));
    }

    // Handle Level 1 button click
    $('.click-level1').click(function () {
        const price = $('.total1').val();
        redirectToSignup(1, price);
    });

    // Handle Level 2 button click
    $('.click-level2').click(function () {
        const price = $('.total2').val();
        redirectToSignup(2, price);
    });

    // Handle Level 3 button click - show addons
    $('.click-level3').click(function () {
        $('.step-1').hide();
        $('.step-2').show();
    });

    // Handle Level 4 button click
    $('.click-level4').click(function () {
        const price = $('.total4').val();
        redirectToSignup(4, price);
    });

    // Handle back button from Level 3 addons
    $('.back').click(function () {
        $('.step-2').hide();
        $('.step-1').show();
    });

    // Handle Level 3 confirmation after selecting addons
    $('.level-3-confirm').click(function () {
        const price = $('.total3').val();

        // Check if Plus package is selected which makes it Level 4
        const isPlusPackage = $('.plus_package').val() === '1';
        const level = isPlusPackage ? 4 : 3;

        // Get selected addons
        const addons = {
            breakdown: $('.breakdown_of_estimated_repair_costs').val(),
            aerial: $('.aerial_roof_and_chimney').val(),
            insurance: $('.insurance_reinstatement_valuation').val(),
            thermal: $('.thermal_images').val(),
            plus: $('.plus_package').val()
        };

        // Redirect to signup with level and addon info
        redirectToSignup(level, price, addons);
    });

    // Handle addon selection changes
    $('.addon').change(function () {
        // Recalculate Level 3 price with selected addons
        let basePrice = parseFloat($('#pricing-data').data('base-3') || 611.73);
        const marketValue = parseFloat($('.market_value').val()) || 0;
        const bedrooms = parseInt($('.number_of_bedrooms').val()) || 0;
        const marketValuePercentage3 = parseFloat($('#pricing-data').data('mv-percent-3') || 0.0005);
        const bedroomCost = parseFloat($('#pricing-data').data('bedroom-cost') || 50);

        // Calculate base price with market value and bedrooms
        let price = basePrice;
        if (marketValue >= 100000) {
            price += marketValue * marketValuePercentage3;
        }
        if (bedrooms > 4) {
            price += (bedrooms - 4) * bedroomCost;
        }

        // Check if Plus package is selected
        if ($('.plus_package').val() == '1') {
            // If Plus package selected, use Level 4 price
            $('.addons').hide();
            price = parseFloat($('.total4').val());
        } else {
            // Otherwise calculate with individual addons
            $('.addons').show();

            if ($('.breakdown_of_estimated_repair_costs').val() == '1') {
                price += parseFloat($('.breakdown_of_estimated_repair_costs').data('cost') || 300);
            }
            if ($('.aerial_roof_and_chimney').val() == '1') {
                price += parseFloat($('.aerial_roof_and_chimney').data('cost') || 200);
            }
            if ($('.insurance_reinstatement_valuation').val() == '1') {
                price += parseFloat($('.insurance_reinstatement_valuation').data('cost') || 200);
            }
            if ($('.thermal_images').val() == '1') {
                price += parseFloat($('.thermal_images').data('cost') || 250);
            }
        }

        // Update Level 3 price display
        $('.total3').val(price.toFixed(2));
        $('.level3-price').text('£' + price.toFixed(2));
    });

    // Function to redirect to customer signup page
    function redirectToSignup(level, price, addons = null) {
        // Prepare URL parameters
        const params = {
            first_name: $('.first_name').val() || queryData.first_name,
            last_name: $('.last_name').val() || queryData.last_name,
            email: $('.email_address').val() || queryData.email,
            phone: $('.telephone_number').val() || queryData.phone,
            address: $('.full_address').val() || queryData.address,
            bedrooms: $('.number_of_bedrooms').val() || queryData.bedrooms,
            property_type: $('.house_or_flat').val() || queryData.property_type,
            market_value: $('.market_value').val() || queryData.market_value,
            level: level,
            total: price,
            quote_id: queryData.quote_id
        };

        // Add addons data if available
        if (addons) {
            params.addons = JSON.stringify(addons);
        }

        // First save data via AJAX
        $.ajax({
            type: 'POST',
            url: flettons_ajax.ajax_url,
            data: {
                action: 'update_survey_selection',
                nonce: flettons_ajax.nonce,
                quote_data: params
            },
            success: function (response) {
                if (response.success) {
                    // Create URL parameters string
                    const queryString = Object.keys(params)
                        .filter(key => params[key] !== null && params[key] !== undefined && params[key] !== '')
                        .map(key => encodeURIComponent(key) + '=' + encodeURIComponent(params[key]))
                        .join('&');

                    // Redirect to customer signup page
                    window.location.href = flettons_ajax.signup_page_url + '?' + queryString;
                } else {
                    alert("Error saving selection: " + (response.data && response.data.message ? response.data.message : "Unknown error"));
                }
            },
            error: function () {
                alert("Error communicating with server. Please try again.");
            }
        });
    }

    // Prevent form submission with Enter key
    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    // Google Places autocomplete initialization
    function initializeAutocomplete() {
        if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
            console.log('Google Places API not loaded');
            return;
        }

        const addressField = document.getElementById('searchTextField');
        if (addressField) {
            const autocomplete = new google.maps.places.Autocomplete(addressField, {
                types: ['address'],
                componentRestrictions: { country: 'gb' }
            });

            autocomplete.addListener('place_changed', function () {
                const place = autocomplete.getPlace();
                if (place.address_components) {
                    for (let i = 0; i < place.address_components.length; i++) {
                        for (let j = 0; j < place.address_components[i].types.length; j++) {
                            if (place.address_components[i].types[j] == "postal_code") {
                                $('#postcode').val(place.address_components[i].long_name);
                            }
                            if (place.address_components[i].types[j] == "postal_town") {
                                $('#city').val(place.address_components[i].long_name);
                            }
                        }
                    }
                }
            });
        }
    }

    // Initialize Google Places if available
    if (typeof google !== 'undefined') {
        google.maps.event.addDomListener(window, 'load', initializeAutocomplete);
    }

    // Handle responsive layout adjustments
    function adjustLayout() {
        const windowWidth = $(window).width();

        if (windowWidth <= 768) {
            // Mobile adjustments
            $('.level-choice').css('margin-bottom', '30px');
            $('.btn-style-group').css('flex-direction', 'column');
        } else {
            // Desktop layout
            $('.level-choice').css('margin-bottom', '20px');
            $('.btn-style-group').css('flex-direction', 'row');
        }
    }

    // Initial layout adjustment
    adjustLayout();

    // Adjust layout on window resize
    $(window).resize(function () {
        adjustLayout();
    });
});