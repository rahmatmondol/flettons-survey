/**
 * Quote Form JavaScript
 */
(function ($) {
    "use strict";

    // Initialize when document is ready
    $(document).ready(function () {
        // Toggle sqft area box
        window.toggleSqftAreaBox = function () {
            $("#sqftPriceBox").toggle($("#over1650").is(":checked"));
        };

        // Calculate the quote
        function calculateQuote() {
            let basePrice = 500; // Base price
            let additionalCost = 0;

            // Add costs for options
            if ($("#listed").is(":checked")) additionalCost += 100;
            if ($("#extended").is(":checked")) additionalCost += 150;

            // Square footage calculation
            if ($("#over1650").is(":checked")) {
                const sqft = parseFloat($("#sqft_area").val()) || 1651;
                if (sqft > 1650) {
                    additionalCost += (sqft - 1650) * 0.25;
                }
            }

            // Bedroom calculation
            const bedrooms = parseInt($("select[name='number_of_bedrooms']").val()) || 0;
            if (bedrooms > 3) {
                additionalCost += (bedrooms - 3) * 50;
            }

            const total = basePrice + additionalCost;
            return {
                base: basePrice,
                additional: additionalCost,
                total: total
            };
        }

        // Form submission
        $("#quoteForm").on("submit", function (e) {
            e.preventDefault();

            // Validate form
            if (!$(this)[0].checkValidity()) {
                $(this)[0].reportValidity();
                return;
            }

            // Calculate pricing
            const pricing = calculateQuote();
            $("#total").val(pricing.total.toFixed(2));

            // Show pricing summary
            $("#quoteDetails").html(`
                <p><strong>Base Survey Cost:</strong> £${pricing.base.toFixed(2)}</p>
                <p><strong>Additional Options:</strong> £${pricing.additional.toFixed(2)}</p>
                <p style="font-size:1.2em; font-weight:bold; margin-top:10px; border-top:1px solid #ddd; padding-top:10px;">
                    Total: £${pricing.total.toFixed(2)}
                </p>
            `);

            $("#quoteForm").hide();
            $("#quoteSummary").show();
        });

        // Back button
        $("#backBtn").on("click", function () {
            $("#quoteSummary").hide();
            $("#quoteForm").show();
        });

        // Proceed button - Store data and redirect to listing page
        $("#proceedBtn").on("click", function () {
            $(this).prop("disabled", true).text("Processing...");
            $("#backBtn").prop("disabled", true);

            // Show loading message
            $("#quote-message")
                .show()
                .html('<div style="padding:10px; text-align:center;">Processing your request...</div>');

            // Get form data
            const formData = $("#quoteForm").serialize();

            // Send AJAX request to store quote data temporarily
            $.ajax({
                type: "POST",
                url: flettons_ajax.ajax_url,
                data: {
                    action: 'store_temp_quote_data',
                    form_data: formData,
                    nonce: flettons_ajax.nonce
                },
                success: function (response) {
                    if (response.success) {
                        // Redirect to listing page with quote data in URL
                        const redirectUrl = flettons_ajax.listing_page_url + '?' + buildQueryString({
                            first_name: $("input[name='first_name']").val(),
                            last_name: $("input[name='last_name']").val(),
                            email: $("input[name='email_address']").val(),
                            phone: $("input[name='telephone_number']").val(),
                            address: $("input[name='full_address']").val(),
                            bedrooms: $("select[name='number_of_bedrooms']").val(),
                            property_type: $("select[name='house_or_flat']").val(),
                            market_value: $("input[name='market_value']").val(),
                            total: $("#total").val(),
                            quote_id: response.data.quote_id
                        });

                        window.location.href = redirectUrl;
                    } else {
                        // Show error message
                        $("#quote-message")
                            .html('<div style="color:#721c24; padding:10px; border-radius:4px;">' +
                                (response.data ? response.data.message : "An error occurred.") + '</div>');

                        // Re-enable buttons
                        $("#proceedBtn").prop("disabled", false).text("Proceed with Quote");
                        $("#backBtn").prop("disabled", false);
                    }
                },
                error: function () {
                    // Show error message
                    $("#quote-message")
                        .html('<div style="color:#721c24; padding:10px; border-radius:4px;">' +
                            "There was an error processing your request. Please try again." + '</div>');

                    // Re-enable buttons
                    $("#proceedBtn").prop("disabled", false).text("Proceed with Quote");
                    $("#backBtn").prop("disabled", false);
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

        $(window).resize(function () {
            if ($(window).width() < 768) {
                $(".telephone-field").css("flex-direction", "column");
            } else {
                $(".telephone-field").css("flex-direction", "row");
            }
        });
    });
})(jQuery);
