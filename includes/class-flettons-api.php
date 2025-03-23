<?php

/**
 * Flettons Survey API Integration Class
 */
class Flettons_API
{
    /**
     * API key for Keap/Infusionsoft
     */
    private $keap_api_key;

    /**
     * API key for Stripe
     */
    private $stripe_api_key;

    /**
     * API base URL
     */
    private $api_base;

    /**
     * Constructor
     */
    public function __construct()
    {
        $settings = get_option('flettons_survey_settings', array());

        $this->keap_api_key = isset($settings['api_keys_keap']) ? $settings['api_keys_keap'] : '';
        $this->stripe_api_key = isset($settings['api_keys_stripe']) ? $settings['api_keys_stripe'] : '';
        $this->api_base = 'https://api.infusionsoft.com/crm/rest/v1';
    }

    /**
     * Save customer data to CRM
     */
    public function save_to_crm($data)
    {
        // Find or create contact in Keap/Infusionsoft
        $contact_id = $this->find_or_create_contact($data);

        return $contact_id;
    }

    /**
     * Find or create contact in Keap/Infusionsoft
     */
    private function find_or_create_contact($data)
    {
        if (empty($data['email_address']) && empty($data['email'])) {
            return false;
        }

        // Use email_address if available, otherwise use email
        $email = !empty($data['email_address']) ? $data['email_address'] : $data['email'];

        // Check if contact exists
        $contact = $this->find_contact_by_email($email);

        if ($contact) {
            // Update existing contact
            return $this->update_contact($contact['id'], $data);
        } else {
            // Create new contact
            return $this->create_contact($data);
        }
    }

    /**
     * Find contact by email
     */
    private function find_contact_by_email($email)
    {
        $url = $this->api_base . "/contacts?email=" . urlencode($email);

        $response = $this->make_api_request($url, 'GET');

        if (isset($response['contacts']) && !empty($response['contacts'])) {
            return $response['contacts'][0];
        }

        return false;
    }


    /**
     * Update existing contact
     */
    private function update_contact($contact_id, $data)
    {
        $url = $this->api_base . "/contacts/{$contact_id}";

        $contact_data = $this->prepare_contact_data($data);

        $response = $this->make_api_request($url, 'PATCH', $contact_data);

        return isset($response['id']) ? $response['id'] : false;
    }

    /**
     * Create new contact
     */
    private function create_contact($data)
    {
        $url = $this->api_base . "/contacts";

        $contact_data = $this->prepare_contact_data($data);

        $response = $this->make_api_request($url, 'POST', $contact_data);

        return isset($response['id']) ? $response['id'] : false;
    }

