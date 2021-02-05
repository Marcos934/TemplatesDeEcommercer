<div class="panel woocommerce_options_panel <# if (data.panel != 'filter') { #>hidden<# } #>">
  <div class="options_group wooccm-premium wooccm-enhanced-between-days">
    <p class="form-field dimensions_field">
      <label><?php esc_html_e('Cart subtotal', 'woocommerce-checkout-manager'); ?></label>
      <span class="wrap">
        <input style="width:48.1%" type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" placeholder="<?php esc_attr_e('minimun', 'woocommerce-checkout-manager'); ?>" min="0" class="short " name="show_cart_minimum" value="{{data.show_cart_minimum}}">
          <input style="width:48.1%;margin: 0;" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" type="number" placeholder="<?php esc_attr_e('maximun', 'woocommerce-checkout-manager'); ?>" min="0" class="short" name="show_cart_maximun" value="{{data.show_cart_maximun}}">
      </span>
      <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
    </p>
  </div>
  <div class="options_group">
    <p class="form-field">
      <label><?php esc_html_e('Show for roles', 'woocommerce-checkout-manager'); ?></label>
      <select class="wooccm-enhanced-select" name="show_role[]" data-placeholder="<?php esc_attr_e('Filter by roles', 'woocommerce-checkout-manager'); ?>" data-allow_clear="true" multiple="multiple">
        <?php foreach ($wp_roles->roles as $key => $value): ?>
          <option <# if ( _.contains(data.show_role, '<?php echo esc_attr($key); ?>') ) { #>selected="selected"<# } #> value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value['name']); ?></option>
        <?php endforeach; ?>
      </select> 
    </p>
    <p class="form-field">
      <label><?php esc_html_e('Hide for roles', 'woocommerce-checkout-manager'); ?></label>
      <select class="wooccm-enhanced-select" name="hide_role[]" data-placeholder="<?php esc_attr_e('Filter by roles', 'woocommerce-checkout-manager'); ?>" data-allow_clear="true" multiple="multiple">
        <?php foreach ($wp_roles->roles as $key => $value): ?>
          <option <# if ( _.contains(data.hide_role, '<?php echo esc_attr($key); ?>') ) { #>selected="selected"<# } #> value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value['name']); ?></option>
        <?php endforeach; ?>
      </select> 
    </p>
  </div>

  <div class="options_group">
    <p class="form-field">
      <label><?php esc_html_e('More', 'woocommerce-checkout-manager'); ?></label>
      <input <# if (data.more_product) { #>checked="checked"<# } #> type="checkbox" name="more_product" value="1">
        <span class="description"><?php esc_html_e('Apply conditions event it there is more than one product', 'woocommerce-checkout-manager'); ?></span>
    </p>
  </div>

  <div class="options_group">
    <p class="form-field">
      <label><?php esc_html_e('Show for products', 'woocommerce-checkout-manager'); ?></label>
      <select class="wooccm-product-search" name="show_product[]" data-placeholder="<?php esc_attr_e('Filter by product', 'woocommerce-checkout-manager'); ?>" data-selected="{{data.show_product}}" data-allow_clear="true" multiple="multiple">
        <# _.each(data.show_product_selected, function(title, id){ #>
        <option value="{{id}}" selected="selected">{{title}}</option>
        <# }); #>
      </select>
    </p>
    <p class="form-field">
      <label><?php esc_html_e('Hide for products', 'woocommerce-checkout-manager'); ?></label>
      <select class="wooccm-product-search" name="hide_product[]" data-placeholder="<?php esc_attr_e('Filter by product', 'woocommerce-checkout-manager'); ?>" data-selected="{{data.hide_product}}" data-allow_clear="true" multiple="multiple">
        <# _.each(data.hide_product_selected, function(title, id){ #>
        <option value="{{id}}" selected="selected">{{title}}</option>
        <# }); #>
      </select>
    </p>
  </div>

  <div class="options_group">
    <p class="form-field">
      <label><?php esc_html_e('Show for category', 'woocommerce-checkout-manager'); ?></label>
      <select class="wooccm-enhanced-select" name="show_product_cat[]" data-placeholder="<?php esc_attr_e('Filter by categories', 'woocommerce-checkout-manager'); ?>" data-selected="{{data.show_product_cat}}" data-allow_clear="true" multiple="multiple">
        <?php if ($product_categories) : ?>
          <?php foreach ($product_categories as $category): ?>
            <option <# if ( _.contains(data.show_product_cat, '<?php echo esc_attr($category->term_id); ?>') ) { #>selected="selected"<# } #> value="<?php echo esc_attr($category->term_id); ?>"><?php echo esc_html($category->name); ?></option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
    </p>
    <p class="form-field">
      <label><?php esc_html_e('Hide for category', 'woocommerce-checkout-manager'); ?></label>
      <select class="wooccm-enhanced-select" name="hide_product_cat[]" data-placeholder="<?php esc_attr_e('Filter by categories', 'woocommerce-checkout-manager'); ?>" data-selected="{{data.hide_product_cat}}" data-allow_clear="true" multiple="multiple">
        <?php if ($product_categories) : ?>
          <?php foreach ($product_categories as $category): ?>
            <option <# if ( _.contains(data.hide_product_cat, '<?php echo esc_attr($category->term_id); ?>') ) { #>selected="selected"<# } #> value="<?php echo esc_attr($category->term_id); ?>"><?php echo esc_html($category->name); ?></option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
    </p>
  </div>
</div>