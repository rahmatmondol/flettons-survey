<?php

/**
 * Plugin Name: Flettons Survey Plugin
 * Description: A comprehensive survey and quote system for RICS services
 * Version: 2.0.0
 * Author: Rahmat Mondol
 * Author URI: https://rahmatmondol.com
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('FLETTONS_SURVEY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FLETTONS_SURVEY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FLETTONS_SURVEY_VERSION', '1.0.0');

// Include required files
require_once FLETTONS_SURVEY_PLUGIN_DIR . 'includes/class-flettons-survey.php';
require_once FLETTONS_SURVEY_PLUGIN_DIR . 'includes/class-flettons-admin.php';
require_once FLETTONS_SURVEY_PLUGIN_DIR . 'includes/class-flettons-api.php';

/**
 * Initialize the plugin
 */
function flettons_survey_init()
{
    // Create the main plugin instance
    $plugin = new Flettons_Survey();
    $plugin->init();

    // Initialize admin if in admin area
    if (is_admin()) {
        $admin = new Flettons_Admin();
        $admin->init();
    }
}
add_action('plugins_loaded', 'flettons_survey_init');

/**
 * Plugin activation
 */
function flettons_survey_activate()
{
    // Set default settings
    $default_settings = array(
        'level-1' => 349.00,
        'level-2' => 499.38,
        'level-3' => 611.73,
        'level-4' => 1024.07,
        'market-value-percentage' => 0.0,
        'market-value-percentage-2' => 0.0004,
        'market-value-percentage-3' => 0.0005,
        'market-value-percentage-4' => 0.0006,
        'number-of-bedrooms' => 50,
        'reception-rooms' => 50,
        'number-of-storeys' => 50,
        'listed-building' => 300,
        'conservation-area' => 100,
        'breakdown-of-estimated-repair-costs' => 300,
        'aerial-roof-and-chimney' => 200,
        'insurance-reinstatement-valuation' => 200,
        'thermal-images' => 250,
        'listinsg-fee' => 250,
        'extra-sqft' => 250,
        'api_keys' => array(
            'keap' => '',
            'stripe' => '',
            'google_places' => ''
        )
    );

    // Only set defaults if they don't exist
    if (!get_option('flettons_survey_settings')) {
        update_option('flettons_survey_settings', $default_settings);
    }

    // Create necessary pages if they don't exist
    $pages = array(
        'flettons-quote-form' => array(
            'title' => 'Quote Form',
            'content' => '[flettons_quote_form]',
        ),
        'flettons-listing-page' => array(
            'title' => 'Listing',
            'content' => '[flettons_listing_page]',
        ),
        'flettons-customer-signup' => array(
            'title' => 'Customer Sign Up',
            'content' => '[flettons_customer_signup]',
        ),
        'flettons-payment-confirmation' => array(
            'title' => 'Confirmation',
            'content' => '[flettons_payment_confirmation]',
        ),
        'flettons-order' => array(
            'title' => 'Order',
            'content' => '[flettons_order]',
        ),
        'thank-you' => array(
            'title' => 'Thank You',
            'content' => '<p>Thank you for your order! We will be in touch soon with the details.</p>',
        ),
    );

    foreach ($pages as $slug => $page_data) {
        // Check if the page exists
        $page_check = get_page_by_path($slug);

        if (!$page_check) {
            // Create the page
            wp_insert_post(
                array(
                    'post_title'     => $page_data['title'],
                    'post_content'   => $page_data['content'],
                    'post_status'    => 'publish',
                    'post_type'      => 'page',
                    'post_name'      => $slug,
                    'comment_status' => 'closed'
                )
            );
        }
    }

    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'flettons_survey_activate');


/**
 * Plugin deactivation
 */
function flettons_survey_deactivate()
{
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'flettons_survey_deactivate');
