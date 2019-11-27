

add_shortcode('subscription_form', function($atts) {
	
	$atts = shortcode_atts(
	  array(
		'form-id' => '1',
	), $atts, 'subscription_form' );
	
	if ( !is_user_logged_in() ) {
		
		return '<style>.pg_signin_form_wrapper { margin: 20px auto 0 !important; } .pg_signin_form_wrapper form { padding: 20px !important; }</style><h3 style="text-align: center;">Login or create a new account</h3>' . 
				do_shortcode('[signin_form nopattern="true"]') .
				do_shortcode('[signup_form nopattern="true"]');
		
	}
	
	$result = '';

	$result .= '<div class="subscription-tabs-wrapper">';
	$result .= '  <a href="#" class="tab active" data-idx="0">Step 1</a>';
	$result .= '  <a href="#" class="tab" data-idx="1">Step 2</a>';
	//$result .= '  <a href="#" class="tab" data-idx="2">Step 3</a>';
	$result .= '</div>';
	
	$user = get_userdata(get_current_user_id());
	
	$result .= '<div class="user-info-holder">';
	$result .= ' <span data-name="fname" data-value="' . get_user_meta(get_current_user_id(), 'billing_first_name', true) . '"></span>';
	$result .= ' <span data-name="lname" data-value="' . get_user_meta(get_current_user_id(), 'billing_last_name', true) . '"></span>';
	$result .= ' <span data-name="email" data-value="' . get_user_meta(get_current_user_id(), 'billing_email', true) . '"></span>';
	$result .= ' <span data-name="address" data-value="' . get_user_meta(get_current_user_id(), 'billing_address_1', true) . '"></span>';
	$result .= ' <span data-name="city" data-value="' . get_user_meta(get_current_user_id(), 'billing_city', true) . '"></span>';
	$result .= ' <span data-name="phone" data-value="' . get_user_meta(get_current_user_id(), 'billing_phone', true) . '"></span>';
	$result .= '</div>';
	
	$result .= do_shortcode('[contact-form-7 id="' . $atts['form-id'] . '"]');
	
	$result .= '<div class="subscription-nav">';
	$result .= '  <a href="#" class="cta-1 nav-btn nav-btn--prev">PREVIOUS</a>';
	$result .= '  <a href="#" class="cta-1 nav-btn nav-btn--next active">NEXT</a>';
	$result .= '  <a href="#" class="cta-1 nav-btn nav-btn--pay" id="jsSubscriptionPayButton">PAY SUBSCRIPTION</a>';
	$result .= '</div>';
	
	return $result;
	
});


/* Subscription Custom Post type */

function cpt_subs() {

  $supports = array(
    'title',
	'author',
	'custom-fields',
  );

  $labels = array(
	'name' 				=> _x('Subscriptions', 'plural'),
	'singular_name' 	=> _x('Subscriptions', 'singular'),
	'menu_name' 		=> _x('Subscriptions', 'admin menu'),
	'name_admin_bar' 	=> _x('Subscriptions', 'admin bar'),
	'add_new' 			=> _x('Add New', 'add new'),
	'add_new_item' 		=> __('Add New Subscription'),
	'new_item' 			=> __('New Subscription'),
	'edit_item' 		=> __('Edit Subscription'),
	'view_item' 		=> __('View Subscriptions'),
	'all_items' 		=> __('All Subscriptions'),
	'search_items' 		=> __('Search Subscriptions'),
	'not_found' 		=> __('No Subscriptions found.'),
  );

  $args = array(
	'supports' 		=> $supports,
	'labels' 		=> $labels,
	'public' 		=> false,
	'query_var' 	=> false,
	'rewrite' 		=> array('slug' => 'subscriptions'),
	'has_archive' 	=> false,
	'hierarchical' 	=> false,
	'capability_type' => 'post',
	'show_in_menu' => true,
	'show_ui' => true,
	'menu_position' => 5
  );

  register_post_type('subscription', $args);

}

