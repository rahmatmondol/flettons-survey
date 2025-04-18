/**
 * Flettons Survey - Listing Page JavaScript
 * Handles the listing page functionality, survey selection, and add-on calculations
 */
jQuery(document).ready(function ($) {

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

});