    /**
     * Prepare contact data for API request
     */
    private function prepare_contact_data($data)
    {
        // Get email from either email_address or email field
        $email = !empty($data['email_address']) ? $data['email_address'] : (
            !empty($data['email']) ? $data['email'] : ''
        );

        $contact_data = array(
            'email_addresses' => array(
                array(
                    'email' => $email,
                    'field' => 'EMAIL1'
                )
            ),
            'custom_fields' => array()
        );

        // Add name if available
        if (!empty($data['first_name'])) {
            $contact_data['given_name'] = $data['first_name'];
        }

        if (!empty($data['last_name'])) {
            $contact_data['family_name'] = $data['last_name'];
        }

        // Get phone from either telephone_number or phone field
        $phone = !empty($data['telephone_number']) ? $data['telephone_number'] : (
            !empty($data['phone']) ? $data['phone'] : ''
        );

        // Add phone if available
        if (!empty($phone)) {
            $country_code = !empty($data['countryCode']) ? $data['countryCode'] : '+44';

            // Remove leading + if present
            $country_code = ltrim($country_code, '+');

            // Remove leading 0 from phone if present
            if (substr($phone, 0, 1) === '0') {
                $phone = substr($phone, 1);
            }

            $contact_data['phone_numbers'] = array(
                array(
                    'number' => '+' . $country_code . $phone,
                    'field' => 'PHONE1'
                )
            );
        }

        // Get address from either full_address, current_address, or address field
        $address = !empty($data['full_address']) ? $data['full_address'] : (
            !empty($data['current_address']) ? $data['current_address'] : (
                !empty($data['address']) ? $data['address'] : ''
            )
        );

        // Add address if available
        if (!empty($address)) {
            $postcode = !empty($data['current_postcode']) ? $data['current_postcode'] : (
                !empty($data['postcode']) ? $data['postcode'] : ''
            );

            $contact_data['addresses'] = array(
                array(
                    'line1' => $address,
                    'postal_code' => $postcode,
                    'field' => 'BILLING'
                )
            );
        }

        // Add survey address if available
        if (!empty($data['survey_address'])) {
            $contact_data['custom_fields'][] = array('id' => '191', 'content' => $data['survey_address']);
        }

        // Add market value if available
        if (!empty($data['market_value'])) {
            $contact_data['custom_fields'][] = array('id' => '193', 'content' => $data['market_value']);
        }

        // Add property type if available
        $property_type = !empty($data['house_or_flat']) ? $data['house_or_flat'] : (
            !empty($data['property_type']) ? $data['property_type'] : ''
        );

        if (!empty($property_type)) {
            $contact_data['custom_fields'][] = array('id' => '195', 'content' => $property_type);
        }

        // Add number of bedrooms if available
        $bedrooms = !empty($data['number_of_bedrooms']) ? $data['number_of_bedrooms'] : (
            !empty($data['bedrooms']) ? $data['bedrooms'] : ''
        );

        if (!empty($bedrooms)) {
            $contact_data['custom_fields'][] = array('id' => '197', 'content' => $bedrooms);
        }

        // Add survey level if available
        if (!empty($data['level'])) {
            $contact_data['custom_fields'][] = array('id' => '229', 'content' => $data['level']);
        }

        // Add total if available
        if (!empty($data['total'])) {
            $contact_data['custom_fields'][] = array('id' => '231', 'content' => $data['total']);
        }

        // Add specific concerns if available
        if (!empty($data['specific_concerns'])) {
            $contact_data['custom_fields'][] = array('id' => '242', 'content' => $data['specific_concerns']);
        }

        // Add agent details if available
        if (!empty($data['agent_company'])) {
            $contact_data['custom_fields'][] = array('id' => '244', 'content' => $data['agent_company']);
        }

        if (!empty($data['agent_name'])) {
            $contact_data['custom_fields'][] = array('id' => '246', 'content' => $data['agent_name']);
        }

        if (!empty($data['agent_phone'])) {
            $contact_data['custom_fields'][] = array('id' => '248', 'content' => $data['agent_phone']);
        }

        if (!empty($data['agent_email'])) {
            $contact_data['custom_fields'][] = array('id' => '250', 'content' => $data['agent_email']);
        }

        // Add listed building info if available
        if (isset($data['listed_building'])) {
            $listed = (bool) $data['listed_building'];
            $contact_data['custom_fields'][] = array('id' => '203', 'content' => $listed ? 'yes' : 'no');
        }

        // Add extended property info if available
        if (isset($data['extended'])) {
            $extended = (bool) $data['extended'];
            $contact_data['custom_fields'][] = array('id' => '252', 'content' => $extended ? 'yes' : 'no');
        }

        // Add redirect URL for payment
        $email_for_url = urlencode($email);
        $total = !empty($data['total']) ? $data['total'] : '0';
        $level = !empty($data['level']) ? $data['level'] : '2';

        $contact_data['custom_fields'][] = array(
            'id' => '234',
            'content' => 'https://flettons.com/flettons/orderform.php?email=' . $email_for_url . '&total=' . $total . '&level=' . $level
        );

        return $contact_data;
    }

    /**
     * Create order/invoice
     */
    public function create_order($contact_id, $data)
    {
        $url = $this->api_base . "/orders";

        $order_data = array(
            'contact_id' => $contact_id,
            'order_title' => 'Survey Quote Level ' . (!empty($data['level']) ? $data['level'] : '2'),
            'order_date' => date('Y-m-d\TH:i:s\Z'),
            'order_items' => array(
                array(
                    'product_id' => '1',
                    'quantity' => 1,
                    'price' => (float) $data['total']
                )
            ),
            'order_type' => 'Online'
        );

        $response = $this->make_api_request($url, 'POST', $order_data);

        if (!isset($response['id'])) {
            return false;
        }

        $order_id = $response['id'];

        // Add order item with price
        $total = !empty($data['total']) ? floatval($data['total']) : 0;
        $this->add_order_item($order_id, $total);

        return $order_id;
    }

    /**
     * Add item to order
     */
    private function add_order_item($order_id, $total)
    {
        $url = $this->api_base . "/orders/{$order_id}/items";

        $item_data = array(
            'description' => 'Survey quote generated from website',
            'product_type' => 'PRODUCT',
            'product_id' => 1,
            'quantity' => 1,
            'price' => (float) $total,
        );

        $response = $this->make_api_request($url, 'POST', $item_data);

        return isset($response['id']);
    }


