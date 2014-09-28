<?php

	// TODO CHECK PRODUCT AVAILABILITY

	//$url = "http://mobileshop.ae/index.php?route=product/search&search=nokia+lumia";
	//$url = "https://uae.jadopado.com/search?sort=products&query=nokia%20lumia%20620&hitsPerPage=24&page=0";
	//$url = "http://www.mygsm.me/search.php?getProductsByLetters=1&letters=nokia+lumia+520";
	//$url = "http://www.aido.com/eshop/faces/tiles/search.jsp?q=apple+iphone+5s";
	$url = "http://uae.souq.com/ae-en/apple-iphone-5s-cable/s/";
	

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

/*	// www.mobileshop.ae
	$product_content = explode('class="span4 product-block"', $page_content);
	$product_url = explode('pavicon-details" href="', $product_content[1]);
	$product_url = explode('">', $product_url[1]);
	$product_url = $product_url[0];

	echo "url =".$product_url[0];

	$price = explode('class="price">', $product_content[1]);
	$price = explode('AED', $price[1]);
	echo "<br>price =".$price[0];*/

	/*//www.mygsm.me
	$product_content = explode('class="clearing"', $page_content);
	if (isset($product_content[1])) {
		$product_url = explode("auto-rs-l-section'><a href=", $product_content[0]);
		$product_url = explode(" ><img", $product_url[1]);
		echo "url =".$product_url[0];
		// echo htmlspecialchars($product_content[0]);
		$price = explode('AED', $product_content[0]);
		$price = explode('</div>', $price[1]);
		echo "<br>www.mygsm.me price = ".$price[0];		
	}*/

	// jadopado.com
	/*$product_content = explode('jp_product_list',$page_content);
	$product_content = explode('jp_product_wrapper_new', $product_content[1]);
	$product_url = explode('<a href="', $product_content[1]);
	$product_url = explode('" title="', $product_url[1]);
	$product_url = "www.jadopado.com".$product_url[0];
	echo "url =".$product_url;
	//echo htmlspecialchars($product_content[1]);
	$price = explode('AED', $product_content[1]);
	$price = explode('</strong>',$price[1]);
	echo "price =".$price[0];*/


	//aido.com
	/*$product_content = explode('prodInfo_110x140_01_None prodInfo_110x140_01_None_2', $page_content);

	$product_url = explode('class="proImg"', $product_content[1]);
	$product_url = explode('<a href="', $product_url[1]);
	$product_url = explode('" title=', $product_url[1]);
	$product_url = "www.aido.com".$product_url[0];

	//echo "url =".$url;

	$price = explode('class="offer-price"',$product_content[1]);
	$price = explode('AED</span>', $price[1]);
	$price = explode('</p>', $price[1]);
	echo "price =".$price[0];*/


	//souq.com
	$page_content = explode('id="col3_content"',$page_content);
	$page_content = $page_content[1];

	$products_list = explode('id="ItemResultList"',$page_content);
	$products_list = explode('class="position-relative txt11"',$products_list[1]);
	$products_list = explode('class="width-160 height-140',$products_list[0]);

	$product_content = $products_list[1];

	$product_url = explode('<a href="', $product_content);
	$product_url = explode('"', $product_url[1]);
	echo "url =".$product_url[0];


	// echo htmlspecialchars($product_content);


	$price = explode('small-price', $product_content);
	$price = explode("AED",$price[1]);

	if (sizeof($price) == 3) {
		// offer price
		$price = explode('<span', $price[1]);
		$price = explode('</span>', $price[0]);
		echo "price =".$price[2];
	} else if (sizeof($price) == 2) {
		//without offer price
		$price = explode('<span', $price[0]);
		$price = explode('marb-5">', $price[0]);
		echo "price = ".$price[1];		
	}








	//echo htmlspecialchars($product_url[1]);

	//$product_url = explode('"', $product_url[1]);


	//echo htmlspecialchars($product_content[1]);



	// echo "price =".htmlspecialchars($price[0]);




	//echo htmlspecialchars($product_content[1]);
	//mygsm 
	// jadopado












	//echo htmlspecialchars($page_content);
	
	curl_close($ch);


?>