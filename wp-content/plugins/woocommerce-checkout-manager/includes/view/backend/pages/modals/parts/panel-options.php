<# if ( _.contains(<?php echo json_encode($option); ?>, data.type)) { #>
<div class="panel woocommerce_options_panel <# if (data.panel != 'options') { #>hidden<# } #>">
     <# if ( _.isObject(data.options)) { #>
     <div class="options_group">
    <table class="wc_gateways widefat wooccm-enhanced-options" style="border:none;box-shadow: none">
      <thead>
        <tr>
          <th class="check-column" style="width: 5%;">
            <label class="screen-reader-text" for="select-all"><?php esc_html_e('Select all', 'woocommerce-checkout-manager'); ?></label>
            <input type="checkbox" id="select-all">
          </th>
          <th scope="col" style="width: 40%;">
            <?php esc_html_e('Label', 'woocommerce-checkout-manager'); ?>
          </th>
          <th scope="col" style="width: 30%;max-width: 85px;">
            <span class="wooccm-premium"><?php esc_html_e('Price', 'woocommerce-checkout-manager'); ?></span>
            <span class="woocommerce-help-tip" data-tip="<?php esc_html_e('This is a premium feature.', 'woocommerce-checkout-manager'); ?>"></span>
          </th>
          <th scope="col" style="width: 10%;min-width: 80px;">
            <span class="wooccm-premium"><?php esc_html_e('Taxable', 'woocommerce-checkout-manager'); ?></span>
            <span class="woocommerce-help-tip" data-tip="<?php esc_html_e('This is a premium feature.', 'woocommerce-checkout-manager'); ?>"></span>
          </th>
          <th scope="col" style="width: 15%;">
            <?php esc_html_e('Default', 'woocommerce-checkout-manager'); ?>
          </th>
          <th scope="col" style="width: 5%;">&nbsp;</th>
        </tr>
      </thead>
      <tbody class="ui-sortable">
        <# _.each(data.options, function (option, index) { #>
        <tr>
          <td class="check-column">
            <input class="check" type="checkbox" <# if(index === 0) { #>disabled="disabled"<# } #>/>
          </td>
          <td>
            <input type="text" class="label" name="options[{{index}}][label]" value="{{option.label}}">
          </td>
          <td class="wooccm-premium">
            <input type="number" class="add-price" name="options[{{index}}][add_price_total]" step="0.01" value="{{option.add_price_total}}">
            <select class="add-price-type" name="options[{{index}}][add_price_type]">
              <option value="fixed" <# if(option.add_price_type == 'fixed') { #>selected="selected"<# } #>>$</option>
              <option value="percent" <# if(option.add_price_type == 'percent') { #>selected="selected"<# } #>>%</option>
            </select>
          </td>
          <td class="wooccm-premium">
            <input type="checkbox" name="options[{{index}}][add_price_tax]" value="1" <# if (option.add_price_tax) { #>checked="checked"<# } #> />
          </td>
          <td>
            <# if ( _.contains(<?php echo json_encode($multiple); ?>, data.type)) { #>
            <input value="{{option.label}}" type="checkbox" name="options[{{index}}][default]" <# if (option.default) { #>checked="checked"<# } #> />
                   <# } else { #>
                   <input value="{{option.label}}" type="radio" name="default" <# if (data.default == option.label) { #>checked="checked"<# } #> />
                   <# } #>
          </td>
          <td class="sort ui-sortable-handle">
            <div class="wc-item-reorder-nav">
              <input value="{{option.order}}" style="width: 50px" class="add-order" type="hidden" name="options[{{index}}][order]"/>
            </div>
          </td>
        </tr>
        <# }); #>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="6">
            <button type="button" class="button button-primary add-option"><?php esc_html_e('Add new option', 'woocommerce-checkout-manager'); ?></button>
            <button type="button" class="button button-secondary remove-options"><?php esc_html_e('Delete selected', 'woocommerce-checkout-manager'); ?></button>
          </th>
        </tr>
      </tfoot>
    </table>
  </div>
  <# } #>
</div>
<# } #>