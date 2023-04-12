<?php 

// Dodanie pol dla firm
add_filter( 'woocommerce_checkout_fields' , 'bbloomer_display_checkbox_and_new_checkout_field' );
    
function bbloomer_display_checkbox_and_new_checkout_field( $fields ) {
    
    $fields['billing']['checkbox_business_trigger'] = array(
        'type'      => 'checkbox',
        'label'     => __('Kupuję jako firma', 'woocommerce'),
        'class'     => array('form-row-wide'),
        'clear'     => true,
    	'priority'  => 21
    );   
        
    $fields['billing']['company_field'] = array(
        'label'     => __('Nazwa firmy', 'woocommerce'),
        'class'     => array('form-row-wide'),
        'clear'     => true,
    	'priority'  => 22	
    );
        
    $fields['billing']['nip_field'] = array(
        'label'     => __('NIP', 'woocommerce'),
        'class'     => array('form-row-wide'),
        'clear'     => true,
    	'priority'  => 23
    );
        
    return $fields;
    
}


// Pokazywanie/ukrywanie nazwy firmy i NIPu
function bbloomer_conditionally_hide_show_new_field() {
    
    ?>
    <script type="text/javascript">
		
		 if (jQuery('#billing_country').val() != 'PL'){
            jQuery('#checkbox_business_trigger_field').hide();
        }
		jQuery('#billing_country').on('change',function() {
                if (jQuery('#billing_country').val() != 'PL'){
                jQuery('#checkbox_business_trigger_field').hide();
            } else {
            jQuery('#checkbox_business_trigger_field').show();
            }
        })
        
        jQuery('#company_field_field').fadeOut();
        jQuery('#nip_field_field').fadeOut();
        
        jQuery('input#checkbox_business_trigger').change(function(){
            if (this.checked) {
            jQuery('#company_field_field').fadeIn();
            jQuery('#company_field_field input').val('');
            jQuery('#nip_field_field').fadeIn();
            jQuery('#nip_field_field input').val('');             
            } else {
            jQuery('#company_field_field').fadeOut();
            jQuery('#nip_field_field').fadeOut();
            }
            
        });
    </script>
    <?php
        
}

add_action( 'woocommerce_after_checkout_form', 'bbloomer_conditionally_hide_show_new_field', 6);


// Zapisanie nazwy firmy i NIPu w zamowieniu
add_action( 'woocommerce_checkout_update_order_meta', 'checkout_vat_number_update_order_meta' );

function checkout_vat_number_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['company_field'] ) ) {
        update_post_meta( $order_id, '_company_field', sanitize_text_field( $_POST['company_field'] ) );
    }
    if ( ! empty( $_POST['nip_field'] ) ) {
        update_post_meta( $order_id, '_nip_field', sanitize_text_field( $_POST['nip_field'] ) );
    }
}


// Wyświetlenie nazwy firmy i NIPu w podsumowaniu zamowienia
add_action( 'woocommerce_thankyou', 'vat_number_display_thankyou', 10, 1 );

function vat_number_display_thankyou( $order_id ) {
    echo '<section class="woocommerce-customer-details"><h2 class="woocommerce-column__title">Dane do faktury:</h2><address><strong>' . __( 'Nazwa firmy', 'woocommerce' ) . ':</strong> ' . get_post_meta( $order_id, '_company_field', true ) . '<br />' . '<strong>' . __( 'NIP', 'woocommerce' ) . ':</strong> ' . get_post_meta( $order_id, '_nip_field', true ) . '</address></section>';
}


// Wyświetlenie nazwy firmy i NIPu w Panelu Admina
add_action( 'woocommerce_admin_order_data_after_billing_address', 'vat_number_display_admin_order_meta', 10, 1 );

function vat_number_display_admin_order_meta( $order ) {
    echo '<p><strong>' . __( 'Nazwa firmy', 'woocommerce' ) . ':</strong> ' . get_post_meta( $order->get_id(), '_company_field', true ) . '</p>';
    echo '<p><strong>' . __( 'NIP', 'woocommerce' ) . ':</strong> ' . get_post_meta( $order->get_id(), '_nip_field', true ) . '</p>';

}


// Wyświetlenie nazwy firmy i NIPu w mailu
add_filter( 'woocommerce_email_order_meta_keys', 'vat_number_display_email' );

function vat_number_display_email( $keys ) {
        $keys['Nazwa firmy'] = '_company_field';
        $keys['NIP'] = '_nip_field';
        return $keys;
}
