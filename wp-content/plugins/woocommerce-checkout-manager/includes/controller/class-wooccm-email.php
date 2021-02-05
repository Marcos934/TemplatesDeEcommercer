<?php

class WOOCCM_Order_Email_Controller
{

  protected static $_instance;

  public function __construct()  {

    add_action('wooccm_sections_header', array($this, 'add_header'));
    add_action('woocommerce_sections_' . WOOCCM_PREFIX, array($this, 'add_section'));
    add_action('woocommerce_settings_save_' . WOOCCM_PREFIX, array($this, 'save_settings'));
  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  // Admin    
  // -------------------------------------------------------------------------

  public function get_settings()
  {
    return array(
      array(
        'type' => 'title',
        'id' => 'section_title'
      ),
      array(
        'name' => esc_html__('Add upload files', 'woocommerce-checkout-manager'),
        'desc_tip' => esc_html__('Allow customers to upload files in the email.', 'woocommerce-checkout-manager'),
        'id' => 'wooccm_email_upload_files',
        'type' => 'select',
        'class' => 'chosen_select wooccm-premium-field',
        'desc' => esc_html__('This is a premium feature.', 'woocommerce-checkout-manager'),
        'options' => array(
          'yes' => esc_html__('Yes', 'woocommerce-checkout-manager'),
          'no' => esc_html__('No', 'woocommerce-checkout-manager'),
        ),
        'default' => 'no',
      ),
      array(
        'name' => esc_html__('Add for this order status', 'woocommerce-checkout-manager'),
        'desc_tip' => esc_html__('Allow customers to upload files in the email.', 'woocommerce-checkout-manager'),
        'id' => 'wooccm_email_upload_files_order_status',
        'type' => 'multiselect',
        'class' => 'chosen_select wooccm-premium-field',
        'desc' => esc_html__('This is a premium feature.', 'woocommerce-checkout-manager'),
        'options' => wc_get_order_statuses(),
        'default' => array_keys(wc_get_order_statuses()),
      ),
      array(
        'name' => esc_html__('Add upload files title', 'woocommerce-checkout-manager'),
        'desc_tip' => esc_html__('Add custom title for the uploads files table.', 'woocommerce-checkout-manager'),
        'id' => 'wooccm_email_upload_files_title',
        'class' => 'wooccm-premium-field',
        'desc' => esc_html__('This is a premium feature.', 'woocommerce-checkout-manager'),
        'type' => 'text',
        'placeholder' => esc_html__('Uploaded files', 'woocommerce-checkout-manager')
      ),
      array(
        'name' => esc_html__('Add custom fields', 'woocommerce-checkout-manager-pro'),
        'desc_tip' => esc_html__('Show the selected fields in the email.', 'woocommerce-checkout-manager-pro'),
        'id' => 'wooccm_email_custom_fields',
        'type' => 'select',
        'class' => 'chosen_select wooccm-premium-field',
        'desc' => esc_html__('This is a premium feature.', 'woocommerce-checkout-manager'),
        'options' => array(
          'yes' => esc_html__('Yes', 'woocommerce-checkout-manager-pro'),
          'no' => esc_html__('No', 'woocommerce-checkout-manager-pro'),
        ),
        'default' => 'yes',
      ),
      array(
        'name' => esc_html__('Add for this order status', 'woocommerce-checkout-manager-pro'),
        'desc_tip' => esc_html__('Allow customers to upload files in the email.', 'woocommerce-checkout-manager-pro'),
        'id' => 'wooccm_email_custom_fields_status',
        'type' => 'multiselect',
        'class' => 'chosen_select wooccm-premium-field',
        'desc' => esc_html__('This is a premium feature.', 'woocommerce-checkout-manager'),
        'options' => wc_get_order_statuses(),
        'default' => array_keys(wc_get_order_statuses()),
      ),
      array(
        'name' => esc_html__('Add custom fields title', 'woocommerce-checkout-manager-pro'),
        'desc_tip' => esc_html__('Add custom title for the uploads files table.', 'woocommerce-checkout-manager-pro'),
        'id' => 'wooccm_email_custom_fields_title',
        'type' => 'text',
        'class' => 'wooccm-premium-field',
        'desc' => esc_html__('This is a premium feature.', 'woocommerce-checkout-manager'),
        'placeholder' => esc_html__('Order extra', 'woocommerce-checkout-manager-pro')
      ),
      array(
        'type' => 'sectionend',
        'id' => 'section_end'
      )
    );
  }

  public function add_header()
  {
    global $current_section;
?>
    <li><a href="<?php echo admin_url('admin.php?page=wc-settings&tab=wooccm&section=email'); ?>" class="<?php echo ($current_section == 'email' ? 'current' : ''); ?>"><?php esc_html_e('Email', 'woocommerce-checkout-manager'); ?></a> | </li>
<?php
  }

  public function add_section()
  {

    global $current_section;

    if ('email' == $current_section) {

      $settings = $this->get_settings();

      include_once(WOOCCM_PLUGIN_DIR . 'includes/view/backend/pages/email.php');
    }
  }

  public function save_settings()
  {
    woocommerce_update_options($this->get_settings());
  }
}

WOOCCM_Order_Email_Controller::instance();
