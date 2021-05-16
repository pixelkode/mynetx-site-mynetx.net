<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
  die;
}

delete_option('_coil_payment_pointer_trtr');
