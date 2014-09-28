<?php

	// TODO CHECK PRODUCT AVAILABILITY

	$url = "http://mobileshop.ae/index.php?route=product/search&search=nokia+lumia";
	//$url = "https://uae.jadopado.com/search?sort=products&query=nokia%20lumia%20620&hitsPerPage=24&page=0";
	//$url = "http://www.mygsm.me/search.php?getProductsByLetters=1&letters=nokia+lumia+520";
	//$url = "http://www.aido.com/eshop/faces/tiles/search.jsp?q=apple+iphone+5s";
	// $url = "http://uae.souq.com/ae-en/apple-iphone-5s-cable/s/";
	//$url ="https://uae.jadopado.com/search?sort=products&query=lkjlkfjslkfjslkfjslfd";
	//$url = "http://www.mygsm.me/search.php?getProductsByLetters=1&letters=sdfsdlkfjsdlkfjslf"; 
	$vendor = "mobileshop";
	

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

		$product_content = explode('class="span4 product-block"', $page_content);

		if (isset($product_content[1])) {

			$product_url = explode('pavicon-details" href="', $product_content[1]);
			$product_url = explode('">', $product_url[1]);
			$product_url = $product_url[0];

			echo "url =".$product_url;

			$price = explode('class="price">', $product_content[1]);
			$price = explode('AED', $price[1]);
			$price = $price[0];
			echo "<br>price =".$price;

		}
		

	}
	

	//www.mygsm.me

	if ($vendor == "mygsm") {

		$product_content = explode('class="clearing"', $page_content);
		if (isset($product_content[1])) {

			$product_url = explode("auto-rs-l-section'><a href=", $product_content[0]);

			if (isset($product_url[1])) {

				$product_url = explode(" ><img", $product_url[1]);
				$product_url = $product_url[0];
				echo "url =".$product_url;

				$price = explode('AED', $product_content[0]);
				$price = explode('</div>', $price[1]);
				$price = $price[0];
				echo "<br>www.mygsm.me price = ".$price;				
			}
			
		}
	
	}


	// jadopado.com
	if ($vendor == "jadopado") {

		$status = explode('prd_count_inner', $page_content);
		$status = explode('class="clear"', $status[1]);

		if (strpos($status[0],'found') !== false) {
			$product_content = explode('jp_product_list',$page_content);
			$product_content = explode('jp_product_wrapper_new', $product_content[1]);

			$product_url = explode('<a href="', $product_content[1]);
			$product_url = explode('" title="', $product_url[1]);
			$product_url = "www.jadopado.com".$product_url[0];
			echo "url =".$product_url;

			$price = explode('AED', $product_content[1]);
			$price = explode('</strong>',$price[1]);
			$price = $price[0];
			echo "<br>price =".$price;
		}		
	}
	


	//aido.com
	if ($vendor == "aido") {
		$product_content = explode('prodInfo_110x140_01_None prodInfo_110x140_01_None_2', $page_content);

		if (isset($product_content[1])) {

			$product_url = explode('class="proImg"', $product_content[1]);
			$product_url = explode('<a href="', $product_url[1]);
			$product_url = explode('" title=', $product_url[1]);
			$product_url = "www.aido.com".$product_url;

			echo "url =".$product_url;

			$price = explode('class="offer-price"',$product_content[1]);
			$price = explode('AED</span>', $price[1]);
			$price = explode('</p>', $price[1]);
			$price = $price[0];
			echo "price =".$price;			
		}	

	}
	

	//souq.com

	if ($vendor == "souq") {

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
			echo "url =".$product_url;

			$price = explode('small-price', $product_content);
			$price = explode("AED",$price[1]);

			if (sizeof($price) == 3) {
				// offer price
				$price = explode('<span', $price[1]);
				$price = explode('</span>', $price[0]);
				$price = $price[2];
				echo "price =".$price;
			} else if (sizeof($price) == 2) {
				//without offer price
				$price = explode('<span', $price[0]);
				$price = explode('marb-5">', $price[0]);
				$price = $price[1];
				echo "price = ".$price;		
			}
		}		
	}	

	curl_close($ch);

?>