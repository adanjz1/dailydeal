	
	<div id="account">
		<ul id="account_menu">
			<li><a href="/myaccount.php">My Account</a></li>
			<li><a href="/myorders.php">My Orders</a></li>
			<li><a href="/myfavorites.php">My Favorites</a></li>
			<li><a href="/myshipping.php">Shipping Address Book</a></li>
			<li><a href="/invite.php">Refer a Friend</a></li>
			<li><a href="/mytestimonial.php">Write Testimonial</a></li>
			<li><a href="/mysupport.php">Support</a> <?php if (GetMessagesTotal($userid) > 0) { ?><span class="new_count"><?php echo GetMessagesTotal($userid); ?></span><?php } ?></li>
			<li><a href="/myprofile.php#password">Change Password</a></li>
			<li><a href="/myprofile.php">Edit Profile</a></li>
			<li><a href="/logout.php">Logout</a></li>
		</ul>
	</div>