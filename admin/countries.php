<?php 
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();
	require_once("../inc/auth_adm.inc.php");
	require_once("../inc/config.inc.php");
	require_once("../inc/pagination.inc.php");
	require_once("./inc/adm_functions.inc.php");


	$results_per_page = 10;


		// Delete countries //
		if (isset($_POST['action']) && $_POST['action'] == "delete")
		{
			$ids_arr	= array();
			$ids_arr	= $_POST['id_arr'];

			if (count($ids_arr) > 0)
			{
				foreach ($ids_arr as $v)
				{
					$countryid = (int)$v;
					DeleteCountry($countryid);
				}

				header("Location: countries.php?msg=deleted");
				exit();
			}	
		}


	if (isset($_POST['action']) && $_POST['action'] == "add")
	{
		$country_name = mysql_real_escape_string(getPostParameter('country_name'));

		if (isset($country_name) && $country_name != "")
		{
			$check_query = smart_mysql_query("SELECT * FROM abbijan_countries WHERE name='$country_name'");
			
			if (mysql_num_rows($check_query) == 0)
			{
				$sql = "INSERT INTO abbijan_countries SET name='$country_name'";

				if (smart_mysql_query($sql))
				{
					header("Location: countries.php?msg=added");
					exit();
				}
			}
			else
			{
				header("Location: countries.php?msg=exists");
				exit();
			}
		}
	}

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;

	$query = "SELECT * FROM abbijan_countries ORDER BY name ASC LIMIT $from, $results_per_page";
	$result = smart_mysql_query($query);

	$total_result = smart_mysql_query("SELECT * FROM abbijan_countries ORDER BY name ASC");
	$total = mysql_num_rows($total_result);

	$cc = 0;


	$title = "Countries";
	require_once ("inc/header.inc.php");

?>

		<h2>Countries</h2>

		<div style="width: 330px; background: #F7F7F7; padding: 10px; margin: 0 auto;">
		  <form action="" method="post">
		  <table align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
          <tr>
            <td valign="middle" align="right" class="tb1">Name:</td>
			<td align="left">
				<input type="text" name="country_name" id="country_name" value="" size="30" class="textbox" />
			</td>
			<td align="left">
				<input type="hidden" name="action" id="action" value="add" />
				<input type="submit" name="add" id="add" class="submit" value="Add Country" />
		    </td>
          </tr>
		  </table>
		  </form>
		</div>


        <?php if ($total > 0) { ?>

			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div style="width:300px;" class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added":	echo "Country was successfully added!"; break;
						case "exists":	echo "Sorry, country exists!"; break;
						case "updated": echo "Country has been successfully edited!"; break;
						case "deleted": echo "Country has been successfully deleted!"; break;
					}

				?>
			</div>
			<?php } ?>

			<form id="form2" name="form2" method="post" action="">
			<table align="center" width="350" border="0" cellpadding="3" cellspacing="0">
			<tr align="center">
				<td colspan="3" align="right"><p align="right">Total countries: <b><?php echo $total ; ?></b></p></td>
			</tr>
			<tr bgcolor="#F7F7F7" align="center">
				<th width="3%"><input type="checkbox" name="selectAll" onclick="checkAll();" class="checkboxx" /></th>
				<th width="75%">Country Name</th>
				<th width="20%">Actions</th>
			</tr>
             <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				<tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center" valign="middle"><input type="checkbox" class="checkboxx" name="id_arr[<?php echo $row['country_id']; ?>]" id="id_arr[<?php echo $row['country_id']; ?>]" value="<?php echo $row['country_id']; ?>" /></td>
					<td nowrap="nowrap" align="left" valign="middle" ><a href="country_edit.php?id=<?php echo $row['country_id']; ?>"><?php echo $row['name']; ?></a></td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="country_edit.php?id=<?php echo $row['country_id']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this country?') )location.href='country_delete.php?id=<?php echo $row['country_id']; ?>'" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
					</td>
				</tr>
			<?php } ?>
				<tr>
					<td colspan="3" align="left">
						<input type="hidden" name="page" value="<?php echo $page; ?>" />
						<input type="hidden" name="action" value="delete" />
						<input type="submit" class="submit" name="GoDelete" id="GoDelete" value="Delete Selected" />
					</td>
				</tr>
				<tr>
					<td colspan="3" valign="top" nowrap="nowrap" align="center">
						<?php
							echo ShowPagination("countries",$results_per_page,"?","");
						?>
					</td>
				</tr>
            </table>
			</form>
          
		  <?php }else{ ?>
					<div class="info_box">There are no countries at this time.</div>
          <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>