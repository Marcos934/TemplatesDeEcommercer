<?php include_once( 'parts/tabs.php' ); ?>
<h1 class="screen-reader-text"><?php esc_html_e('Advanced', 'woocommerce-checkout-manager'); ?></h1>
<h2><?php esc_html_e('Advanced settings', 'woocommerce-checkout-manager'); ?></h2>
<!--<div id="<?php printf('wooccm_%s_settings-description', $current_section); ?>">
  <p><?php printf(esc_html__('Customize and manage the checkout %s fields.', 'woocommerce-checkout-manager'), $current_section); ?></p>
</div>
<?php if (current_user_can('manage_options')) : ?>
  <a href="<?php echo add_query_arg(array('action' => 'wooccm_nuke_options', '_wpnonce' => wp_create_nonce('wooccm_nuke_options'))); ?>" class="button button-secondary" data-confirm="<?php esc_html_e('This will permanently delete all WordPress Options associated with WooCommerce Checkout Manager. Are you sure you want to proceed?', 'woocommerce-checkout-manager'); ?>"><?php esc_html_e('Delete Options', 'woocommerce-checkout-manager'); ?></a>
  <a href="<?php echo add_query_arg(array('action' => 'wooccm_nuke_order_meta', '_wpnonce' => wp_create_nonce('wooccm_nuke_order_meta'))); ?>" class="button button-secondary" data-confirm="<?php esc_html_e('This will permanently delete all WordPress Post meta associated with that is linked to Orders. Are you sure you want to proceed?', 'woocommerce-checkout-manager'); ?>"><?php esc_html_e('Delete Orders Post meta', 'woocommerce-checkout-manager'); ?></a>
  <a href="<?php echo add_query_arg(array('action' => 'wooccm_nuke_user_meta', '_wpnonce' => wp_create_nonce('wooccm_nuke_user_meta'))); ?>" class="button button-secondary" data-confirm="<?php esc_html_e('This will permanently delete all WordPress Post meta associated with that is linked to Users. Are you sure you want to proceed?', 'woocommerce-checkout-manager'); ?>"><?php esc_html_e('Delete Users Post meta', 'woocommerce-checkout-manager'); ?></a>
<?php endif; ?>-->
<?php woocommerce_admin_fields($settings); ?>
