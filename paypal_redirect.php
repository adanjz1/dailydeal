<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();
	require_once("inc/auth.inc.php");
	require_once("inc/config.inc.php");


	if (!(isset($_SESSION['Total']) && $_SESSION['Total'] > 0 && isset($_SESSION['order_id']) && $_SESSION['order_id'] > 0))
	{
			header ("Location: /");
			exit();		
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Paypal Payment";

	require_once ("inc/header.inc.php");

?>
	  
	  <form action="paypal_ipn.php" name="payment_form" id="payment_form" method="post">
		<input type="hidden" name="rm" value="2" />
		<input type="hidden" name="cmd" value="_xclick" />
		<input type="hidden" name="business" value="<?php echo PAYPAL_ACCOUNT; ?>" />
		<input type="hidden" name="item_name" value="<?php echo SITE_TITLE; ?>" />
		<input type="hidden" name="currency_code" value="<?php echo SITE_CURRENCY_CODE; ?>" />
		<input type="hidden" name="custom" value="<?php echo $order_id; ?>" />
		<input type="hidden" name="amount" value="<?php echo DisplayMoney($_SESSION['Total']+$_SESSION['ShippingPrice'], $hide_currency = 1); ?>" />
		<input type="hidden" name="no_shipping" value="1" />
		<input type="hidden" name="no_note" value="1" />
		<input type="hidden" name="tax" value="0" />
		<input type="hidden" name="action" value="checkout" /><br />
		<p align="center"><img src="/images/loading.gif" /></p>
		<p align="center"><span style="font-family: times, Times New Roman, times-roman, georgia, serif; font-size:28px; line-height:10px; letter-spacing:-1px; color:#444;">Please wait, you are being redirected ...</span></p>
		<center>
			If your browser does not redirect, please click this button: 
			<input type="submit" class="submit" name="submit_payment" value="Continue" />
		</center>
      </form>

	<script language="JavaScript" type="text/javascript">
		setTimeout("document.payment_form.submit()",350);
	</script>


	<?php require_once ("inc/footer.inc.php"); ?>