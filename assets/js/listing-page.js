/**
 * Flettons Survey - Listing Page JavaScript
 * Handles the listing page functionality, survey selection, and add-on calculations
 */
jQuery(document).ready(function ($) {


    // Handle button click
    $('.buy-now-btn').click(function (e) {
        e.preventDefault();

        //get data from href
        const href = $(this).attr('href');

        // set data to href
        $('.confirm-yes').attr('href', href);

        // hide listing page
        $('#quote-container').fadeOut();

        // show confirmation page
        $('#confirm-popup-conteiner').fadeIn();
    });

    // hide confirmation page
    $('.confirm-no').click(function (e) {
        e.preventDefault();

        // hide confirmation page
        $('#confirm-popup-conteiner').fadeOut();

        // show listing page
        $('#quote-container').fadeIn();
    });

    // handle confirmation
    $('.confirm-yes').click(function (e) {
        e.preventDefault();

        //check if href is empty
        if ($(this).attr('href') === '') {
            return;
        }

        // check if terms and conditions are checked
        if (!$('#termsCheckbox').is(':checked')) {
            alert('Please accept the terms and conditions');
            return;
        }

        // redirect to href
        window.location.href = $(this).attr('href');

    });

    $('.click-level3').on('click', function () {
        $('.step-1').hide();
        $('.step-2').show();
    });
    
    $('.back').on('click', function () {
        $('.step-1').show();
        $('.step-2').hide();
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
        // Get base prices
        let basePrice = parseFloat($('#level3-base-price').val() || 0);
        let level4Price = parseFloat($('#level4-base-price').val() || 0);

        // Start with base price
        let totalPrice = basePrice;

        // Get base link and remove any existing addon parameters
        let baseLink = $('.level-3-confirm').attr('href');
        baseLink = baseLink.replace(/&breakdown=1/, '');
        baseLink = baseLink.replace(/&aerial=1/, '');
        baseLink = baseLink.replace(/&insurance=1/, '');

        // Check each addon and update price and link accordingly
        if ($('.breakdown_of_estimated_repair_costs').val() == '1') {
            totalPrice += parseFloat($('.breakdown_of_estimated_repair_costs').data('cost') || 0);
            baseLink += '&breakdown=1';
        }

        if ($('.aerial_roof_and_chimney').val() == '1') {
            totalPrice += parseFloat($('.aerial_roof_and_chimney').data('cost') || 0);
            baseLink += '&aerial=1';
        }

        if ($('.insurance_reinstatement_valuation').val() == '1') {
            totalPrice += parseFloat($('.insurance_reinstatement_valuation').data('cost') || 0);
            baseLink += '&insurance=1';
        }

        // Update total price in the URL
        baseLink = baseLink.replace(/total=\d+(?:\.\d+)?/, 'total=' + totalPrice.toFixed(2));

        // Update Level 3 price display
        $('.total3').val(totalPrice.toFixed(2));
        $('.level3-price').text('£' + totalPrice.toFixed(2));
        $('.save-price').text('£' + (totalPrice - level4Price).toFixed(2));
        $('.level-3-confirm').attr('href', baseLink);

        // Show appropriate option based on price comparison
        if (totalPrice > level4Price) {
            $('.level4-all-inlcude-addons').show();
            $('.level-3-confirm').hide();
        } else {
            $('.level4-all-inlcude-addons').hide();
            $('.level-3-confirm').show();
        }
    });

});