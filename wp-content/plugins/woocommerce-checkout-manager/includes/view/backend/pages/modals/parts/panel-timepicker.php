<# if (data.type == 'time') { #>
<div class="panel woocommerce_options_panel <# if (data.panel != 'timepicker') { #>hidden<# } #>">
  <div class="options_group wooccm-premium">
    <p class="form-field">
      <label><?php esc_html_e('Enhance', 'woocommerce-checkout-manager'); ?></label>
      <input <# if ( data.select2 ) { #>checked="checked"<# } #> class="media-modal-render-panels" type="checkbox" name="select2" value="1">
        <span class="description hidden"><?php esc_html_e('Enhance time behaviour with timepicker.', 'woocommerce-checkout-manager'); ?></span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p> 
  </div>
  <div class="options_group wooccm-premium">
    <p class="form-field">
      <label><?php esc_html_e('Hour start', 'woocommerce-checkout-manager'); ?></label>
      <input <# if ( !data.select2 ) { #>disabled="disabled"<# } #> class="short" type="number" min="0" max="24" placeholder="6" step="1" name="time_limit_start" value="{{data.time_limit_start}}">
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
    <p class="form-field">
      <label><?php esc_html_e('Hour end', 'woocommerce-checkout-manager'); ?></label>
      <input <# if ( !data.select2 ) { #>disabled="disabled"<# } #> class="short" type="number" min="0" max="24" placeholder="9" step="1" name="time_limit_end" value="{{data.time_limit_end}}">
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
    <p class="form-field">
      <label><?php esc_html_e('Minutes interval', 'woocommerce-checkout-manager'); ?></label>
      <input <# if ( !data.select2 ) { #>disabled="disabled"<# } #> class="short" type="number" min="0" max="60" step="5" placeholder="15" name="time_limit_interval" value="{{data.time_limit_interval}}">
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
  </div>
  <div class="options_group wooccm-premium">
    <p class="form-field">
      <label><?php esc_html_e('format 24hs', 'woocommerce-checkout-manager'); ?></label>
      <input <# if ( data.time_format_ampm ) { #>checked="checked"<# } #> class="media-modal-render-panels" type="checkbox" name="time_format_ampm" value="1" >
        <span class="description hidden"><?php esc_html_e('Time Format AM/PM', 'woocommerce-checkout-manager'); ?></span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p> 
  </div>
</div>
<# } #>