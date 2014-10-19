<?php

	include("../database/database.php");

	$DBH = get_DB();

  
   if (isset($DBH)) {

		$STH = $DBH->prepare('SELECT * FROM `bestpricy`.vendor');
		$STH->execute();
		$vendors =$STH->fetchAll();


	 $STH = $DBH->prepare('SELECT * FROM `bestpricy`.product_details WHERE visited = 0 ');
	  $STH->execute();
	  $products = $STH->fetchAll();

	//while ($product = $STH->fetch()) { 
	  foreach ($products as $product){

	  	 $product_name = format_name($product['name']);
	 	 $category_id =$product['category_id'];
	 	 $product_id = $product['id'];

	  	echo "product name =".$product_name."<br>";

	  	foreach ($vendors as $vendor){

	  		$vendor_id = $vendor['id'];
	  		$vendor_name = $vendor['name'];	  	
	  		$search_url = $vendor['search_url'];
	  		$url = url_maker($product_name,$vendor_name,$search_url);
	  		$price =fetch_price($url, $vendor_name);
        	echo "<br> url =".$url."  price=".$price;
			
			if($price!=null){

       			$price = explode('.', $price);
   				$price = floatval(str_replace(',', '', $price[0]));		
				echo "<br> url =".$url."  price=".$price;
				$d = date("Y-m-d H:i:s");
				echo "<br>";
				echo $d;
  		        $STH = $DBH->prepare('SELECT * FROM `bestpricy`.price_details WHERE product_id = :product_id AND category_id=:category_id AND  vendor_id=:vendor_id ');
	 		 	$STH->execute(array("product_id"=>$product_id,"category_id"=>$category_id,"vendor_id"=>$vendor_id));
				$Old =$STH->fetch();
				print_r($Old);	
				
				if($Old!=null){
					$Oprice = $Old['price'];
					echo "<br> old_price=".$Oprice;
					echo "<br> new_price=".$price;
			
					if($Oprice!=$price){
						echo " DB UPDATE";
						echo $price;	
						$STH = $DBH->prepare('UPDATE  `bestpricy`.price_details SET price = :price,date = :date
							WHERE product_id=:product_id and category_id=:category_id and vendor_id=:vendor_id');
						$STH->execute(array("price" => $price,"date" =>$d,"product_id"=>$product_id, "category_id"=>$category_id, "vendor_id"=>$vendor_id));
					}
      			}
      		else{
      		echo "INSIDE DB INSERT";
      		$STH = $DBH->prepare('INSERT INTO `bestpricy`.price_details (product_id,category_id,vendor_id,price,date) VALUES (:product_id,:category_id,:vendor_id,:price,:d)');
      		$STH->execute(array(':product_id' => $product_id,':category_id' => $category_id, ':vendor_id' => $vendor_id,':price' => $price,':d' => $d));
    		}
    	
    	}
    }
    	echo "<br><br>";
    }
}
	
	  






	/*// TODO CHECK PRODUCT AVAILABILITY

	$product_name = "nokia lumia 520";

	//$url = "https://uae.jadopado.com/search?sort=products&query=nokia%20lumia%20620&hitsPerPage=24&page=0";
	//$url = "http://www.mygsm.me/search.php?getProductsByLetters=1&letters=nokia+lumia+520";
	//$url = "http://www.aido.com/eshop/faces/tiles/search.jsp?q=apple+iphone+5s";
	// $url = "http://uae.souq.com/ae-en/apple-iphone-5s-cable/s/";
	//$url ="https://uae.jadopado.com/search?sort=products&query=lkjlkfjslkfjslkfjslfd";
	//$url = "http://www.mygsm.me/search.php?getProductsByLetters=1&letters=sdfsdlkfjsdlkfjslf"; 
	$vendor = "aido";


	$url = url_maker($product_name, $vendor);
	echo "url =".$url;

	fetch_price($url, $vendor);*/

	function format_name($name) {
		$name = explode('(', $name);
		if (isset($name[1])) {
			return $name[0];
		}
		return $name;
	}




	function url_maker($product_name, $vendor, $search_url) {

		$product_name = urlencode($product_name);
		$category_name = "mobile";
		switch ($vendor) {
			case "mobileshop":
				$url = $search_url.$product_name;
				break;

			case "mygsm":
				$url = $search_url.$product_name."+".$category_name;
				break;

			case "jadopado":
				$url = $search_url.$product_name."+".$category_name."&hitsPerPage=24&page=0";
				break;
			
			case "aido":
				$url = $search_url.$product_name."+".$category_name;
				break;

			case "souq":
				$product_name = urldecode($product_name);
				$product_name = str_replace(array(" "), '-', $product_name);
				$url = $search_url.$product_name."/s/";
				break;		
		}
		return $url;
	}

	function fetch_price($url,$vendor){

		$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';

		$ch = curl_init();	 

		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		 

		$page_content = curl_exec($ch);

		if($errno = curl_errno($ch)) {
		    $error_message = curl_strerror($errno);
		    echo "cURL error ({$errno}):\n {$error_message}";
		}

		// www.mobileshop.ae

		if ($vendor == "mobileshop") {

			
			$price=null;
			$product_content = explode('class="span4 product-block"', $page_content);

			if (isset($product_content[1])) {

				$product_url = explode('pavicon-details" href="', $product_content[1]);
				$product_url = explode('">', $product_url[1]);
				$product_url = $product_url[0];

				//echo "mobile shop url =".$product_url;

				$price = explode('class="price">', $product_content[1]);
				$price = explode('AED', $price[1]);
				$price = $price[0];
				//echo "<br> mobile shop price =".$price;

			}
			return $price;

		}
		

		//www.mygsm.me

		if ($vendor == "mygsm") {
			/*echo $url;
			$page_content=htmlspecialchars($page_content);
			echo $page_content;*/
			$price=null;
			$product_content = explode('class="clearing"', $page_content);
			if (isset($product_content[1])) {

				$product_url = explode("auto-rs-l-section'><a href=", $product_content[0]);

				if (isset($product_url[1])) {

					$product_url = explode(" ><img", $product_url[1]);
					$product_url = $product_url[0];
				//	echo "my gsm url =".$product_url;

					$price = explode('AED', $product_content[0]);
					$price = explode('</div>', $price[1]);
					$price = $price[0];
				//	echo "<br>mygsm.me price = ".$price;				
				}
				
			}
			return $price;
		}


		// jadopado.com
		if ($vendor == "jadopado") {

			$price=null;
			$status = explode('prd_count_inner', $page_content);
			$status = explode('class="clear"', $status[1]);

			if (strpos($status[0],'found') !== false) {
				$product_content = explode('jp_product_list',$page_content);
				$product_content = explode('jp_product_wrapper_new', $product_content[1]);

				$product_url = explode('<a href="', $product_content[1]);
				$product_url = explode('" title="', $product_url[1]);
				$product_url = "www.jadopado.com".$product_url[0];
				//echo " jadopado url =".$product_url;

				$price = explode('AED', $product_content[1]);
				if (strpos($price[0],'old_price jp-productPrice') !== false){
					$price = explode('</strong>', $price[2]);
				}
				else{
					$price = explode('</strong>',$price[1]);
				}
				$price = $price[0];
				//echo "<br> jadopado price =".$price;
		
			}		
			return $price;
		}
		


		//aido.com
		if ($vendor == "aido") {
			
			$price=null;
			$product_content = explode('prodInfo_110x140_01_None prodInfo_110x140_01_None_2', $page_content);

			if (isset($product_content[1])) {

				$product_url = explode('class="proImg"', $product_content[1]);
				$product_url = explode('<a href="', $product_url[1]);
				$product_url = explode('" title=', $product_url[1]);
				$product_url = "www.aido.com".$product_url[0];


				//echo "aido url =".$product_url;

				$price = explode('class="offer-price"',$product_content[1]);
				$price = explode('AED</span>', $price[1]);
				$price = explode('</p>', $price[1]);
				$price = $price[0];
				//echo "<br> aido price =".$price;			
			}	
			return $price;
		}
		

		//souq.com

		if ($vendor == "souq") {

			$price=null;
			$page_content = explode('id="col3_content"',$page_content);
			$page_content = $page_content[1];

			$products_list = explode('id="ItemResultList"',$page_content);

			if (isset($products_list[1])) {
				$products_list = explode('class="position-relative txt11"',$products_list[1]);
				$products_list = explode('class="width-160 height-140',$products_list[0]);

				$product_content = $products_list[1];

				$product_url = explode('<a href="', $product_content);
				$product_url = explode('"', $product_url[1]);
				$product_url = $product_url[0];
				//echo " souq url =".$product_url;

				$price = explode('small-price', $product_content);
				$price = explode("AED",$price[1]);

				if (sizeof($price) == 3) {
					// offer price
					$price = explode('<span', $price[1]);
					$price = explode('</span>', $price[0]);
					$price = $price[2];
				//	echo "price =".$price;
				} else if (sizeof($price) == 2) {
					//without offer price
					$price = explode('<span', $price[0]);
					$price = explode('marb-5">', $price[0]);
					$price = $price[1];
				//	echo "<br> souq price = ".$price;		
				}
			}	
			return $price;	
		}

				
		curl_close($ch);
		
	}
?>