<?php
add_action( 'rest_api_init', function () {
	register_rest_route( 'orders', '', array(
	  'methods' => 'GET',
	  'callback' => 'orders_alaa',
	) );
  } );

function orders_alaa(){
      $arg = get_posts(array(
          'post_type'=>'ordrar',
      ));
  };
$wpdb;