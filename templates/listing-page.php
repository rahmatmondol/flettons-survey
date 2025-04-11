<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get settings
$settings = get_option('flettons_survey_settings', array());
$level1_price = isset($settings['level-1']) ? $settings['level-1'] : '349.00';
$level2_price = isset($settings['level-2']) ? $settings['level-2'] : '499.38';
$level3_price = isset($settings['level-3']) ? $settings['level-3'] : '611.73';
$level4_price = isset($settings['level-4']) ? $settings['level-4'] : '1024.07';

?>

<div class="container">
    <form class="quote-f" action="">
        <div class="form1" style="display: none;">
            <div class="g-recaptcha" data-sitekey="<?php echo esc_attr($settings['api_keys']['recaptcha'] ?? ''); ?>"></div>
            <input type="hidden" id="pricing-data"
                data-mv-percent-1="<?php echo esc_attr(isset($settings['market-value-percentage']) ? $settings['market-value-percentage'] : '0.0003'); ?>"
                data-mv-percent-2="<?php echo esc_attr(isset($settings['market-value-percentage-2']) ? $settings['market-value-percentage-2'] : '0.0004'); ?>"
                data-mv-percent-3="<?php echo esc_attr(isset($settings['market-value-percentage-3']) ? $settings['market-value-percentage-3'] : '0.0005'); ?>"
                data-mv-percent-4="<?php echo esc_attr(isset($settings['market-value-percentage-4']) ? $settings['market-value-percentage-4'] : '0.0006'); ?>"
                data-base-1="<?php echo esc_attr($level1_price); ?>"
                data-base-2="<?php echo esc_attr($level2_price); ?>"
                data-base-3="<?php echo esc_attr($level3_price); ?>"
                data-base-4="<?php echo esc_attr($level4_price); ?>"
                data-bedroom-cost="<?php echo esc_attr(isset($settings['number-of-bedrooms']) ? $settings['number-of-bedrooms'] : '50'); ?>"
                data-listed-cost="<?php echo esc_attr(isset($settings['listed-building']) ? $settings['listed-building'] : '300'); ?>"
                data-extended-cost="<?php echo esc_attr(isset($settings['extended']) ? $settings['extended'] : '150'); ?>">
            <div class="quote">
                <div class="quote_text">
                    Get your quotes
                </div>
                <p class="warning" style="display:none; color:red;"></p>
            </div>
            <div class="form-group" style="display:none;">
                <label for="">Choose level</label>
                <select name="level" class="level form-control">
                    <option value="1">Level 1</option>
                    <option value="2" selected="">Level 2</option>
                    <option value="3">Level 3</option>
                    <option value="4">Level 3+</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">First Name</label>
                        <input type="text" class="form-control first_name" name="first_name" required="">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Last Name</label>
                        <input type="text" class="form-control last_name" name="last_name" required="">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Address</label>
                        <input type="text" id="searchTextField" class="form-control full_address pac-target-input" name="full_address" required="" placeholder="Enter a location" autocomplete="off">
                    </div>
                </div>

                <div class="form-group" style="display:none;">
                    <label for="exampleInputEmail1">City</label>
                    <input id="city" type="text" class="form-control city" name="city">
                </div>
                <div class="col-md-6" style="display:none;">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Post Code</label>
                        <input id="postcode" type="text" class="form-control city" name="postcode">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Market Value</label>
                        <input min="6" type="number" class="form-control market_value" name="market_value" required="">
                        <p class="show-warning" style="display:none; color:red; font-weight: bold;">Please enter a six figure number without a comma</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">House or Flat</label>
                        <select class="form-control house_or_flat" name="house_or_flat">
                            <option value="">House or Flat</option>
                            <option>House</option>
                            <option>Flat</option>
                            <option>Maisonette</option>
                            <option>Barn Conversion</option>
                            <option>Warehouse Conversion</option>
                            <option>Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Number of Bedrooms</label>
                        <select class="form-control number_of_bedrooms" name="number_of_bedrooms" required="">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6" style="display:none;">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Reception Rooms</label>
                        <input type="text" class="form-control reception_rooms" name="reception_rooms" required="">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" style="display:none;">
                        <label for="exampleInputEmail1">Number of Storeys (Dwelling Only)</label>
                        <select class="form-control number_of_storeys" name="number_of_storeys" required="">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group" style="display:none;">
                        <label for="exampleInputEmail1">Listed Building</label>
                        <select name="listed_building" class="listed_building form-control">
                            <option value="no">No</option>
                            <option value="yes">Yes</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="overlay">
            <div class="inner">
                <div class="step-1">
                    <div class="row">
                        <div id="frm_field_189_container" class="col-sm-12 text-center" style="color: #f8a000; font-weight: bold;">
                            <h3 class="frm_pos_top frm_section_spacing">Your Flettons Surveyors Quotes</h3>
                            <p>To instruct us, please<br>choose from one of the options below and click Buy Now.</p>
                            <p style="color:black;">The price displayed is the total amount due.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 level-choice click-level1 border">
                            <h3 class="frm_pos_top frm_section_spacing">Roof Report</h3>
                            <img class="img-responsive" alt="Flettons Survyors Full Building Survey Report" title="Level 1" src="<?php echo FLETTONS_SURVEY_PLUGIN_URL; ?>assets/images/ROOF-SURVEY-4.png">
                            <label for="field_fullsurvey2" class="frm_primary_label">Drone Survey <span class="frm_required"></span> </label>
                            <div class="level1-price level-price">£<?php echo esc_html($level1_price); ?></div>
                            <button class="btn btn-primary click-level1 btn-style">Buy Now</button>
                        </div>
                        <div class="col-sm-3 level-choice click-level2 border">
                            <h3 class="frm_pos_top frm_section_spacing">Homebuyer Report</h3>
                            <img alt="Flettons Survyors Full Building Survey Report" title="Level 2" src="<?php echo FLETTONS_SURVEY_PLUGIN_URL; ?>assets/images/FLETTONS-LEVEL-2-4.png">
                            <label for="field_fullsurvey2" class="frm_primary_label">Level 2 <span class="frm_required"></span> </label>
                            <div class="level2-price level-price">£<?php echo esc_html($level2_price); ?></div>
                            <button class="btn btn-primary click-level2 btn-style">Buy Now</button>
                        </div>
                        <div class="col-sm-3 level-choice click-level3 border">
                            <h3 class="frm_pos_top frm_section_spacing">Building Survey</h3>
                            <img alt="Flettons Survyors Full Building Survey Report" title="Level 3" src="<?php echo FLETTONS_SURVEY_PLUGIN_URL; ?>assets/images/FLETTONS-LEVEL-3-4.png">
                            <label for="field_fullsurvey2" class="frm_primary_label">Level 3 <span class="frm_required"></span> </label>
                            <div class="level3-price level-price">£<?php echo esc_html($level3_price); ?></div>
                            <div class="btn btn-primary click-level3 btn-style">Choose Add-ons</div>
                        </div>
                        <div class="col-sm-3 level-choice click-level4 border">
                            <h3 class="frm_pos_top frm_section_spacing">The Building Survey Plus</h3>
                            <img alt="Flettons Survyors Full Building Survey Report" title="Level 3+" src="<?php echo FLETTONS_SURVEY_PLUGIN_URL; ?>assets/images/FLETTONS-LEVEL-3-4-1.png">
                            <label for="field_fullsurvey2" class="frm_primary_label">Level 3+ <span class="frm_required"></span> </label>
                            <div class="level4-price level-price">£<?php echo esc_html($level4_price); ?></div>
                            <button class="btn btn-primary click-level4 btn-style">Buy Now</button>
                        </div>
                    </div>
                </div>
                <div class="step-2" style="display:none;">
                    <div class="row level3">
                        <div class="col-sm-12 text-center">
                            <h3 class="frm_pos_top frm_section_spacing text-bold">Level 3 Add-ons</h3>
                            <div id="frm_field_189_container" class="frm_form_field frm_html_container form-field" style="color: #f8a000; font-weight: bold;">
                                <p>Choose additional services individually to add to your level 3 RICS Building Survey.</p>
                            </div>
                            <span class="quote_text addons">
                                Addons
                            </span>
                        </div>

                        <?php
                        // Get addon prices from settings
                        $breakdown_cost = isset($settings['breakdown-of-estimated-repair-costs']) ? $settings['breakdown-of-estimated-repair-costs'] : '300';
                        $aerial_cost = isset($settings['aerial-roof-and-chimney']) ? $settings['aerial-roof-and-chimney'] : '200';
                        $insurance_cost = isset($settings['insurance-reinstatement-valuation']) ? $settings['insurance-reinstatement-valuation'] : '200';
                        $thermal_cost = isset($settings['thermal-images']) ? $settings['thermal-images'] : '250';
                        ?>
                        <div class="col-md-6 addons">
                            <label>Breakdown of estimated repair costs, improvement costs and provisional costs (£<?php echo esc_html($breakdown_cost); ?>)</label>
                            <select name="breakdown_of_estimated_repair_costs" class="breakdown_of_estimated_repair_costs form-control addon">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-6 addons">
                            <label>Aerial roof and chimney images (£<?php echo esc_html($aerial_cost); ?>)</label>
                            <select name="aerial_roof_and_chimney" class="aerial_roof_and_chimney form-control addon">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-6 addons" style="margin-top:50px">
                            <label>Insurance reinstatement Valuation (Rebuild Cost) (£<?php echo esc_html($insurance_cost); ?>)</label>
                            <select name="insurance_reinstatement_valuation" class="insurance_reinstatement_valuation form-control addon">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>

                        <div class="level3-price level-price addons">£<?php echo esc_html($level3_price); ?></div>
                        <div class="btn-style-group">
                            <div class="btn btn-primary level-3-confirm btn-style">Buy Now</div>
                            <div class="btn btn-primary back btn-style">Back</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="form1" style="display: none;">
            <div class="row last-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email Address</label>
                        <input type="text" class="form-control email_address" name="email_address" required="">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Telephone Number </label>
                        <div class="row">
                            <div class="col-md-4">
                                <select name="countryCode" class="form-control">
                                    <option data-countrycode="GB" value="44" selected="">UK (+44)</option>
                                    <option data-countrycode="US" value="1">USA (+1)</option>
                                    <!-- Additional country codes omitted for brevity -->
                                </select>
                            </div>
                            <div class="col-md-8 telephone-col">
                                <input type="text" class="form-control telephone_number" name="telephone_number" required="">
                            </div>
                        </div>
                    </div>
                </div>

                <input type="text" class="form-control total" name="total" style="display:none;">
                <input type="text" class="form-control total1" name="total1" style="display:none;">
                <input type="text" class="form-control total2" name="total2" style="display:none;">
                <input type="text" class="form-control total3" name="total3" style="display:none;">
                <input type="text" class="form-control total4" name="total4" style="display:none;">

                <input type="text" class="form-control link1" name="link1" style="display:none;">
                <input type="text" class="form-control link2" name="link2" style="display:none;">
                <input type="text" class="form-control link3" name="link3" style="display:none;">
                <input type="text" class="form-control link4" name="link4" style="display:none;">

                <input type="hidden" name="action" value="process_listing_form">
                <?php wp_nonce_field('flettons_listing_form_nonce', 'listing_form_nonce'); ?>

                <button type="submit" class="btn btn-primary submit-form" style="display:none;">Submit</button>
                <div style="width:100%; text-align:center; margin-top:20px;">
                    <div class="privacy-checkbox">
                        <div class="privacy-inner">
                            <p>Your details are never shared.</p>
                        </div>
                    </div>
                    <div class="btn btn-primary pre-submit btn-style">Get Quote</div>
                </div>
            </div>
        </div>
    </form>
</div>