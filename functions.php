<?php
/**
 * Understrap functions and definitions
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$understrap_includes = array(
	'/theme-settings.php',                  // Initialize theme default settings.
	'/setup.php',                           // Theme setup and custom theme supports.
	'/widgets.php',                         // Register widget area.
	'/enqueue.php',                         // Enqueue scripts and styles.
	'/template-tags.php',                   // Custom template tags for this theme.
	'/pagination.php',                      // Custom pagination for this theme.
	'/hooks.php',                           // Custom hooks.
	'/extras.php',                          // Custom functions that act independently of the theme templates.
	'/customizer.php',                      // Customizer additions.
	'/custom-comments.php',                 // Custom Comments file.
	'/jetpack.php',                         // Load Jetpack compatibility file.
	'/class-wp-bootstrap-navwalker.php',    // Load custom WordPress nav walker.
	'/woocommerce.php',                     // Load WooCommerce functions.
	'/editor.php',                          // Load Editor functions.
	  '/deprecated.php',                      // Load deprecated functions.
	  '/ordrar.php'

);

foreach ( $understrap_includes as $file ) {
	$filepath = locate_template( 'inc' . $file );
	if ( ! $filepath ) {
		trigger_error( sprintf( 'Error locating /inc%s for inclusion', $file ), E_USER_ERROR );
	}
	require_once $filepath;
}
function registerRoute($namespace, $uri, $args)
{
    add_action('rest_api_init', function () use ($namespace, $uri, $args) {
        $args['show_in_index'] = false;

        register_rest_route($namespace, $uri, $args);
    });
}
registerRoute('myname', 'customerssystem', ['methods' => 'GET', 'callback' => function($req) {
	global $wpdb;
	$arg = get_posts(array(
		'post_type'=>'produkter',
		'posts_per_page'=>-1
	));
	$maing = array();
	foreach($arg as $single){
		$id = $single->ID;

		$MainState = array(
			'id'=>$id,
	   'img'=>get_the_post_thumbnail_url($id),
	      'title'=>get_the_title($id),
	   'price'=>get_field('price_cat',$id),
	   'cat'=>get_field('categories_cat',$id),
	   'des'=>$single->post_content
		);
		array_push($maing,$MainState);
	}

	return $maing;
   
}]);
registerRoute('myname', 'receiveorder', ['methods' => 'POST', 'callback' => function($req) {
	global $wpdb;
	$cart = $req['cart'];
	$user = $req['user'];
	$post_content = array();
	$post_content['cart']= $cart;
	$post_content['user']= $user;
	wp_insert_post( array(
		'post_type'=>'ordrar',
		'post_status'=>'publish',
		'post_content'=>json_encode($post_content,true),
		'post_title'=>date('Y m d').'_'.rand(100,1000)

	) );
	return $post_content;
   

}]);


registerRoute('myname', 'SendOrder', ['methods' => 'POST', 'callback' => function($req) {
	global $wpdb;
	$cart =$req['item']['des']['cart'];
	$user = $req['item']['des']['user'];
	$to = $user['email'];
$subject = 'Offert från EC UTBILDNING';
$body  = 'Hej kolla gärna din order här '.'<br>';
$body .= get_the_permalink($req['item']['id']);
$headers = array('Content-Type: text/html; charset=UTF-8');
wp_mail($to,$subject,$message,$headers);
	return true;

}]);

registerRoute('myname', 'UpdateOrder', ['methods' => 'POST', 'callback' => function($req) {
	global $wpdb;
	$cart = $req['order']['des']['cart'];
	$user = $req['order']['des']['user'];
	$post_content = array();
	$post_content['cart']= $cart;
	$post_content['user']= $user;
	wp_update_post(array(
		'ID'=>$req['order']['id'],
		'post_type'=>'ordrar',
		'post_status'=>'publish',
		'post_content'=>json_encode($post_content,true),
	) );
	return $post_content;
   

}]);


registerRoute('myname', 'deleteOrder', ['methods' => 'POST', 'callback' => function($req) {
	global $wpdb;
	$id = $req['id'];
	wp_delete_post( $id, true );
	return 'success';
	}]);
registerRoute('myname', 'ordersssystem', ['methods' => 'GET', 'callback' => function($req) {
	global $wpdb;
	$arg = get_posts(array(
		'post_type'=>'ordrar',
		'posts_per_page'=>-1
	));
	 $maing = array();
	foreach($arg as $single){
		$id = $single->ID;

		$Order = array(
			'id'=>$id,
	   
	   'des'=>json_decode($single->post_content,true)
		);
		array_push($maing,$Order);
	}

	return $maing ;
   
}]);


