<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	$setts_sql = "SELECT * FROM abbijan_settings";
	$setts_result = smart_mysql_query($setts_sql);

	unset($settings);
	$settings = array();

	while ($setts_row = mysql_fetch_array($setts_result))
	{
		$settings[$setts_row['setting_key']] = $setts_row['setting_value'];
	}

	define('SITE_TITLE', $settings['website_title']);
	define('SITE_MAIL', $settings['website_email']);
	define('SITE_ALERTS_MAIL', $settings['website_alerts_email']);
	define('SITE_URL', $settings['website_url']);
	define('SITE_HOME_TITLE', $settings['website_home_title']);
	define('SITE_LANGUAGE', $settings['website_language']);
	define('BLOCK_SAME_IP', $settings['block_same_ip']);
	define('SITE_TIMEZONE', $settings['website_timezone']);
	define('SITE_CURRENCY', $settings['website_currency']);
	define('SITE_CURRENCY_FORMAT', $settings['website_currency_format']);
	define('SITE_CURRENCY_CODE', $settings['website_currency_code']);
	define('MAINTENANCE_MODE', $settings['maintenance_mode']);
	define('CC_GATEWAY', $settings['cc_gateway']);
	define('PAYPAL_IPN', $settings['paypal_ipn']);
	define('PAYPAL_ACCOUNT', $settings['paypal_account']);
	define('PAYPAL_API_USERNAME', $settings['paypal_api_username']);
	define('PAYPAL_API_PASSWORD', $settings['paypal_api_password']);
	define('PAYPAL_API_SIGNATURE', $settings['paypal_api_signature']);
	define('RESULTS_PER_PAGE', $settings['results_per_page']);
	define('COMMENTS_PER_PAGE', $settings['comments_per_page']);
	define('DISCUSSIONS_PER_PAGE', $settings['discussions_per_page']);
	define('NEWS_PER_PAGE', $settings['news_per_page']);
	define('TESTIMONIALS_PER_PAGE', $settings['testimonials_per_page']);
	define('COMMENTS_APPROVE', $settings['comments_approve']);
	define('MAX_COMMENT_LENGTH', $settings['max_comment_length']);
	define('MIN_PAYOUT', $settings['min_payout']);
	define('REFER_FRIEND_BONUS', $settings['refer_credit']);
	define('SHOW_RANDOM', $settings['show_random']);
	define('ON_COMMENTS', $settings['on_comments']);
	define('SIDEBAR_RESULTS', $settings['sidebar_results']);
	define('OTHER_DEALS_RESULTS', $settings['other_deals_results']);
	define('PAST_DEALS_RESULTS', $settings['past_deals_results']);
	define('FACEBOOK_URL', $settings['facebook_url']);
	define('FACEBOOK_BOX', $settings['facebook_box']);
	define('FACEBOOK_APP_ID', $settings['facebook_app_id']);
	define('TWITTER_URL', $settings['twitter_url']);
	define('GPLUS_URL', $settings['gplus_url']);
	define('PINTEREST_URL', $settings['pinterest_url']);
	define('TUBMLR_URL', $settings['tumblr_url']);
	define('GOOGLE_ANALYTICS', $settings['google_analytics']);
	define('IMAGES_URL', '/images/deals/');
	define('MIN_IMAGE_WIDTH', '200');
	define('MIN_IMAGE_HEIGHT', '200');
	define('ALLOW_AVATARS', $settings['allow_avatars']);
	define('AVATARS_URL', '/images/avatars/');
	define('AVATAR_WIDTH', $settings['avatar_width']);
	define('AVATAR_HEIGHT', $settings['avatar_height']);
	define('THUMB_WIDTH', $settings['thumb_width']);
	define('THUMB_HEIGHT', $settings['thumb_height']);
	define('MEDIUM_IMAGE_WIDTH', $settings['medium_image_width']);
	define('MEDIUM_IMAGE_HEIGHT', $settings['medium_image_height']);
	define('SMALL_IMAGE_WIDTH', $settings['small_image_width']);
	define('SMALL_IMAGE_HEIGHT', $settings['small_image_height']);
	define('SHOW_SALES_STATS', $settings['show_sales_stats']);
	define('SHOW_STATS', $settings['show_stats']);
	define('SHOW_QUANTITY', $settings['show_quantity']);
	define('SHOW_STOCK_BAR', $settings['show_stock_bar']);
	define('COUNTDOWN_COMPACT', $settings['countdown_compact']);
	define('COUNTDOWN_FORMAT', $settings['countdown_format']);
	define('COUNTDOWN_LAYOUT', $settings['countdown_layout']);
	define('CHECKOUT_MINUTES_LIMIT', $settings['checkout_reservation_time']);
	define('NEW_ORDER_ALERT', $settings['email_new_order']);
	define('NEW_TICKET_ALERT', $settings['email_new_ticket']);
	define('DEAL_EXPIRED_ALERT', $settings['email_deal_expired']);
	define('SOLD_OUT_ALERT', $settings['email_sold_out']);
	define('NEW_COMMENT_ALERT', $settings['email_new_comment']);
	define('NEW_TESTIMONIAL_ALERT', $settings['email_new_testimonial']);
	define('NEW_DEAL_ALERT', $settings['email_new_deal']);
	
?>