add_action('init', 'cpt_subs');

function subs_add_meta_boxes( $post ){
	add_meta_box( 'sub_meta_box', 'Subscription Data', 'sub_build_meta_box', 'subscription', 'advanced', 'low' );
}
add_action( 'add_meta_boxes_subscription', 'subs_add_meta_boxes' );

function sub_build_meta_box( $post ){

	$first_name = get_post_meta( $post->ID, 'sub_fname', true ) != '' ? get_post_meta( $post->ID, 'sub_fname', true ) : 'Nan';
	$last_name = get_post_meta( $post->ID, 'sub_lname', true ) != '' ? get_post_meta( $post->ID, 'sub_lname', true ) : 'Nan';
	$email = get_post_meta( $post->ID, 'sub_email', true ) != '' ? get_post_meta( $post->ID, 'sub_email', true ) : 'Nan';
	$city = get_post_meta( $post->ID, 'sub_city', true ) != '' ? get_post_meta( $post->ID, 'sub_city', true ) : 'Nan';
	$tel = get_post_meta( $post->ID, 'sub_tel', true ) != '' ? get_post_meta( $post->ID, 'sub_tel', true ) : 'Nan';
	$status = get_post_meta( $post->ID, 'sub_status', true ) != '' ? get_post_meta( $post->ID, 'sub_status', true ) : 'Cancelled';
	

	?>
	<style>
		#cstm-hdr-print{
			display:none;
		}
		@media print{
		@page { size: auto;  margin: 0mm;}
			body{ 
				visibility:hidden;
			}
			#advanced-sortables {
				padding:10px;
				position:fixed;
				top:0;
				left:0;
				right:0;
				visibility:visible;
				display:block !important;
				background:#000;
			}
			#cstm-print-btn{
				display: none;
			}
			textarea{
				height:100px !important;
			}
			#cstm-hdr-print{
				display:block;
				text-align:center;
			}
			.hndle.ui-sortable-handle{
				display:none;
			}


		}
	</style>
	<div class='print'>
	  <h2 id="cstm-hdr-print">The Gorgeous Flower Company Subscription Print Form</h2>

      <div style="background-color: #f2f2f2; padding: 15px;">
	    Created: <strong><?php echo get_the_date(); ?></strong><br><br>
		Transaction ID: <strong><?php echo get_post_meta($post->ID, 'sub_transaction_id', true); ?></strong><br><br>
		Telr Transaction Ref: <strong><?php echo get_post_meta($post->ID, 'telr_transaction_ref', true); ?></strong><br><br>
		Subscription status: <strong><?php echo $status; ?></strong><br><br>
		Agreement ID: <strong><?php echo get_post_meta($post->ID, 'sub_agreement_id', true); ?></strong>
	  </div>

	  <h3>Subscription Details</h3>
	  <?php echo 'How often would you like to receive your gorgeous bunch of flowers: <strong>' . get_post_meta($post->ID, 'sub_length', true); ?></strong><br><br>
	  <?php echo 'Subscription Size: <strong>' . get_post_meta($post->ID, 'sub_size', true); ?></strong><br><br>
	  <?php echo 'Subscription Notes:'; ?><br><br>
	  <textarea readonly style="resize: none; width: 100%; height: 200px;"><?php echo get_post_meta($post->ID, 'sub_notes', true); ?></textarea>
	  <hr style="margin-top: 20px;">
	  <h3>Delivery Details</h3>
	  <?php echo 'Delivery Address: <strong>' . get_post_meta($post->ID, 'sub_address', true); ?></strong><br><br>
	  <?php echo 'Delivery Area: <strong>' . get_post_meta($post->ID, 'sub_area', true); ?></strong><br><br>
	  <?php echo 'Delivery Area Details: <strong>' . get_post_meta($post->ID, 'sub_area_details', true); ?></strong><br><br>
	  <?php echo 'Delivery Time: <strong>' . get_post_meta($post->ID, 'sub_deliverytime', true); ?></strong><br><br>
	  <?php echo 'Delivery Address Notes:'; ?><br><br>
	  <textarea readonly style="resize: none; width: 100%; height: 200px;"><?php echo get_post_meta($post->ID, 'sub_address_notes', true); ?></textarea>
	  <hr style="margin-top: 20px;">
      <h3>Personal Details</h3>
	  <?php echo 'First Name: <strong>' . $first_name; ?></strong><br><br>
	  <?php echo 'Last Name: <strong>' . $last_name; ?></strong><br><br>
	  <?php echo 'Email: <strong>' . $email; ?></strong><br><br>
	  <?php echo 'City: <strong>' . $city; ?></strong><br><br>
	  <?php echo 'Tel: <strong>' . $tel; ?></strong><br><br>
	<!-- 	  <div class="debug-area" style="margin-bottom: 20px;">
	  	<h3>Debug </h3>
	  	<textarea readonly style="resize: none; width: 100%; height: 200px;"><?php echo get_post_meta($post->ID, 'sub_debug', true); ?></textarea>
	  </div> -->
	</div>
	<input type="submit" class="button button-primary button-large" id="cstm-print-btn" value="Print Details" onclick="window.print();return false;" />
	<?php
}

