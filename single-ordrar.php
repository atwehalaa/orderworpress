<?php
/**
 * The template for displaying all single posts.
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper" id="single-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

			<main class="site-main" id="main">

				<?php while ( have_posts() ) : the_post(); ?>
                  <?php $content = json_decode(get_the_content(),true);
                        $item = $content['cart'];
                        $user = $content['user'];
                      /*  print_r($content);*/
                        ?>
                        <div class="row">
                        <div class="col-md-12">
                        <h3 class="mb-4">your information</h3>
                        </div>
                        <div class="col-md-6">
                        User name: <?php echo $user['username'];?>
                        </div>
                        <div class="col-md-6">
                        Email: 
                        <?php echo $user['email']?>
                        </div>
                        <div class="col-md-6">
                        Meddelande: <?php echo $user['med'];?>
                        </div>
                        </div>
                    
                       <h3 class="mb-4 mt-4">your order </h3>
                    <div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-12 mb-4">
                                <?php
                                   foreach($item as $cart){
                                         echo '<div><img src="' .$cart['img'].'" style="width:300px;height:300px;" ></div>';
                                       }
                                ?>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">

                                     <?php
                                         foreach($item as $cart){
            
                                            echo '<h2 class="text-warning mb-4 text-center">'.$cart['title'].'</h2>';
                                            echo '<div class="mb-2">'.$cart['des'].'</div>';
                                            echo '<div class="mb-2">'.$cart['price'].'</div>';
                                            echo '<div class="mb-2"><span>category:</span>' .$cart['cat'].'</div>';
                                            echo '<div class="mb-2"><span>quantity:</span>'  .$cart['quantity'].'</div>';
                                            echo '<div class="mb-4"><span>totalprice:</span>' .$cart['totalPrice'].'</div>';
                    
                            
                                            }
                                            
                                            
                                        ?>
                           </div>
                        
                        
                  
				                <?php endwhile; // end of the loop. ?>
                        </div>
                    </div>

                    <?php
 while ( have_posts() ) : the_post();  
 $content = json_decode(get_the_content(),true);
 $item2 = $content['cart'];
 $user = $content['user'];
 $order_lines_array = array();
 $orderTotal = array_sum(array_column($item, 'totalPrice'));
 foreach($item2 as $cart){
   $orderline = 
   [
    "type"=>"physical",
     "reference"=>'ec'.rand(5,1000),
     "name"=>$cart['title'],
     "quantity"=> $cart['quantity'],
     "quantity_unit"=> "pcs",
     "unit_price"=> $cart['price']*100,
     "tax_rate"=> 0 ,
     "total_amount"=> $cart['totalPrice']*100,
     "total_discount_amount"=>0,
     "total_tax_amount"=> 0
   ];
   array_push($order_lines_array,$orderline);
  }
endwhile;
        $productss = array(
            "purchase_country"=>"SE",
            "purchase_currency"=>"SEK",
            "locale"=>"sv-se",
            "order_tax_amount"=> 0,
            "merchant_urls"=> array(
                "terms"=>"https://www.example.com/terms.html",
                "checkout"=>"https://www.example.com/checkout.html",
                "confirmation"=>"http://localhost/ordercms/confirmation/",
                "push"=>"https://www.example.com/api/push"
            )
            );
        $productss['order_amount']= $orderTotal*100;    
        $productss['order_lines']=$order_lines_array;
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.playground.klarna.com/checkout/v3/orders',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>json_encode($productss,true),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: Basic UEszNTAwNl9hYmMyYTUyYjIxMGE6VVE0UEpkcXpsVEJVWGVXbw=='
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$decoded = json_decode($response);
echo $decoded->html_snippet

?>
			</main><!-- #main -->

		

	</div><!-- #content -->

</div><!-- #single-wrapper -->
 
 
<?php get_footer(); ?>
