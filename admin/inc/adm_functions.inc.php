<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/


if (!function_exists('DeleteUser')) {
	function DeleteUser ($user_id)
	{
		$userid = (int)$user_id;
		smart_mysql_query("DELETE FROM abbijan_users WHERE user_id='$userid'");
		smart_mysql_query("DELETE FROM abbijan_favorites WHERE user_id='$userid'");
		smart_mysql_query("DELETE FROM abbijan_transactions WHERE user_id='$userid'");
		smart_mysql_query("DELETE FROM abbijan_orders WHERE user_id='$userid'");
		smart_mysql_query("DELETE FROM abbijan_messages WHERE user_id='$userid'");
		smart_mysql_query("DELETE FROM abbijan_reports WHERE reporter_id='$userid'");
	}
}


if (!function_exists('DeleteNews')) {
	function DeleteNews ($news_id)
	{
		$newsid = (int)$news_id;
		smart_mysql_query("DELETE FROM abbijan_news WHERE news_id='$newsid'");
	}
}


if (!function_exists('DeletePayment')) {
	function DeletePayment ($payment_id)
	{
		$payment_id = (int)$payment_id;
		smart_mysql_query("DELETE FROM abbijan_transactions WHERE transaction_id='$payment_id'");
	}
}


if (!function_exists('DeleteFAQ')) {
	function DeleteFAQ ($faq_id)
	{
		$faq_id = (int)$faq_id;
		smart_mysql_query("DELETE FROM abbijan_faqs WHERE faq_id='$faq_id'");
	}
}


if (!function_exists('DeleteReport')) {
	function DeleteReport ($report_id)
	{
		$report_id = (int)$report_id;
		smart_mysql_query("DELETE FROM abbijan_reports WHERE report_id='$report_id'");
	}
}


if (!function_exists('DeleteDeal')) {
	function DeleteDeal ($item_id)
	{
		$item_id = (int)$item_id;

		$result = smart_mysql_query("SELECT * FROM abbijan_item_images WHERE item_id='$item_id'");

		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result))
			{
				if ($row['image'] != "" && file_exists(PUBLIC_HTML_PATH.IMAGES_URL.$row['image'])) @unlink(PUBLIC_HTML_PATH.IMAGES_URL.$row['image']);
				if ($row['thumb_image'] != "" && file_exists(PUBLIC_HTML_PATH.IMAGES_URL.$row['thumb_image'])) @unlink(PUBLIC_HTML_PATH.IMAGES_URL.$row['thumb_image']);
				if ($row['medium_image'] != "" && file_exists(PUBLIC_HTML_PATH.IMAGES_URL.$row['medium_image'])) @unlink(PUBLIC_HTML_PATH.IMAGES_URL.$row['medium_image']);
			}
		}

		smart_mysql_query("DELETE FROM abbijan_item_images WHERE item_id='$item_id'");
		smart_mysql_query("DELETE FROM abbijan_items WHERE item_id='$item_id'");
		smart_mysql_query("DELETE FROM abbijan_item_to_category WHERE item_id='$item_id'");
		smart_mysql_query("DELETE FROM abbijan_favorites WHERE item_id='$item_id'");
	}
}


if (!function_exists('DeleteImage')) {
	function DeleteImage ($image_id)
	{
		$image_id = (int)$image_id;

		$result = smart_mysql_query("SELECT * FROM abbijan_item_images WHERE item_image_id='$image_id'");

		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			if ($row['image'] != "" && file_exists(PUBLIC_HTML_PATH.IMAGES_URL.$row['image'])) @unlink(PUBLIC_HTML_PATH.IMAGES_URL.$row['image']);
			if ($row['thumb_image'] != "" && file_exists(PUBLIC_HTML_PATH.IMAGES_URL.$row['thumb_image'])) @unlink(PUBLIC_HTML_PATH.IMAGES_URL.$row['thumb_image']);
			if ($row['medium_image'] != "" && file_exists(PUBLIC_HTML_PATH.IMAGES_URL.$row['medium_image'])) @unlink(PUBLIC_HTML_PATH.IMAGES_URL.$row['medium_image']);

			smart_mysql_query("DELETE FROM abbijan_item_images WHERE item_image_id='$image_id'");
		}
	}
}



if (!function_exists('DeleteMessage')) {
	function DeleteMessage ($message_id)
	{
		$mid = (int)$message_id;
		smart_mysql_query("DELETE FROM abbijan_messages WHERE message_id='$mid'");
		smart_mysql_query("DELETE FROM abbijan_messages_answers WHERE message_id='$mid'");
	}
}



if (!function_exists('DeleteDiscussion')) {
	function DeleteDiscussion ($discussion_id)
	{
		$id = (int)$discussion_id;
		smart_mysql_query("DELETE FROM abbijan_forums WHERE forum_id='$id'");
		smart_mysql_query("DELETE FROM abbijan_forum_comments WHERE forum_id='$id'");
	}
}



if (!function_exists('DeleteComment')) {
	function DeleteComment ($comment_id)
	{
		$id = (int)$comment_id;
		smart_mysql_query("DELETE FROM abbijan_forum_comments WHERE forum_comment_id='$id'");
	}
}



if (!function_exists('DeleteTestimonial')) {
	function DeleteTestimonial ($testimonial_id)
	{
		$testimonialid = (int)$testimonial_id;
		smart_mysql_query("DELETE FROM abbijan_testimonials WHERE testimonial_id='$testimonialid'");
	}
}


