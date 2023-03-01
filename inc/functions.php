<?php
/**
 * Customize the Thank You page for Lipa Na M-Pesa payments
 */
 
 add_action( 'some_action_hook', 'wc_lipa_na_mpesa_thankyou_page' );

 
function wc_lipa_na_mpesa_thankyou_page( $order_id ) {
    // get the order object
    $order = wc_get_order( $order_id );
    
    // get the payment method used for the order
    $payment_method = $order->get_payment_method();
    
    // check if the payment method is Lipa Na M-Pesa
    if ( 'lipa_na_mpesa' === $payment_method ) {
        // display a custom message for Lipa Na M-Pesa payments
        echo '<p>Thank you for your payment using Lipa Na M-Pesa.</p>';
    }
}



?>