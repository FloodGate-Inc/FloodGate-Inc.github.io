<?php
/*
Plugin Name: Atom Payment Gateway
Plugin URI: http://atomtech.in
Description: Extends WooCommerce 3 by Adding the Paynetz Gateway.
Version: 3.2
Author: Atom Paynetz
Author URI: http://atomtech.in
*/

// Include our Gateway Class and register Payment Gateway with WooCommerce
add_action( 'plugins_loaded', 'woocommerce_atom_init', 0 );
define('IMGDIR', WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__)) . '/assets/img/');
require_once 'AtomAES.php';

function woocommerce_atom_init() {
 
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) return; 
   
    class WC_Gateway_Atom extends WC_Payment_Gateway {

        function __construct() {
            global $woocommerce;
            global $wpdb;
            $this->id = "atom";
            $this->icon = IMGDIR . 'logo.png';
            $this->method_title = __( "Online Payment", 'wc_gateway_atom' );
            $this->method_description = "Online Payment setting page.";
            $this->title = __( "Online Payment", 'wc_gateway_atom' );
            $this->has_fields = false;
            $this->init_form_fields();
            $this->init_settings();
            $this->url 				= $this->settings['atom_domain'];
            $this->atom_port		= $this->settings['atom_port'];
            $this->login_id 		= $this->settings['login_id'];
            $this->password 		= $this->settings['password'];
            $this->description 		= $this->settings['description'];
            $this->atom_product_id  = $this->settings['atom_prod_id'];
            $this->req_hash_code = $this->settings['req_hash_code'];
            $this->res_hash_code = $this->settings['res_hash_code'];
            $this->req_enc_key = $this->settings['req_enc_key'];
            $this->req_salt_key = $this->settings['req_salt_key'];
            $this->res_enc_key = $this->settings['res_enc_key'];
            $this->res_salt_key = $this->settings['res_salt_key'];

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

//            $checkout_url = wc_get_checkout_url();
//            $this->notify_url = $checkout_url;

            $this->check_atom_response();

        }

        public function update_ransaction_status() {

            global $wpdb;
            $held_duration = get_option( 'woocommerce_hold_stock_minutes' );

            if ( $held_duration < 1 || get_option( 'woocommerce_manage_stock' ) != 'yes' )
                return;

            $date = date( "Y-m-d H:i:s", strtotime( '-' . absint( 0 ) . ' MINUTES', current_time( 'timestamp' ) ) );

            $unpaid_orders = $wpdb->get_results( $wpdb->prepare( "
			SELECT posts.ID, postmeta.meta_key, postmeta.meta_value, posts.post_modified
			FROM {$wpdb->posts} AS posts
			RIGHT JOIN {$wpdb->postmeta} AS postmeta ON posts.id=postmeta.post_id
			WHERE 	posts.post_type   IN ('" . implode( "','", wc_get_order_types() ) . "')
			AND 	posts.post_status = 'wc-pending'
			AND 	posts.post_modified + INTERVAL 10 MINUTE < %s
		", $date ) );
            
        
            $pending_array = [];
            foreach($unpaid_orders as $value){
                if($value->meta_key == '_order_total'){
                   array_push($pending_array, $value);
                }
            }
            
            if(!empty($pending_array)){
     
                foreach($pending_array as $val){
                   
                    $mer_txn=$val->ID;
                    $amt=$val->meta_value;
                    $date = date("Y-m-d", strtotime($val->post_modified));
                    $merchant_id = $this->login_id;
                    
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => "https://paynetzuat.atomtech.in/paynetz/vfts?merchantid=".$merchant_id."&merchanttxnid=".$mer_txn."&amt=".$amt."&tdate=".$date,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_PORT => 443,
                      CURLOPT_RETURNTRANSFER => 1,    
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "GET",
                      CURLOPT_POSTFIELDS => "merchantid=192&merchanttxnid=700004977456&amt=900.00&tdate=2020-04-11",
                      CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: application/x-www-form-urlencoded"
                      ),
                     CURLOPT_USERAGENT =>'woo-commerce plugin',  
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    if ($err) {
                       echo '<div class="woocommerce-error">Curl error: "'. $err.". Error in gateway credentials.</div>";
                       exit;
                    } 
                    
                    curl_close($curl);
                
                    $parser = xml_parser_create('');
                    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
                    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
                    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
                    xml_parse_into_struct($parser, trim($response), $xml_values);
                    xml_parser_free($parser);
                    $result_resp=$xml_values[0]['attributes']['VERIFIED'];

                    $unpaid_order=$mer_txn;
                         
                    if ($unpaid_order) {
                        $order = wc_get_order( $unpaid_order );
                        if ( apply_filters( 'woocommerce_cancel_unpaid_order', 'checkout' === get_post_meta( $unpaid_order, '_created_via', true ), $order ) ) {
                            if($result_resp=='SUCCESS'){
                                $order->update_status( 'completed', __( 'Unpaid order completed - time limit reached.', 'woocommerce' ) );
                            }
                        }
                    }
                }
            } 
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'type'             => 'checkbox',
                    'label'         => __('Enable Online Payment Module.', 'wc_gateway_atom'),
                    'default'         => 'no',
                    'description'     => 'Show in the Payment List as a payment option'
                ),
                'title' => array(
                    'title'         => __('Title:', 'wc_gateway_atom'),
                    'type'            => 'text',
                    'default'         => __('Pay Online', 'wc_gateway_atom'),
                    'description'     => __('This controls the title which the user sees during checkout.', 'wc_gateway_atom'),
                    'desc_tip'         => true
                ),
                'description' => array(
                    'title'         => __('Description:', 'wc_gateway_atom'),
                    'type'             => 'textarea',
                    'default'         => __("Pay securely by Credit or Debit Card or Internet Banking through Secure Servers."),
                    'description'     => __('This controls the description which the user sees during checkout.', 'wc_gateway_atom'),
                    'desc_tip'         => true
                ),
                'atom_domain' => array(
                    'title'         => __('Specify Domain', 'wc_gateway_atom'),
                    'type'             => 'text',
                    'description'     => __('Will be provided by Atom Paynetz Team after production movement', 'wc_gateway_atom'),
                    'desc_tip'         => true
                ),
                'login_id' => array(
                    'title'         => __('Login Id', 'wc_gateway_atom'),
                    'type'             => 'text',
                    'description'     => __('As provided by Atom Paynetz Team', 'wc_gateway_atom'),
                    'desc_tip'         => true
                ),
                'password' => array(
                    'title'         => __('Password', 'wc_gateway_atom'),
                    'type'             => 'password',
                    'description'     => __('As provided by Atom Paynetz Team', 'wc_gateway_atom'),
                    'desc_tip'         => true
                ),
                'atom_prod_id'     => array(
                    'title'         => __('Product ID', 'wc_gateway_atom'),
                    'type'             => 'text',
                    'description'     =>  __('Will be provided by Atom Paynetz Team after production movement', 'wc_gateway_atom'),
                    'desc_tip'         => true
                ),
                'atom_port'     => array(
                    'title'         => __('Port Number', 'wc_gateway_atom'),
                    'type'             => 'text',
                    'description'     =>  __('80 for Test Server & 443 for Production Server', 'wc_gateway_atom'),
                    'desc_tip'         => true
                ),
                'req_hash_code'     => array(
                    'title'         => __('Request Hashcode', 'wc_gateway_atom'),
                    'type'             => 'text',
                    'description'     =>  __('Request hash code, provided by Atom', 'wc_gateway_atom'),
                    'desc_tip'         => true
                ),
                'req_enc_key'     => array(
                    'title'         => __('Request Encypriton Key', 'wc_gateway_atom'),
                    'type'             => 'text',
                    'description'     =>  __('Request Encypriton Key, provided by Atom', 'wc_gateway_atom'),
                    'desc_tip'         => true
                ),
                'req_salt_key'     => array(
                    'title'         => __('Request Salt Key', 'wc_gateway_atom'),
                    'type'             => 'text',
                    'description'     =>  __('Request Salt Key, provided by Atom', 'wc_gateway_atom'),
                    'desc_tip'         => true
                ),
                
                'res_hash_code'     => array(
                    'title'         => __('Response Hashcode', 'wc_gateway_atom'),
                    'type'             => 'text',
                    'description'     =>  __('Response hash code, provided by Atom', 'wc_gateway_atom'),
                    'desc_tip'         => true
                ),
                'res_enc_key'     => array(
                    'title'         => __('Response Encypriton Key', 'wc_gateway_atom'),
                    'type'             => 'text',
                    'description'     =>  __('Response Encypriton Key, provided by Atom', 'wc_gateway_atom'),
                    'desc_tip'         => true
                ),
                'res_salt_key'     => array(
                    'title'         => __('Response Salt Key', 'wc_gateway_atom'),
                    'type'             => 'text',
                    'description'     =>  __('Response Salt Key, provided by Atom', 'wc_gateway_atom'),
                    'desc_tip'         => true
                ),
            );
        }

        private function validateResponse($responseParams)
        {
            $str = $responseParams["mmp_txn"].$responseParams["mer_txn"].$responseParams["f_code"].$responseParams["prod"].$responseParams["discriminator"].$responseParams["amt"].$responseParams["bank_txn"];
            $signature =  hash_hmac("sha512",$str,$this->res_hash_code,false);
            
            if($signature == $responseParams["signature"]){
                return true;
            } else {
                return false;
            }
        }

        function check_atom_response(){
            global $woocommerce;
            global $wpdb, $woocommerce;
            
             if($_REQUEST['wc-api']== get_class( $this )){
                    $atomenc = new AtomAES();
                    $decrypted = $atomenc->decrypt($_POST['encdata'], $this->res_enc_key, $this->res_salt_key);
                    $array_response = explode('&', $decrypted); //change & to | for production
                  
                         write_log('Data received from paynetz');
                  
                    $equalSplit = array();
                    foreach ($array_response as $ar) {
                        $equalSub = explode('=', $ar);
                        if(!empty($equalSub[1]) && !empty($equalSub[0])){
                            $temp = array(
                                $equalSub[0] => $equalSub[1],
                            );
                            $equalSplit += $temp;
                        }
                    }
            
            
                    $order = new WC_Order($equalSplit['mer_txn']);

                    $order_id = $equalSplit['mer_txn'];
            
                    $VERIFIED = $equalSplit['f_code'];
                             
                                if($VERIFIED == 'Ok') {
                                     $order->payment_complete('completed');
                                     $woocommerce->cart->empty_cart();
                                     wp_safe_redirect($this->get_return_url( $order));
                                  }else if($VERIFIED == 'F') {
                                    $order->update_status('failed');
                                    $this->msg['class'] = 'woocommerce-error';
                                    $this->msg['message'] = "<b style='color:red;font-size:20px'>The transaction has been failed or declined.</b>";
                                    $this->msg['order'] = $order; 
                                    
                                    wc_add_notice( __( 'The transaction has been failed or declined.', 'woocommerce' ) ,'error');	
                                    wp_safe_redirect( wc_get_checkout_url() );	 
                                 }else{
                                    $order->update_status('cancelled');
                                    $this->msg['class'] = 'woocommerce-error';
                                    $this->msg['message'] = "<b style='color:red;font-size:20px'>The transaction has been failed or declined.</b>";
                                    $this->msg['order'] = $order; 
                                    
                                    wc_add_notice( __( 'The transaction has been cancelled.', 'woocommerce' ) ,'error');	
                                    wp_safe_redirect( wc_get_checkout_url() );	
                                }
                 
                     add_action('the_content', array(&$this, 'showMessage'));
                     exit;		
                  
                }    
            
      
        }

        function showErrMessage($content){
            $cont = '';
            $cont .= '<div class="woocommerce">';
            $cont .= '<p>'.$this->msg['message'].$content.'</p></div>';
            return $cont;
        }
        
        function showMessage ($content) {
               return '<div class="woocommerce"><div class="'.$this->msg['class'].'">'.$this->msg['message'].'</div></div>'.$content;
        }

        private function getChecksum($data){
            $amt = $data['amt'];
            //$amt = $amt.".00";
            $str = $data['login'] . $data['pass'] . "NBFundTransfer" . $data['prodid'] . $data['txnid'] . $amt . "INR";
            //echo $str.'<br>';
            //echo $data['reqHashCode'];  exit;           
            $signature =  hash_hmac("sha512",$str,$data['reqHashCode'],false);
            return $signature;
        }

        // Submit payment and handle response
        public function process_payment($order_id) {
            global $woocommerce;
            global $current_user;
            //get user details   
            $current_user	= wp_get_current_user();

            $user_email     = $current_user->user_email;
            $first_name     = $current_user->shipping_first_name;
            $last_name      = $current_user->shipping_last_name;
            $phone_number   = $current_user->billing_phone;
            $country       	= $current_user->shipping_country;
            $state       	= $current_user->shipping_state;
            $city       	= $current_user->shipping_city;
            $postcode       = $current_user->shipping_postcode;
            $address_1      = $current_user->shipping_address_1;
            $address_2      = $current_user->shipping_address_2;
            $udf1 			= $first_name." ".$last_name;
            $udf2			= $user_email;
            $udf3			= $phone_number;
            $udf4			= $country." ".$state." ".shipping_city." ".$address_1." ".$address_2." ".$postcode;

            $user_email 	= $_POST['billing_email'];
            $first_name 	= $_POST['billing_first_name'];
            $last_name  	= $_POST['billing_last_name'];
            $phone_number 	= $_POST['billing_phone'];
            $country       	= $_POST['billing_country'];
            $state       	= $_POST['billing_state'];
            $city       	= $_POST['billing_city'];
            $postcode       = $_POST['billing_postcode'];
            $address_1      = $_POST['billing_address_1'];
            $address_2      = $_POST['billing_address_2'];
            $udf1 		= $first_name." ".$last_name;
            $udf2		= $user_email;
            $udf3		= $phone_number;
            $udf4		= $country." ".$state." ".shipping_city." ".$address_1." ".$address_2." ".$postcode;

            $order 			= new WC_Order( $order_id );
            $atom_login_id 	= $this->login_id;
            $atom_password 	= $this->password;
            $atom_prod_id 	= $this->atom_product_id;
            $amount 		= $order->get_total();
            $currency 		= "INR";
            $custacc 		= "1234567890";
            $txnid 			= $order_id;    
            $clientcode 	= urlencode(base64_encode(007));
            $datenow 		= date("d/m/Y h:m:s");
            $encodedDate 	= str_replace(" ", "%20", $datenow);
            
            if(!function_exists('wc_get_checkout_url')){ 
                require_once '/includes/wc-core-functions.php'; 
            } 
            
           $this->notify_url = add_query_arg('wc-api', get_class( $this ), home_url('/'));
		   $this->return_url = add_query_arg(array('act' => "ret"), $this->notify_url);

//            $ru = wc_get_checkout_url(); 
            $ru = $this->notify_url; 
            
//            echo $this->notify_url;
//            exit;

            $data["login"] = $atom_login_id;
            $data["pass"] = $atom_password;
            $data["prodid"] = $atom_prod_id;
            $data['txnid']=$txnid;
            $data['amt'] = $amount;
            $data['reqHashCode'] = $this->req_hash_code;
            
            $data['requestEncypritonKey'] = $this->req_enc_key;
            $data['salt'] = $this->req_salt_key;
           
            $signature = $this->getChecksum($data);

            $param = "login=".$atom_login_id."&pass=".$atom_password."&ttype=NBFundTransfer"."&prodid=".$atom_prod_id."&amt=".$amount."&txncurr=".$currency."&txnscamt=0"."&clientcode=".$clientcode."&txnid=".$txnid."&date=".$encodedDate ."&custacc=".$custacc."&udf1=".$udf1."&udf2=".$udf2."&udf3=".$udf3."&udf4=".$udf4."&ru=".$ru;
            $param = $param."&signature=".$signature;
         
            $atomenc = new AtomAES();
            $encData = $atomenc->encrypt($param, $data['requestEncypritonKey'], $data['salt']);

            global $wpdb, $woocommerce;
            return array('result' => 'success', 'redirect' => $this->url."?" ."login=".$atom_login_id."&encdata=".strtoupper($encData));
            exit;
        }
    }
    
     if (!function_exists('write_log')) {
    
        function write_log($log) {
            if (true === WP_DEBUG) {
                if (is_array($log) || is_object($log)) {
                    error_log(print_r($log, true));
                } else {
                    error_log($log);
                }
            }
        }
    
    }

    add_filter( 'woocommerce_payment_gateways', 'add_atom_gateway' );
    function add_atom_gateway( $methods ) {
        $methods[] = 'WC_Gateway_Atom';
        return $methods;
    }
}
