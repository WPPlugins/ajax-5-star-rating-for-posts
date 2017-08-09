<?php include_once('../../../wp-config.php');


			
		   global $wpdb;
		   
		   $table_name 		= $wpdb->prefix.'post_ratings';
		   $ipAddress  		= $_SERVER['REMOTE_ADDR'];
		   $rate_datetime 	= date('Y-m-d H:i:s');
		   
		   # checks if the rating has already done
		   $ratingTotal = $wpdb->get_var("SELECT AVG(rating_id) FROM ".$table_name." WHERE ip_address = '".$_SERVER['REMOTE_ADDR']."' AND post_id='".$_POST["postId"]."'");
		   $ratingTotal = ceil($ratingTotal);
		   # /checks if the rating has already done
		   
		   
	if($ratingTotal<1){
		
	if($_POST["postId"]!="" && $_POST["rateVal"]!=""){
		  
		   $rows_affected = $wpdb->insert($table_name , 
			   array( 
					   'post_id' => $_POST["postId"], 
					   'ip_address' => $ipAddress , 
					   'rating_grade' => $_POST["rateVal"] , 
					   'rate_datetime' => $rate_datetime)
		   			);
		   
		   if($rows_affected){
			   get_ratings_body($_POST["postId"],"success","ajax");
		   }else{
			   get_ratings_body($_POST["postId"],"error","ajax");
		   }
	}else{
			   get_ratings_body($_POST["postId"],"error1","ajax");
	}
	}else{
			   get_ratings_body($_POST["postId"],"error2","ajax");
	}
?>