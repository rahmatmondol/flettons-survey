<?php

/**
 * Flettons Survey Admin Class
 */
class Flettons_Admin
{
    /**
     * Initialize the admin functionality
     */
    public function init()
    {
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // Register settings
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Add admin menu page
     */
    public function add_admin_menu()
    {
        add_menu_page(
            'Flettons Survey Settings',
            'Flettons Survey',
            'manage_options',
            'flettons-survey',
            array($this, 'settings_page'),
            'dashicons-clipboard',
            30
        );
    }

    /**
     * Register plugin settings
     */
    public function register_settings()
    {
        register_setting('flettons_survey_settings', 'flettons_survey_settings');

        // Add sections
        add_settings_section(
            'flettons_pricing_section',
            'Survey Pricing Settings',
            array($this, 'pricing_section_callback'),
            'flettons-survey'
        );

        add_settings_section(
            'flettons_api_section',
            'API Settings',
            array($this, 'api_section_callback'),
            'flettons-survey'
        );

        // Pricing fields
        $pricing_fields = array(
            'level-1' => 'Level 1 Base Price',
            'level-2' => 'Level 2 Base Price',
            'level-3' => 'Level 3 Base Price',
            'level-4' => 'Level 4 Base Price',
            'market-value-percentage' => 'Level 1 Market Value Percentage',
            'market-value-percentage-2' => 'Level 2 Market Value Percentage',
            'market-value-percentage-3' => 'Level 3 Market Value Percentage',
            'market-value-percentage-4' => 'Level 4 Market Value Percentage',
            'breakdown-of-estimated-repair-costs' => 'Repair Costs Breakdown Cost',
            'aerial-roof-and-chimney' => 'Aerial Roof and Chimney Cost',
            'insurance-reinstatement-valuation' => 'Insurance Reinstatement Valuation Cost',
            'thermal-images' => 'Thermal Images Cost',
            'listinsg-fee' => 'Listing Fee Cost',
            'extra-sqft' => 'Extra Sqft Cost',
            'reception-rooms' => 'Extra Reception Room Cost',
            'extra-rooms' => 'Extra Rooms Cost'
        );

        foreach ($pricing_fields as $id => $label) {
            add_settings_field(
                $id,
                $label,
                array($this, 'render_pricing_field'),
                'flettons-survey',
                'flettons_pricing_section',
                array('id' => $id, 'label' => $label, 'step' => '0.000001')
            );
        }

        // API fields
        $api_fields = array(
            'api_keys_keap' => 'Keap/Infusionsoft API Key',
            'api_keys_stripe' => 'Stripe API Key',
            'api_keys_google_places' => 'Google Places API Key'
        );

        foreach ($api_fields as $id => $label) {
            add_settings_field(
                $id,
                $label,
                array($this, 'render_api_field'),
                'flettons-survey',
                'flettons_api_section',
                array('id' => $id, 'label' => $label)
            );
        }
    }

    /**
     * Pricing section callback
     */
    public function pricing_section_callback()
    {
        echo '<p>Configure the pricing for different survey types and additional options.</p>';
    }

    /**
     * API section callback
     */
    public function api_section_callback()
    {
        echo '<p>Enter your API keys for various services used by the plugin.</p>';
    }

    /**
     * Render pricing field
     */
    public function render_pricing_field($args)
    {
        $id = $args['id'];
        $settings = get_option('flettons_survey_settings', array());
        $value = isset($settings[$id]) ? $settings[$id] : '';

        echo '<input type="number" step="0.000001" id="' . esc_attr($id) . '" name="flettons_survey_settings[' . esc_attr($id) . ']" value="' . esc_attr($value) . '" class="regular-text" />';
    }

    /**
     * Render API field
     */
    public function render_api_field($args)
    {
        $id = $args['id'];
        $settings = get_option('flettons_survey_settings', array());
        $value = isset($settings[$id]) ? $settings[$id] : '';
        echo '<input type="text" id="' . esc_attr($id) . '" name="flettons_survey_settings[' . esc_attr($id) . ']" value="' . esc_attr($value) . '" class="regular-text" />';
    }

    /**
     * Render the settings page
     */
    public function settings_page()
    {
?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <form method="post" action="options.php">
                <?php settings_fields('flettons_survey_settings'); ?>
                <?php do_settings_sections('flettons-survey'); ?>
                <?php submit_button(); ?>
            </form>

            <div class="flettons-shortcodes-info">
                <h2>Available Shortcodes</h2>
                <p>Use these shortcodes to display the survey forms on your pages:</p>
                <ul>
                    <li><code>[flettons_quote_form]</code> - Displays the survey quote form</li>
                    <li><code>[flettons_listing_page]</code> - Displays the survey options listing page</li>
                    <li><code>[flettons_customer_signup]</code> - Displays the customer sign up form</li>
                    <li><code>[flettons_payment_confirmation]</code> - payment confirmation page</li>
                </ul>
            </div>
        </div>
<?php
    }
}
