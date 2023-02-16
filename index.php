<?php

add_action('plugins_loaded', 'wc_add_custom_payment_method');
function wc_add_custom_payment_method()
{
    class WC_Custom_Payment_Method extends WC_Payment_Gateway
    {
        public function __construct()
        {
            $this->id                 = 'custom_payment_method';
            $this->method_title       = __('Custom Payment Method', 'textdomain');
            $this->method_description = __('Description of your custom payment method', 'textdomain');
            $this->has_fields         = true;

            $this->init_form_fields();
            $this->init_settings();

            $this->title       = $this->get_option('title');
            $this->description = $this->get_option('description');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ));
        }

        public function init_form_fields()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title'   => __('Enable/Disable', 'textdomain'),
                    'type'    => 'checkbox',
                    'label'   => __('Enable Custom Payment Method', 'textdomain'),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title'       => __('Title', 'textdomain'),
                    'type'        => 'text',
                    'description' => __('This controls the title which the user sees during checkout.', 'textdomain'),
                    'default'     => __('Custom Payment Method', 'textdomain'),
                    'desc_tip'    => true
                ),
                'description' => array(
                    'title'       => __('Description', 'textdomain'),
                    'type'        => 'textarea',
                    'description' => __('Payment method description that the customer will see on your checkout.', 'textdomain'),
                    'default'     => __('Description of your custom payment method', 'textdomain'),
                    'desc_tip'    => true
                )
            );
        }

        public function process_payment($order_id)
        {
            global $woocommerce;
            $order = new WC_Order($order_id);

            // Here you can write the code for processing the payment, for example, sending the payment to your payment gateway.
            // After processing the payment, you can return the result as an array, for example:

            // return array(
            //     'result'   => 'success',
            //     'redirect' => $this->get_return_url( $order )
            // );

            // Please note that this is a simplified example and the actual implementation will depend on the requirements of your custom payment method.

            // Mark as on-hold (we're awaiting the payment)
            $order->update_status('on-hold', __('Awaiting payment', 'textdomain'));

            // Reduce stock levels
            $order
                ->reduce_order_stock()
                ->set_customer_note(__('Awaiting payment', 'textdomain'))
                ->save();

            // Remove cart
            $woocommerce->cart->empty_cart();

            // Return thankyou redirect
            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }

          public function payment_fields()
          {
              // Description of payment method from settings
              if ($this->description) {
                  echo wpautop(wptexturize($this->description));
              }

              // Custom payment method fields
              echo '<fieldset id="wc-' . esc_attr($this->id) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background: #eee; padding: 1em 2em;">';

              // Add this action hook if you want your custom payment method to support it
              do_action('woocommerce_credit_card_form_start', $this->id);

              // I recommend to use unique IDs, because other gateways could already use #ccNo, #expdate, #cvc
              echo '<div class="form-row form-row-wide">
                      <label>Card Number <span class="required">*</span></label>
                      <input id="custom_ccNo" name="custom_ccNo" type="text" autocomplete="off">
                  </div>
                  <div class="form-row form-row-first">
                      <label>Expiry Date <span class="required">*</span></label>
                      <input id="custom_expdate" name="custom_expdate" type="text" autocomplete="off" placeholder="MM / YY">
                  </div>
                  <div class="form-row form-row-last">
                      <label>Card Code (CVC) <span class="required">*</span></label>
                      <input id="custom_cvc" name="custom_cvc" type="text" autocomplete="off" placeholder="CVC">
                  </div>
                  <div class="clear"></div>';

              do_action('woocommerce_credit_card_form_end', $this->id);

              echo '<div class="clear"></div></fieldset>';
          }

          public function validate_fields()
          {
              // Here you can write the validation rules for your custom payment method fields, for example:

              // if ( empty( $_POST['custom_ccNo'] ) ) {
              //     wc_add_notice(  'Card number is required!', 'error' );
              //     return false;
              // }

              // if ( empty( $_POST['custom_expdate'] ) ) {
              //     wc_add_notice(  'Expiry date is required!', 'error' );
              //     return false;
              // }

              // if ( empty ( $_POST['custom_cvc'] ) ) {
              //     wc_add_notice(  'Card code is required!', 'error' );
              //     return false;
              // }

              return true;
          }
    }
}

add_filter('woocommerce_payment_gateways', 'wc_add_custom_payment_method_class');
function wc_add_custom_payment_method_class($methods)
{
    $methods[] = 'WC_Custom_Payment_Method';
    return $methods;
}

add_action('wp_enqueue_scripts', 'wc_custom_payment_method_scripts');
function wc_custom_payment_method_scripts()
{
    if (! is_checkout()) {
        return;
    }

    // If you need any JavaScript to be loaded on the checkout page, write it here
}

add_action('woocommerce_order_status_on-hold', 'wc_custom_payment_method_process_payment');

