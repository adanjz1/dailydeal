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

	$results_per_page = TESTIMONIALS_PER_PAGE;
	$cc = 0;

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;
	
	$result = smart_mysql_query("SELECT * FROM abbijan_testimonials WHERE status='active' ORDER BY added DESC LIMIT $from, $results_per_page");
	
	$total_result = smart_mysql_query("SELECT * FROM abbijan_testimonials WHERE status='active' ORDER BY added DESC");
	$total = mysql_num_rows($total_result);
	$total_on_page = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Testimonials";

	require_once ("inc/header.inc.php");

?>

	<h1 class="feedbacks">Testimonials</h1>

	<?php if ($total > 0) { ?>

		<p>See what others are saying about us.</p>

		<?php while ($row = mysql_fetch_array($result)) { ?>
			<div class="testimonials">
				<div class="testimonial_author"><?php echo $row['author']; ?></div>
				<div class="testimonial"><?php echo $row['testimonial']; ?></div>
			</div>
		<?php } ?>

		<p align="center">
			<?php
				echo ShowPagination("testimonials",$results_per_page,"testimonials.php?","WHERE status='active'");
			?>
		</p>

	<?php }else{ ?>
			<p>There are no testimonial at this time.</p>
	<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>