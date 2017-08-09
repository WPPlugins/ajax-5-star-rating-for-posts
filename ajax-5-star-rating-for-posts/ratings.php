<?php
/*
Plugin Name: Ajax 5 Star Rating for Posts
Plugin URI: http://www.facebook.com/FunkyKnight
Description: A simple Post Rating wordpress plugin
Version: 1.0.0
Author: Anil Sharma
Author URI: http://www.facebook.com/FunkyKnight
License: GPL
*/

/* Call the html code */


   function my_init() {
	if (!is_admin()) {
		wp_enqueue_script('jquery');
	}
}
add_action('init', 'my_init');



add_action('init', 'activate_auto_update');
function activate_auto_update()
{
    require_once ('wp_autoupdate.php');
    $anil_plugin_current_version = '1.0.0';
    $anil_plugin_remote_path = 'http://www.buddhabless.com.np/ajax-5-star-rating-for-posts.zip';
    $anil_plugin_slug = plugin_basename(__FILE__);
    new wp_auto_update ($anil_plugin_current_version, $anil_plugin_remote_path, $anil_plugin_slug);
}

add_action('admin_menu', 'post_ratings_admin_menu');



function add_rating_to_the_post($content) {
	//if(!is_feed() && !is_home()) {
		$content .= get_ratings_body();
	//}
	return $content;
}
add_filter('the_content', 'add_rating_to_the_post');

function post_ratings_admin_menu() 
{
	add_options_page('Post Ratings', 'Post Ratings', 1,'Post-Ratings', 'postRatings_admin');
}

function get_ratings()
{
	echo "OPtion= ".get_option('post_ratings');
}

function postRatings_admin(){
	include("ratings_admin.php");
}

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'post_ratings_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'post_ratings_remove' );

function post_ratings_install() {
/* Creates new database field */
	ratings_install();
}

function post_ratings_remove() {
	global $wpdb;

/* Deletes the database field */
	   $table_name = $wpdb->prefix . "post_ratings";
	   $table_name2 = $wpdb->prefix . "post_ratings_settings";
	   $sql = "DROP TABLE IF EXISTS `".$table_name."`";
	   $sql2 = "DROP TABLE IF EXISTS `".$table_name2."`";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
					    //dbDelta($sql);
					    //dbDelta($sql2);
					    $wpdb->query($sql);
					    $wpdb->query($sql2);
	delete_option('post_ratings');
}

function ratings_install()
{
	   global $wpdb;
	
	   $table_name = $wpdb->prefix . "post_ratings";
	   $table_name2 = $wpdb->prefix . "post_ratings_settings";
		  
	   $sql = "CREATE TABLE `".$table_name."` (                          
                   `rating_id` int(250) NOT NULL AUTO_INCREMENT,           
                   `post_id` varchar(250) DEFAULT NULL,                    
                   `ip_address` varchar(250) DEFAULT NULL,                    
                   `rating_grade` enum('1','2','3','4','5') DEFAULT NULL,  
                   `rating_stop` enum('0','1') DEFAULT '0',                
                   `rate_datetime` datetime DEFAULT NULL,                  
                   PRIMARY KEY (`rating_id`)                               
                 )";
						
		$sql2 = "CREATE TABLE `".$table_name2."` (                            
									  `theme_id` int(10) NOT NULL AUTO_INCREMENT,  
									  `theme_name` varchar(100) DEFAULT NULL,      
									  `is_current` enum('0','1') DEFAULT '0',      
									  PRIMARY KEY (`theme_id`)                     
								)";
							
		$sql3 = "insert into ".$table_name2." 
									(theme_id, 
									theme_name, 
									is_current
									)
									values
									('1', 
									'Blue', 
									'0'
									)";
	
		$sql4 = "insert into ".$table_name2." 
									(theme_id, 
									theme_name, 
									is_current
									)
									values
									('2', 
									'Green', 
									'0'
									)";
	
		$sql5 = "insert into ".$table_name2."
									(theme_id, 
									theme_name, 
									is_current
									)
									values
									('3', 
									'Orange', 
									'1'
									)";
	
						require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
						
					    dbDelta($sql);
					    dbDelta($sql2);
					    $wpdb->query($sql3);
					    $wpdb->query($sql4);
					    $wpdb->query($sql5);
						
						add_option("post_ratings", 'Default', '', 'yes');
}