function wc_custom_payment_method_process_payment($order_id)
{
    $order = new WC_Order($order_id);

    // Here you can write the code for processing the payment, for example, sending the payment to your payment gateway.
    // After processing the payment, you can update the order status, for example:

    // $order->update_status( 'processing', __( 'Payment received', 'textdomain' ) );

    // Please note that this is a simplified example and the actual implementation will depend on the requirements of your custom payment method.
}

add_action('woocommerce_thankyou_custom_payment_method', 'wc_custom_payment_method_thankyou_page');


function wc_custom_payment_method_thankyou_page($order_id)
{
    $order = new WC_Order($order_id);

    // Here you can write the code for processing the payment, for example, sending the payment to your payment gateway.
    // After processing the payment, you can update the order status, for example:

    // $order->update_status( 'processing', __( 'Payment received', 'textdomain' ) );

    // Please note that this is a simplified example and the actual implementation will depend on the requirements of your custom payment method.
}

add_action('woocommerce_email_before_order_table', 'wc_custom_payment_method_email_instructions', 10, 3);

function wc_custom_payment_method_email_instructions($order, $sent_to_admin, $plain_text = false)
{
    if ($order->get_payment_method() !== 'custom_payment_method') {
        return;
    }

    // Here you can write the code for processing the payment, for example, sending the payment to your payment gateway.
    // After processing the payment, you can update the order status, for example:

    // $order->update_status( 'processing', __( 'Payment received', 'textdomain' ) );

    // Please note that this is a simplified example and the actual implementation will depend on the requirements of your custom payment method.
}

add_action('woocommerce_api_wc_custom_payment_method', 'wc_custom_payment_method_webhook');

function wc_custom_payment_method_webhook()
{
    // Here you can write the code for processing the payment, for example, sending the payment to your payment gateway.
    // After processing the payment, you can update the order status, for example:

    // $order->update_status( 'processing', __( 'Payment received', 'textdomain' ) );

    // Please note that this is a simplified example and the actual implementation will depend on the requirements of your custom payment method.
}


add_action('woocommerce_order_status_processing', 'wc_custom_payment_method_order_status_processing');

function wc_custom_payment_method_order_status_processing($order_id)
{
    $order = new WC_Order($order_id);

    // Here you can write the code for processing the payment, for example, sending the payment to your payment gateway.
    // After processing the payment, you can update the order status, for example:

    // $order->update_status( 'processing', __( 'Payment received', 'textdomain' ) );

    // Please note that this is a simplified example and the actual implementation will depend on the requirements of your custom payment method.
}

add_action('woocommerce_order_status_completed', 'wc_custom_payment_method_order_status_completed');

function wc_custom_payment_method_order_status_completed($order_id)
{
    $order = new WC_Order($order_id);

    // Here you can write the code for processing the payment, for example, sending the payment to your payment gateway.
    // After processing the payment, you can update the order status, for example:

    // $order->update_status( 'processing', __( 'Payment received', 'textdomain' ) );

    // Please note that this is a simplified example and the actual implementation will depend on the requirements of your custom payment method.
}

add_action('woocommerce_order_status_cancelled', 'wc_custom_payment_method_order_status_cancelled');

function wc_custom_payment_method_order_status_cancelled($order_id)
{
    $order = new WC_Order($order_id);

    // Here you can write the code for processing the payment, for example, sending the payment to your payment gateway.
    // After processing the payment, you can update the order status, for example:

    // $order->update_status( 'processing', __( 'Payment received', 'textdomain' ) );

    // Please note that this is a simplified example and the actual implementation will depend on the requirements of your custom payment method.
}

add_action('woocommerce_order_status_failed', 'wc_custom_payment_method_order_status_failed');

function wc_custom_payment_method_order_status_failed($order_id)
{
    $order = new WC_Order($order_id);

    // Here you can write the code for processing the payment, for example, sending the payment to your payment gateway.
    // After processing the payment, you can update the order status, for example:

    // $order->update_status( 'processing', __( 'Payment received', 'textdomain' ) );

    // Please note that this is a simplified example and the actual implementation will depend on the requirements of your custom payment method.
}

add_action('woocommerce_order_status_refunded', 'wc_custom_payment_method_order_status_refunded');

function wc_custom_payment_method_order_status_refunded($order_id)
{
    $order = new WC_Order($order_id);

    // Here you can write the code for processing the payment, for example, sending the payment to your payment gateway.
    // After processing the payment, you can update the order status, for example:

    // $order->update_status( 'processing', __( 'Payment received', 'textdomain' ) );

    // Please note that this is a simplified example and the actual implementation will depend on the requirements of your custom payment method.
}

add_action('woocommerce_order_status_on-hold_to_processing', 'wc_custom_payment_method_order_status_on_hold_to_processing');

function wc_custom_payment_method_order_status_on_hold_to_processing($order_id)
{
    $order = new WC_Order($order_id);

    // Here you can write the code for processing the payment, for example, sending the payment to your payment gateway.
    // After processing the payment, you can update the order status, for example:

    // $order->update_status( 'processing', __( 'Payment received', 'textdomain' ) );

    // Please note that this is a simplified example and the actual implementation will depend on the requirements of your custom payment method.
}
