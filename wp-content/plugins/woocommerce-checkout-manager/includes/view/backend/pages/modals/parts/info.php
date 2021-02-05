<span class="settings-save-status">
  <span class="spinner"></span>
  <span class="saved"><?php esc_html_e('Saved.'); ?></span>
</span>

<div class="details">
  <div class="filename"><strong><?php esc_html_e('Field id', 'woocommerce-checkout-manager'); ?>:</strong> {{data.id}}</div>
  <div class="filename"><strong><?php esc_html_e('Field key', 'woocommerce-checkout-manager'); ?>:</strong> #{{data.key}}</div>
  <# if (data.parent != undefined) { #>
  <div class="filename"><strong><?php esc_html_e('Parent type', 'woocommerce-checkout-manager'); ?>:</strong> {{data.parent.type}}</div>
  <# } #>
</div>

<div class="panel woocommerce_options_panel">
  <div class="settings">
    <p class="form-field">
      <label><?php esc_html_e('Conditional', 'woocommerce-checkout-manager'); ?></label>
      <input <# if (data.conditional) { #>checked="checked"<# } #> type="checkbox" name="conditional" value="1">
        <span class="description"><?php esc_html_e('Activate conditional field requirement.', 'woocommerce-checkout-manager'); ?></span>
    </p>
  </div>
  <div class="settings">
    <p class="form-field">
      <label><?php esc_html_e('Parent', 'woocommerce-checkout-manager'); ?></label>
      <select class="media-modal-parent media-modal-render-info wooccm-enhanced-select" name="conditional_parent_key" data-placeholder="<?php esc_attr_e('Select parent field&hellip;', 'woocommerce-checkout-manager'); ?>" data-allow_clear="false">
        <option <# if (data.conditional_parent_key == '') { #>selected="selected"<# } #> value=""></option>
        <?php foreach ($fields as $field_id => $field) : ?>
          <?php if (in_array($field['type'], $conditionals)): ?>
            <# if ( data.id != '<?php echo esc_attr($field['id']); ?>' ) { #>
            <option <# if ( data.conditional_parent_key == '<?php echo esc_attr($field['key']); ?>' ) { #>selected="selected"<# } #> value="<?php echo esc_attr($field['key']); ?>"><?php printf('%s', $field['label'] ? esc_html($field['label']) : sprintf(esc_html__('Field %s', 'woocommerce-checkout-manager'), $field_id)); ?></option>
            <# } #>
          <?php endif; ?>
        <?php endforeach; ?>
      </select>
      <span class="description"><?php esc_html_e('Select conditional parent field.', 'woocommerce-checkout-manager'); ?></span>
    </p>
  </div>
  <div class="settings">
    <p class="form-field">
      <# if( data.parent != undefined && data.parent.label != '' ) { #>
      <label>{{data.parent.label}}</label>
      <# } else { #>
      <label><?php esc_html_e('Value', 'woocommerce-checkout-manager'); ?></label>
      <# } #>
      <# if ( data.parent != undefined && _.contains(<?php echo json_encode($option); ?>, data.parent.type) && _.isObject(data.parent.options)) { #>
      <select class="wooccm-enhanced-select" name="conditional_parent_value">
        <# _.each(data.parent.options, function (option, index) { #>
        <option <# if ( option.label == data.conditional_parent_value ) { #>selected="selected"<# } #> value="{{option.label}}">{{option.label}}</option>
        <# }); #>
      </select>
      <# } else if( data.parent != undefined && data.parent.type == 'checkbox' ) { #>
      <select class="select short" name="conditional_parent_value">
        <option <# if ( 1 == data.conditional_parent_value ) { #>selected="selected"<# } #> value="1"><?php esc_html_e('Yes'); ?></option>
        <option <# if ( 0 == data.conditional_parent_value ) { #>selected="selected"<# } #> value="0"><?php esc_html_e('No'); ?></option>
      </select>
      <# } else if( data.parent != undefined && data.parent.type == 'country' ) { #>
      <select class="wooccm-enhanced-select" name="conditional_parent_value" data-placeholder="<?php esc_attr_e('Preserve default country&hellip;', 'woocommerce-checkout-manager'); ?>" data-allow_clear="true">
        <option <# if (data.default == '') { #>selected="selected"<# } #> value=""></option>
        <?php foreach (WC()->countries->get_countries() as $id => $name) : ?>
          <option <# if (data.conditional_parent_value == '<?php echo esc_attr($id); ?>') { #>selected="selected"<# } #> value="<?php echo esc_attr($id); ?>"><?php echo esc_html($name); ?></option>
        <?php endforeach; ?>       
      </select>
      <# } else if( data.parent != undefined ) { #>
      <input type="text" name="conditional_parent_value" placeholder="{{data.parent.default}}" value="{{data.conditional_parent_value}}">
      <# } else { #>
      <input type="text" name="conditional_parent_value" placeholder="<?php esc_html_e('Conditional parent value', 'woocommerce-checkout-manager'); ?>" value="{{data.conditional_parent_value}}">
      <# } #>
      <span class="description"><?php esc_html_e('Show field if parent has this value.', 'woocommerce-checkout-manager'); ?></span>
    </p>
  </div>
</div>

<div class="actions">
  <a target="_blank" class="view-attachment" href="<?php echo wc_get_page_permalink('checkout'); ?>"><?php esc_html_e('View checkout page', 'woocommerce-checkout-manager'); ?></a> |
  <a target="_blank" href="<?php echo WOOCCM_PURCHASE_URL; ?>"><?php esc_html_e('Get premium version', 'woocommerce-checkout-manager'); ?></a> |
  <a target="_blank" href="<?php echo WOOCCM_DOCUMENTATION_URL; ?>"><?php esc_html_e('View documentation', 'woocommerce-checkout-manager'); ?></a>
</div>