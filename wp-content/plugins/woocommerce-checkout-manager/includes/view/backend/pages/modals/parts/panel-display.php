<div class="panel woocommerce_options_panel <# if (data.panel != 'display') { #>hidden<# } #>">
  <div class="options_group wooccm-premium">
    <p class="form-field">
      <label><?php esc_html_e('Hide on account', 'woocommerce-checkout-manager'); ?></label>
      <input <# if (data.hide_account) { #>checked="checked"<# } #> type="checkbox" name="hide_account" value="1">
        <span class="description hidden" style="display: inline-block"><?php esc_html_e('Hide this field on the account page', 'woocommerce-checkout-manager'); ?></span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
    <p class="form-field">
      <label><?php esc_html_e('Hide on checkout', 'woocommerce-checkout-manager'); ?></label>
      <input <# if (data.hide_checkout) { #>checked="checked"<# } #> type="checkbox" name="hide_checkout" value="1">
        <span class="description hidden" style="display: inline-block"><?php esc_html_e('Hide this field on the checkout page', 'woocommerce-checkout-manager'); ?></span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
    <p class="form-field">
      <label><?php esc_html_e('Hide on orders', 'woocommerce-checkout-manager'); ?></label>
      <input <# if (data.hide_order) { #>checked="checked"<# } #> type="checkbox" name="hide_order" value="1">
        <span class="description hidden" style="display: inline-block"><?php esc_html_e('Hide this field on the user order', 'woocommerce-checkout-manager'); ?></span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
    <p class="form-field">
      <label><?php esc_html_e('Hide on emails', 'woocommerce-checkout-manager'); ?></label>
      <input <# if (data.hide_email) { #>checked="checked"<# } #> type="checkbox" name="hide_email" value="1">
        <span class="description hidden" style="display: inline-block"><?php esc_html_e('Hide this field on the user email', 'woocommerce-checkout-manager'); ?></span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
    <p class="form-field">
      <label><?php esc_html_e('Hide on invoices', 'woocommerce-checkout-manager'); ?></label>
      <input <# if (data.hide_invoice) { #>checked="checked"<# } #> type="checkbox" name="hide_invoice" value="1">
        <span class="description hidden" style="display: inline-block"><?php esc_html_e('Hide this field on the order invoice', 'woocommerce-checkout-manager'); ?></span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
  </div>
</div>