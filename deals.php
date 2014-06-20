<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();
	require_once("inc/config.inc.php");
	require_once("inc/pagination.inc.php");


	$results_per_page = RESULTS_PER_PAGE;
	$cc = 0;


	function getCategory($category_id, $show_description = 0)
	{
		if (isset($category_id) && is_numeric($category_id) && $category_id != 0)
		{
			$query = "SELECT name, description FROM abbijan_categories WHERE category_id='".(int)$category_id."'";
			$result = smart_mysql_query($query);
			if (mysql_num_rows($result) > 0)
			{
				$row = mysql_fetch_array($result);
				if ($show_description == 1) return $row['description']; else return $row['name']." Deals";
			}
			else
			{
				return "Category not found";
			}
		}
		else
		{
			if ($show_description != 1) return "All Deals";
		}
	}

	////////////////// filter  //////////////////////
		if (isset($_GET['column']) && $_GET['column'] != "")
		{
			switch ($_GET['column'])
			{
				case "added": $rrorder = "added"; break;
				case "end_date": $rrorder = "end_date"; break;
				case "price": $rrorder = "price"; break;
				case "discount": $rrorder = "discount"; break;
				case "views": $rrorder = "views"; break;
				default: $rrorder = "added"; break;
			}
		}
		else
		{
			$rrorder = "added";
		}

		if (isset($_GET['order']) && $_GET['order'] != "")
		{
			switch ($_GET['order'])
			{
				case "asc": $rorder = "asc"; break;
				case "desc": $rorder = "desc"; break;
				default: $rorder = "desc"; break;
			}
		}
		else
		{
			$rorder = "desc";
		}
	//////////////////////////////////////////////////

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;

	$where = "";

	if (isset($_GET['cat']) && is_numeric($_GET['cat']) && $_GET['cat'] > 0)
	{
		$cat_id = (int)$_GET['cat'];
		
		unset($deals_per_category);
		$deals_per_category = array();
		$deals_per_category[] = "111111111111111111111";

		$sub_categories = array();
		$sub_categories = GetSubCategories($cat_id);
		$sub_categories[] = $cat_id;

		$sql_deals_per_category = smart_mysql_query("SELECT item_id FROM abbijan_item_to_category WHERE category_id IN (".implode(",",$sub_categories).")");

		while ($row_deals_per_category = mysql_fetch_array($sql_deals_per_category))
		{
			$deals_per_category[] = $row_deals_per_category['item_id'];
		}
		$deals_per_category = array_map('intval', $deals_per_category);
		$where .= "item_id IN (".implode(",",$deals_per_category).") AND ";
	}

	$where .= " start_date<=NOW() AND end_date>NOW() AND status!='inactive'";
	
	$query = "SELECT *, DATE_FORMAT(end_date, '%Y, %c-1, %e, %H, %i, %s') AS deal_end_date FROM abbijan_items WHERE $where ORDER BY featured DESC, $rrorder $rorder LIMIT $from, $results_per_page";
	$total_result = smart_mysql_query("SELECT * FROM abbijan_items WHERE $where ORDER BY title ASC");
	$total = mysql_num_rows($total_result);
	$result = smart_mysql_query($query);
	$total_on_page = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = getCategory($_GET['cat']);

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo getCategory($_GET['cat']); ?></h1>

	<p class="category_description"><?php echo getCategory($_GET['cat'], 1); ?></p>

	<div class="cats">
		<a <?php if (!isset($cat_id)) echo "class='cat_link_active'"; else echo "class='cat_link'"; ?> href="/deals.php"><b>All Deals</b></a> 
				<?php

					$sql_cats = "SELECT * FROM abbijan_categories WHERE parent_id='0' ORDER BY category_id";
					$rs_cats = smart_mysql_query($sql_cats);
					$total_cats = mysql_num_rows($rs_cats);

					if ($total_cats > 0)
					{
						while ($row_cats = mysql_fetch_array($rs_cats))
						{
							if ($cat_id == $row_cats['category_id'])
								echo "<a class='cat_link_active' href='/deals.php?cat=".$row_cats['category_id']."'>".$row_cats['name']."</a> ";
							else
								echo "<a class='cat_link' href='/deals.php?cat=".$row_cats['category_id']."'>".$row_cats['name']."</a> ";
						}
					}

				?>
	</div>

	<?php

		if ($total > 0) {
	?>
	<div class="browse_top">
		<div class="sortby">
			<form action="" id="form1" name="form1" method="get">
				<span>Sort by:</span>
				<select name="column" id="column" onChange="document.form1.submit()">
					<option value="added" <?php if ($_GET['column'] == "added") echo "selected"; ?>>Newest</option>
					<option value="end_date" <?php if ($_GET['column'] == "end_date") echo "selected"; ?>>End Soonest</option>
					<option value="price" <?php if ($_GET['column'] == "price") echo "selected"; ?>>Price</option>
					<option value="discount" <?php if ($_GET['column'] == "discount") echo "selected"; ?>>Discount</option>
					<option value="views" <?php if ($_GET['column'] == "views") echo "selected"; ?>>Popularity</option>
				</select>
				<select name="order" id="order" onChange="document.form1.submit()">
					<option value="desc"<?php if ($_GET['order'] == "desc") echo "selected"; ?>>Descending</option>
					<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>>Ascending</option>
				</select>
				<?php if ($cat_id) { ?><input type="hidden" name="cat" value="<?php echo $cat_id; ?>" /><?php } ?>
				<input type="hidden" name="page" value="<?php echo $page; ?>" />
			</form>
		</div>
		<div class="results">
			Showing <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> of <?php echo $total; ?>
		</div>
	</div>
	<div style="clear: both"></div>

	<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
	<div class="deal-item" style="background: <?php if (($cc%2) == 0) echo "#F9F9F9"; else echo "#FFFFFF"; ?>">
	<div class="deal-image">
		<?php if ($row['featured'] == 1) { ?><span class="featured" alt="Featured Deal" title="Featured Deal"></span><?php } ?>
		<a href="/deal_details.php?id=<?php echo $row['item_id']; ?>"><img src="<?php echo IMAGES_URL.$row['thumb']; ?>" width="<?php echo THUMB_WIDTH; ?>" height="<?php echo THUMB_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" class="thumb" /></a>
		<?php if ($row['allow_comments'] == 1) { ?>
			<br/><a class="comment" href="/deal_details.php?id=<?php echo $row['item_id']; ?>#comments"><?php echo GetDealCommentsTotal($row['item_id']); ?> comments</a>
		<?php } ?>
	</div>
	<div class="deal-info">
		<h3><a href="/deal_details.php?id=<?php echo $row['item_id']; ?>"><?php echo (strlen($row['title']) > 150) ? substr($row["title"], 0, 150)."..." : $row["title"]; ?></a></h3>
		<div class="info-detail"><?php echo substr(stripslashes($row["brief_description"]), 0, 250); ?></div>
		<div class="share-button">
			<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
			<fb:like href="<?php echo SITE_URL."deal_details.php?id=".$row['item_id']; ?>" layout="button_count" show_faces="false" ></fb:like>
		</div>
		<div class="share-button">
			<a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php echo SITE_URL."deal_details.php?id=".$row['item_id']; ?>" data-text="<?php echo $row['title']." - ".DisplayPrice($row['price']); ?>" data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
		</div>
		<div class="share-button">
			<g:plusone size="medium" annotation="none"></g:plusone>
			<script type="text/javascript">
			  (function() {
			    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			    po.src = 'https://apis.google.com/js/plusone.js';
			    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			  })();
			</script>
		</div>
		<div class="share-button">
			<a href="mailto:?body=<?php echo htmlentities(SITE_URL."deal_details.php?id=".$row['item_id']."&subject=".$row['title']); ?>"><img src="/images/icon_mail.png" /></a>
			<a href="/myfavorites.php?id=<?php echo $row['item_id']; ?>&act=add" title="Add to favorites"><img src="/images/icon_favorite.png" alt="Add to favorites" /></a>
		</div>
	</div>
	<div class="deal-values">
		<div class="deal-price"><?php echo DisplayPrice($row['price']); ?></div>
		<div class="deal-buy">
		<?php if ($row['status'] == "active") { ?>
			<?php if ($row['deal_type'] == "affiliate") { ?>
				<a class="buy" href="/visit.php?id=<?php echo $row['item_id']; ?>" target="_blank">Buy Now!</a>
			<?php } else { ?>
				<a class="buy" href="/cart.php?id=<?php echo $row['item_id']; ?>&action=add">Add to Cart</a>
			<?php } ?>
		<?php } ?>
		</div>

		<?php if ($row['retail_price'] != "0.00") { ?>
		<table class="deal-savings">
			<tr>
				<td class="deal-regular-price"><span>Value</span><?php echo DisplayPrice($row['retail_price']); ?></td>
				<td class="deal-discount-percent"><span>Discount</span><?php echo CalculateSavingsPercentage($row['retail_price'],$row['price']); ?></td>
				<td class="deal-discount-amount"><span>Savings</span><?php echo DisplayPrice($row['discount']); ?></td>
			</tr>
		</table>
		<?php } ?>

		<?php if ($row['status'] == "expired") { ?>
			<b>Deal has ended!</b>
		<?php }elseif ($row['status'] == "sold") { ?>
			<b>Deal is sold out!</b>
		<?php }else{ ?>

			<?php
				/// Countdown ///
				echo "
					<script type=\"text/javascript\">
						$(function () {
						$('#count_".$row['item_id']."').countdown({until: $.countdown.UTCDate(".SITE_TIMEZONE.", ".$row['deal_end_date']."), compact: ".COUNTDOWN_COMPACT.",  format: '".COUNTDOWN_FORMAT."',  serverSync: ahead5Mins, 
						layout: '".COUNTDOWN_LAYOUT."'}); 
					});
					</script>";
			?>
			<div id="count_<?php echo $row['item_id']; ?>" class="timeformat"></div>
		
		<?php } ?>

	</div>
	<div style="clear: both"></div>
	</div>

	<?php } ?>

			<?php
					$params = "";
					if (isset($cat_id) && $cat_id > 0) { $params = "cat=$cat_id&"; }
					echo ShowPagination("items",$results_per_page,"deals.php?".$params."column=$rrorder&order=$rorder&","WHERE ".$where);
			?>

	<?php }else{ ?>
			<p align="center">There are no available deals at this time.</p>
	<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>