function get_ratings_body($post_id="",$msgType="",$returnFrom="none")
{
	global $wpdb;
	
	if($returnFrom=="none"){
		$post_id = get_the_ID();
	}
	
	if($msgType=="error"){
		$printMsg = '<span style="color:red;font-size:10px;">There was an error !</span>';
	}
	if($msgType=="error1"){
		$printMsg = '<span style="color:red;font-size:10px;">There was a big error !</span>';
	}
	if($msgType=="error2"){
		$printMsg = '<span style="color:red;font-size:10px;">You have already voted !</span>';
	}
	if($msgType=="success"){
		$printMsg = '<span style="color:green; font-size:10px;">Thanks for the vote !</span>';
	}
	
	if($post_id!=""){
		
		$ratingTotal = $wpdb->get_var("SELECT AVG(rating_grade) FROM ".$wpdb->prefix. "post_ratings WHERE post_id='".$post_id."'");
		$ratingTotal = ceil($ratingTotal);
		
		if($ratingTotal<1){
			$basicClass = "unrated_anil";	
		}else{
			$basicClass = "rated".$ratingTotal."_anil";	
		}

if($returnFrom=="none"){
		$ratingSTr_1 = '
		<style type="text/css">
.ratingDiv_anil{height:20px;margin-bottom:15px;padding:5px;background:#f8f8f8;font-size:12px;color:#999;border-bottom:1px dashed #ccc;}
.rating_anil{ background-image:url('.get_bloginfo('wpurl').'/wp-content/plugins/ajax-5-star-rating-for-posts/images/rating_'.currentThemeName().'.png); width:96px; height:17px; background-repeat:no-repeat;}
.unrated_anil{ background-position:0 0;}
.rated1_anil{ background-position:0 -23px;}
.rated2_anil{ background-position:0 -46px;}
.rated3_anil{ background-position:0 -69px;}
.rated4_anil{ background-position:0 -91px;}
.rated5_anil{ background-position:0 -114px;}


.rated1_anil_hover{ background-position:0 -23px;}
.rated2_anil_hover{ background-position:0 -46px;}
.rated3_anil_hover{ background-position:0 -69px;}
.rated4_anil_hover{ background-position:0 -91px;}
.rated5_anil_hover{ background-position:0 -114px;}

.rating_label_anil{height:17px; width:17px; display:inline-block; cursor:pointer; float:left; margin-right:2px;}
</style>

		<div id="populateRatingParent_'.$post_id.'" class="ratingDiv_anil">';	
}

$ratingSTr = '<div id="ratingDiv_'.$post_id.'">

<div style="float:left;margin-right:10px;">Please rate this post : </div>
<div class="rating_anil '.$basicClass.'" id="orginalRating_'.$post_id.'" style="float:left;">
<label for="rating1_'.$post_id.'" class="rating_label_anil" onmouseout="rateIt3('.$post_id.',1)" onmouseover="rateIt2('.$post_id.',1)"></label>
<label for="rating2_'.$post_id.'" class="rating_label_anil" onmouseout="rateIt3('.$post_id.',2)" onmouseover="rateIt2('.$post_id.',2)"></label>
<label for="rating3_'.$post_id.'" class="rating_label_anil" onmouseout="rateIt3('.$post_id.',3)" onmouseover="rateIt2('.$post_id.',3)"></label>
<label for="rating4_'.$post_id.'" class="rating_label_anil" onmouseout="rateIt3('.$post_id.',4)" onmouseover="rateIt2('.$post_id.',4)"></label>
<label for="rating5_'.$post_id.'" class="rating_label_anil" onmouseout="rateIt3('.$post_id.',5)" onmouseover="rateIt2('.$post_id.',5)"></label>
<br clear="all">
</div>
<div style="float:left; margin-left:5px;">'.$printMsg.'</div>
<br clear="all">


<div style="display:none">
<input id="rating1_'.$post_id.'" type="radio" '.returnChecked(1,$ratingTotal).' name="rating_grade_'.$post_id.'" value="1" onClick=rateIt(this.value,'.$post_id.');>
<input id="rating2_'.$post_id.'" type="radio"'.returnChecked(2,$ratingTotal).' name="rating_grade_'.$post_id.'" value="2" onClick=rateIt(this.value,'.$post_id.');>
<input id="rating3_'.$post_id.'" type="radio"'.returnChecked(3,$ratingTotal).' name="rating_grade_'.$post_id.'" value="3" onClick=rateIt(this.value,'.$post_id.');>
<input id="rating4_'.$post_id.'" type="radio"'.returnChecked(4,$ratingTotal).' name="rating_grade_'.$post_id.'" value="4" onClick=rateIt(this.value,'.$post_id.');>
<input id="rating5_'.$post_id.'" type="radio"'.returnChecked(5,$ratingTotal).' name="rating_grade_'.$post_id.'" value="5" onClick=rateIt(this.value,'.$post_id.');>
 </div>

</div>';

if($returnFrom=="none"){
	$ratingSTr_2 = '</div>';
}
 
$jsCode = '


<script>

function rateIt2(postId,id)
{
	jQuery("#orginalRating_"+postId).addClass("rated"+id+"_anil_hover");
}

function rateIt3(postId,id)
{
	jQuery("#orginalRating_"+postId).removeClass("rated"+id+"_anil_hover");
}

function rateIt(rateVal,postId)
{
		jQuery.ajax({
			   type: "POST",
			   data: "rateVal="+rateVal+"&postId="+postId,
			   url: "'.get_bloginfo('wpurl').'/wp-content/plugins/ajax-5-star-rating-for-posts/insert_ratings.php",
			   beforeSend:function(){
				   jQuery("#populateRatingParent_"+postId).html("<img src='.get_bloginfo('wpurl').'/wp-content/plugins/ajax-5-star-rating-for-posts/images/loader.gif>").show();
			   },
			   success: function(data){
				  jQuery("#populateRatingParent_"+postId).html(data);
			   }
			});	

	}

</script>';
	echo $jsCode.$ratingSTr_1.$ratingSTr.$ratingSTr_2;
	}
}


function returnChecked($val,$rating)
{
	$str = " ";
	if($val==$rating){
		$str = " Checked='Checked' ";
	}
	return $str;
}

function getThemesList()
{
	global $wpdb;
	
	$table_name2 = $wpdb->prefix . "post_ratings_settings";

	$themes = $wpdb->get_results("SELECT * FROM ".$table_name2);
	
	if($themes){
		$themeSTR = '<select name="theme" style="width:180px;">
					<option value="">Select a Theme</option>';
		foreach($themes as $themesListing){
			if($themesListing->is_current=="1"){
				$strSelected = " (Current) ";
			}else{
			$strSelected = " ";	
			}
					$themeSTR .= '<option  value="'.$themesListing->theme_id.'">'.$themesListing->theme_name.$strSelected.'</option>';
		}
		$themeSTR .= '</select>';
	}else{
		$themeSTR .= '<span style="color:red;">No themes found !</span>';
	}
	return $themeSTR;
}

function getRatingData()
{
	
		global $wpdb;
	
		$sql = "SELECT p.* from $wpdb->posts AS p, ".$wpdb->prefix. "post_ratings AS r
		WHERE p.post_type = 'post'
		AND p.post_status = 'publish'
		AND p.ID	      = r.post_id
		GROUP BY p.ID
		ORDER by p.post_title";
		
		$mypages = $wpdb->get_results($sql);
		 
		if ($mypages) :
		$listingTable = '
		<div class="tablenav top">

		<div class="alignleft actions">
			<select name="action" class="postform" style="width:180px;">
                    <option value="" selected="selected">Select an Action</option>
                    <option value="reset">Reset Ratings to Zero (0)</option>
                    <!--<option value="csv">Export detail record To CSV (0)</option>-->
            </select>
            <input type="submit" value="Apply" class="button-secondary action" id="doaction" name="" onclick=return confirm ("You are about to reset the selected post ratings to zero. Please click OK to proceed.")>

		</div>
				<div style="float:right">Themes : 
			'.getThemesList().'
		 <input type="submit" value="Apply" class="button-secondary action" id="doaction" name="" onclick=return confirm ("You are about to set the selected theme. Please click OK to proceed.")>
		</div>
		<br clear="all">

	</div>
						<table cellpadding="2" cellspacing="2" border="0" width="100%" class="wp-list-table widefat fixed posts">
						<thead>
							<tr>
								<th width="2%"></th>
								<th width="3%">S.No.</th>
								<th width="72%">Post Title</th>
								<th width="16%">Total Ratings</th>
								<!--<th width="7%">Detail</th>-->
							</tr>
						</thead>
						';
		   $limit = 5;   // The number of posts per page
		   $range = 5;   // The number of page links to show in the middle
		   $mypage = (isset($_GET['mypage'])) ? $mypage = $_GET['mypage'] : 1;
		   $start = ($mypage - 1) * $limit;
		   for ($i=$start;$i<($start + $limit);++$i) {
			   
			  if ($i < sizeof($mypages)) {
				// Process each element of the result array here
				$post = $mypages[$i];
				setup_postdata($post);
				$listingTable .= '<tbody>   
							<tr class="alternate" valign="middle">
							    <th width="2%"><input type="checkbox" class="checkbox2check" name="chk[]" value="'.$post->ID.'" /></th>
								<th width="3%">'.($i+1).'.</th>
								<th width="72%">'.$post->post_title.'
								<th width="16%">
								<img src="'.get_bloginfo('wpurl').'/wp-content/plugins/ajax-5-star-rating-for-posts/images/ppl.png" style="vertical-align:middle;">
								 ('.getRatingsTotal($post->ID)->ratedPpl.')
								<img src="'.get_bloginfo('wpurl').'/wp-content/plugins/ajax-5-star-rating-for-posts/images/star.png" style="vertical-align:middle;">
								 ('.ceil(getRatingsTotal($post->ID)->avgRatings).')
								<img src="'.get_bloginfo('wpurl').'/wp-content/plugins/ajax-5-star-rating-for-posts/images/stars.png" style="vertical-align:middle;">
								 ('.getRatingsTotal($post->ID)->totalPoints.')
								</th>
								<!--<th width="7%"><a href="javascript:void(0);")>View</a></th>-->
							</tr>
						</tbody>';
			  }// getRatingsTotal($id)->ratedPpl avgRatings totalPoints
		   }
		   $listingTable .= '</table>';
		   echo $listingTable;
		   echo '<br />';
		   echo _mam_paginate(sizeof($mypages),$limit,$range);
		else:
		   echo '<div style="float:right">Themes : 
			'.getThemesList().'
		 <input type="submit" value="Apply" class="button-secondary action" id="doaction" name="" onclick=return confirm ("You are about to set the selected theme. Please click OK to proceed.")>
		</div>
		<br clear="all">';
		   echo '<span>Sorry, There are no records to list !</span>';
		endif;
}


