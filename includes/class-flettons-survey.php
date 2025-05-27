<?php

/**
 * Main Flettons Survey Plugin Class
 */
class Flettons_Survey
{

    /**
     * Initialize the plugin
     */
    public function init()
    {
        // Register assets
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));

        // Register shortcodes
        add_shortcode('flettons_quote_form', array($this, 'quote_form_shortcode'));
        add_shortcode('flettons_listing_page', array($this, 'listing_page_shortcode'));
        add_shortcode('flettons_customer_signup', array($this, 'customer_sign_up_page_shortcode'));
        add_shortcode('flettons_order', array($this, 'customer_order_page_shortcode'));
        add_shortcode('flettons_payment_confirmation', array($this, 'payment_confirmation_page_shortcode'));

        // Register AJAX handlers
        add_action('wp_ajax_store_temp_quote_data', array($this, 'store_temp_quote_data'));
        add_action('wp_ajax_nopriv_store_temp_quote_data', array($this, 'store_temp_quote_data'));

        add_action('wp_ajax_update_survey_selection', array($this, 'update_survey_selection'));
        add_action('wp_ajax_nopriv_update_survey_selection', array($this, 'update_survey_selection'));

        add_action('wp_ajax_save_customer_to_crm', array($this, 'save_customer_to_crm'));
        add_action('wp_ajax_nopriv_save_customer_to_crm', array($this, 'save_customer_to_crm'));

        // Register admin-post handler for form submission
        add_action('admin_post_flettons_process_signup', array($this, 'process_signup_form'));
        add_action('admin_post_nopriv_flettons_process_signup', array($this, 'process_signup_form'));

        // add webhooks get requests
        add_action('rest_api_init', function () {
            register_rest_route('flettons/v1', '/quote-initiated', array(
                'methods' => 'POST',
                'callback' => array($this, 'handle_quote_initiated_webhook'),
                'permission_callback' => '__return_true',
            ));
        });
    }


    /**
     * Get custom field value by field ID
     *
     * @param array $customFields
     * @param int $fieldId
     * @return mixed|null
     */
    public function getCustomFieldValue($customFields, $fieldId)
    {
        foreach ($customFields as $field) {
            if ($field['id'] == $fieldId) {
                return $field['content'];
            }
        }
        return null;
    }

    /**
     * Handle quote initiated webhook
     */
    public function handle_quote_initiated_webhook(WP_REST_Request $request)
    {
        $contactId = $_REQUEST['id'];
        if (empty($contactId)) {
            return new WP_Error('missing_contact_id', 'Contact ID is required', array('status' => 400));
        }
        // Get the contact ID from the request

        $api = new Flettons_API();

        // Get quote data from transient
        $contact = $api->find_contact_by_id($contactId);

        if (empty($contact)) {
            return new WP_Error('quote_not_found', 'Quote data not found', array('status' => 404));
        }

        $data = array(
            'market_value' => $this->getCustomFieldValue($contact['custom_fields'], 193),
            'number_of_bedrooms' => $this->getCustomFieldValue($contact['custom_fields'], 197),
            'house_or_flat' => $this->getCustomFieldValue($contact['custom_fields'], 195),
            'listed_building' => $this->getCustomFieldValue($contact['custom_fields'], 203),
            'breakdown_of_estimated_repair_costs' => $this->getCustomFieldValue($contact['custom_fields'], 208),
            'aerial_roof_and_chimney' => $this->getCustomFieldValue($contact['custom_fields'], 210),
            'insurance_reinstatement_valuation' => $this->getCustomFieldValue($contact['custom_fields'], 212),
        );

        $data = $this->sanitize_form_data($data);
        $data['email_address'] = $contact['email_addresses'][0]['email'];

        // Save listing page URL in a transient for 1 month with a unique key
        $key = hash('sha256', $contactId);
        set_transient('encrypted_contact_id' . $key, $contactId, MONTH_IN_SECONDS);

        $listing_page_url = site_url('/flettons-listing-page?contact_id=' . $key . '&temp=1');

        $contactData = array(
            'email_addresses' => array(
                array(
                    'email' => $contact['email_addresses'][0]['email'],
                    'field' => 'EMAIL1'
                )
            ),

            'custom_fields' => array(
                array('id' => '218', 'content' => site_url() . '/flettons-order/?email=' . $data['email_address'] . '&total=' . $data['total1'] . '&level=1&order=1'),
                array('id' => '222', 'content' => site_url() . '/flettons-order/?email=' . $data['email_address'] . '&total=' . $data['total2'] . '&level=2&order=1'),
                array('id' => '226', 'content' => site_url() . '/flettons-order/?email=' . $data['email_address'] . '&total=' . $data['total3'] . '&level=3&order=1'),
                array('id' => '240', 'content' => site_url() . '/flettons-order/?email=' . $data['email_address'] . '&total=' . $data['total4'] . '&level=4&order=1'),

                array('id' => '601', 'content' => $listing_page_url),
                array('id' => '207', 'content' => $data),
                array('id' => '220', 'content' => isset($data['total1']) ? $data['total1'] : ''),
                array('id' => '224', 'content' => isset($data['total2']) ? $data['total2'] : ''),
                array('id' => '228', 'content' => isset($data['total3']) ? $data['total3'] : ''),
                array('id' => '238', 'content' => isset($data['total4']) ? $data['total4'] : ''),
            )
        );

        // update contact
        $response = $api->update_webhook_contact($contactId, $contactData);

        if (is_wp_error($response)) {
            return new WP_Error('update_failed', 'Failed to update contact', array('status' => 500));
        }

        return rest_ensure_response(array(
            'message' => 'Quote initiated successfully',
            'data' => $listing_page_url,
        ));
    }

    /**
     * Register and enqueue assets
     */
    public function register_assets()
    {
        $settings = get_option('flettons_survey_settings', array());

        $google_maps_api_key = $this->get_setting('api_keys_google_places') ?? '';

        // Register styles
        wp_register_style(
            'flettons-quote-form',
            FLETTONS_SURVEY_PLUGIN_URL . 'assets/css/quote-form.css',
            array(),
            FLETTONS_SURVEY_VERSION
        );

        // Register styles
        wp_register_style(
            'flettons-customer-signup-page',
            FLETTONS_SURVEY_PLUGIN_URL . 'assets/css/signup-page.css',
            array(),
            FLETTONS_SURVEY_VERSION
        );

        // Register styles
        wp_register_style(
            'flettons-listing-page',
            FLETTONS_SURVEY_PLUGIN_URL . 'assets/css/listing-page.css',
            array(),
            FLETTONS_SURVEY_VERSION
        );

        // Register scripts
        wp_register_script(
            'flettons-quote-form-js',
            FLETTONS_SURVEY_PLUGIN_URL . 'assets/js/quote-form.js',
            array('jquery'),
            FLETTONS_SURVEY_VERSION,
            true
        );

        // google maps
        wp_register_script(
            'google-maps',
            'https://maps.googleapis.com/maps/api/js?key=' . $google_maps_api_key . '&libraries=places',
            array('jquery'),
            null,
            true
        );

        wp_register_script(
            'flettons-listing-page-js',
            FLETTONS_SURVEY_PLUGIN_URL . 'assets/js/listing-page.js',
            array('jquery'),
            FLETTONS_SURVEY_VERSION,
            true
        );

        wp_register_script(
            'flettons-customer-signup-js',
            FLETTONS_SURVEY_PLUGIN_URL . 'assets/js/customer-signup.js',
            array('jquery'),
            FLETTONS_SURVEY_VERSION,
            true
        );

        // Localize the scripts with data
        wp_localize_script('flettons-quote-form-js', 'flettons_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('flettons_quote_form_nonce'),
            'listing_page_url' => get_permalink(get_page_by_path('flettons-listing-page'))

        ));

        wp_localize_script('flettons-listing-page-js', 'flettons_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('flettons_listing_page_nonce'),
            'signup_page_url' => get_permalink(get_page_by_path('flettons-customer-signup'))
        ));

        wp_localize_script('flettons-customer-signup-js', 'flettons_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('flettons_customer_signup_nonce')
        ));
    }

    /**
     * Quote Form Shortcode
     */
    public function quote_form_shortcode()
    {
        // Enqueue assets
        wp_enqueue_style('flettons-quote-form');
        // wp_enqueue_script('flettons-quote-form-js');
        wp_enqueue_script('google-maps');

        ob_start();
        include FLETTONS_SURVEY_PLUGIN_DIR . 'templates/quote-form.php';
        return ob_get_clean();
    }

    /**
     * Listing Page Shortcode
     */
    public function listing_page_shortcode()
    {

        wp_enqueue_style('flettons-listing-page');
        wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css', array(), 'all');
        // Enqueue listing page script
        wp_enqueue_script('flettons-listing-page-js');

        // Get quote ID from URL
        $contact_id = isset($_GET['contact_id']) ? $_GET['contact_id'] : '';
        $temp = isset($_GET['temp']) ? $_GET['temp'] : null;

        // Check if contact ID is provided
        if (empty($contact_id)) {
            wp_redirect(site_url('/flettons-quote-form'));
            exit;
        }

        // If temp is set, use the contact ID as a key to get the encrypted contact ID
        if ($temp) {
            // get contact ID from transient
            $contact_id = get_transient('encrypted_contact_id' . $contact_id);
            $contact_id = $contact_id;
        }

        // get data from API
        $api = new Flettons_API();
        $contact = $api->find_contact_by_id($contact_id);

        if (empty($contact)) {
            wp_redirect(site_url('/flettons-quote-form'));
            exit;
        }

        $quote_data = array(
            'first_name' => $contact['given_name'] ?? '',
            'last_name' => $contact['family_name'] ?? '',
            'email_address' => $contact['email_addresses'][0]['email'] ?? '',
            'telephone_number' => $contact['phone_numbers'][0]['number'] ?? '',
            'full_address' => $this->getCustomFieldValue($contact['custom_fields'], 191),
            'house_or_flat' => $this->getCustomFieldValue($contact['custom_fields'], 195),
            'number_of_bedrooms' => $this->getCustomFieldValue($contact['custom_fields'], 197),
            'market_value' => $this->getCustomFieldValue($contact['custom_fields'], 193),
            'sqft_area' => $this->getCustomFieldValue($contact['custom_fields'], 603),
            'listed_building' => $this->getCustomFieldValue($contact['custom_fields'], 203) ? 1 : 0,
            'breakdown_of_estimated_repair_costs' => $this->getCustomFieldValue($contact['custom_fields'], 208),
            'aerial_roof_and_chimney' => $this->getCustomFieldValue($contact['custom_fields'], 210),
            'insurance_reinstatement_valuation' => $this->getCustomFieldValue($contact['custom_fields'], 212),
            'thermal_images' => $this->getCustomFieldValue($contact['custom_fields'], 214),
            'plus_package' => $this->getCustomFieldValue($contact['custom_fields'], 216),
            'total1' => $this->getCustomFieldValue($contact['custom_fields'], 220),
            'total2' => $this->getCustomFieldValue($contact['custom_fields'], 224),
            'total3' => $this->getCustomFieldValue($contact['custom_fields'], 228),
            'total4' => $this->getCustomFieldValue($contact['custom_fields'], 238),
        );


        $settings = get_option('flettons_survey_settings', array());


        ob_start();
        include FLETTONS_SURVEY_PLUGIN_DIR . 'templates/listing-page.php';
        return ob_get_clean();
    }

    /**
     * payment confirm Page Shortcode
     */
    public function payment_confirmation_page_shortcode()
    {
        $api = new Flettons_API();
        $amount = isset($_GET['amount']) ? $_GET['amount'] : '';
        $contact_id = isset($_GET['contact_id']) ? $_GET['contact_id'] : '';
        $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

        if (empty($amount) || empty($contact_id) || empty($order_id)) {
            wp_redirect(site_url('/flettons-quote-form'));
            exit;
        }

        $confirm = $api->handle_stripe_payment_confirmation($amount, $contact_id, $order_id);

        if ($confirm) {
            echo '<script>window.location.href="' . site_url('/thank-you') . '";</script>';
        } else {
            echo '<script>window.location.href="' . site_url('/flettons-quote-form') . '";</script>';
        }
    }

    /**
     * Customer Sign Up Page Shortcode
     */
    public function customer_sign_up_page_shortcode()
    {

        wp_enqueue_style('flettons-customer-signup-page');
        // Enqueue customer signup script
        wp_enqueue_script('google-maps');
        wp_enqueue_script('flettons-customer-signup-js');

        $contactId = isset($_GET['contact_id']) ? $_GET['contact_id'] : null;
        $email = isset($_GET['email']) ? $_GET['email'] : null;
        $lavel = isset($_GET['level']) ? $_GET['level'] : null;
        $total = isset($_GET['total']) ? $_GET['total'] : null;
        $breakdown = isset($_GET['breakdown']) ? true : false;
        $aerial = isset($_GET['aerial']) ? true : false;
        $insurance = isset($_GET['insurance']) ? true : false;

        $quote_data = get_transient('flettons_temp_quote_' . $contactId);

        if (empty($quote_data)) {
            wp_redirect(site_url('/flettons-quote-form'));
            exit;
        }

        $api = new Flettons_API();

        if ($email !== null && $lavel !== null && $total !== null && $contactId !== null) {
            $data = array(
                'email_address' => $email,
                'level' => $lavel,
                'total' => $total,
                'breakdown' => $breakdown,
                'aerial' => $aerial,
                'insurance' => $insurance,
            );

            // update contact
            $contact = $api->update_contact($contactId, $data);

            if (empty($contact)) {
                wp_redirect(site_url('/flettons-listing-page?contact_id=' . $contactId));
                exit;
            }
            //update tags
            $result = $api->apply_level_tags($contactId, $lavel);
            if (!$result) {
                wp_redirect(site_url('/flettons-listing-page?contact_id=' . $contactId));
                exit;
            }
        }


        ob_start();
        // include FLETTONS_SURVEY_PLUGIN_DIR . 'templates/customer-signup.php';
        include FLETTONS_SURVEY_PLUGIN_DIR . 'templates/signup-page.php';
        // $settings = get_option('flettons_survey_settings', array());
        // echo '<pre>';
        // print_r($quote_data);
        // echo '</pre>';

        return ob_get_clean();
    }

    /**
     * customer order Page Shortcode
     */
    public function customer_order_page_shortcode()
    {
        $contactId = isset($_GET['contact_id']) ? $_GET['contact_id'] : null;
        $email = isset($_GET['email']) ? $_GET['email'] : null;
        $lavel = isset($_GET['level']) ? $_GET['level'] : null;
        $total = isset($_GET['total']) ? $_GET['total'] : null;
        $order = isset($_GET['order']) ? $_GET['order'] : null;

        $api = new Flettons_API();

        // direct order
        if ($email !== null && $lavel !== null && $total !== null && $order !== null) {
            // find_contact_by_email
            $conatct = $api->find_contact_by_email_address($email);

            if (empty($conatct)) {
                wp_redirect(site_url('/flettons-listing-page?contact_id=' . $contactId));
                exit;
            }

            $data = array(
                'email_address' => $email,
                'level' => $lavel,
                'total' => $total,
            );
            $order_id = $api->create_order($conatct['id'], $data);

            $checkout = $api->create_stripe_checkout($data, $conatct['id'], $order_id);

            if ($checkout) {
                wp_redirect($checkout);
                exit;
            } else {
                wp_redirect(site_url('/flettons-listing-page?contact_id=' . $contactId));
                exit;
            }
            exit;
        }

        //get parameters from URL
        $email = isset($_GET['inf_field_Email']) ? $_GET['inf_field_Email'] : '';
        $contactId = isset($_GET['contactId']) ? $_GET['contactId'] : '';

        $link = $api->get_order_link($contactId);
        if ($link) {
            wp_redirect($link);
            exit;
        } else {
            wp_redirect(site_url('/flettons-listing-page?contact_id=' . $contactId));
            exit;
        }
    }

    /**
     * AJAX handler for storing temporary quote data
     */
    public function store_temp_quote_data()
    {
        // Parse the form data
        $form_data = array();
        parse_str($_POST['formData'], $form_data);

        // Sanitize and validate data
        $data = $this->sanitize_form_data($form_data);

        $api = new Flettons_API();
        // Validate the data

        // Generate a unique quote ID
        $contact_id = $api->create_contact($data);
        // $contact_id = 136859;


        if (!$contact_id) {
            wp_send_json_error(array('message' => 'Failed to create contact'));
            return;
        }

        // save the data to a transient
        set_transient('flettons_temp_quote_' . $contact_id, $data);

        $api->apply_tags($contact_id, 643);
        // apply tags

        wp_send_json_success(array(
            'message' => 'Quote data stored successfully',
            'contact_id' => $contact_id,
        ));
    }

    /**
     * AJAX handler for updating survey selection
     */
    public function update_survey_selection()
    {
        // Check nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'flettons_listing_page_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed'));
        }

        // Get the quote data
        $quote_data = isset($_POST['quote_data']) ? $_POST['quote_data'] : array();

        // Sanitize the data
        $sanitized_data = array();
        foreach ($quote_data as $key => $value) {
            if ($key === 'addons') {
                $sanitized_data[$key] = sanitize_textarea_field($value);
            } else {
                $sanitized_data[$key] = sanitize_text_field($value);
            }
        }

        // Update the stored quote data
        if (isset($sanitized_data['quote_id'])) {
            $quote_id = $sanitized_data['quote_id'];
            $existing_data = get_transient('flettons_quote_' . $quote_id);

            if ($existing_data) {
                // Merge with existing data
                $updated_data = array_merge($existing_data, $sanitized_data);
                set_transient('flettons_quote_' . $quote_id, $updated_data, 24 * HOUR_IN_SECONDS);

                wp_send_json_success(array('message' => 'Survey selection updated'));
            } else {
                wp_send_json_error(array('message' => 'Quote data not found'));
            }
        } else {
            wp_send_json_error(array('message' => 'Quote ID not provided'));
        }
    }

    /**
     * AJAX handler for saving customer data to CRM
     */
    public function save_customer_to_crm()
    {
        // Check nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'flettons_customer_signup_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed'));
        }

        // Parse form data
        $form_data = array();
        parse_str($_POST['form_data'], $form_data);

        // Get quote data from transient
        $quote_id = isset($form_data['quote_id']) ? sanitize_text_field($form_data['quote_id']) : '';

        $quote_data = get_transient('flettons_quote_' . $quote_id);


        if (!$quote_data) {
            wp_send_json_error(array('message' => 'Quote data expired or not found. Please start over.'));
            return;
        }


        // Merge form data with quote data
        $combined_data = array_merge($quote_data, $form_data);

        // Initialize the API class
        $api = new Flettons_API();

        // Save to CRM and get contact ID
        $contact_id = $api->save_to_crm($combined_data);

        if (!$contact_id) {
            wp_send_json_error(array('message' => 'Failed to save data to CRM'));
            return;
        }

        // Create order/invoice
        $order_id = $api->create_order($contact_id, $combined_data);

        if (!$order_id) {
            wp_send_json_error(array('message' => 'Failed to create order'));
            return;
        }

        // Apply tag based on survey level
        $level = isset($combined_data['level']) ? absint($combined_data['level']) : 2;
        $api->apply_level_tags($contact_id, $level);

        // Create Stripe checkout session
        $stripe_url = $api->create_stripe_checkout($combined_data, $contact_id, $order_id);

        if (!$stripe_url) {
            wp_send_json_error(array('message' => 'Failed to create payment session'));
            return;
        }

        // Success - return redirect URL
        wp_send_json_success(array(
            'message' => 'Data saved successfully',
            'redirect_url' => $stripe_url
        ));
    }

    /**
     * Process form submission from customer sign-up form
     */
    public function process_signup_form()
    {
        // Check nonce for security
        if (!isset($_POST['signup_nonce']) || !wp_verify_nonce($_POST['signup_nonce'], 'flettons_signup_nonce')) {
            wp_die('Security check failed', 'Error', array('response' => 403));
        }

        // Get quote ID from form
        $quote_id = isset($_POST['quote_id']) ? sanitize_text_field($_POST['quote_id']) : '';
        $quote_data = get_transient('flettons_quote_' . $quote_id);

        if (!$quote_data) {
            wp_die('Quote data expired or not found. Please start over.', 'Error', array('response' => 400));
            return;
        }

        // Sanitize form data
        $form_data = array();
        foreach ($_POST as $key => $value) {
            if (is_array($value)) {
                $form_data[$key] = array_map('sanitize_text_field', $value);
            } else {
                $form_data[$key] = sanitize_text_field($value);
            }
        }

        // Combine form data with quote data
        $combined_data = array_merge($quote_data, $form_data);

        // Initialize the API class
        $api = new Flettons_API();

        // Save to CRM and get contact ID
        $contact_id = $api->save_to_crm($combined_data);

        if (!$contact_id) {
            wp_die('Failed to save data to CRM. Please try again.', 'Error', array('response' => 500));
            return;
        }

        // Create order/invoice
        $order_id = $api->create_order($contact_id, $combined_data);

        if (!$order_id) {
            wp_die('Failed to create order. Please try again.', 'Error', array('response' => 500));
            return;
        }

        // Apply tag based on survey level
        $level = isset($combined_data['level']) ? absint($combined_data['level']) : 2;
        $api->apply_level_tags($contact_id, $level);

        // Create Stripe checkout session
        $stripe_url = $api->create_stripe_checkout($combined_data, $contact_id, $order_id);

        if (!$stripe_url) {
            wp_die('Failed to create payment session. Please try again.', 'Error', array('response' => 500));
            return;
        }

        // Redirect to Stripe checkout
        wp_redirect($stripe_url);
        exit;
    }

    /**
     * Sanitize form data
     */
    private function sanitize_form_data($data)
    {
        $sanitized = array();

        // Basic fields sanitization
        if (isset($data['first_name'])) {
            $sanitized['first_name'] = sanitize_text_field($data['first_name']);
        }

        if (isset($data['last_name'])) {
            $sanitized['last_name'] = sanitize_text_field($data['last_name']);
        }

        if (isset($data['email_address'])) {
            $sanitized['email_address'] = sanitize_email($data['email_address']);
        }

        if (isset($data['telephone_number'])) {
            $sanitized['telephone_number'] = preg_replace('/[^0-9]/', '', $data['telephone_number']);
        }

        if (isset($data['countryCode'])) {
            $sanitized['countryCode'] = sanitize_text_field($data['countryCode']);
        }

        if (isset($data['full_address'])) {
            $sanitized['full_address'] = sanitize_textarea_field($data['full_address']);
        }

        if (isset($data['house_or_flat'])) {
            $sanitized['house_or_flat'] = sanitize_text_field($data['house_or_flat']);
        }

        if (isset($data['number_of_bedrooms'])) {
            $sanitized['number_of_bedrooms'] = absint($data['number_of_bedrooms']);
        }

        if (isset($data['market_value'])) {
            $sanitized['market_value'] = floatval($data['market_value']);
        }

        // Boolean fields
        $boolean_fields = array('listed_building', 'extended', 'over1650');
        foreach ($boolean_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = ($data[$field] === 'Yes' || $data[$field] === '1' || $data[$field] === true);
            }
        }

        if (isset($data['sqft_area'])) {
            $sanitized['sqft_area'] = absint($data['sqft_area']);
        }

        $level1_price = $this->get_setting('level-1') ?? 0;
        $level2_price = $this->get_setting('level-2') ?? 0;
        $level3_price = $this->get_setting('level-3') ?? 0;
        $level4_price = $this->get_setting('level-4') ?? 0;

        $market_value_percentage = $this->get_setting('market-value-percentage') ?? 0;
        $market_value_percentage_2 = $this->get_setting('market-value-percentage-2') ?? 0;
        $market_value_percentage_3 = $this->get_setting('market-value-percentage-3') ?? 0;
        $market_value_percentage_4 = $this->get_setting('market-value-percentage-4') ?? 0;

        $listing_fee = $this->get_setting('listinsg-fee') ?? 0;
        $extra_sqft = $this->get_setting('extra-sqft') ?? 0;
        $extra_rooms = $this->get_setting('extra-rooms') ?? 0;

        //extra bedrooms
        if (isset($data['number_of_bedrooms']) && $data['number_of_bedrooms'] > 4) {
            $level1_price += ($data['number_of_bedrooms'] - 4) * $extra_rooms;
            $level2_price += ($data['number_of_bedrooms'] - 4) * $extra_rooms;
            $level3_price += ($data['number_of_bedrooms'] - 4) * $extra_rooms;
            $level4_price += ($data['number_of_bedrooms'] - 4) * $extra_rooms;
        }

        //listing_fee
        if (isset($data['listed_building']) && $data['listed_building'] == 'Yes') {
            $level1_price += $listing_fee;
            $level2_price += $listing_fee;
            $level3_price += $listing_fee;
            $level4_price += $listing_fee;
        }

        //exra sqft
        if (isset($data['sqft_area']) && $data['sqft_area'] > 1650) {
            // $level1_price += ($data['sqft_area'] - 1650) * $extra_sqft;
            $level2_price += ($data['sqft_area'] - 1650) * $extra_sqft;
            $level3_price += ($data['sqft_area'] - 1650) * $extra_sqft;
            $level4_price += ($data['sqft_area'] - 1650) * $extra_sqft;
        }

        // market value
        if (isset($data['market_value'])) {
            $level1_price += $data['market_value'] * $market_value_percentage;
            $level2_price += $data['market_value'] * $market_value_percentage_2;
            $level3_price += $data['market_value'] * $market_value_percentage_3;
            $level4_price += $data['market_value'] * $market_value_percentage_4;
        }


        $sanitized['total1'] = number_format((float)$level1_price, 2, '.', '');
        $sanitized['total2'] = number_format((float)$level2_price, 2, '.', '');
        $sanitized['total3'] = number_format((float)$level3_price, 2, '.', '');
        $sanitized['total4'] = number_format((float)$level4_price, 2, '.', '');

        return $sanitized;
    }

    /**
     * Get a setting from the options
     */
    public function get_setting($key)
    {
        $settings = get_option('flettons_survey_settings', array());

        // Handle nested settings with dot notation (e.g., 'api_keys.google_places')
        if (strpos($key, '.') !== false) {
            $parts = explode('.', $key);
            $value = $settings;

            foreach ($parts as $part) {
                if (isset($value[$part])) {
                    $value = $value[$part];
                } else {
                    return null;
                }
            }

            return $value;
        }

        return isset($settings[$key]) ? $settings[$key] : null;
    }
}
