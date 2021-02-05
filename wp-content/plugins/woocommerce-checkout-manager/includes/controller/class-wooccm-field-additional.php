<?php

class WOOCCM_Field_Controller_Additional extends WOOCCM_Field_Controller
{

  protected static $_instance;
  public $additional;

  public function __construct()
  {

    include_once(WOOCCM_PLUGIN_DIR . 'includes/model/class-wooccm-field-additional.php');

    add_action('woocommerce_admin_order_data_after_shipping_address', array($this, 'add_order_data'));
    add_action('woocommerce_checkout_update_order_meta', array($this, 'save_order_data'), 10, 2);
    add_action('wooccm_sections_header', array($this, 'add_header'));
    add_action('woocommerce_sections_' . WOOCCM_PREFIX, array($this, 'add_section'), 99);
    add_action('woocommerce_settings_save_' . WOOCCM_PREFIX, array($this, 'save_settings'));
  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  function save_order_data($order_id, $data)
  {

    if (count($fields = WOOCCM()->additional->get_fields())) {

      foreach ($fields as $field_id => $field) {

        $key = sprintf('_%s', $field['key']);

        if (!empty($data[$field['key']])) {

          $value = $data[$field['key']];

          if ($field['type'] == 'textarea') {
            update_post_meta($order_id, $key, wp_kses($value, false));
          } else if (is_array($value)) {
            update_post_meta($order_id, $key, implode(',', array_map('sanitize_text_field', $value)));
          } else {
            update_post_meta($order_id, $key, sanitize_text_field($value));
          }
        }
      }
    }
  }

  function save_settings()
  {

    global $current_section;

    if ('additional' == $current_section) {
      woocommerce_update_options($this->get_settings());
    }
  }

  function get_settings()
  {

    return array(
      array(
        'desc_tip' => esc_html__('Select the position of the additional fields.', 'woocommerce-checkout-manager'),
        'id' => 'wooccm_additional_position',
        'type' => 'select',
        //'class' => 'chosen_select',
        'options' => array(
          'before_billing_form' => esc_html__('Before billing form', 'woocommerce-checkout-manager'),
          'after_billing_form' => esc_html__('After billing form', 'woocommerce-checkout-manager'),
          'before_order_notes' => esc_html__('Before order notes', 'woocommerce-checkout-manager'),
          'after_order_notes' => esc_html__('After order notes', 'woocommerce-checkout-manager'),
        ),
        'default' => 'before_order_notes',
      )
    );
  }

  // Admin Order
  // ---------------------------------------------------------------------------

  function add_order_data($order)
  {

    include_once(WOOCCM_PLUGIN_DIR . 'includes/view/frontend/class-wooccm-fields-handler.php');

    if ($fields = WOOCCM()->additional->get_fields()) {
      $template = WOOCCM()->additional->get_template_types();
      $options = WOOCCM()->additional->get_option_types();
      $multiple = WOOCCM()->additional->get_multiple_types();
?>
      </div>
      <style>
        #order_data .order_data_column {
          width: 23%;
        }

        #order_data .order_data_column .wooccm-premium {
          width: 100% !important;
          float: none !important;
          clear: both;
        }

        #order_data .order_data_column .wooccm-premium:after,
        #order_data .order_data_column .wooccm-premium:before {
          display: block;
          content: "";
          clear: both;
        }

        #order_data .order_data_column_additional .form-field {
          width: 100%;
          clear: both;
        }
      </style>
      <div class="order_data_column order_data_column_additional">
        <h3>
          <?php esc_html_e('Additional', 'woocommerce-checkout-manager'); ?>
          <a href="#" class="edit_address"><?php esc_html_e('Edit', 'woocommerce-checkout-manager'); ?></a>
          <span>
            <a href="<?php echo esc_url(WOOCCM_PURCHASE_URL); ?>" class="load_customer_additional" target="_blank" style="display:none;font-size: 13px;font-weight: 400;">
              <?php esc_html_e('This is a premium feature.', 'woocommerce-checkout-manager'); ?>
            </a>
          </span>
        </h3>
        <div class="address">
          <?php
          foreach ($fields as $field_id => $field) {

            $key = sprintf('_%s', $field['key']);

            if (!$value = get_post_meta($order->get_id(), $key, true)) {

              $value = maybe_unserialize(get_post_meta($order->get_id(), sprintf('%s', $field['name']), true));

              if (is_array($value)) {
                $value = implode(',', $value);
              }

              update_post_meta($order->get_id(), $key, $value);
              delete_post_meta($order->get_id(), sprintf('%s', $field['name']));
            }

            if ($value) {
          ?>
              <p id="<?php echo esc_attr($field['key']); ?>" class="form-field form-field-wide form-field-type-<?php echo esc_attr($field['type']); ?>">
                <strong title="<?php echo esc_attr(sprintf(esc_html__('ID: %s | Field Type: %s', 'woocommerce-checkout-manager'), $key, esc_html__('Generic', 'woocommerce-checkout-manager'))); ?>">
                  <?php printf('%s', $field['label'] ? esc_html($field['label']) : sprintf(esc_html__('Field %s', 'woocommerce-checkout-manager'), $field_id)); ?>
                </strong>
                <?php echo esc_html($value); ?>
              </p>
          <?php
            }
          }
          ?>
        </div>
        <div class="edit_address">
          <?php
          foreach ($fields as $field_id => $field) {

            if (in_array($field['type'], $template)) {
              continue;
            }

            $key = sprintf('_%s', $field['key']);

            $field['id'] = sprintf('_%s', $field['key']);
            $field['name'] = $field['key'];
            $field['value'] = null;
            $field['class'] = join(' ', $field['class']);
            $field['wrapper_class'] = 'wooccm-premium';

            if (!$field['value'] = get_post_meta($order->get_id(), $key, true)) {

              $field['value'] = maybe_unserialize(get_post_meta($order->get_id(), sprintf('%s', $field['name']), true));

              if (is_array($field['value'])) {
                $field['value'] = implode(',', $field['value']);
              }
            }

            switch ($field['type']) {
              case 'textarea':
                woocommerce_wp_textarea_input($field);
                break;
              default:
                $field['type'] = 'text';
                woocommerce_wp_text_input($field);
                break;
            }
          }
          ?>
        </div>
      <?php
    }
  }

  // Admin
  // ---------------------------------------------------------------------------

  public function add_header()
  {
    global $current_section;
      ?>
      <li><a href="<?php echo admin_url('admin.php?page=wc-settings&tab=wooccm&section=additional'); ?>" class="<?php echo ($current_section == 'additional' ? 'current' : ''); ?>"><?php esc_html_e('Additional', 'woocommerce-checkout-manager'); ?></a> | </li>
  <?php
  }

  public function add_section()
  {

    global $current_section, $wp_roles, $wp_locale;

    if ('additional' == $current_section) {

      $fields = WOOCCM()->additional->get_fields();
      $defaults = WOOCCM()->additional->get_defaults();
      $types = WOOCCM()->additional->get_types();
      $conditionals = WOOCCM()->additional->get_conditional_types();
      $option = WOOCCM()->additional->get_option_types();
      $multiple = WOOCCM()->additional->get_multiple_types();
      $template = WOOCCM()->additional->get_template_types();
      $disabled = WOOCCM()->additional->get_disabled_types();
      $product_categories = $this->get_product_categories();
      $settings = $this->get_settings();

      include_once(WOOCCM_PLUGIN_DIR . 'includes/view/backend/pages/additional.php');
    }
  }
}

WOOCCM_Field_Controller_Additional::instance();
