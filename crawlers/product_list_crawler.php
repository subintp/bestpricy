<?php


  // get url from database
	$base_url = "http://uae.souq.com/ae-en/mobile-phone/l/";



	$total_products = get_total_products($base_url);
	echo "total_products =". $total_products."<br>";

	$total_pages = ceil($total_products/32);
	echo "total_pages =". $total_pages."<br>";

	for ($i=1; $i <= $total_pages; $i++) {

		$url = $base_url."?page=".$i;
		echo "url =".$url."</br>";
		get_products_list($url);
	}	



function get_total_products($url){

	$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';

	$ch = curl_init();	 

	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
	 

	$page_content = curl_exec($ch);
	$page_content = explode('id="col3_content"',$page_content);
	$page_content = $page_content[1];

	$total_products = explode('<em>',$page_content);
	$total_products = explode('</em>',$total_products[1]);
	$total_products = $total_products[0];

	curl_close($ch);

	return $total_products;
}


function get_products_list($url) {

	$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';

	$ch = curl_init();	 

	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
	 

	$page_content = curl_exec($ch);
	$page_content = explode('id="col3_content"',$page_content);
	$page_content = $page_content[1];

	$products_list = explode('id="ItemResultList"',$page_content);
	$products_list = explode('class="position-relative txt11"',$products_list[1]);
	$products_list = explode('class="width-160 height-140',$products_list[0]);
	array_shift($products_list);

	foreach ($products_list as $product) {
		$product_url = explode('<a href="',$product);
		$product_url = explode('" title="',$product_url[1]);
		$product_url = $product_url[0];
		echo $product_url;
		echo "<br>";
		// save url to database
	}

	curl_close($ch);

}



?>