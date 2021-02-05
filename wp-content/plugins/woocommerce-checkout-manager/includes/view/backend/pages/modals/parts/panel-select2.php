<# if (_.contains(<?php echo json_encode(array('select', 'multiselect')); ?>, data.type)) { #>
<div class="panel woocommerce_options_panel <# if (data.panel != 'select2') { #>hidden<# } #>">
     <div class="options_group wooccm-premium">
    <p class="form-field">
      <label><?php esc_html_e('Select2', 'woocommerce-checkout-manager'); ?></label>
      <input <# if ( data.select2 ) { #>checked="checked"<# } #> type="checkbox" name="select2" value="1">
        <span class="description hidden"><?php esc_html_e('Enhance select behaviour with select2.', 'woocommerce-checkout-manager'); ?></span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p> 
  </div>
  <div class="options_group wooccm-premium">
    <p class="form-field">
      <label><?php esc_html_e('Allow clear', 'woocommerce-checkout-manager'); ?></label>
      <input <# if ( data.select2_allowclear ) { #>checked="checked"<# } #> type="checkbox" name="select2_allowclear" value="1">
        <span class="description hidden"><?php esc_html_e('Provides support for clearable selections.', 'woocommerce-checkout-manager'); ?></span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>                   
    <p class="form-field">
      <label><?php esc_html_e('Allow search', 'woocommerce-checkout-manager'); ?></label>
      <input <# if ( data.select2_search ) { #>checked="checked"<# } #> type="checkbox" name="select2_search" value="1">
        <span class="description hidden"><?php esc_html_e('Display the search box for options.', 'woocommerce-checkout-manager'); ?></span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
  </div>
  <div class="options_group wooccm-premium">
    <p class="form-field">
      <label><?php esc_html_e('Select on close', 'woocommerce-checkout-manager'); ?></label>
      <input <# if ( data.select2_selectonclose ) { #>checked="checked"<# } #> type="checkbox" name="select2_selectonclose" value="1">
        <span class="description hidden"><?php esc_html_e('Implements automatic selection when the dropdown is closed.', 'woocommerce-checkout-manager'); ?></span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>                   
    <p class="form-field">
      <label><?php esc_html_e('Close on select', 'woocommerce-checkout-manager'); ?></label>
      <input <# if ( data.select2_closeonselect ) { #>checked="checked"<# } #> type="checkbox" name="select2_closeonselect" value="1">
        <span class="description hidden"><?php esc_html_e('Controls whether the dropdown is closed after a selection is made.', 'woocommerce-checkout-manager'); ?></span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>    
  </div>
</div>
<# } #>