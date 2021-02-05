<?php include_once('parts/header.php' ); ?>
<div class="wrap about-wrap full-width-layout">
  <div class="has-2-columns is-wider-left" style="max-width: 100%">
    <div class="column">
      <div class="welcome-header">
        <h1><?php esc_html_e('Premium', 'woocommerce-checkout-manager'); ?></h1>
        <div class="about-description">
          <?php printf(esc_html__('%s allows you to customize, add, edit and delete fields displayed on the checkout page. With the premium version you can also create conditional fields and include custom fees based on the checkout fields.', 'woocommerce-checkout-manager'), WOOCCM_PLUGIN_NAME); ?>
        </div>
        <br/>
        <a class="button button-primary" target="_blank" href="<?php echo esc_url(WOOCCM_PURCHASE_URL); ?>"><?php esc_html_e('Purchase Now', 'woocommerce-checkout-manager'); ?></a>
        <a class="button button-secondary" target="_blank" href="<?php echo esc_url(WOOCCM_SUPPORT_URL); ?>"><?php esc_html_e('Get Support', 'woocommerce-checkout-manager'); ?></a>
      </div>
      <hr/>
      <div class="feature-section" style="padding: 10px 0;">
        <h3><?php esc_html_e('Add checkout fees', 'woocommerce-checkout-manager'); ?></h3>
        <p>
          <?php printf(esc_html__('%s allows you to include fees to each custom or core field. Fees can be fixed or percentage amounts based on checkout total.', 'woocommerce-checkout-manager'), WOOCCM_PLUGIN_NAME); ?>
        </p>
      </div>
      <div class="feature-section" style="padding: 10px 0;">
        <h3><?php esc_html_e('Remove account fields', 'woocommerce-checkout-manager'); ?></h3>
        <p>
          <?php esc_html_e('By default all fields attached to the billing or shipping sections are included in the edit address area of the user account page. With the premium version, you can hide the fields on the checkout or my account page.', 'woocommerce-checkout-manager'); ?>
        </p>
      </div>
      <div class="feature-section" style="padding: 10px 0;">
        <h3><?php esc_html_e('Enhance select fields', 'woocommerce-checkout-manager'); ?></h3>
        <p>
          <?php esc_html_e('The enhance options allow you to improve the behavior of the select and multiselect fields with the select2 jquery plugin.', 'woocommerce-checkout-manager'); ?>
        </p>
      </div>
    </div>
    <div class="column">
      <img src="<?php echo plugins_url('/assets/backend/img/fees.png', WOOCCM_PLUGIN_FILE); ?>">
    </div>
  </div>
  <hr/>
  <div class="has-2-columns" style="max-width: 100%">
    <div class="column">
      <img style="margin-top: -30px" src="<?php echo plugins_url('/assets/backend/img/admin.png', WOOCCM_PLUGIN_FILE); ?>">
    </div>
    <div class="column">
      <div class="feature-section" style="padding: 10px 0;">
        <h3><?php esc_html_e('Display on admin list orders', 'woocommerce-checkout-manager'); ?></h3>
        <p>
          <?php esc_html_e('This option allows you to display the fields in the WooCommerce order list.', 'woocommerce-checkout-manager'); ?>
        </p>
      </div>
      <div class="feature-section" style="padding: 10px 0;">
        <h3><?php esc_html_e('Allow sorting on admin list orders', 'woocommerce-checkout-manager'); ?></h3>
        <p>
          <?php esc_html_e('This option allows you to sort the WooCommerce orders list based on the field values.', 'woocommerce-checkout-manager'); ?>
        </p>
      </div>
      <div class="feature-section" style="padding: 10px 0;">
        <h3><?php esc_html_e('Allow filtering on admin list orders', 'woocommerce-checkout-manager'); ?></h3>
        <p>
          <?php esc_html_e('This option allows you to add a search field to filter the options based on the selected values.', 'woocommerce-checkout-manager'); ?>
        </p>
      </div>
    </div>
  </div>
</div>