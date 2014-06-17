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


	$results_per_page = 10;
	$cc = 0;

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;
	
	$result = smart_mysql_query("SELECT *, DATE_FORMAT(added, '%M %e, %Y') AS faq_date FROM abbijan_faqs WHERE status='active' ORDER BY added DESC LIMIT $from, $results_per_page");
	$total_result = smart_mysql_query("SELECT * FROM abbijan_faqs WHERE status='active' ORDER BY added DESC");
	$total = mysql_num_rows($total_result);
	$total_on_page = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Frequently Asked Questions";

	require_once ("inc/header.inc.php");

?>

	<h1>Frequently Asked Questions</h1>


	<?php if ($total > 0) { ?>

		<div id="faqs">
		<?php while ($row = mysql_fetch_array($result)) { ?>
		<h3><?php echo $row['question']; ?></h3>
		<div>
			<p><?php echo $row['answer']; ?></p>
		</div>
		<?php } ?>
		</div>

		<p align="center">
			<?php echo ShowPagination("faqs",$results_per_page,"faq.php?","WHERE status='active'"); ?>
		</p>

	<?php }else{ ?>
			<p align="center">There are no faqs at this time.</p>
	<?php } ?>


<?php require_once("inc/footer.inc.php"); ?>