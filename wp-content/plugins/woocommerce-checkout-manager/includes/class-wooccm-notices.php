<?php

class WOOCCM_Notices
{

  protected static $_instance;

  public function __construct()
  {
    add_action('wp_ajax_wooccm_dismiss_notice', array($this, 'ajax_dismiss_notice'));
    add_action('admin_notices', array($this, 'add_notices'));
    add_filter('plugin_action_links_' . plugin_basename(WOOCCM_PLUGIN_FILE), array($this, 'add_action_links'));
  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function ajax_dismiss_notice()
  {

    if (check_admin_referer('wooccm_dismiss_notice', 'nonce') && isset($_REQUEST['notice_id'])) {

      $notice_id = sanitize_key($_REQUEST['notice_id']);

      update_user_meta(get_current_user_id(), $notice_id, true);

      wp_send_json($notice_id);
    }

    wp_die();
  }

  public function add_notices()
  {

    if (!get_user_meta(get_current_user_id(), 'wooccm-beta-notice', true) && get_option('wccs_settings')) {
?>
      <div class="wooccm-notice notice is-dismissible" data-notice_id="wooccm-beta-notice">
        <div class="notice-container" style="padding-top: 10px; padding-bottom: 10px; display: flex; justify-content: left; align-items: center;">
          <div class="notice-image">
            <img style="border-radius:50%;max-width: 90px;" src="<?php echo plugins_url('/assets/backend/img/logo.jpg', WOOCCM_PLUGIN_FILE); ?>" alt="<?php echo esc_html(WOOCCM_PLUGIN_NAME); ?>>">
          </div>
          <div class="notice-content" style="margin-left: 15px;">
            <p>
              <h3>Hello! the new admin panel is here!</h3>
            </p>
            <p>
              As you know, we've recently acquired this plugin and we've been working very hard to bring you a quality product.
            </p>
            <p>
              Finally, we're glad to introduce you to the new admin panel available in the WooCommerce &gt; Checkout dashboard.
            </p>
            <p>
              The entire panel has been rebuilt and now each field has its own modal of settings. Also, conditional relationships have been simplified.
            </p>
            <p>
              This has been a titanic task and we were forced to remove some settings. We want to apologies to you if you experience some issues in the last couple of updates or even in this one. :)
            </p>
            <p>
              Don't hesitate to contact us to report any issue or join our community to be in touch.
            </p>
            <p>
              We wish you the best! and good luck with your sales! The QuadLayers team.
            </p>
            <p>
              <a href="<?php echo esc_url(WOOCCM_GROUP_URL); ?>" class="button-primary" target="_blank">
                <?php esc_html_e('Join Community!', 'woocommerce-checkout-manager'); ?>
              </a>
              <a href="<?php echo esc_url(WOOCCM_SUPPORT_URL); ?>" class="button-secondary" target="_blank">
                <?php esc_html_e('Report a bug', 'woocommerce-checkout-manager'); ?>
              </a>
              <a style="margin-left: 10px;" href="https://quadlayers.com/?utm_source=wooccm_admin" target="_blank">
                <?php esc_html_e('About us', 'woocommerce-checkout-manager'); ?>
              </a>
            </p>
          </div>
        </div>
      </div>
    <?php
    } elseif (!get_user_meta(get_current_user_id(), 'wooccm-user-rating', true) && !get_transient('wooccm-first-rating') && !get_option('wccs_settings')) {
    ?>
      <div class="wooccm-notice notice is-dismissible" data-notice_id="wooccm-user-rating">
        <div class="notice-container" style="padding-top: 10px; padding-bottom: 10px; display: flex; justify-content: left; align-items: center;">
          <div class="notice-image">
            <img style="border-radius:50%;max-width: 90px;" src="<?php echo plugins_url('/assets/backend/img/logo.jpg', WOOCCM_PLUGIN_FILE); ?>" alt="<?php echo esc_html(WOOCCM_PLUGIN_NAME); ?>>">
          </div>
          <div class="notice-content" style="margin-left: 15px;">
            <p>
              <?php printf(esc_html__('Hello! We\'ve recently acquired this plugin!', 'woocommerce-checkout-manager'), WOOCCM_PLUGIN_NAME); ?>
              <br />
              <?php esc_html_e('We will do our best to improve it and include new features gradually. Please be patient and let us know about the issues and improvements that you want to see in this plugin.', 'woocommerce-checkout-manager'); ?>
            </p>
            <a href="<?php echo esc_url(WOOCCM_GROUP_URL); ?>" class="button-primary" target="_blank">
              <?php esc_html_e('Join Community!', 'woocommerce-checkout-manager'); ?>
            </a>
            <a href="<?php echo esc_url(WOOCCM_SUPPORT_URL); ?>" class="button-secondary" target="_blank">
              <?php esc_html_e('Report a bug', 'woocommerce-checkout-manager'); ?>
            </a>
            <a style="margin-left: 10px;" href="https://quadlayers.com/?utm_source=wooccm_admin" target="_blank">
              <?php esc_html_e('About us', 'woocommerce-checkout-manager'); ?>
            </a>
          </div>
        </div>
      </div>
    <?php } elseif (!get_user_meta(get_current_user_id(), 'wooccm-update-5', true)) { ?>
      <div id="wooccm-update-5" class="wooccm-notice notice is-dismissible" data-notice_id="wooccm-update-5">
        <div class="notice-container" style="padding-top: 10px; padding-bottom: 10px; display: flex; justify-content: left; align-items: center;">
          <div class="notice-image">
            <img style="border-radius:50%;max-width: 90px;" src="<?php echo plugins_url('/assets/backend/img/logo.jpg', WOOCCM_PLUGIN_FILE); ?>" alt="<?php echo esc_html(WOOCCM_PLUGIN_NAME); ?>>">
          </div>
          <div class="notice-content" style="margin-left: 15px;">
            <p>
              <b><?php printf(esc_html__('Important! Manual update is required.', 'woocommerce-checkout-manager'), WOOCCM_PLUGIN_NAME); ?></b>
              <br />
              <?php esc_html_e('Due to the recent WooCommerce 4.0 changes it is necessary to reconfigure conditional fields. If you have conditional fields, please go to the billing, shipping and advanced sections and set conditionals relationships again.', 'woocommerce-checkout-manager'); ?>
            </p>
            <a href="<?php echo admin_url('admin.php?page=wc-settings&tab=wooccm'); ?>" class="button-primary" target="_blank">
              <?php esc_html_e('Settings', 'woocommerce-checkout-manager'); ?>
            </a>
            <a href="<?php echo str_replace('/?utm_source=wooccm_admin', '/conditional/?utm_source=wooccm_admin', WOOCCM_DOCUMENTATION_URL); ?>" class="button-secondary" target="_blank">
              <?php esc_html_e('Documentation', 'woocommerce-checkout-manager'); ?>
            </a>
          </div>
        </div>
      </div>
    <?php
    }
    ?>
    <script>
      (function($) {
        $('.wooccm-notice').on('click', '.notice-dismiss', function(e) {
          e.preventDefault();
          var notice_id = $(e.delegateTarget).data('notice_id');
          $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
              notice_id: notice_id,
              action: 'wooccm_dismiss_notice',
              nonce: '<?php echo wp_create_nonce('wooccm_dismiss_notice'); ?>'
            },
            success: function(response) {
              console.log(response);
            },
          });
        });
      })(jQuery);
    </script>
<?php
  }

  public function add_action_links($links)
  {

    $links[] = '<a target="_blank" href="' . WOOCCM_PURCHASE_URL . '">' . esc_html__('Premium', 'woocommerce-checkout-manager') . '</a>';
    $links[] = '<a href="' . admin_url('admin.php?page=wc-settings&tab=' . sanitize_title(WOOCCM_PREFIX)) . '">' . esc_html__('Settings', 'woocommerce-checkout-manager') . '</a>';

    return $links;
  }
}

WOOCCM_Notices::instance();
