<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	// Error Reporting
	@error_reporting(E_ALL ^ E_NOTICE);

	/// DATABASE SETTINGS ///////////////////////////////////////////////////////////////////////////
	define('DB_NAME', 'abbijan');				// MySQL database name
	define('DB_USER', 'root');				// MySQL database user
	define('DB_PASSWORD', '123456');			// MySQL database password
	define('DB_HOST', 'localhost');		// MySQL database host name (in most cases, it's localhost)	
	/////////////////////////////////////////////////////////////////////////////////////////////////

	if (isset($_SESSION['userid']) && is_numeric($_SESSION['userid']))
	{
		$userid = (int)$_SESSION['userid'];
	}

	define('PUBLIC_HTML_PATH', $_SERVER['DOCUMENT_ROOT']);
	define('DOCS_ROOT', $_SERVER['DOCUMENT_ROOT']);
	define('abbijan_ROOT', dirname(__FILE__) . '/');
	define('abbijan_PAGE', TRUE);

	require_once(abbijan_ROOT."db.inc.php");
	require_once(abbijan_ROOT."functions.inc.php");

	if (!defined('is_Setup'))
	{
		require_once(abbijan_ROOT."siteconfig.inc.php");
		//require_once(DOCS_ROOT."/language/".SITE_LANGUAGE.".inc.php");
	}

	// maintenance mode //
	if (MAINTENANCE_MODE == 1 && !$admin_panel)
	{
		require_once(DOCS_ROOT."/maintenance.inc.php");
		die();
	}	

?>