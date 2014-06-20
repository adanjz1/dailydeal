<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();
	require_once("../inc/auth_adm.inc.php");
	require_once("../inc/config.inc.php");
	require_once("./inc/adm_functions.inc.php");


	$affiliate_link = "http://";


if (isset($_POST['action']) && $_POST['action'] == "add")
 {
		unset($errors);
		$errors = array();

		$deal_title			= mysql_real_escape_string(getPostParameter('deal_title'));
		$deal_type			= mysql_real_escape_string(getPostParameter('deal_type'));
		$affiliate_link		= mysql_real_escape_string(trim($_POST['affiliate_link']));

		$category			= array();
		$category			= $_POST['category_id'];

		$retail_price		= mysql_real_escape_string(getPostParameter('retail_price'));
		$price				= mysql_real_escape_string(getPostParameter('price'));
		if ($retail_price != "") $discount = $retail_price - $price;
		$condition			= mysql_real_escape_string(getPostParameter('condition'));
		$quantity			= mysql_real_escape_string(getPostParameter('quantity'));
		$customer_limit		= mysql_real_escape_string(getPostParameter('customer_limit'));
		$start_date			= mysql_real_escape_string(getPostParameter('start_date'));
		$start_time			= mysql_real_escape_string(getPostParameter('start_time'));
		$end_date			= mysql_real_escape_string(getPostParameter('end_date'));
                if($end_date == "") $end_date = '2099-12-31';
		$end_time			= mysql_real_escape_string(getPostParameter('end_time'));
		if ($end_time == "") $end_time = "00:00";
		$sale_start_date	= $start_date." ".$start_time;
		$sale_end_date		= $end_date." ".$end_time;
		$brief_description	= mysql_real_escape_string($_POST['brief_description']);
		$description		= mysql_real_escape_string($_POST['description']);
		$specs				= mysql_real_escape_string($_POST['specs']);
		$youtube_video		= mysql_real_escape_string(getPostParameter('youtube_video'));
		$meta_description	= mysql_real_escape_string(getPostParameter('meta_description'));
		$meta_keywords		= mysql_real_escape_string(getPostParameter('meta_keywords'));
		$featured			= (int)getPostParameter('featured');
		$main_deal			= (int)getPostParameter('main_deal');
		$allow_comments		= (int)getPostParameter('allow_comments');
		$send_alert			= (int)getPostParameter('send_alert');
		$upload_dir			= PUBLIC_HTML_PATH.IMAGES_URL;


		if (!($deal_title && $price  && $description))
		{
			$errors[] = "Please ensure that all fields marked with an asterisk are complete";
		}
		elseif ($deal_type == "")
		{
			$errors[] = "Please select deal type";
		}
		else
		{
			if (isset($deal_type) && $deal_type == "affiliate")
			{
				if (!$affiliate_link || $affiliate_link == "")
					$errors[] = "Please enter your affiliate link";

				if (substr($affiliate_link, 0, 7) != 'http://')
				{
					$errors[] = "Enter correct affiliate link format, enter the 'http://' statement before your link";
				}
				elseif ($affiliate_link == 'http://')
				{
					$errors[] = "Please enter correct affiliate link";
				}
			}

			if (!($_FILES['item_image']['tmp_name'][0]))
			{
				$errors[] = "Please select deal image";
			}

			if (strlen($deal_title) < 3)
			{
				$errors[] = "Too short deal title";
			}

			if (!(is_numeric($price) && $price > 0))
			{
				$errors[] = "Please enter correct deal price";
				$price = "0.00";
			}

			if (isset($retail_price)  && $retail_price != "" && !(is_numeric($retail_price) && $retail_price > 0))
			{
				$errors[] = "Please enter correct retail price";
				$retail_price = "0.00";

				if ($price >= $retail_price)
				{
					$errors[] = "Deal Price must be lower than Retail Price";
				}
			}

			if (isset($youtube_video) && $youtube_video != "" && (strpos($youtube_video, 'http://www.youtube.com/watch?v=') === false))
			{
				$errors[] = "Please enter correct youtube link";
			}

			if (isset($quantity)  && $quantity != "" && !(is_numeric($quantity) && $quantity >= 0))
			{
				$errors[] = "Please enter correct quantity";
			}

			if (isset($customer_limit)  && $customer_limit != "" && !(is_numeric($customer_limit) && $customer_limit >= 0))
			{
				$errors[] = "Please enter correct customer limit value";
			}

			if ($sale_start_date >= $sale_end_date)
			{
				$errors[] = "Deal start date must be less than end date";
			}
			else
			{
				// change deal status from 'expired' to 'active'
				if ($sale_end_date > date('Y-m-d H:i:s')) $status = "active";
				// change deal status to 'expired'
				if ($sale_end_date <= date('Y-m-d H:i:s')) $status = "expired";
			}

			if ($main_deal == 1)
			{
				$check_query = smart_mysql_query("SELECT * FROM abbijan_items WHERE start_date<=NOW() AND end_date>NOW() AND main_deal='1' AND status='active'");
				if (mysql_num_rows($check_query) != 0)
				{
					$errors[] = "Sorry, you can mark just one deal as main.";
				}
			}
		}


		if (count($errors) == 0)
		{
			$rnumber = mt_rand(1,10000).time();
			$ee = 0;

			while(list($key,$value) = each($_FILES['item_image']['name']))
			{
				if (is_uploaded_file($_FILES['item_image']['tmp_name'][$key]))
				{
					$ee ++;
					
					if ($key == 0) $img_id = "main"; else $img_id = "#".$key;

					list($width, $height, $type) = getimagesize($_FILES['item_image']['tmp_name'][$key]);

					if (!getimagesize($_FILES['item_image']['tmp_name'][$key]))
					{
						$errormsg = "Only image uploads are allowed";
					}
					elseif ($width < MIN_IMAGE_WIDTH || $height < MIN_IMAGE_HEIGHT)
					{
						$errormsg = "Too low $img_id image dimension. Please upload a larger image.";
					}
					elseif ($_FILES['item_image']['size'][$key] > 2097152)
					{
						$errormsg = "The $img_id image file size is too big. It exceeds 2Mb.";
					}
					elseif (preg_match('/\\.(gif|jpg|png|jpeg)$/i', $_FILES['item_image']['name'][$key]) != 1)
					{
						$errormsg = "Please upload a JPEG, PNG, or GIF image";
						unlink($_FILES['item_image']['tmp_name'][$key]);
					}
					else
					{
						$img_path = $upload_dir.$_FILES['item_image']['name'][$key];
						
						if ($ee == 1)
						{
							$random_num = $rnumber;
						}
						else
						{
							$random_num = $rnumber."_".$ee;
						}
							
						$new_img_name			= "deal_".$random_num.".jpg";
						$new_medium_img_name	= "deal_".$random_num."_medium.jpg";
						$new_thumb_name			= "deal_".$random_num."_thumb.jpg";

						$new_img_path			= $upload_dir.$new_img_name;
						$new_medium_img_path	= $upload_dir.$new_medium_img_name;
						$img_thumb_path			= $upload_dir.$new_thumb_name;

						create_thumb($_FILES['item_image']['tmp_name'][$key],$img_thumb_path, THUMB_WIDTH, THUMB_HEIGHT);
						resize_img($_FILES['item_image']['tmp_name'][$key],$new_medium_img_path, false, MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT);
						resize_img($_FILES['item_image']['tmp_name'][$key],$new_img_path);

						$item_images[]			= $new_img_name;
						$item_medium_images[]	= $new_medium_img_name;
						$item_thumb_images[]	= $new_thumb_name;
					}
				}

			}
		}
		else
		{
			$errormsg = "";
			foreach ($errors as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
		}


			if ($errormsg == "" && count($errors) == 0)
			{
				$main_image = $item_images[0];
				$main_thumb = $item_thumb_images[0];

				$insert_sql = "INSERT INTO abbijan_items SET title='$deal_title', deal_type='$deal_type', url='$affiliate_link', image='$main_image', thumb='$main_thumb', quantity='$quantity', retail_price='$retail_price', price='$price', discount='$discount', customer_limit='$customer_limit', start_date='$sale_start_date', end_date='$sale_end_date', conditions='$condition', brief_description='$brief_description', description='$description', specs='$specs', youtube_video='$youtube_video', meta_description='$meta_description', meta_keywords='$meta_keywords', featured='$featured', main_deal='$main_deal', allow_comments='$allow_comments', alert_sent='$send_alert', status='$status', added=NOW()";
				$result = smart_mysql_query($insert_sql);
				$new_item_id = mysql_insert_id();

				if (count($category) > 0)
				{
					foreach ($category as $cat_id)
					{
						$cats_insert_sql = "INSERT INTO abbijan_item_to_category SET item_id='$new_item_id', category_id='".(int)$cat_id."'";
						smart_mysql_query($cats_insert_sql);
					}
				}

				if (count($item_images) > 0)
				{
					foreach ($item_images as $k=>$img_name)
					{
						if ($k == 0)
							smart_mysql_query("INSERT INTO abbijan_item_images SET item_id='$new_item_id', image='$img_name', thumb_image='".$item_thumb_images[$k]."', medium_image='".$item_medium_images[$k]."', main_image='1'");
						else
							smart_mysql_query("INSERT INTO abbijan_item_images SET item_id='$new_item_id', image='$img_name', thumb_image='".$item_thumb_images[$k]."', medium_image='".$item_medium_images[$k]."'");
					}
				}

				if ($allow_comments == 1)
				{
					//insert post in to discussions
					smart_mysql_query("INSERT INTO abbijan_forums SET item_id='$new_item_id', user_id='0', title='$deal_title', discussion='$description', created=NOW()");
					$forum_id = mysql_insert_id();
					smart_mysql_query("UPDATE abbijan_items SET forum_id='$forum_id' WHERE item_id='$new_item_id'");
				}

				//send email to members
				if ($send_alert == 1)
				{
					SendDealInvitations($new_item_id);
				}

				header("Location: deals.php?msg=added");
				exit();
			}

}

	$cc = 0;

	$title = "Add Deal";
	require_once ("inc/header.inc.php");

?>

    <h2>Add Deal</h2>

	<script type="text/javascript">
	<!--
		function hiddenDiv(id,showid){
			if(document.getElementById(id).value == "affiliate"){
				document.getElementById(showid).style.display = "";
			}else{
				document.getElementById(showid).style.display = "none";
			}
		}
	-->
	</script>

	<?php if (isset($errormsg) && $errormsg != "") { ?>
		<div style="margin: 0 auto; width: 90%;" class="error_box"><?php echo $errormsg; ?></div>
	<?php } elseif (isset($_GET['msg']) && ($_GET['msg']) == "added") { ?>
		<div style="margin: 0 auto; width: 90%;" class="success_box">Deal has been successfully added</div>
	<?php } ?>

      <form action="" method="post" name="form1" enctype="multipart/form-data">
        <table width="100%" cellpadding="2" cellspacing="5" border="0" align="center">
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Title:</td>
            <td valign="top"><input type="text" name="deal_title" id="deal_title" value="<?php echo getPostParameter('deal_title'); ?>" size="60" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Type:</td>
            <td valign="top">
				<select name="deal_type" id="deal_type" onchange="javascript:hiddenDiv('deal_type','affiliate_link')">
					<option value="own" <?php if ($deal_type == "own") echo "selected='selected'"; ?>>Own Product</option>
					<option value="affiliate" <?php if ($deal_type == "affiliate") echo "selected='selected'"; ?>>Affiliate Product</option>	
				</select>
			</td>
          </tr>
          <tr id="affiliate_link" <?php if ($deal_type != "affiliate") { ?>style="display: none;" <?php } ?>>
			<td valign="middle" align="right" class="tb1"><span class="req">* </span>Affiliate Link:</td>
			<td valign="top"><input type="text" name="affiliate_link" value="<?php echo $affiliate_link; ?>" size="60" class="textbox" /><small class="note">Your affiliate link</small></td>
          </tr>
		  <?php if (GetCategoriesTotal() > 0) { ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Category:</td>
            <td valign="top">
				<div class="scrollbox">
				<?php

					$allcategories = array();
					$allcategories = CategoriesList(0);
					foreach ($allcategories as $category_id => $category_name)
					{
						$cc++;
						if (is_array($category) && in_array($category_id, $category)) $checked = 'checked="checked"'; else $checked = '';

						if (($cc%2) == 0)
							echo "<div class=\"even\"><input type=\"checkbox\" name=\"category_id[]\" value=\"".(int)$category_id."\" ".$checked.">".$category_name."</div>";
						else
							echo "<div class=\"odd\"><input type=\"checkbox\" name=\"category_id[]\" value=\"".(int)$category_id."\" ".$checked.">".$category_name."</div>";
					}

				?>
				</div>
			</td>
			</tr>
		<?php } ?>
			<tr>
				<td valign="middle" align="right" class="tb1"><span class="req">* </span>Main Image:</td>
				<td valign="top"><input type="file" name="item_image[]" class="textbox" /><span class="note">Min Size <?php echo MIN_IMAGE_WIDTH."x".MIN_IMAGE_HEIGHT; ?> px</span></td>
			</tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Image #2:</td>
				<td valign="top"><input type="file" name="item_image[]" class="textbox" /></td>
			</tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Image #3:</td>
				<td valign="top"><input type="file" name="item_image[]" class="textbox" /></td>
			</tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Image #4:</td>
				<td valign="top"><input type="file" name="item_image[]" class="textbox" /></td>
			</tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Image #5:</td>
				<td valign="top"><input type="file" name="item_image[]" class="textbox" /></td>
			</tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Image #6:</td>
				<td valign="top"><input type="file" name="item_image[]" class="textbox" /></td>
			</tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Image #7:</td>
				<td valign="top"><input type="file" name="item_image[]" class="textbox" /></td>
			</tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Retail Price:</td>
				<td valign="top"><?php echo SITE_CURRENCY; ?> <input type="text" name="retail_price" id="retail_price" value="<?php echo getPostParameter('retail_price'); ?>" size="7" class="textbox" /></td>
			</tr>
			<tr>
				<td valign="middle" align="right" class="tb1"><span class="req">* </span>Price:</td>
				<td valign="top"><?php echo SITE_CURRENCY; ?> <input type="text" name="price" id="price" value="<?php echo getPostParameter('price'); ?>" size="7" class="textbox" /></td>
			</tr>
<script>
    $(function() {
        $('#start_date').calendricalDate();
        $('#end_date').calendricalDate();
        $('#start_time').calendricalTime({
            minTime: {hour: 0, minute: 0},
            maxTime: {hour: 23, minute: 59},
            timeInterval: 30
        });
        $('#end_time').calendricalTime({
            minTime: {hour: 0, minute: 0},
            maxTime: {hour: 23, minute: 59},
            timeInterval: 30
        })
    });
</script>
            <tr>
				<td valign="middle" align="right" class="tb1">Sale Start Date:</td>
				<td valign="middle"><input type="text" name="start_date" id="start_date" value="<?php echo getPostParameter('start_date'); ?>" size="10" maxlength="10" class="textbox" />&nbsp; <input type="text" name="start_time" id="start_time" value="<?php echo getPostParameter('start_time'); ?>" size="6" maxlength="8" class="textbox" /><span class="note">YYYY-MM-DD &nbsp; HH:MM</span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Sale End Date:</td>
				<td valign="middle"><input type="text" name="end_date" id="end_date" value="<?php echo getPostParameter('end_date'); ?>" size="10"  maxlength="10" class="textbox" />&nbsp; <input type="text" name="end_time" id="end_time" value="<?php echo getPostParameter('end_time'); ?>" size="6" maxlength="8" class="textbox" /><span class="note">YYYY-MM-DD &nbsp; HH:MM</span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">&nbsp;</td>
				<td valign="middle"><img src="images/icons/small_time.png" align="absmiddle" /> <span style="font-size: 10px; color:#838383;">Current Site Date/Time: <?php $server_time = mysql_fetch_array(smart_mysql_query("SELECT CURRENT_TIMESTAMP;")); echo substr($server_time['CURRENT_TIMESTAMP'], 0, -3); ?></span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Quantity:</td>
				<td valign="middle"><input type="text" name="quantity" id="quantity" value="<?php echo (isset($quantity)) ? getPostParameter('quantity') : "0" ?>" size="2" class="textbox" /><span class="note">0 means unlimited</span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Limit per customer:</td>
				<td valign="middle"><input type="text" name="customer_limit" id="customer_limit" value="<?php echo (isset($customer_limit)) ? getPostParameter('customer_limit') : "0" ?>" size="2" class="textbox" /><span class="note">0 means no limit</span></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Condition:</td>
				<td valign="middle"><input type="text" name="condition" id="condition" value="<?php echo getPostParameter('condition'); ?>" size="10" class="textbox" /><span class="note">Used, New, etc</span></td>
            </tr>
 			<tr>
 				<td valign="middle" align="right" class="tb1">Youtube video:</td>
				<td valign="middle"><input type="text" name="youtube_video" id="youtube_video" value="<?php echo getPostParameter('youtube_video'); ?>" size="45" class="textbox" /><span class="note">e.g: http://www.youtube.com/watch?v=wJhSnb789gk</span></td>
            </tr>
			<tr>
				<td valign="top" align="right" class="tb1">Brief Description:<br/><small>(Few words about deal.<br/> This description will be appear<br/> under the deal title)</small></td>
				<td valign="top"><textarea cols="80" id="editor1" name="brief_description" rows="10"><?php echo stripslashes($_POST['brief_description']); ?></textarea></td>
             </tr>
 			<tr>
 				<td valign="top" align="right" class="tb1"><span class="req">* </span>Description:</td>
				<td valign="top"><textarea cols="80" id="editor2" name="description" rows="10"><?php echo stripslashes($_POST['description']); ?></textarea></td>
            </tr>
 			<tr>
 				<td valign="top" align="right" class="tb1">Specs:</td>
				<td valign="top"><textarea cols="80" id="editor3" name="specs" rows="10"><?php echo stripslashes($_POST['specs']); ?></textarea></td>
            </tr>
				<script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
				<script>
					CKEDITOR.replace( 'editor1' );
					CKEDITOR.replace( 'editor2' );
					CKEDITOR.replace( 'editor3' );
				</script>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Description:</td>
				<td valign="top"><textarea name="meta_description" cols="115" rows="2" class="textbox2"><?php echo getPostParameter('meta_description'); ?></textarea></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Keywords:</td>
				<td valign="top"><input type="text" name="meta_keywords" id="meta_keywords" value="<?php echo getPostParameter('meta_keywords'); ?>" size="118" class="textbox" /></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Featured?</td>
				<td valign="middle"><input type="checkbox" class="checkboxx" name="featured" value="1" <?php if (getPostParameter('featured') == 1) echo "checked=\"checked\""; ?> /> Yes!</td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Main Deal?</td>
				<td valign="middle"><input type="checkbox" class="checkboxx" name="main_deal" value="1" <?php if (getPostParameter('main_deal') == 1) echo "checked=\"checked\""; ?> /> Yes! <span class="note" title="site's homepage main deal"></span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Allow Comments?</td>
				<td valign="middle"><input type="checkbox" class="checkboxx" name="allow_comments" value="1" <?php if (getPostParameter('allow_comments') == 1) echo "checked=\"checked\""; ?> /> Yes!</td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Sent Deal Alert?</td>
				<td valign="middle"><input type="checkbox" class="checkboxx" name="send_alert" value="1" <?php if (getPostParameter('send_alert') == 1) echo "checked=\"checked\""; ?> /> Yes! <span class="note" title="send deal information to members and subscribers"></span></td>
            </tr>
            <tr>
				<td align="center" colspan="2" valign="bottom">
					<input type="hidden" name="action" id="action" value="add">
					<input type="submit" class="submit" name="add" id="add" value="Add Deal" />
				</td>
            </tr>
          </table>
      </form>

<?php require_once ("inc/footer.inc.php"); ?>