function _mam_paginate($numrows,$limit=10,$range=7) {
 
   $pagelinks = "<div class=\"pagelinks\">";
   $currpage = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];
   if ($numrows > $limit) {
      if(isset($_GET['mypage'])){
         $mypage = $_GET['mypage'];
      } else {
         $mypage = 1;
      }
      $currpage = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];
      $currpage = str_replace("&mypage=".$mypage,"",$currpage); // Use this for non-pretty permalink
      $currpage = str_replace("?mypage=".$mypage,"",$currpage); // Use this for pretty permalink
      if($mypage == 1){
         $pagelinks .= "<span class=\"pageprevdead\">&laquo PREV </span>";
      }else{
         $pageprev = $mypage - 1;
         $pagelinks .= "<a class=\"pageprevlink\" href=\"" . $currpage .
               "&mypage=" . $pageprev . "\">&laquo PREV </a>";
      }
      $numofpages = ceil($numrows / $limit);
      if ($range == "" or $range == 0) $range = 7;
      $lrange = max(1,$mypage-(($range-1)/2));
      $rrange = min($numofpages,$mypage+(($range-1)/2));
      if (($rrange - $lrange) < ($range - 1)) {
         if ($lrange == 1) {
            $rrange = min($lrange + ($range-1), $numofpages);
         } else {
            $lrange = max($rrange - ($range-1), 0);
         }
      }
      if ($lrange > 1) {
         $pagelinks .= "<a class=\"pagenumlink\" " .
            "href=\"" . $currpage . "&mypage=" . 1 .
            "\"> [1] </a>";
         if ($lrange > 2) $pagelinks .= "&nbsp;...&nbsp;";
      } else {
         $pagelinks .= "&nbsp;&nbsp;";
      }
      for($i = 1; $i <= $numofpages; $i++){
         if ($i == $mypage) {
            $pagelinks .= "<span class=\"pagenumon\"> [$i] </span>";
         } else {
            if ($lrange <= $i and $i <= $rrange) {
               $pagelinks .= "<a class=\"pagenumlink\" " .
                        "href=\"" . $currpage . "&mypage=" . $i .
                        "\"> [" . $i . "] </a>";
            }
         }
      }
      if ($rrange < $numofpages) {
         if ($rrange < $numofpages - 1) $pagelinks .= "&nbsp;...&nbsp;";
            $pagelinks .= "<a class=\"pagenumlink\" " .
               "href=\"" . $currpage . "&mypage=" . $numofpages .
               "\"> [" . $numofpages . "] </a>";
      } else {
         $pagelinks .= "&nbsp;&nbsp;";
      }
      if(($numrows - ($limit * $mypage)) > 0){
         $pagenext = $mypage + 1;
         $pagelinks .= "<a class=\"pagenextlink\" href=\"" . $currpage .
                    "&mypage=" . $pagenext . "\"> NEXT &raquo;</a>";
      } else {
         $pagelinks .= "<span class=\"pagenextdead\"> NEXT &raquo;</span>";
      }
 
   }
$pagelinks .= "</div>";
return $pagelinks;
}

function getRatingsTotal($id)
{
	global $wpdb;
	
	if($id){
		$sql = $wpdb->get_row("SELECT 
		COUNT(rating_id) AS ratedPpl, 
		AVG(rating_grade) AS avgRatings, 
		SUM(rating_grade) AS totalPoints 
			FROM 
		".$wpdb->prefix. "post_ratings 
			WHERE 
		post_id = '".$id."'");
		return $sql;
	}
	
}

function currentThemeName()
{
	global $wpdb;
	$table_name2 = $wpdb->prefix . "post_ratings_settings";
	
	$getName = $wpdb->get_var("SELECT theme_name FROM ".$table_name2." WHERE is_current='1'");
	
	if($getName!=""){
			return $getName;
	}
}

?>