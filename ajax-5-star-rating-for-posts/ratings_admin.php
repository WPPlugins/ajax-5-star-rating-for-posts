<?php 
global $wpdb;

//if($_POST["action"]=="stop"){
// style="margin:10px;padding:10px;border:#FC0 solid 1px;background-color:#FFC;color:#630;"		
//}

if($_POST["action"]=="reset"){
	
	$chk = $_POST["chk"];
	$totalCnt = count($_POST["chk"]);
	
	if($_POST["chk"]){
		for($ii=0;$ii<$totalCnt;$ii++){
			$updQry = $wpdb->query("DELETE FROM ".$wpdb->prefix. "post_ratings WHERE post_id='".$chk[$ii]."'");
		}
		if($updQry){
			$strMesageANil = "Total ".$totalCnt." record(s) has been updated !";
			$msgType = "updated";
		}else{
			$strMesageANil = "Error !";
			$msgType = "error";
		}
	}else{
			$strMesageANil = "You have to select at least one data.";
			$msgType = "error";
	}
	
}else{

if($_POST["action"]=="csv"){
	
	$chk = $_POST["chk"];
	$totalCnt = count($_POST["chk"]);
	
	if($_POST["chk"]){
		
				$table_name = $wpdb->prefix . "post_ratings";
				$csv_output = '';
						// titles
						$csv_output .= "Post ID;IP Address;Rating Grade;Rated datetime;\n";
				
				
				for($ii=0;$ii<$totalCnt;$ii++){
						$selQry = $wpdb->get_results("SELECT * FROM ".$table_name." WHERE post_id='".$chk[$ii]."'");
								if($selQry){
									foreach($selQry as $selQryList){
										$csv_output .= $selQryList->post_id.";";
										$csv_output .= $selQryList->ip_address.";";
										$csv_output .= $selQryList->rating_grade.";";
										$csv_output .= $selQryList->rate_datetime.";";
									}
										$csv_output .= "\n";
								}
								
										$csv_output .= "\n";
										$csv_output .= "\n";
				}
					
					    $filename = $file."_".date("Y-m-d_H-i",time());
						  header("Content-type: application/x-msdownload");
						  header("Content-Disposition: attachment; filename=$filename.xls");
					echo $csv_output;
				

	
		if($selQry){
			//$strMesageANil = "Total ".$totalCnt." record(s) has been updated !";
			//$msgType = "updated";
		}else{
			$strMesageANil = "Error !";
			$msgType = "error";
		}
	
	}else{
			$strMesageANil = "You have to select at least one data.";
			$msgType = "error";
	}
	
}	
	
if($_POST["theme"]!=""){
	
	$updQry = $wpdb->query("UPDATE ".$wpdb->prefix."post_ratings_settings SET  is_current='0'");
	$updQry = $wpdb->query("UPDATE ".$wpdb->prefix."post_ratings_settings SET is_current='1' WHERE theme_id='".$_POST["theme"]."'");
		
		if($updQry){
			$strMesageANil = "Theme has been updated !";
			$msgType = "updated";
		}else{
			$strMesageANil = "Error !";
			$msgType = "error";
		}

}
}
//print_r($_POST);
?>
<div class="wrap">
<img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/ajax-5-star-rating-for-posts/images/ratings_icon.png';?>" width="45" style="float:left; margin-right:10px;">
<h2>List of Posts and their individual Ratings</h2>


<div class="<?php echo $msgType;?>">
<p>
<?php echo $strMesageANil;?>
</p>
</div>

<form name="oscimp_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);//ratings_icon.png ?>">
	<?php getRatingData();?>
</form>


</div>