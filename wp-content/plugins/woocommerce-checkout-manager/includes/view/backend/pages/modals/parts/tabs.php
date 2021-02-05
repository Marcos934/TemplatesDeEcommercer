<ul class="wc-tabs">
  <li class="media-modal-tab active">
    <a href="#general"><span><?php esc_html_e('General', 'woocommerce-checkout-manager'); ?></span></a>
  </li>
  <# if ( _.contains(<?php echo json_encode(array('select', 'multiselect')); ?>, data.type)) { #>
  <li class="media-modal-tab">
    <a href="#select2"><span><?php esc_html_e('Select2', 'woocommerce-checkout-manager'); ?></span></a>
  </li>
  <# } #>
  <# if ( _.contains(<?php echo json_encode($option); ?>, data.type)) { #>
  <li class="media-modal-tab">
    <a href="#options"><span><?php esc_html_e('Options', 'woocommerce-checkout-manager'); ?></span></a>
  </li>
  <# } #>
  <li class="media-modal-tab">
    <a href="#filter"><span><?php esc_html_e('Filter', 'woocommerce-checkout-manager'); ?></span></a>
  </li>
  <li class="media-modal-tab">
    <a href="#display"><span><?php esc_html_e('Display', 'woocommerce-checkout-manager'); ?></span></a>
  </li>
  <# if ( !_.contains(<?php echo json_encode(array_merge($option, $template)); ?>, data.type)) { #>
  <li class="media-modal-tab">
    <a href="#price"><span><?php esc_html_e('Price', 'woocommerce-checkout-manager'); ?></span></a>
  </li>
  <# } #>
  <# if (data.type == 'time') { #>
  <li class="media-modal-tab">
    <a href="#timepicker"><span><?php esc_html_e('Timepicker', 'woocommerce-checkout-manager'); ?></span></a>
  </li>
  <# } #>
  <# if (data.type == 'date') { #>
  <li class="media-modal-tab">
    <a href="#datepicker"><span><?php esc_html_e('Datepicker', 'woocommerce-checkout-manager'); ?></span></a>
  </li>
  <# } #>
  <# if ( !_.contains(<?php echo json_encode($template); ?>, data.type)) { #>
  <li class="media-modal-tab">
    <a href="#admin"><span><?php esc_html_e('Admin', 'woocommerce-checkout-manager'); ?></span></a>
  </li>
  <# } #>
</ul>