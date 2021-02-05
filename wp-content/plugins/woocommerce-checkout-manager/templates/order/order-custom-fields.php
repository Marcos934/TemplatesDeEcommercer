<h2 class="woocommerce-order-details__title"><?php echo ($title = get_option('wooccm_order_custom_fields_title', false)) ? esc_html($title) : esc_html__('Order extra', 'woocommerce-checkout-manager'); ?></h2>
<table class="woocommerce-table shop_table order_details">
  <tbody>
    <?php
    if (count($checkout = WC()->checkout->get_checkout_fields())) :
      foreach ($checkout as $field_type => $fields) :
        if (isset(WOOCCM()->$field_type)) :
          $defaults = array_column(WOOCCM()->$field_type->get_defaults(), 'key');
          foreach ($fields as $key => $field) : ?>
            <?php if (!in_array($key, $defaults) && empty($field['hide_order'])) : ?>
              <?php if ($value = get_post_meta($order_id, sprintf('_%s', $key), true)) : ?>
                <tr id="tr-<?php echo esc_attr($key); ?>">
                  <th>
                    <?php echo esc_html($field['label']); ?>
                  </th>
                  <td>
                    <?php echo esc_html($value); ?>
                  </td>
                </tr>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>