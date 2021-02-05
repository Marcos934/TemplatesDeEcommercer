<?php include_once( 'parts/tabs.php' ); ?>
<h1 class="screen-reader-text"><?php esc_html_e('Checkout', 'woocommerce-checkout-manager'); ?></h1>
<h2><?php esc_html_e('Checkout settings', 'woocommerce-checkout-manager'); ?></h2>
<div id="<?php printf('wooccm_%s_settings-description', $current_section); ?>">
  <p><?php printf(esc_html__('Customize and manage the checkout settings.', 'woocommerce-checkout-manager'), $current_section); ?></p>
</div>
<?php woocommerce_admin_fields($settings); ?>