// Add subscription AJAX

/**
 * Enqueue our JS file
 */
function sub_enqueue_scripts() {
  wp_register_script( 'sub-script', get_template_directory_uri() . '/js/subs-ajax.js', array( 'jquery-blockui' ), time(), true );
  wp_localize_script( 'sub-script',
    'prefix_vars',
    array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    )
  );
  wp_enqueue_script( 'sub-script' );
}
add_action( 'wp_enqueue_scripts', 'sub_enqueue_scripts' );

function prefix_add_subscription() {

  $output = array();	
  
  if ( $_POST['subaction'] == 'add' ) {
  
	// Add the Subscription
	$transaction_ref = uniqid('gfs', false);
  
    $post_id = wp_insert_post(array(
      'post_type'	=> 'subscription',
	  'post_status'	=> 'publish',
	  'post_title'	=> 'Subscription_' . $transaction_ref
    ), false);
  
    $values = $_POST['values'];
    foreach ( $values as $key => $val ) {
	  add_post_meta($post_id, $key, $val);
    }
	
	// Pay for the Subscription (telr request)
	
	// Create an initial 12-digit transaction reference code
	//i'm not sure we need this one.... check for delete
	//$transaction_ref = join('', array_map(function($value) { return $value == 1 ? mt_rand(1, 9) : mt_rand(0, 9); }, range(1, 12)));
	add_post_meta($post_id, 'sub_transaction_id', $transaction_ref);

	$sub_size = get_post_meta($post_id, 'sub_size', true);
	$sub_length = get_post_meta($post_id, 'sub_length', true);

	if( $sub_size == "Gorgeous" ){
		$price = 180;
	} elseif( $sub_size == "Extra Gorgeous" ){
		$price = 250;
	} else {
		$price = 350;
	}

	/*if( $sub_length == "4 Weeks" ){
		$sub_interval = 4;
	} elseif( $sub_length == "3 Weeks" ){
		$sub_interval = 3;
	} else {
		$sub_interval = 2;
	}*/
	$output['sub_length'] = $sub_length;
	$sub_interval = preg_replace('/[^0-9]/', '', $sub_length);
	$output['sub_interval'] = $sub_interval;

	/* Fix fo1 week error */
	if( $sub_interval == '' ){
		$sub_interval = 1;
	}
	
	$params = array(
		'ivp_method' => 'create',
		'ivp_store' => '*ENTER YOUR TELR STORE*',
		'ivp_authkey' => '*ENTER YOUR TELR AUTH KEY*',
		'ivp_cart' => $transaction_ref,
		'ivp_test' => 0,
		'ivp_amount' => $price,
		'ivp_currency' => 'AED',
		'ivp_desc' => $sub_size . ' flowers every ' . $sub_length,
		'return_auth' => home_url('/subscription/thanks'),
		'return_can' => home_url('/subscription/canceled'),
		'return_decl' => home_url('/subscription/declined'),
		'repeat_amount' => $price,
		'repeat_period' => 'W',
		'repeat_interval' => $sub_interval,
		'repeat_start' => 'next',
		'repeat_term' => 0,
		'repeat_final' => 0,
		
		'bill_fname'	=> $_POST['values']['sub_fname'],
		'bill_lname'	=> $_POST['values']['sub_lname'],
		'bill_addr1'	=> $_POST['values']['sub_address'],
		'bill_city'		=> $_POST['values']['sub_city'],
		'bill_email'	=> $_POST['values']['sub_email'],
		'bill_country'	=> 'AE'
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://secure.telr.com/gateway/order.json");
	curl_setopt($ch, CURLOPT_POST, count($params));
	curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
	$results = curl_exec($ch);
	curl_close($ch);

	$results_json = $results;
	$results = json_decode($results,true);

	$url= trim($results['order']['url']);
	$ref= trim($results['order']['ref']);

	add_post_meta($post_id, 'telr_transaction_ref', $ref);
	add_post_meta($post_id, 'sub_status', 'NotPaid');
	add_post_meta($post_id, 'sub_debug', $results_json);

	$_SESSION['telr_ref'] = $ref;
	$_SESSION['subscription_post_id'] = $post_id;

	$output['success'] = 1;
	$output['url'] = $url;

  
  } elseif ( $_POST['subaction'] == 'remove' ) {

	$currentDateDay = date('l');
	if( $currentDateDay != 'Tuesday' || $currentDateDay != 'Wednesday' || $currentDateDay != 'Thursday' ){
		$post_id = $_POST['postid'];
		//cancel the agreement in Telr
		$url = 'https://secure.innovatepayments.com/tools/api/xml/agreement/' . get_post_meta($post_id, 'sub_agreement_id', true);
		$credentials = base64_encode('5690:0e374ae4MN4c3Ds5kZz6Zfkd');
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$result = json_decode($result);
		curl_close($ch);

		// Send Email before delete post
		$to =  get_post_meta($post_id, 'sub_email', true );
		$subject = 'Subscription Canceled';
		$message = "
					<table style='max-width:600px; margin:0 auto; font-family: arial;'>
						<tr>
							<td style='width:100%; height:auto; padding:30px; background: #F0537E; color:#fff;'>
								<h1 style='margin: 0;'>Your subscription has been canceled!</h1>
							</td>
						</tr>
						<tr>
							<td style='padding:20px; '>
									<p>Dear <strong>". ucfirst(get_post_meta($post_id, 'sub_fname', true )) ."</strong>,</p>
									<p>You have successfully canceled your subscription on gorgeous flowers.</p>
							</td>
						</tr>
						<tr style='background: #262626; color:#c7c7c7;'>
							<td style='padding:20px; '>
								<p style='text-align: center'><strong>Copyright @ 2019 | <a href='//www.thegorgeousflowerco.com/' style='color:#c7c7c7;'>THE GORGEOUS FLOWER COMPANY</a></strong>
									<br/>We are committed to gorgeous bouquets and great customer service.</p>
							</td>
						</tr>
					</table>
		";
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail($to, $subject, $message ,$headers );

		//delete the post afterwards
		wp_delete_post($post_id, true);

		$output['success'] = 1;
		//$output['output'] = $result;
	}else{
		$output['success'] = 0;
	}

  } elseif ( $_POST['subaction'] == 'edit' ) {

	$post_id = $_POST['postid'];

	$sub_area = $_POST['sub_area'];
	$sub_area_details = $_POST['sub_area_details'];
	$sub_deliverytime = $_POST['sub_deliverytime'];
	$sub_notes = $_POST['sub_notes'];
	$sub_address_notes = $_POST['sub_address_notes'];
	$sub_address = $_POST['sub_address'];

	update_post_meta( $post_id, 'sub_area', $sub_area );
	update_post_meta( $post_id, 'sub_area_details', $sub_area_details );
	update_post_meta( $post_id, 'sub_deliverytime', $sub_deliverytime );
	update_post_meta( $post_id, 'sub_notes', $sub_notes );
	update_post_meta( $post_id, 'sub_address_notes', $sub_address_notes );
	update_post_meta( $post_id, 'sub_address', $sub_address );

	$output['success'] = 1;
	  
  }
  
  wp_send_json( $output );
  exit;
}
add_action( 'wp_ajax_add_subscription', 'prefix_add_subscription' );
add_action( 'wp_ajax_nopriv_add_subscription', 'prefix_add_subscription' );

/**
 * @snippet       WooCommerce Add New Tab @ My Account
 * @how-to        Watch tutorial @ //businessbloomer.com/?p=19055
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 3.5.7
 * @donate $9     //businessbloomer.com/bloomer-armada/
 */
  
// ------------------
// 1. Register new endpoint to use for My Account page
// Note: Resave Permalinks or it will give 404 error
add_action( 'init', function() {
  add_rewrite_endpoint( 'subscription-list', EP_ROOT | EP_PAGES );
});
  
// ------------------
// 2. Add new query var
add_filter( 'query_vars', function( $vars ) {
  $vars[] = 'subscription-list';
  return $vars;
}, 0 );
  
// ------------------
// 3. Insert the new endpoint into the My Account menu 
add_filter( 'woocommerce_account_menu_items', function( $items ) {
    $items['subscription-list'] = __('My Subscriptions', 'woocommerce');
    return $items;
});
  
// ------------------
// 4. Add content to the new endpoint
  
function gfs_subscription_list_content() {
	global $current_user; // Call the current user ID
    wp_get_current_user();
  $sub_query = new WP_Query(array(
    'post_type'			=> 'subscription',
	'posts_per_page'	=> -1,
	'author' 			=> $current_user->ID,
	'meta_key'			=> 'sub_status',
	'meta_value'		=> 'Active'
  ));
  if ( $sub_query->have_posts() ) { $counter = 0;
	while ( $sub_query->have_posts() ) {
	  
	  $sub_query->the_post();

	  $sub_notes = get_post_meta(get_the_ID(), 'sub_notes', true);
	  $sub_address = get_post_meta(get_the_ID(), 'sub_address', true);
	  $sub_area = get_post_meta(get_the_ID(), 'sub_area', true);
	  $sub_area_details = get_post_meta(get_the_ID(), 'sub_area_details', true);
	  $sub_address_notes = get_post_meta(get_the_ID(), 'sub_address_notes', true);
	  $sub_deliverytime = get_post_meta(get_the_ID(), 'sub_deliverytime', true);

	  $sub_area_select = '<select class="sub-area-select"><option value="Area" '.(($sub_area === "Area") ? "selected" : "" ).'>Area</option><option value="Area1" '.(($sub_area === "Area1") ? "selected" : "" ).'>Area1</option><option value="Area2" '.(($sub_area === "Area2") ? "selected" : "" ).'>Area2</option></select>';

	  $sub_area_details_select = '<select class="sub-area-details-select"><option value="Villa / Apt / Office" '.(($sub_area_details === "Villa / Apt / Office") ? "selected" : "" ).'>Villa / Apt / Office</option><option value="House" '.(($sub_area_details === "House") ? "selected" : "" ).'>House</option></select>';

	  $sub_deliverytime_select = '<select class="sub-deliverytime-select"><option value="9AM-12PM" '.(($sub_deliverytime === "9AM-12PM") ? "selected" : "" ).'>9AM-12PM</option><option value="12PM-3PM" '.(($sub_deliverytime === "12PM-3PM") ? "selected" : "" ).'>12PM-3PM</option><option value="3PM-6PM" '.(($sub_deliverytime === "3PM-6PM") ? "selected" : "" ).'>3PM-6PM</option><option value="AFTER 6PM" '.(($sub_deliverytime === "AFTER 6PM") ? "selected" : "" ).'>AFTER 6PM</option></select>';

	  $sub_notes_textarea = '<textarea class="sub-notes-textarea">'. $sub_notes .'</textarea>';
	  $sub_address_notes_textarea = '<textarea class="sub-address-notes-textarea">'. $sub_address_notes .'</textarea>';
	  $sub_address_input = '<input type="text" class="sub-address-input" value="'. $sub_address .'"/>';
	  
	  echo '<div class="acount-subscription-wrapper" data-post="' . get_the_ID() . '">';
	  echo '  <div class="title"><span>[' . ($counter + 1) . ']</span>&nbsp;&nbsp;Subscription - ' . get_post_meta(get_the_ID(), 'sub_fname', true) . ' ' . get_post_meta(get_the_ID(), 'sub_lname', true) . ' - ' . get_the_date() . '</div>';
	  
	  echo '  <div class="content">';
	  echo '    <div class="entry entry--date">' . '<span>Created</span><span>' . get_the_date() . '</span></div>';
	  echo '    <div class="entry entry--trans">' . '<span>Transaction ID</span><span>' . get_post_meta(get_the_ID(), 'sub_transaction_id', true) . '</span></div>';
	  
	  echo '    <h3>Subscription Details</h3>';
	  echo '    <div class="entry">' . '<span>Length</span><span>' . get_post_meta(get_the_ID(), 'sub_length', true) . '</span></div>';
	  echo '    <div class="entry">' . '<span>Size</span><span>' . get_post_meta(get_the_ID(), 'sub_size', true) . '</span></div>';
	  echo '    <div class="entry">' . '<span>Notes</span><span>' . $sub_notes_textarea . '</span></div>';
	  
	  echo '    <h3>Delivery Details</h3>';
	  echo '    <div class="entry">' . '<span>Address</span><span>' . $sub_address_input . '</span></div>';
	  echo '    <div class="entry">' . '<span>Area</span><span>' . $sub_area_select . '</span></div>';
	  echo '    <div class="entry">' . '<span>Area Details</span><span>' . $sub_area_details_select . '</span></div>';
	  echo '    <div class="entry">' . '<span>Address Notes</span><span>' . $sub_address_notes_textarea . '</span></div>';
	  
	  echo '    <h3>Personal Details</h3>';
	  echo '    <div class="entry">' . '<span>First Name</span><span>' . get_post_meta(get_the_ID(), 'sub_fname', true) . '</span></div>';
	  echo '    <div class="entry">' . '<span>Last Name</span><span>' . get_post_meta(get_the_ID(), 'sub_lname', true) . '</span></div>';
	  echo '    <div class="entry">' . '<span>Email</span><span>' . get_post_meta(get_the_ID(), 'sub_email', true) . '</span></div>';
	  echo '    <div class="entry">' . '<span>City</span><span>' . get_post_meta(get_the_ID(), 'sub_city', true) . '</span></div>';
	  echo '    <div class="entry">' . '<span>Telephone</span><span>' . get_post_meta(get_the_ID(), 'sub_tel', true) . '</span></div>';
  	  $currentDateDay = date('l');
	  if( $currentDateDay == 'Tuesday' || $currentDateDay == 'Wednesday' || $currentDateDay == 'Thursday'  ){
	  	$cancelSubClass = 'can-not-unsub';
	  }else{
	  	$cancelSubClass = '';
	  }
	  echo '    <div class="btn-wrapper">';
	  echo '    	<a href="#" class="cta-2 jsSubscriptionUpdateButton">UPDATE SUBSCRIPTION</a> <a href="#" class="cta-1 jsSubscriptionCancelButton '.$cancelSubClass.' '.$currentDateDay.'">CANCEL SUBSCRIPTION</a>';
	  echo '    </div>';

	  echo '  </div>';
	  
	  echo '</div>';
	  
	  $counter++;
	  
	}  
  }
  else{
	  echo do_shortcode('[elementor-template id="659"]');
  }
  
}
  
add_action( 'woocommerce_account_subscription-list_endpoint', 'gfs_subscription_list_content' );
// Note: add_action must follow 'woocommerce_account_{your-endpoint-slug}_endpoint' format

//After a successfull subscription
function subscription_thanks_funcs($atts, $content = null) {
	extract(shortcode_atts(array(
		"title" => 'Thank you',
		"subtitle" => 'You\'ve subscribed to weekly flowers.'
	), $atts));

	$output = '';

	if( !session_id())
		session_start();

	$ref = $_SESSION['telr_ref'];
	$post_id = $_SESSION['subscription_post_id'];

	if( $ref && $post_id ){

		$params = array(
			'ivp_method' => 'check',
			'ivp_store' => '*ENTER YOUR TELR STORE*',
			'ivp_authkey' => '*ENTER YOUR TELR AUTH KEY*',
			'order_ref' => $ref,			
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://secure.telr.com/gateway/order.json");
		curl_setopt($ch, CURLOPT_POST, count($params));
		curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		$results = curl_exec($ch);
		curl_close($ch);

		$results_json = $results;
		$results = json_decode($results,true);
		
		$orderstatus = trim($results['order']['transaction']['status']);
		$ref = trim($results['order']['transaction']['ref']);
		$agreement_id = trim($results['order']['agreement']['id']);

		$sub_debug = get_post_meta($post_id, 'sub_debug');
		$sub_debug = $sub_debug . '\n\n' . $results_json;
		update_post_meta($post_id, 'sub_debug', $sub_debug);
		
		if( $orderstatus != "" ){
			
			$test = update_post_meta($post_id, 'sub_status', 'Active');
			add_post_meta($post_id, 'sub_agreement_id', $agreement_id);

			$output = '<h2 style="font-weight: bold;">'.$title.'</h2><br/><p>'.$subtitle.'</p>';
			
			$output .= '<div style="margin: 40px 0; font-size: 16px;">Subscription<span style="display: block; color: #f0537e; font-size: 20px; font-weight: bold; margin-top: 7px;">';
			$output .= get_post_meta($post_id, 'sub_size', true) . ' - ' . get_post_meta($post_id, 'sub_length', true);
			$output .= '</span></div>';
			
			$output .= '<div style="overflow: hidden; margin-bottom: 40px;">';
			$output .= '<div style="float: left; margin-right: 50px; font-size: 16px;">Total<span style="display: block; color: #f0537e; font-size: 20px; font-weight: bold; margin-top: 7px;">';
			$output .= 'AED ' . ( get_post_meta($post_id, 'sub_size', true) === 'Gorgeous' ? '180' : ( get_post_meta($post_id, 'sub_size', true) === 'Extra Gorgeous' ? '250' : '350' ) );
			$output .= '</span></div>';
			$output .= '<div style="float: left; font-size: 16px;">Payment Method<span style="display: block; color: #f0537e; font-size: 20px; font-weight: bold; margin-top: 7px;">';
			$output .= 'Credit Card';
			$output .= '</span></div>';
			$output .= '</div>';
			
			$output .= '<h4 style="font-weight: bold;">Have a Gorgeous Day</h4>';
			
			$nextThursday_day_representation = date('l', strtotime('next thursday') );
			$nextThursday_delivery_date = date('jS', strtotime('next thursday') );
			$nextThursday_month = date('F', strtotime('next thursday') );
			$to =  get_post_meta($post_id, 'sub_email', true );
			$subject = 'Subscription Successful';
			$message = "
						<table style='max-width:600px; margin:0 auto; font-family: arial;'>
							<tr>
								<td style='width:100%; height:auto; padding:30px; background: #F0537E; color:#fff;'>
									<h1 style='margin: 0;'>Thank you for Subscribing!</h1>
								</td>
							</tr>
							<tr>
								<td style='padding:20px; '>
										<p>Dear <strong>". ucfirst(get_post_meta($post_id, 'sub_fname', true )) ."</strong>,</p>
										<p>
											I’m delighted that you have decided to give our gorgeous flowers a go.
										</p>
										<p>
											Here’s how it works....
										</p>
										<p>
											Your deliveries will arrive on ". $nextThursday_day_representation ."’s and your first delivery will be ". $nextThursday_day_representation ." ". $nextThursday_delivery_date ." of ". $nextThursday_month .".
										</p>
										<p>
											If you pop over to our website <a href='//www.thegorgeousflowerco.com/my-account/'>The Gorgeous Flower Company</a> and sign in to your account you can do all sorts of things.	
										</p>
										<hr/>
										<p>
											Enjoy your first box on Thursday and if you have any questions or suggestions please do get in touch <a href='mailto:info@thegorgeousflowerco.com'>info@thegorgeousflowerco.com</a>
										</p>
								</td>
							</tr>
							<tr style='background: #262626; color:#c7c7c7;'>
								<td style='padding:20px; '>
									<p style='text-align: center'><strong>Copyright @ 2019 | <a href='//www.thegorgeousflowerco.com/' style='color:#c7c7c7;'>THE GORGEOUS FLOWER COMPANY</a></strong>
										<br/>We are committed to gorgeous bouquets and great customer service.</p>
								</td>
							</tr>
						</table>
			";
			
			$headers = array('Content-Type: text/html; charset=UTF-8');
			wp_mail($to, $subject, $message ,$headers );
		} else {
			$output = '<h2>Something went wrong...</h2><br/><br/><p>Please contact us for assistance. 1</p>';
		}

	} 
	else {

		$output = '<h2>Something went wrong...</h2><br/><br/><p>Please contact us for assistance. 2</p>';
	}

	return $output;
	
}
add_shortcode("subscription_thanks", "subscription_thanks_funcs");

//register a session
function register_session(){
    if( !session_id() )
        session_start();
}
add_action('init','register_session');

//Cancel agreement if the admin deletes a subscription from the Dashboard
function cancel_agreement_before_delete_post( $postid ){

    global $post_type;   
	if ( $post_type != 'subscription' ) return;

	$agreement_id = get_post_meta($postid, 'sub_agreement_id', true);

	//cancel the agreement in Telr
	if( $agreement_id ){

		$url = '//secure.innovatepayments.com/tools/api/xml/agreement/' . $agreement_id;
		$credentials = base64_encode('5690:0e374ae4MN4c3Ds5kZz6Zfkd');
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);

	}

}
add_action( 'before_delete_post', 'cancel_agreement_before_delete_post' );

// Disable POSTCODE
add_filter( 'woocommerce_default_address_fields', 'customise_postcode_fields' );
function customise_postcode_fields( $address_fields ) {
    $address_fields['postcode']['required'] = false;
	$address_fields['postcode']['validate'] = false;
	
	unset($address_fields['postcode']);

    return $address_fields;
}

function pg_reorder_tabs( $tabs ) {

	$tabs['myreminder']['priority'] = 1;

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'pg_reorder_tabs', 99 );


add_action('wp_ajax_sor_update_info', 'sor_update_info');
function sor_update_info() {
  if ( isset( $_POST['info'] ) ) {
			
	update_user_meta(get_current_user_id(), 'billing_first_name', $_POST['info']['fname']);
	update_user_meta(get_current_user_id(), 'billing_last_name', $_POST['info']['lname']);
	update_user_meta(get_current_user_id(), 'billing_email', $_POST['info']['email']);
	update_user_meta(get_current_user_id(), 'billing_phone', $_POST['info']['tel']);
	update_user_meta(get_current_user_id(), 'billing_address_1', $_POST['info']['address']);
	
  }
  die();
}
