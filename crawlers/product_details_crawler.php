<?php

  include("../database/database.php");


    $DBH = get_DB();
    if (isset($DBH)) {
      //echo "connection success!";
      $STH = $DBH->prepare('SELECT * FROM `bestpricy`.product_list WHERE visited = 0');
      $STH->execute();
      while ($product = $STH->fetch()) { 
        // TODO make  visited = 1
        $url = $product['url'];
        $product_id = $product['id'];
        $category_id = $product['category_id'];

        //echo "url =".$url;
        //echo "category_id =".$category_id;
        get_product_details($url,$category_id,$product_id);

      }
    }


  function format_data($data) {
    $data = str_replace(array("\n", "\t", "\r"), '', $data);
    $data = strip_tags(trim($data));
    return $data;
  }

  function format_price($price) {
    $price = explode('.', $price);
    $price = floatval(str_replace(',', '', $price[0]));
    return $price;
  }


	function get_product_details($url,$category_id,$product_id) {

			$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';

			$ch = curl_init();	 

			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_USERAGENT, $agent);	 

			$page_content = curl_exec($ch);

			$product_details = explode('id="main"',$page_content);
			$product_details = explode('id="product-box-wrapper"', $product_details[1]);
			$product_details = explode('id="connectionsBoxHeader"',$product_details[1]);
			$product_details = $product_details[0];

			//name
			$product_name = explode('itemprop="name">', $product_details);
			$product_name = explode('</h1>', $product_name[1]);
			$product_name = format_data($product_name[0]);
			echo "name =".$product_name."<br>";

			//price
			$product_price = explode('class="price-holder', $product_details);
			$product_price = explode('>', $product_price[1]);
			$product_price = explode('<', $product_price[1]);
			$product_price = format_data($product_price[0]);
			echo "price =".$product_price."<br>";

			//image url
			$image_url = explode('class="thumb gallery" href="', $product_details);
			$image_url = explode('"', $image_url[1]);
			$image_url = trim($image_url[0]);
			echo "image url =".$image_url."<br>";

			//brand
			$product_brand = explode('id="brand"', $product_details);
			$product_brand = explode('">', $product_brand[1]);
			$product_brand = explode('</a>', $product_brand[1]);
			$product_brand = format_data($product_brand[0]);
			echo "brand =".$product_brand."<br>";

			// specs
			$product_specs = explode('product_text">',$page_content);
			$product_specs = explode('<ul',$product_specs[1]);
			$product_specs = explode('</table>', $product_specs[0]);

		  $spec_data = array();


			foreach ($product_specs as $product_spec) {

		    $group_name = explode('group-name-',$product_spec);
		    if(!isset($group_name[1])){
		      continue;
		    }
		    $group_name = explode('">', $group_name[1]);
        if(!isset($group_name[1])){
          continue;
        }
		    $group_name = explode('</div>',$group_name[1]);
		    $group_name = format_data($group_name[0]);


		    $group_specs = explode('<tr',$product_spec);
		    array_shift($group_specs);

		    $spec_data[$group_name] = array();

		    foreach ($group_specs as $specs) {

		      $spec = explode('</td>', $specs);
		      array_pop($spec);

		      $spec_key = $spec[0];
		      $spec_value = $spec[1];

		      $spec_key = explode('width-150">', $spec_key);

		      if (isset($spec_key[1])){
		        $spec_key = format_data($spec_key[1]);

		        $spec_value = explode('itemOne-attributeData', $spec_value);
		        if (isset($spec_value[1])) {
		          $spec_value = explode('">',$spec_value[1],2);
		          $spec_value = format_data($spec_value[1]);
		          $spec_data[$group_name][$spec_key] = $spec_value;
		        }
		        echo "hello world";
		      }
		      echo "<br>";
		    }
		}

    $spec_data = json_encode($spec_data);

    $DBH = get_DB();
    if (isset($DBH)) {
      echo "INSIDE DB INSERT";
      echo "PRICE =",$product_price;

      // insert to db
      $STH = $DBH->prepare('INSERT INTO `bestpricy`.product_details (category_id,product_id,name,price,brand,image_url,specification) VALUES (:category_id, :product_id, :name, :price, :brand,:image_url, :specification)');
      $STH->execute(array(':category_id' => $category_id, ':product_id' => $product_id, ':name' => $product_name, ':price' => format_price($product_price), ':brand' => $product_brand, ':image_url' => $image_url, ':specification' => $spec_data ));


      //mark product_url as visited
      $STH = $DBH->prepare('UPDATE `bestpricy`.product_list SET visited = 1 WHERE id = :product_id');
      $STH->execute(array(':product_id' => $product_id));



    }

		//print json_encode($spec_data);

		curl_close($ch);
	}

?>