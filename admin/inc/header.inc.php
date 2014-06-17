<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo $title; ?> | abbijan Admin Panel</title>
	<link href="css/abbijan.css" rel="stylesheet" type="text/css" />
	<link href="css/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="js/jquery.fancybox-1.3.4.js"></script>
	<script type="text/javascript" src="js/jquery.calendrical.js"></script>
	<script type="text/javascript" src="js/abbijan_scripts.js"></script>
<script>
	$(document).ready(function() {
	$("a[rel=group]").fancybox({
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'titlePosition' 	: 'over',
				'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
					//return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
				}
				});	
	});
</script>

</head>
<body>

<div id="wrapper">

	<div id="header">
		<div id="logo"><a href="" target="_blank"><img src="./images/logo.png" border="0" /></a></div>
		<div id="right_header">
			Welcome, admin! <a href="<?php echo SITE_URL; ?>" target="_blank">View Site</a> | <a class="logout" href="logout.php">Logout</a>
		</div>
	</div>

	<div id="content-wrapper">

		<div id="sidebar">
			<ul>
				<li><a href="index.php">Home</a></li>
				<?php if (GetLiveDealsTotal() > 0) { ?>
					<li><a href="todays_deals.php">Today's Deals <span class="new_count" style="background:#6FBCFC;"><?php echo GetLiveDealsTotal(); ?></span></a></a></li>
				<?php } ?>
				<li><a href="deals.php">Deals</a></li>
			<?php if (GetDealReportsTotal() > 0) { ?>
				<li><a href="deal_reports.php">Deals Reports <span class="new_count" style="background:#FC0505;"><?php echo GetDealReportsTotal(); ?></span></a></li>
			<?php } ?>
				<li><a href="users.php">Users</a></li>
			<?php if (GetUserReportsTotal() > 0) { ?>
				<li><a href="user_reports.php">Users Reports <span class="new_count" style="background:#FC0505;"><?php echo GetUserReportsTotal(); ?></span></a></li>
			<?php } ?>
				<li><a href="orders.php">Orders <?php if (GetPendingOrdersTotal() > 0) { ?><span class="new_count" style="background:#F9960C;"><?php echo GetPendingOrdersTotal(); ?></span><?php } ?></a></li>
				<li><a href="discussions.php">Discussions</a></li>
				
				<li><a href="categories.php">Categories</a></li>
				<li><a href="countries.php">Countries</a></li>
				<!--
			<?php if (GetRequestsTotal() > 0) { ?>
				<li><a href="payout_requests.php">Withdraw Requests <span class="new_count" style="background:#FF9F05;"><?php echo GetRequestsTotal(); ?></span></a></li>
			<?php } ?>
			
				<li><a href="payments.php">Payments</a></li>
			-->
				<li><a href="shipping.php">Shipping Methods</a></li>
				<li><a href="pmethods.php">Payment Methods</a></li>
				<li><a href="messages.php">Support Tickets <?php if (GetMessagesTotal() > 0) { ?><span class="new_count"><?php echo GetMessagesTotal(); ?></span><?php } ?></a></li>
				<li><a href="faqs.php">FAQ</a></li>
				<li><a href="news.php">News</a></li>
				<li><a href="testimonials.php">Testimonials <?php if (GetTestimonialsTotal() > 0) { ?><span class="new_count"><?php echo GetTestimonialsTotal(); ?></span><?php } ?></a></li>
				<li><a href="content.php">Content</a></li>
				<li><a href="etemplates.php">Email Templates</a></li>
				<li><a href="subscribers.php">Subscribers</a></li>
				<li><a href="email2users.php">Email Members</a></li>
				<li><a href="settings.php">Settings</a></li>
				<li><a href="logout.php">Log Out</a></li>
			</ul>
		</div>

		<div id="content">
