<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $PAGE_TITLE." - ".SITE_TITLE; ?></title>
		<?php if ($PAGE_DESCRIPTION != "") { ?><meta name="description" content="<?php echo $PAGE_DESCRIPTION; ?>" /><?php } ?>
		<?php if ($PAGE_KEYWORDS != "") { ?><meta name="keywords" content="<?php echo $PAGE_KEYWORDS; ?>" /><?php } ?>
		<link href="http://fonts.googleapis.com/css?family=Droid+Serif" rel="stylesheet" type="text/css">
		<link href="http://fonts.googleapis.com/css?family=Nobile" rel="stylesheet" type="text/css">
		<link href="http://fonts.googleapis.com/css?family=Oswald:300,700" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="/css/style.css" />
		<link rel="stylesheet" type="text/css" href="/css/jquery.fancybox-1.3.4.css" />
		<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="/js/jquery.fancybox-1.3.4.pack.js"></script>
		<script type="text/javascript" src="/js/jquery.jcarousel.min.js"></script>
		<script type="text/javascript" src="/js/countdown.js"></script>
		<script type="text/javascript" src="/js/abbijan.js"></script>
		<link rel="shortcut icon" href="/favicon.ico" />
		<link rel="icon" type="image/ico" href="/favicon.ico" />

		<script type="text/javascript">
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

		$(document).ready(function() {
			$("a[class=fancy]").fancybox();
		});
		</script>

</head>
<body>

<div id="container">

<div id="header">
	<a href="#" class="scrollup">Top</a>
	<div id="logo"><a href="<?php echo SITE_URL; ?>" title="<?php echo SITE_TITLE; ?>"><img src="/images/logo.png" alt="<?php echo SITE_TITLE; ?>" border="0" /></a></div>

	<!--
	<div id="share-box">
		<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
		<fb:like href="<?php echo SITE_URL; ?>" layout="button_count" show_faces="false" ></fb:like>
		<a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php echo SITE_URL; ?>" data-text="<?php echo SITE_TITLE; ?>" data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
		<g:plusone size="medium"></g:plusone>
		<script type="text/javascript">
		  (function() {
		    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		    po.src = 'https://apis.google.com/js/plusone.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();
		</script>
	</div>
	-->

	<?php if (FACEBOOK_BOX == 1 && FACEBOOK_APP_ID != "") { ?>
		<div id="facebook_box">
			<span>FACEBOOK</span>
			<iframe allowtransparency="" frameborder="0" scrolling="no" src="http://www.facebook.com/plugins/likebox.php?id=<?php echo FACEBOOK_APP_ID; ?>&amp;width=200&amp;connections=6&amp;stream=false&amp;header=false&amp;height=287" style="border: none; width: 200px; height: 287px; overflow: hidden;"></iframe>
		</div>
	<?php } ?>

	<?php if (SHOW_STATS == 1) { ?>
		<div id="stats-box">		
			We've saved our members <br/>
			<span class="saved_total"><?php echo GetSavingsTotal(); ?></span>
		</div>
	<?php } ?>


	<div class="cart">
		<img src="/images/icon_cart.png" align="absmiddle" />
		<a href="/cart.php"><span class="cart_items_count"><?php $cart_items = GetCartItemsTotal($_SESSION['quantity']); echo $cart_items; ?></span> <?php echo ($cart_items == 1) ? "Item" : "Items"; ?></a>
		<?php if ($cart_items > 0) { ?>
			&middot; <span class="cart_price"><?php echo DisplayPrice($_SESSION['Total']); ?></span> &middot; <a href="/checkout.php">Checkout</a>
		<?php } ?>
	</div>

	<div id="toplinks">
		<?php if (isset($_SESSION['userid']) && is_numeric($_SESSION['userid'])) { ?>
			Welcome, <a href="/myprofile.php"><?php echo $_SESSION['FirstName']; ?></a> | <a href="/myaccount.php">My Account</a> | <a href="/logout.php">Logout</a>
		<?php }else{ ?>
			<a href="/login.php">Log In</a> | <a href="/signup.php">Sign Up</a>
		<?php } ?>
    </div>

	<div id="menu">
		<a href="/">Today's Deal</a>
		<a href="/deals.php">All Deals</a>
		<?php if (GetFutureDealsTotal() > 0) { ?>
			<a href="/future_deals.php">Future Deals</a>
		<?php } ?>
		<a href="/discussion.php">Discussion</a>
		<?php if (GetPastDealsTotal() > 0) { ?>
			<a href="/past_deals.php">Past Deals</a>
		<?php } ?>
		<a href="/faq.php">FAQs</a>
		<?php if (GetTestimonialsTotal(1) > 0) { ?>
			<a href="/testimonials.php">Testimonials</a>
		<?php } ?>
		<a href="/help.php">Help</a>
		<?php if (is_numeric(REFER_FRIEND_BONUS) && REFER_FRIEND_BONUS > 0) { ?>
			<a href="/refer_a_friend.php">Refer a Friend</a>		
		<?php } ?>
	</div>

</div>

<div id="content">
