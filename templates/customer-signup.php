<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get pre-filled data from URL parameters
$first_name = isset($_GET['first_name']) ? sanitize_text_field($_GET['first_name']) : '';
$last_name = isset($_GET['last_name']) ? sanitize_text_field($_GET['last_name']) : '';
$email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';
$phone = isset($_GET['phone']) ? sanitize_text_field($_GET['phone']) : '';
$address = isset($_GET['address']) ? sanitize_text_field($_GET['address']) : '';
$bedrooms = isset($_GET['bedrooms']) ? sanitize_text_field($_GET['bedrooms']) : '';
$property_type = isset($_GET['property_type']) ? sanitize_text_field($_GET['property_type']) : '';
$market_value = isset($_GET['market_value']) ? sanitize_text_field($_GET['market_value']) : '';
$total = isset($_GET['total']) ? sanitize_text_field($_GET['total']) : '0';
$level = isset($_GET['level']) ? sanitize_text_field($_GET['level']) : '2';
$quote_id = isset($_GET['quote_id']) ? sanitize_text_field($_GET['quote_id']) : '';

// Get API key from settings
$settings = get_option('flettons_survey_settings', array());
$google_api_key = isset($settings['api_keys']['google_places']) ? $settings['api_keys']['google_places'] : '';
?>

<div class="survey-signup-container">
    <div class="text" id="webformErrors" name="errorContent"></div>

    <form id="customer_signup_form" method="POST" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" class="survey-form">
        <input type="hidden" name="action" value="save_customer_to_crm">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('flettons_customer_signup_nonce'); ?>">
        <input type="hidden" name="quote_id" id="quote_id" value="<?php echo esc_attr($quote_id); ?>">
        <input type="hidden" name="survey_level" id="survey_level" value="<?php echo esc_attr($level); ?>">
        <input type="hidden" name="survey_total" id="survey_total" value="<?php echo esc_attr($total); ?>">

        <div class="survey-header">
            <h2>SURVEY INSTRUCTION FORM</h2>
        </div>

        <div class="survey-section">
            <h3>Client Details</h3>
            <div class="form-divider"></div>

            <div class="form-row">
                <div class="form-group">
                    <label for="title">Title</label>
                    <select id="title" name="title">
                        <option value="">Please select one</option>
                        <option value="Dr.">Dr.</option>
                        <option value="Lord.">Lord.</option>
                        <option value="Miss.">Miss.</option>
                        <option value="Mr.">Mr.</option>
                        <option value="Mrs.">Mrs.</option>
                        <option value="Ms.">Ms.</option>
                        <option value="Prof">Prof</option>
                        <option value="Sir">Sir</option>
                        <option value="Other.">Other.</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input id="first_name" name="first_name" placeholder="First Name" type="text" value="<?php echo esc_attr($first_name); ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input id="last_name" name="last_name" placeholder="Last Name" type="text" value="<?php echo esc_attr($last_name); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input id="email" name="email" placeholder="Email" type="email" value="<?php echo esc_attr($email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" placeholder="Phone" type="tel" value="<?php echo esc_attr($phone); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="current_address">Your Current Address *</label>
                    <input id="current_address" name="current_address" placeholder="Your Current Address" type="text" value="<?php echo esc_attr($address); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="current_postcode">Postal Code *</label>
                    <input id="current_postcode" name="current_postcode" placeholder="Your Postcode" type="text" required>
                </div>
            </div>
        </div>

        <div class="survey-section">
            <h3>Survey Property Details</h3>
            <div class="form-divider"></div>

            <div class="form-row">
                <div class="form-group">
                    <label for="property_type">Property Type *</label>
                    <select id="property_type" name="property_type" required>
                        <option value="">Please select one</option>
                        <option value="Semi Detached" <?php selected($property_type, 'Semi Detached'); ?>>Semi Detached</option>
                        <option value="Detached" <?php selected($property_type, 'Detached'); ?>>Detached</option>
                        <option value="Flat" <?php selected($property_type, 'Flat'); ?>>Flat</option>
                        <option value="Bungalow" <?php selected($property_type, 'Bungalow'); ?>>Bungalow</option>
                        <option value="Purpose built flat" <?php selected($property_type, 'Purpose built flat'); ?>>Purpose built flat</option>
                        <option value="House" <?php selected($property_type, 'House'); ?>>House</option>
                        <option value="Period conversion" <?php selected($property_type, 'Period conversion'); ?>>Period conversion</option>
                        <option value="Warehouse conversion" <?php selected($property_type, 'Warehouse conversion'); ?>>Warehouse conversion</option>
                        <option value="Maisonette" <?php selected($property_type, 'Maisonette'); ?>>Maisonette</option>
                        <option value="Other" <?php selected($property_type, 'Other'); ?>>Other</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="property_link">Rightmove/Zoopla/ Agents' Link Etc</label>
                    <input id="property_link" name="property_link" placeholder="Rightmove/Zoopla/ Agents' Link Etc" type="text">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="survey_address">Survey Street Address *</label>
                    <input id="survey_address" name="survey_address" placeholder="Survey Address" type="text" value="<?php echo esc_attr($address); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="survey_postcode">Survey Postal Code *</label>
                    <input id="survey_postcode" name="survey_postcode" placeholder="Survey Postcode" type="text" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="vacant_occupied">Vacant or Occupied *</label>
                    <select id="vacant_occupied" name="vacant_occupied" required>
                        <option value="">Please select one</option>
                        <option value="Vacant">Vacant</option>
                        <option value="Occupied">Occupied</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Any Extensions? *</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="extended" value="1" required> Yes
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="extended" value="0" required> No
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="bedrooms">Number of Bedrooms *</label>
                    <select id="bedrooms" name="bedrooms" required>
                        <option value="">Please select one</option>
                        <?php for ($i = 1; $i <= 9; $i++) : ?>
                            <option value="<?php echo $i; ?>" <?php selected($bedrooms, $i); ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Garage? *</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="garage" value="1" required> Yes
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="garage" value="0" required> No
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-row garage-location" style="display:none;">
                <div class="form-group">
                    <label for="garage_location">Garage Location</label>
                    <input id="garage_location" name="garage_location" placeholder="Garage Location" type="text">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="specific_concerns">Tell us about your specific concerns *</label>
                    <textarea id="specific_concerns" name="specific_concerns" rows="5" required></textarea>
                </div>
            </div>
        </div>

        <div class="survey-section">
            <h3>Agents Details</h3>
            <div class="form-divider"></div>

            <div class="form-row">
                <div class="form-group">
                    <label for="agent_company">Agent Company Name *</label>
                    <input id="agent_company" name="agent_company" placeholder="Agent Company Name" type="text" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="agent_name">Agent Name *</label>
                    <input id="agent_name" name="agent_name" placeholder="Agent Name" type="text" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="agent_phone">Agent Phone Number *</label>
                    <input id="agent_phone" name="agent_phone" placeholder="Agent Phone Number" type="tel" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="agent_email">Agents Email *</label>
                    <input id="agent_email" name="agent_email" placeholder="Agents Email" type="email" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="agent_address">Agent address *</label>
                    <input id="agent_address" name="agent_address" placeholder="Agent Address" type="text" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="agent_postcode">Agent Postal Code *</label>
                    <input id="agent_postcode" name="agent_postcode" placeholder="Agent Postcode" type="text" required>
                </div>
            </div>
        </div>

        <div class="important-notice">
            <strong>IMPORTANT NOTICE:</strong><br><br>
            Please ensure that you have chosen the correct survey for your property before payment. Our policy is as follows:<br><br>
            1. If the property exceeds 1650sqft or is a listed building, a bespoke quote is required.<br><br>
            2. If the property is house or flat built before 1985, or has been altered structurally in any way, a LEVEL THREE SURVEY is required, not a LEVEL TWO SURVEY. We do not deviate from this.<br><br>
            Please see our home page for the <strong>WHICH SURVEY:</strong> flow chart.<br><br>
            If you proceed to book incorrectly, we will cancel the order and charge 1.25% of the transaction fee to cover the cost of the transaction.<br><br>

            <div class="terms-checkbox">
                <label>
                    <input type="checkbox" id="termsCheckbox" name="terms_agreed" value="1" required>
                    I confirm that I have read and understand the terms.
                </label>
            </div>
        </div>

        <div class="survey-summary">
            <h3>Your Order Summary</h3>
            <div class="summary-details">
                <p><strong>Survey Type:</strong> Level <?php echo esc_html($level); ?> Survey</p>
                <p><strong>Total Amount:</strong> £<?php echo esc_html(number_format((float)$total, 2)); ?></p>
            </div>
        </div>

        <div class="form-submit">
            <button type="submit" id="submit_button">Confirm and Pay</button>
        </div>
    </form>
