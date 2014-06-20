</div>

<?php if (!(isset($_SESSION['userid']) && is_numeric($_SESSION['userid']))) { // display subscribe box only for unregistered visitors ?>
<div id="subscribe_box">
	<div class="col_left">
		
		<h2>Get our latest deals delivered straight to your inbox</h2>
		<form action="subscribe.php" method="post">
			<input type="text" id="email" name="email" required="required" placeholder="Enter your email to get all our latest deals!" class="textbox" size="47" />
			<input type="hidden" name="action" value="subscribe" />
			<input type="submit" class="submit" value="Submit" />
		</form>

	</div>
	<div class="col_right">
		<p>Subscribe to our newsletter and we will send you all the latest deals, straight to your inbox. Also you will receive them before the deals start, giving you a heads-up on what's coming next.</p>
	</div>
</div>
<?php } ?>


<div id="footer">

	<div class="col1">
		<span>About Us</span><br/>
		<ul>
			<li><a href="/">Home</a></li>
			<li><a href="/about.php">About Us</a></li>
			<li><a href="/news.php">News</a></li>
			<li><a href="/deal_submit.php" rel="nofollow">Submit a Deal</a></li>
			<li><a href="/contact.php" rel="nofollow">Contact Us</a></li>
		</ul>	
	</div>

	<div class="col1">
		<span>Need Help?</span><br/>
		<ul>
			<li><a href="/track_order.php" rel="nofollow">Track my Order</a></li>
			<li><a href="/help.php" rel="nofollow">Help</a></li>
			<li><a href="/faq.php" rel="nofollow">FAQs</a></li>
			<li><a href="/privacy.php" rel="nofollow">Privacy Policy</a></li>
			<li><a href="/terms.php" rel="nofollow">Terms &amp; Conditions</a></li>
		</ul>
	</div>

	<div class="col1">
		<span>Follow Us</span><br/><br/>
		<?php if (FACEBOOK_URL != "" ) { ?><a href="<?php echo FACEBOOK_URL; ?>" target="_blank" rel="nofollow"><img src="/images/facebook_icon.png" align="absmiddle" /> Facebook</a> <?php } ?>
		<?php if (TWITTER_URL != "" ) { ?><a href="<?php echo TWITTER_URL; ?>" target="_blank" rel="nofollow"><img src="/images/twitter_icon.png" align="absmiddle" /> Twitter</a> <?php } ?>
		<?php if (GPLUS_URL != "" ) { ?><a href="<?php echo GPLUS_URL; ?>" target="_blank" rel="nofollow"><img src="/images/gplus_icon.png" align="absmiddle" /> Google Plus</a> <?php } ?>
		<?php if (PINTEREST_URL != "" ) { ?><a href="<?php echo PINTEREST_URL; ?>" target="_blank" rel="nofollow"><img src="/images/pinterest_icon.png" align="absmiddle" /> Pinterest</a> <?php } ?>
		<?php if (TUBMLR_URL != "" ) { ?><a href="<?php echo TUBMLR_URL; ?>" target="_blank" rel="nofollow"><img src="/images/twitter_icon.png" align="absmiddle" /> Tubmlr</a> <?php } ?>
		<a href="/rss.php" title="RSS"><img src="/images/feed_icon.png" align="absmiddle" alt="rss" /></a>
	</div>

	<div class="col2">
	<p>Copyright &copy; 2014 <?php echo SITE_TITLE; ?>. All rights reserved.</p>
	</div>

	<!-- Do not remove this copyright notice! -->
		<!--<div class="powered-by-abbijan">Powered by <a href="" target="_blank">abbijan</a><div>-->
	<!-- Do not remove this copyright notice! -->
</div>


</div>
	<?php echo GOOGLE_ANALYTICS; ?>
</body>
</html>