if (!function_exists('DeleteCountry')) {
	function DeleteCountry ($country_id)
	{
		$countryid = (int)$country_id;
		smart_mysql_query("DELETE FROM abbijan_countries WHERE country_id='$countryid'");
	}
}


if (!function_exists('DeleteSubscriber')) {
	function DeleteSubscriber ($subscriber_id)
	{
		$subscriberid = (int)$subscriber_id;
		smart_mysql_query("DELETE FROM abbijan_subscribers WHERE subscriber_id='$subscriberid'");
	}
}



if (!function_exists('DeleteOrder')) {
	function DeleteOrder ($order_id)
	{
		$order_id = (int)$order_id;
		smart_mysql_query("DELETE FROM abbijan_orders WHERE order_id='$order_id'");
		smart_mysql_query("DELETE FROM abbijan_order_items WHERE order_id='$order_id'");
	}
}



if (!function_exists('BlockUnblockUser')) {
	function BlockUnblockUser ($user_id, $unblock=0)
	{
		$userid = (int)$user_id;

		if ($unblock == 1)
			smart_mysql_query("UPDATE abbijan_users SET status='active' WHERE user_id='$userid'");
		else
			smart_mysql_query("UPDATE abbijan_users SET status='inactive' WHERE user_id='$userid'");
	}
}



if (!function_exists('GetUsername')) {
	function GetUsername($user_id)
	{
		$user_id = (int)$user_id;
		$result = smart_mysql_query("SELECT fname, lname, username FROM abbijan_users WHERE user_id='$user_id' LIMIT 1");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			return "<a class='user' href='user_details.php?id=$user_id'>".$row['fname']." ".$row['lname']."</a>";
		}
		else
		{
			return "<span class='no_user'>User not found</span>";
		}
	}
}



if (!function_exists('GetRepliesNum')) {
	function GetRepliesNum($message_id)
	{
		$message_id = (int)$message_id;
		$row = mysql_fetch_array(smart_mysql_query("SELECT COUNT(answer_id) as total_replies FROM abbijan_messages_answers WHERE message_id='$message_id' AND is_admin='1'"));
		$total_replies = $row['total_replies'];

		if ($total_replies > 0) 
			return "<font color='#333'><b>".$total_replies."</b></font>";
		else
			return "<font color='#A3A3A3'>".$total_replies."</font>";
	}
}



if (!function_exists('CategoryTotalDeals')) {
	function CategoryTotalDeals ($category_id)
	{
		$result = smart_mysql_query("SELECT COUNT(item_id) as total FROM abbijan_item_to_category WHERE category_id='$category_id'");
		$row = mysql_fetch_array($result);
		return $row['total'];
	}
}



if (!function_exists('GetCategoriesTotal')) {
	function GetCategoriesTotal() {
		$result = smart_mysql_query("SELECT COUNT(category_id) as total FROM abbijan_categories");
		$row = mysql_fetch_array($result);
		return $row['total'];
	}
}


/*
if (!function_exists('CategoriesDropDown')) {
	function CategoriesDropDown ($parent_id, $sep = "", $current = 0, $parent = 0)
	{
		$result = smart_mysql_query("SELECT name, category_id FROM abbijan_categories WHERE category_id<>'$current' AND parent_id='$parent_id' ORDER BY name");
		$total = mysql_num_rows($result);

		if ($total > 0)
		{
			while ($row = mysql_fetch_array($result))
			{
				$category_id = $row['category_id'];
				$category_name = $row['name'];
				if ($parent > 0 && $category_id == $parent) $selected = " selected=\"selected\""; else $selected = "";
				echo "<option value=\"".$category_id."\"".$selected.">".$sep.$category_name."</option>\n";
				CategoriesDropDown($category_id, $sep.$category_name." &gt; ", $current, $parent);
			}
		}
	}
}
*/


if (!function_exists('CategoriesList')) {
	function CategoriesList ($parent_id, $sep = "")
	{
		static $allcategories;
		$result = smart_mysql_query("SELECT name, category_id FROM abbijan_categories WHERE parent_id='$parent_id' ORDER BY name");
		$total = mysql_num_rows($result);

		if ($total > 0)
		{
			while ($row = mysql_fetch_array($result))
			{
				$category_id = $row['category_id'];
				$category_name = $row['name'];
				$allcategories[$category_id] = $sep.$category_name;
				CategoriesList($category_id, $sep.$category_name." &gt; ");
			}
		}
		return $allcategories;
	}
}



if (!function_exists('GetDealCategories')) {
	function GetDealCategories($item_id)
	{
		unset($cat_list);
		unset($item_cats);
		$item_cats = array();

		$sql_item_cats = smart_mysql_query("SELECT category_id FROM abbijan_item_to_category WHERE item_id='$item_id'");

		if (mysql_num_rows($sql_item_cats) > 0)
		{
			while ($row_item_cats = mysql_fetch_array($sql_item_cats))
			{
				$item_cats[] = $row_item_cats['category_id'];
			}

			$categories_list = array();
			$allcategories = array();
			$allcategories = CategoriesList(0);
			
			if (count($allcategories) > 0)
			{
				foreach ($allcategories as $category_id => $category_name)
				{
					if (is_array($item_cats) && in_array($category_id, $item_cats))
					{
						$categories_list[] = $category_name;
					}
				}
	
				foreach ($categories_list as $cat_name)
				{
					$cat_list .= "<span style='background:#9B9B9B; color:#FFF; padding:2px 4px; margin-right:5px;' class='category_list'>".$cat_name."</span>"; //<br/>
				}

				return $cat_list;
			}
		}
		else
		{
			return false;
		}
	}
}

?>