</div>


<style type="text/css">
    /* Survey Form Styles */
    .survey-signup-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }

    .survey-header h2 {
        text-align: center;
        font-size: 24px;
        margin-bottom: 20px;
    }

    .survey-section {
        margin-bottom: 30px;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .survey-section h3 {
        font-size: 18px;
        margin-top: 0;
        margin-bottom: 10px;
        color: #333;
    }

    .form-divider {
        height: 1px;
        background: #ddd;
        margin-bottom: 20px;
    }

    .form-row {
        margin-bottom: 15px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="tel"],
    .form-group input[type="number"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .form-group select {
        height: 40px;
    }

    .form-group textarea {
        resize: vertical;
    }

    .radio-group {
        display: flex;
        gap: 20px;
    }

    .radio-option {
        display: flex;
        align-items: center;
        font-weight: normal;
    }

    .radio-option input {
        margin-right: 5px;
    }

    .important-notice {
        margin: 20px 0;
        padding: 15px;
        background: #fff8f8;
        border: 1px solid #ffb6b6;
        border-radius: 4px;
        font-size: 14px;
        color: #c00;
    }

    .terms-checkbox {
        margin-top: 15px;
    }

    .terms-checkbox label {
        display: flex;
        align-items: flex-start;
    }

    .terms-checkbox input {
        margin-right: 10px;
        margin-top: 3px;
    }

    .survey-summary {
        margin: 20px 0;
        padding: 15px;
        background: #f3f8ff;
        border: 1px solid #b6d4ff;
        border-radius: 4px;
    }

    .summary-details {
        font-size: 16px;
    }

    .form-submit {
        text-align: center;
        margin-top: 20px;
    }

    .form-submit button {
        background: #95C11F;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 12px 24px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
    }

    .form-submit button:hover {
        background: #7a9f19;
    }

    /* Responsive styles */
    @media (min-width: 768px) {
        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }
    }

    @media (max-width: 767px) {
        .survey-signup-container {
            padding: 10px;
        }

        .survey-section {
            padding: 15px;
        }

        .survey-header h2 {
            font-size: 20px;
        }

        .survey-section h3 {
            font-size: 16px;
        }

        .form-submit button {
            width: 100%;
        }
    }
</style>