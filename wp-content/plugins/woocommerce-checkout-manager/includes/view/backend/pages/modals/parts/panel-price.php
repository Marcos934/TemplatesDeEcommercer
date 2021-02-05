<# if (!_.contains(<?php echo json_encode(array_merge($option, $template)); ?>, data.type)) { #>
<div class="panel woocommerce_options_panel <# if (data.panel != 'price') { #>hidden<# } #>">
     <div class="options_group wooccm-premium">
    <p class="form-field">
      <label><?php esc_html_e('Price', 'woocommerce-checkout-manager'); ?></label>
      <input <# if (data.add_price) { #>checked="checked"<# } #> type="checkbox" name="add_price" value="1">
        <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
    <p class="form-field">
      <label><?php esc_html_e('Name', 'woocommerce-checkout-manager'); ?></label>
      <input class="short" name="add_price_name" type="text" value="{{data.add_price_name}}" placeholder="<?php esc_html_e('My Custom Charge', 'woocommerce-checkout-manager'); ?>">
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
    <p class="form-field">
      <label><?php esc_html_e('Total', 'woocommerce-checkout-manager'); ?></label>
      <input class="short" name="add_price_total" type="text" value="{{data.add_price_total}}" placeholder="50">
      <select style="margin:0 0 0 10px;line-height: 30px; height: 30px;" class="select" name="add_price_type">
        <option <# if (data.add_price_type == 'fixed') { #>selected="selected"<# } #> value="fixed">$</option>
        <option <# if (data.add_price_type == 'percent') { #>selected="selected"<# } #> value="percent">%</option>
      </select>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
  </div>
  <div class="options_group wooccm-premium">
    <p class="form-field">
      <label><?php esc_html_e('Tax', 'woocommerce-checkout-manager'); ?></label>
      <input <# if (data.add_price_tax) { #>checked="checked"<# } #> type="checkbox" name="add_price_tax" value="1">
        <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
  </div>
</div>
<# } #>