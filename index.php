<?php

/*
Plugin Name: Lipa Na Mpesa
Plugin URI: 
Description: Lipa na Mpesa
Version:     1.0
Author:   Susan    
Author URI: 
License:     
License URI: 
Text Domain: lipa-na-mpesa
*/

add_action( 'plugins_loaded', 'wc_add_lipa_na_mpesa' );
function wc_add_lipa_na_mpesa() {
    class WC_lipa_na_mpesa extends WC_Payment_Gateway {

        public function __construct() {
            $this->id                 = 'lipa_na_mpesa';
            $this->method_title       = __( 'Lipa Na Mpesa', 'textdomain' );
            $this->method_description = __( 'Easily Add Payment Instructions for your customers to Pay Via Mpesa' );
            $this->has_fields         = true;

            $this->init_form_fields();
            $this->init_settings();

            $this->title       = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title'   => __( 'Enable/Disable', 'textdomain' ),
                    'type'    => 'checkbox',
                    'label'   => __( 'Enable Lipa Na Mpesa', 'textdomain' ),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title'       => __( 'Title', 'textdomain' ),
                    'type'        => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'textdomain' ),
                    'default'     => __( 'Custom Payment Method', 'textdomain' ),
                    'desc_tip'    => true
                ),
                'description' => array(
                    'title'       => __( 'Description', 'textdomain' ),
                    'type'        => 'textarea',
                    'description' => __( 'Payment method description that the customer will see on your checkout.', 'textdomain' ),
                    'default'     => __( 'Lipa Na Mpesa Paybill >   Forward Mpesa Message to 0745765503 ', 'textdomain' ),
                    'desc_tip'    => true
                )
            );
        }

        public function process_payment( $order_id ) {
    global $woocommerce;
    $order = new WC_Order($order_id);
    
    // function process_payment( $order_id ) {
        // $order = new WC_Order( $order_id );
    
        // Mark the order as paid
        $order->update_status( 'processing' );
    
        // Return the result to skip the payment processing step
        return array(
            'result' => 'success',
            'redirect' => $order->get_checkout_order_received_url()
        );
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
          
          public function payment_fields() {
              // Description of payment method from settings
              if ( $this->description ) {
                  echo wpautop( wptexturize( $this->description ) );
              }
  
              
              echo '<div class="form-row form-row-wide">
                <img src="image containing your paybill/till number" alt="mpesa_paybill" width="400px" height="400px"/>
              </div>';
           
          }
  
      }
  }

add_filter( 'woocommerce_payment_gateways', 'wc_add_lipa_na_mpesa_class' );
function wc_add_lipa_na_mpesa_class( $methods ) {
    $methods[] = 'WC_lipa_na_mpesa';
    return $methods;
}

add_action( 'wp_enqueue_scripts', 'wc_lipa_na_mpesa_scripts' );
function wc_lipa_na_mpesa_scripts() {
    if ( ! is_checkout() ) {
        return;
    }

    // If you need any JavaScript to be loaded on the checkout page, write it here
}

add_action( 'woocommerce_order_status_on-hold', 'wc_lipa_na_mpesa_process_payment' );

function wc_lipa_na_mpesa_process_payment( $order_id ) {
    
    // function process_payment( $order_id ) {
    // $order = new WC_Order( $order_id );

    // // Mark the order as paid
    // $order->update_status( 'completed' );

    // // Return the result to skip the payment processing step
    // return array(
    //     'result' => 'success',
    //     'redirect' => $order->get_checkout_order_received_url()
    // );
}


    // Here you can write the code for processing the payment, for example, sending the payment to your payment gateway.
    // After processing the payment, you can update the order status, for example:

    // $order->update_status( 'processing', __( 'Payment received', 'textdomain' ) );

    // Please note that this is a simplified example and the actual implementation will depend on the requirements of your custom payment method.


add_action( 'woocommerce_thankyou_lipa_na_mpesa', 'wc_lipa_na_mpesa_thankyou_page' );







function wc_lipa_na_mpesa_webhook() {
    
    $order = new WC_Order( $order_id );

    // Mark the order as paid
    $order->update_status( 'processing' );

    // Return the result to skip the payment processing step
    return array(
        'result' => 'success',
        'redirect' => $order->get_checkout_order_received_url()
    );


    // Here you can write the code for processing the payment, for example, sending the payment to your payment gateway.
    // After processing the payment, you can update the order status, for example:

    // $order->update_status( 'processing', __( 'Payment received', 'textdomain' ) );

    // Please note that this is a simplified example and the actual implementation will depend on the requirements of your custom payment method.
}


add_action( 'woocommerce_order_status_processing', 'wc_lipa_na_mpesa_order_status_processing' );

function wc_lipa_na_mpesa_order_status_processing( $order_id ) {
    $order = new WC_Order( $order_id );
    

    // Mark the order as paid
    $order->update_status( 'processing' );

    // Return the result to skip the payment processing step
    return array(
        'result' => 'success',
        'redirect' => $order->get_checkout_order_received_url()
    );



    // Here you can write the code for processing the payment, for example, sending the payment to your payment gateway.
    // After processing the payment, you can update the order status, for example:

    $order->update_status( 'processing', __( 'Payment received', 'textdomain' ) );

    // Please note that this is a simplified example and the actual implementation will depend on the requirements of your custom payment method.
}

add_action( 'woocommerce_order_status_completed', 'wc_lipa_na_mpesa_order_status_completed' );

function wc_lipa_na_mpesa_order_status_completed( $order_id ) {
    $order = new WC_Order( $order_id );


    // Mark the order as paid
    $order->update_status( 'completed' );

    // Return the result to skip the payment processing step
    return array(
        'result' => 'success',
        'redirect' => $order->get_checkout_order_received_url()
    );


    // Here you can write the code for processing the payment, for example, sending the payment to your payment gateway.
    // After processing the payment, you can update the order status, for example:

    $order->update_status( 'processing', __( 'Payment received', 'textdomain' ) );

    // Please note that this is a simplified example and the actual implementation will depend on the requirements of your custom payment method.
}








require plugin_dir_path(__FILE__) . 'inc/functions.php';

?>