<?php

class WOOCCM_Field_Controller_Billing extends WOOCCM_Field_Controller
{

    protected static $_instance;
    public $billing;

    public function __construct()
    {

        include_once(WOOCCM_PLUGIN_DIR . 'includes/model/class-wooccm-field-billing.php');

        add_action('wooccm_sections_header', array($this, 'add_header'));
        add_action('woocommerce_sections_' . WOOCCM_PREFIX, array($this, 'add_section'), 99);
        add_filter('woocommerce_admin_billing_fields', array($this, 'add_admin_billing_fields'), 999);
        //add_filter('woocommerce_admin_shipping_fields', array($this, 'add_admin_shipping_fields'));
        // add_action('woocommerce_admin_order_data_after_billing_address', array($this, 'add_order_data'));
    }

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    // Admin
    // ---------------------------------------------------------------------------

    public function add_section()
    {

        global $current_section, $wp_roles, $wp_locale;

        if ('billing' == $current_section) {

            $fields = WOOCCM()->billing->get_fields();
            $defaults = WOOCCM()->billing->get_defaults();
            $types = WOOCCM()->billing->get_types();
            $conditionals = WOOCCM()->billing->get_conditional_types();
            $option = WOOCCM()->billing->get_option_types();
            $multiple = WOOCCM()->billing->get_multiple_types();
            $template = WOOCCM()->billing->get_template_types();
            $disabled = WOOCCM()->billing->get_disabled_types();
            $product_categories = $this->get_product_categories();

            include_once(WOOCCM_PLUGIN_DIR . 'includes/view/backend/pages/billing.php');
        }
    }

    public function add_header()
    {
        global $current_section;
?>
        <li><a href="<?php echo admin_url('admin.php?page=wc-settings&tab=wooccm&section=billing'); ?>" class="<?php echo ($current_section == 'billing' ? 'current' : ''); ?>"><?php esc_html_e('Billing', 'woocommerce-checkout-manager'); ?></a> | </li>
<?php
    }

    // Admin Order
    // ---------------------------------------------------------------------------

    function add_admin_billing_fields($billing_fields)
    {

        if (!$fields = WOOCCM()->billing->get_fields()) {
            return $billing_fields;
        }

        //$defaults = WOOCCM()->billing->get_defaults();
        $template = WOOCCM()->billing->get_template_types();

        foreach ($fields as $field_id => $field) {

            if (!isset($field['name'])) {
                continue;
            }

            if (isset($billing_fields[$field['name']])) {
                continue;
            }

            if (in_array($field['name'], $template)) {
                continue;
            }

            if (!isset($field['type']) || $field['type'] != 'textarea') {
                $field['type'] = 'text';
            }

            $billing_fields[$field['name']] = $field;
            $billing_fields[$field['name']]['id'] = sprintf('_%s', (string) $field['key']);
            $billing_fields[$field['name']]['label'] = $field['label'];
            $billing_fields[$field['name']]['name'] = $field['key'];
            $billing_fields[$field['name']]['value'] = null;
            $billing_fields[$field['name']]['class'] = join(' ', $field['class']);
            //$billing_fields[$field['name']]['wrapper_class'] = 'wooccm-premium';
        }


        return $billing_fields;
    }
}

WOOCCM_Field_Controller_Billing::instance();
