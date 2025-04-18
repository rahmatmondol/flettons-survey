<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="container">
    <!-- 
    <pre>
        <?php print_r($quote_data); ?>
    </pre> -->

    <form class="quote-f" action="">
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
                            <div class="level1-price level-price">£<?php echo esc_html($quote_data['total1']); ?></div>
                            <a href="<?php echo site_url() . '/flettons-order/?email=' . $quote_data['email_address'] . '&contact_id=' . $contact_id . '&total=' . $quote_data['total1'] . '&level=1'; ?>" class="btn btn-primary click-level1 btn-style">Buy Now</a>
                        </div>
                        <div class="col-sm-3 level-choice click-level2 border">
                            <h3 class="frm_pos_top frm_section_spacing">Homebuyer Report</h3>
                            <img alt="Flettons Survyors Full Building Survey Report" title="Level 2" src="<?php echo FLETTONS_SURVEY_PLUGIN_URL; ?>assets/images/FLETTONS-LEVEL-2-4.png">
                            <label for="field_fullsurvey2" class="frm_primary_label">Level 2 <span class="frm_required"></span> </label>
                            <div class="level2-price level-price">£<?php echo esc_html($quote_data['total2']); ?></div>
                            <a href="<?php echo site_url() . '/flettons-order/?email=' . $quote_data['email_address'] . '&contact_id=' . $contact_id . '&total=' . $quote_data['total2'] . '&level=2'; ?>" class="btn btn-primary click-level2 btn-style">Buy Now</a>
                        </div>
                        <div class="col-sm-3 level-choice click-level3 border">
                            <h3 class="frm_pos_top frm_section_spacing">Building Survey</h3>
                            <img alt="Flettons Survyors Full Building Survey Report" title="Level 3" src="<?php echo FLETTONS_SURVEY_PLUGIN_URL; ?>assets/images/FLETTONS-LEVEL-3-4.png">
                            <label for="field_fullsurvey2" class="frm_primary_label">Level 3 <span class="frm_required"></span> </label>
                            <div class="level3-price level-price">£<?php echo esc_html($quote_data['total3']); ?></div>
                            <div class="btn btn-primary click-level3 btn-style">Choose Add-ons</div>
                        </div>
                        <div class="col-sm-3 level-choice click-level4 border">
                            <h3 class="frm_pos_top frm_section_spacing">The Building Survey Plus</h3>
                            <img alt="Flettons Survyors Full Building Survey Report" title="Level 3+" src="<?php echo FLETTONS_SURVEY_PLUGIN_URL; ?>assets/images/FLETTONS-LEVEL-3-4-1.png">
                            <label for="field_fullsurvey2" class="frm_primary_label">Level 3+ <span class="frm_required"></span> </label>
                            <div class="level4-price level-price">£<?php echo esc_html($quote_data['total4']); ?></div>
                            <a href="<?php echo site_url() . '/flettons-order/?email=' . $quote_data['email_address'] . '&contact_id=' . $contact_id . '&total=' . $quote_data['total4'] . '&level=4'; ?>" class="btn btn-primary click-level4 btn-style">Buy Now</a>
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
                        $breakdown_cost = isset($settings['breakdown-of-estimated-repair-costs']) ? $settings['breakdown-of-estimated-repair-costs'] : '0';
                        $aerial_cost = isset($settings['aerial-roof-and-chimney']) ? $settings['aerial-roof-and-chimney'] : '0';
                        $insurance_cost = isset($settings['insurance-reinstatement-valuation']) ? $settings['insurance-reinstatement-valuation'] : '0';
                        $thermal_cost = isset($settings['thermal-images']) ? $settings['thermal-images'] : '0';
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

                        <div class="level3-price level-price addons">£<?php echo esc_html($quote_data['total3']); ?></div>
                        <div class="btn-style-group">
                            <div class="btn btn-primary level-3-confirm btn-style">Buy Now</div>
                            <div class="btn btn-primary back btn-style">Back</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>