    /**
     * Apply tags based on survey level
     */
    public function apply_level_tags($contact_id, $level)
    {
        $tag_ids = array();
        $remove_tag_ids = array();

        // Determine which tag to apply and which to remove based on level
        switch ($level) {
            case 1:
                $tag_ids = array(368);
                $remove_tag_ids = array(370, 372, 500);
                break;
            case 2:
                $tag_ids = array(370);
                $remove_tag_ids = array(368, 372, 500);
                break;
            case 3:
                $tag_ids = array(372);
                $remove_tag_ids = array(368, 370, 500);
                break;
            case 4:
                $tag_ids = array(500);
                $remove_tag_ids = array(368, 370, 372);
                break;
        }

        // Apply the new tag
        if (!empty($tag_ids)) {
            $url = $this->api_base . "/contacts/{$contact_id}/tags";
            $tag_data = array('tagIds' => $tag_ids);
            $this->make_api_request($url, 'POST', $tag_data);
        }

        // Remove other level tags
        if (!empty($remove_tag_ids)) {
            $ids = implode(',', $remove_tag_ids);
            $url = $this->api_base . "/contacts/{$contact_id}/tags/" . urlencode($ids);
            $this->make_api_request($url, 'DELETE');
        }

        return true;
    }

    /**
     * Create Stripe checkout session
     */
    public function create_stripe_checkout($data, $contact_id, $order_id)
    {
        if (!class_exists('Stripe\Stripe')) {
            require_once FLETTONS_SURVEY_PLUGIN_DIR . 'includes/stripe-php/init.php';
        }

        try {
            \Stripe\Stripe::setApiKey($this->stripe_api_key);

            // Get the email and total from data
            $email = !empty($data['email_address']) ? $data['email_address'] : (
                !empty($data['email']) ? $data['email'] : ''
            );

            $total = !empty($data['total']) ? floatval($data['total']) : 0;

            // Create session
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'gbp',
                            'unit_amount' => $total * 100,
                            'product_data' => [
                                'name' => 'Flettons'
                            ]
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'allow_promotion_codes' => 'true',
                'customer_email' => $email,
                'success_url' => site_url('/flettons-payment-confirmation?amount=' . $total . '&contact_id=' . $contact_id . '&order_id=' . $order_id),
                'cancel_url' => site_url('?strp_error=stripe'),
            ]);

            return $session->url;
        } catch (\Exception $e) {
            error_log('Stripe error: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Handle Stripe payment confirmation
     */
    public function handle_stripe_payment_confirmation($amount, $contactId, $orderId)
    {

        function getCustomFieldValue($customFields, $fieldId)
        {
            foreach ($customFields as $field) {
                if ($field['id'] == $fieldId) {
                    return $field['content'];
                }
            }
            return null;
        }

        $url = $this->api_base . "/contacts/{$contactId}?optional_properties=custom_fields";

        // contact data 
        // $conDat = makeCurlRequest($contactUrl, 'GET', null, $headers);

        $conDat = $this->make_api_request($url, 'GET');

        error_log('conDat: ' . print_r($conDat));

        $conID = isset($conDat['id']) ? $conDat['id'] : null;

        if ($conID) {
            $assignTagUrl = $this->api_base . "/contacts/{$conID}/tags";
            $tagData = ['tagIds' => [361]];

            // add tag 
            $response = $this->make_api_request($assignTagUrl, 'POST', $tagData);
            error_log('add tag 361: ' . json_encode($response));

            $assignTagUrl = $this->api_base . "/contacts/{$conID}/tags";
            $tagData = ['tagIds' => [363]];
            // add tag 
            $response = $this->make_api_request($assignTagUrl, 'POST', $tagData);

            error_log('add tag 363: ' . json_encode($response));
        }

        $url = $this->api_base . "/orders/$orderId/payments";

        $currentDate = date("Y-m-d\TH:i:s\Z");
        $pDate = $currentDate;
        $amount = (float) str_replace(',', '', $amount);
        $paymentAmountString = number_format($amount, 2, '.', ''); // e.g., "10.75"

        $data = [
            "apply_to_commissions" => false,
            "charge_now" => false,
            "credit_card_id" => $orderId,
            "date" => $pDate,
            "notes" => "Fletton order Payment",
            "payment_amount" => $paymentAmountString,
            "payment_gateway_id" => "Stripe",
            "payment_method_type" => "CREDIT_CARD"
        ];


        $response = $this->make_api_request($url, 'POST', $data);
        error_log('add payment: ' . json_encode($response));

        // Redirect to success page
        wp_redirect(site_url('/thank-you'));
        exit;
    }



    /**
     * Make API request to Keap/Infusionsoft
     */
    private function make_api_request($url, $method = 'GET', $data = null)
    {
        $headers = [
            'Authorization: Bearer ' . $this->keap_api_key,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Disable SSL verification for development environments
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if ($data && ($method === 'POST' || $method === 'PATCH' || $method === 'PUT')) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            error_log('API Error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        // Handle response based on status code
        if ($http_code >= 200 && $http_code < 300) {
            return json_decode($response, true);
        } elseif ($method === 'DELETE' && $http_code === 204) {
            return true;
        } else {
            error_log('API Error: ' . $response);
            return false;
        }
    }
}
