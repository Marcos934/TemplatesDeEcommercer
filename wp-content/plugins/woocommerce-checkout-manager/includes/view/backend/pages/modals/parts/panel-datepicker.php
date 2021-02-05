<# if (data.type == 'date') { #>
<div class="panel woocommerce_options_panel <# if (data.panel != 'datepicker') { #>hidden<# } #>">
     <div class="options_group wooccm-premium">
    <p class="form-field">
      <label><?php esc_html_e('Enhance', 'woocommerce-checkout-manager'); ?></label>
      <input <# if ( data.select2 ) { #>checked="checked"<# } #> class="media-modal-render-panels" type="checkbox" name="select2" value="1">
        <span class="description hidden"><?php esc_html_e('Enhance date behaviour with datepicker.', 'woocommerce-checkout-manager'); ?></span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p> 
  </div>
  <div class="options_group wooccm-premium">
    <p class="form-field">
      <label><?php esc_html_e('Date format', 'woocommerce-checkout-manager'); ?></label>
      <input <# if ( !data.select2 ) { #>disabled="disabled"<# } #> class="short" type="text" placeholder="dd-mm-yy" name="date_format" value="{{data.date_format}}">
        <span class="description"><a target="_blank" href="https://quadlayers.com/documentation/woocommerce-checkout-manager/fields/datepicker/?utm_source=wooccm_admin">Documentation on date and time formatting</a>.</span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
  </div>
  <div class="options_group wooccm-premium">
    <p class="form-field">
      <label><?php esc_html_e('Date limit', 'woocommerce-checkout-manager'); ?></label>
      <select <# if ( !data.select2 ) { #>disabled="disabled"<# } #> class="media-modal-render-panels select short" name="date_limit">
        <option <# if ( data.date_limit == 'variable' ) { #>selected="selected"<# } #> value="variable"><?php esc_html_e('Since current date', 'woocommerce-checkout-manager'); ?></option>
        <option <# if ( data.date_limit == 'fixed' ) { #>selected="selected"<# } #> value="fixed"><?php esc_html_e('Between fixed dates', 'woocommerce-checkout-manager'); ?></option>
      </select>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
  </div>
  <div class="options_group wooccm-premium">
    <p class="form-field">
      <label><?php esc_html_e('Days disable', 'woocommerce-checkout-manager'); ?></label>
      <select <# if ( !data.select2 ) { #>disabled="disabled"<# } #> class="wooccm-enhanced-select" name="date_limit_days[]" data-placeholder="<?php esc_attr_e('Disable week days', 'woocommerce-checkout-manager'); ?>" data-allow_clear="true" multiple="multiple">
      <?php for ($day_index = 0; $day_index <= 6; $day_index++) : ?>
          <option <# if ( _.contains(data.date_limit_days, '<?php echo esc_attr($day_index); ?>') ) { #>selected="selected"<# } #> value="<?php echo esc_attr($day_index); ?>"><?php echo $wp_locale->get_weekday($day_index); ?></option>
          <?php endfor; ?>
      </select>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
  </div>
  <# if ( data.date_limit == 'variable' ) { #>
  <div class="options_group wooccm-premium wooccm-enhanced-between-days">
    <p class="form-field dimensions_field">
      <label><?php esc_html_e('Between days', 'woocommerce-checkout-manager'); ?></label>
      <span class="wrap">
        <input <# if ( !data.select2 ) { #>disabled="disabled"<# } #> style="width:48.1%" type="number" placeholder="-3" min="-365" max="365" class="short " name="date_limit_variable_min" value="{{data.date_limit_variable_min}}" required>
          <input <# if ( !data.select2 ) { #>disabled="disabled"<# } #> style="width:48.1%;margin: 0;" type="number" placeholder="3" min="-365" max="365" class="short" name="date_limit_variable_max" value="{{data.date_limit_variable_max}}" required>
      </span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
  </div>
  <# } else { #>
  <div class="options_group wooccm-premium wooccm-enhanced-between-dates">
    <p class="form-field dimensions_field">
      <label><?php esc_html_e('Between dates', 'woocommerce-checkout-manager'); ?></label>
      <span class="wrap">
        <input <# if ( !data.select2 ) { #>disabled="disabled"<# } #> style="width:48.1%" type="text" class="short " name="date_limit_fixed_min" value="{{data.date_limit_fixed_min}}" placeholder="<?php esc_html_e('From… YYYY-MM-DD', 'woocommerce-checkout-manager'); ?>" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])">
          <input <# if ( !data.select2 ) { #>disabled="disabled"<# } #> style="width:48.1%;margin: 0;" type="text" class="short" name="date_limit_fixed_max" value="{{data.date_limit_fixed_max}}" placeholder="<?php esc_html_e('From… YYYY-MM-DD', 'woocommerce-checkout-manager'); ?>" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])">
      </span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
  </div>
  <# } #>
</div>
<# } #>