<?php
require_once('classes/Plugin.php');
include_once('plugins/Report/classes/Reports.php');

class Report extends Plugin {

	/**
	 * Attributes
	 */
	protected $name = 'Report';
	protected $lang;
	public $reportsdb;

	/**
	 * Constructor
	 * Call parent constructor, passing the language that will be used.
	 * @param $language Current phpPgAdmin language. If it was not found in the plugin, English will be used.
	 */
	function __construct($language) {
		$plugin_directory = dirname(__FILE__);
		parent::__construct($language, $plugin_directory);
		//
		global $status, $data;
		if ($data != null) {
			$this->reportsdb = new Reports($status);
		}
	}

	/**
	 * This method returns the functions that will hook in the phpPgAdmin core.
	 * To do include a function just put in the $hooks array the follwing code:
	 * 'hook' => array('function1', 'function2').
	 *
	 * Example:
	 * $hooks = array(
	 *	'toplinks' => array('add_plugin_toplinks'),
	 *	'tabs' => array('add_tab_entry'),
	 *  'action_buttons' => array('add_more_an_entry')
	 * );
	 *
	 * @return $hooks
	 */
	function get_hooks() {
		$hooks = array(
			'tabs' => array('add_plugin_tabs'),
			'trail' => array('add_plugin_trail'),
			'navlinks' => array('add_plugin_navlinks')
		);
		return $hooks;
	}

	/**
	 * This method returns the functions that will be used as actions.
	 * To do include a function that will be used as action, just put in the $actions array the follwing code:
	 *
	 * $actions = array(
	 *	'show_page',
	 *	'show_error',
	 * );
	 *
	 * @return $actions
	 */
	function get_actions() {
		$actions = array(
			'save_edit',
			'edit',
			'properties',
			'save_create',
			'create',
			'drop',
			'confirm_drop',
			'default_action'
		);
		return $actions;
	}

	/**
	 * Add plugin in the tabs
	 * @param $plugin_functions_parameters
	 */
	function add_plugin_tabs(&$plugin_functions_parameters) {
		global $misc;

		$tabs = &$plugin_functions_parameters['tabs'];

		switch ($plugin_functions_parameters['section']) {
			case 'server':
				$tabs['report_plugin'] = array (
					'title' => $this->lang['strplugindescription'],
					'url' => 'plugin.php',
					'urlvars' => array('subject' => 'server', 'action' => 'default_action', 'plugin' => $this->name),
					'hide' => false,
					'icon' => 'Report'
				);
				break;
		}
	}

	/**
	 * Add plugin in the trail
	 * @param $plugin_functions_parameters
	 */
	function add_plugin_trail(&$plugin_functions_parameters) {
		global $misc, $lang;
		$trail = &$plugin_functions_parameters['trail'];
		$done = false;
		$subject = '';
		if (isset($_REQUEST['subject'])) {
			$subject = $_REQUEST['subject'];
		}

		$action = '';
		if (isset($_REQUEST['action'])) {
			$action = $_REQUEST['action'];
		}

		if ($subject == 'server' and 
				in_array($action, array('edit', 'properties', 'create', 'drop','default_action'))) {

			$url = array (
				'url' => 'plugin.php',
				'urlvars' => array (
					'plugin' => $this->name,
					'subject' => 'server',
					'action' => 'default_action'
				)
			);
			if (isset($_REQUEST['report'])) $url['urlvars']['report'] = field('report');
			
			$trail['report_plugin'] = array (
				'title' => $lang['strreport'],
				'text' => $lang['strreport'],
				'url'   => $misc->printActionUrl($url, $_REQUEST),
				'icon' => 'Report'
			);
			if (isset($_REQUEST['report'])) {
				$trail['report_plugin']['text'] = $_REQUEST['report'];
			}
		}
	}

	/**
	 * Add plugin in the navlinks
	 * @param $plugin_functions_parameters
	 */
	function add_plugin_navlinks(&$plugin_functions_parameters) {
		global $misc;

		$navlinks = array();

		if (count($navlinks) > 0) {
			//Merge the original navlinks array with Examples' navlinks 
			$plugin_functions_parameters['navlinks'] = array_merge($plugin_functions_parameters['navlinks'], $navlinks);
		}
	}

	function edit($msg = '') {
		global $data, $reportsdb, $misc;
		global $lang;

		$misc->printHeader($lang['strreports']);
		$misc->printBody();
		$misc->printTrail('server');
		$misc->printTabs('server', 'report_plugin');
		$misc->printMsg($msg);

		// If it's a first, load then get the data from the database
		$report = $this->reportsdb->getReport($_REQUEST['report_id']);
		if ($_REQUEST['action'] == 'edit') {			
			$_POST['report_name'] = $report->fields['report_name'];
			$_POST['db_name'] = $report->fields['db_name'];
			$_POST['descr'] = $report->fields['descr'];
			$_POST['report_sql'] = $report->fields['report_sql'];
			if ($report->fields['paginate'] == 't') {
				$_POST['paginate'] = TRUE;
			}
		}

		// Get a list of available databases
		$databases = $data->getDatabases();

		$_REQUEST['report'] = $report->fields['report_name'];

		echo "<form action=\"plugin.php?plugin={$this->name}\" method=\"post\">\n";
		echo $misc->form;
		echo "<table style=\"width: 100%\">\n";
		echo "<tr><th class=\"data left required\">{$lang['strname']}</th>\n";
		echo "<td class=\"data1\"><input name=\"report_name\" size=\"32\" maxlength=\"{$data->_maxNameLen}\" value=\"",
			htmlspecialchars($_POST['report_name']), "\" /></td></tr>\n";
		echo "<tr><th class=\"data left required\">{$lang['strdatabase']}</th>\n";
		echo "<td class=\"data1\"><select name=\"db_name\">\n";
		while (!$databases->EOF) {
			$dbname = $databases->fields['datname'];
			echo "<option value=\"", htmlspecialchars($dbname), "\"",
			($dbname == $_POST['db_name']) ? ' selected="selected"' : '', ">",
				htmlspecialchars($dbname), "</option>\n";
			$databases->moveNext();
		}
		echo "</select></td></tr>\n";
		echo "<tr><th class=\"data left\">{$lang['strcomment']}</th>\n";
		echo "<td class=\"data1\"><textarea style=\"width:100%;\" rows=\"5\" cols=\"50\" name=\"descr\">",
			htmlspecialchars($_POST['descr']), "</textarea></td></tr>\n";
		echo "<tr><th class=\"data left required\">{$lang['strsql']}</th>\n";
		echo "<td class=\"data1\"><textarea style=\"width:100%;\" rows=\"15\" cols=\"50\" name=\"report_sql\">",
			htmlspecialchars($_POST['report_sql']), "</textarea></td></tr>\n";
		echo "</table>\n";
		echo "<label for=\"paginate\"><input type=\"checkbox\" id=\"paginate\" name=\"paginate\"", (isset($_POST['paginate']) ? ' checked="checked"' : ''), " />&nbsp;{$lang['strpaginate']}</label>\n";
		echo "<p><input type=\"hidden\" name=\"action\" value=\"save_edit\" />\n";
		echo "<input type=\"submit\" value=\"{$lang['strsave']}\" />\n";
		echo "<input type=\"submit\" name=\"cancel\" value=\"{$lang['strcancel']}\" /></p>\n";
		echo "<input type=\"hidden\" name=\"report_id\" value=\"{$report->fields['report_id']}\" />\n";
		echo "</form>\n";
		$misc->printFooter();
	}

	/**
	 * Saves changes to a report
	 */
	function save_edit() {
		global $reportsdb, $lang;

		if (isset($_REQUEST['cancel'])) {
			$this->default_action();
			exit;
		}

		if (!isset($_POST['report_name'])) $_POST['report_name'] = '';
		if (!isset($_POST['db_name'])) $_POST['db_name'] = '';
		if (!isset($_POST['descr'])) $_POST['descr'] = '';
		if (!isset($_POST['report_sql'])) $_POST['report_sql'] = '';

		// Check that they've given a name and a definition
		if ($_POST['report_name'] == '') {
			$this->edit($lang['strreportneedsname']);
		} elseif ($_POST['report_sql'] == '') {
			$this->edit($lang['strreportneedsdef']);
		} else {
			$status = $this->reportsdb->alterReport($_POST['report_id'], $_POST['report_name'], $_POST['db_name'],
				$_POST['descr'], $_POST['report_sql'], isset($_POST['paginate']));
			if ($status == 0)
				$this->default_action($lang['strreportcreated']);
			else
				$this->edit($lang['strreportcreatedbad']);
		}
	}

	/**
	 * Display read-only properties of a report
	 */
	function properties($msg = '') {
		global $data, $reportsdb, $misc;
		global $lang;
		
		$misc->printHeader($lang['strreports']);
		$misc->printBody();
		$misc->printTrail('server');
		$misc->printTabs('server', 'report_plugin');
		$misc->printMsg($msg);

		$report = $this->reportsdb->getReport($_REQUEST['report_id']);

		$_REQUEST['report'] = $report->fields['report_name'];

		if ($report->recordCount() == 1) {
			echo "<table>\n";
			echo "<tr><th class=\"data left\">{$lang['strname']}</th>\n";
			echo "<td class=\"data1\">", $misc->printVal($report->fields['report_name']), "</td></tr>\n";
			echo "<tr><th class=\"data left\">{$lang['strdatabase']}</th>\n";
			echo "<td class=\"data1\">", $misc->printVal($report->fields['db_name']), "</td></tr>\n";
			echo "<tr><th class=\"data left\">{$lang['strcomment']}</th>\n";
			echo "<td class=\"data1\">", $misc->printVal($report->fields['descr']), "</td></tr>\n";
			echo "<tr><th class=\"data left\">{$lang['strsql']}</th>\n";
			echo "<td class=\"data1\">", $misc->printVal($report->fields['report_sql']), "</td></tr>\n";
			echo "</table>\n";
		}
		else echo "<p>{$lang['strinvalidparam']}</p>\n";

		$urlvars = array ('plugin' => $this->name, 'server' => field('server'));
		if (isset($_REQUEST['schema'])) $urlvars['schema'] = field('schema');
		if (isset($_REQUEST['schema'])) $urlvars['database'] = field('database');
		
		$navlinks = array (
			array (
				'attr'=> array (
					'href' => array (
						'url' => 'plugin.php',
						'urlvars' => array_merge($urlvars, array('action' => 'default_action'))
					)
				),
				'content' => $lang['strshowallreports']
			), array (
				'attr'=> array (
					'href' => array (
						'url' => 'plugin.php',
						'urlvars' => array_merge($urlvars, array('action' => 'edit', 'report_id' => $report->fields['report_id']))
					)
				),
				'content' => $lang['stredit']
			)
		);
		$misc->printNavLinks($navlinks, 'reports-properties');
	}

	/**
	 * Displays a screen where they can enter a new report
	 */
	function create($msg = '') {
		global $data, $reportsdb, $misc;
		global $lang;

		$misc->printHeader($lang['strreports']);
		$misc->printBody();
		$misc->printTrail('server');
		$misc->printTabs('server', 'report_plugin');
		$misc->printMsg($msg);
		
		if (!isset($_REQUEST['report_name'])) $_REQUEST['report_name'] = '';
		if (!isset($_REQUEST['db_name'])) $_REQUEST['db_name'] = '';
		if (!isset($_REQUEST['descr'])) $_REQUEST['descr'] = '';
		if (!isset($_REQUEST['report_sql'])) $_REQUEST['report_sql'] = '';

		if (isset($_REQUEST['database'])) {
			$_REQUEST['db_name'] = $_REQUEST['database'];
			unset($_REQUEST['database']);
			$misc->setForm();
		}
		
		$databases = $data->getDatabases();

		echo "<form action=\"plugin.php?plugin={$this->name}\" method=\"post\">\n";
		echo $misc->form;
		echo "<table style=\"width: 100%\">\n";
		echo "<tr><th class=\"data left required\">{$lang['strname']}</th>\n";
		echo "<td class=\"data1\"><input name=\"report_name\" size=\"32\" maxlength=\"{$data->_maxNameLen}\" value=\"",
			htmlspecialchars($_REQUEST['report_name']), "\" /></td></tr>\n";
		echo "<tr><th class=\"data left required\">{$lang['strdatabase']}</th>\n";
		echo "<td class=\"data1\"><select name=\"db_name\">\n";
		while (!$databases->EOF) {
			$dbname = $databases->fields['datname'];
			echo "<option value=\"", htmlspecialchars($dbname), "\"",
			($dbname == $_REQUEST['db_name']) ? ' selected="selected"' : '', ">",
				htmlspecialchars($dbname), "</option>\n";
			$databases->moveNext();
		}
		echo "</select></td></tr>\n";
		echo "<tr><th class=\"data left\">{$lang['strcomment']}</th>\n";
		echo "<td class=\"data1\"><textarea style=\"width:100%;\" rows=\"5\" cols=\"50\" name=\"descr\">",
			htmlspecialchars($_REQUEST['descr']), "</textarea></td></tr>\n";
		echo "<tr><th class=\"data left required\">{$lang['strsql']}</th>\n";
		echo "<td class=\"data1\"><textarea style=\"width:100%;\" rows=\"15\" cols=\"50\" name=\"report_sql\">",
			htmlspecialchars($_REQUEST['report_sql']), "</textarea></td></tr>\n";
		echo "</table>\n";
		echo "<label for=\"paginate\"><input type=\"checkbox\" id=\"paginate\" name=\"paginate\"", (isset($_REQUEST['paginate']) ? ' checked="checked"' : ''), " />&nbsp;{$lang['strpaginate']}</label>\n";
		echo "<p><input type=\"hidden\" name=\"action\" value=\"save_create\" />\n";
		echo "<input type=\"submit\" value=\"{$lang['strsave']}\" />\n";
		echo "<input type=\"submit\" name=\"cancel\" value=\"{$lang['strcancel']}\" /></p>\n";
		echo "</form>\n";
		$misc->printFooter();
	}

	/**
	 * Actually creates the new report in the database
	 */
	function save_create() {
		global $reportsdb, $lang;

		if (isset($_REQUEST['cancel'])) {
			$this->default_action();
			exit;
		}

		if (!isset($_POST['report_name'])) $_POST['report_name'] = '';
		if (!isset($_POST['db_name'])) $_POST['db_name'] = '';
		if (!isset($_POST['descr'])) $_POST['descr'] = '';
		if (!isset($_POST['report_sql'])) $_POST['report_sql'] = '';

		// Check that they've given a name and a definition
		if ($_POST['report_name'] == '') $this->create($lang['strreportneedsname']);
		elseif ($_POST['report_sql'] == '') $this->create($lang['strreportneedsdef']);
		else {
			$status = $this->reportsdb->createReport($_POST['report_name'], $_POST['db_name'],
					$_POST['descr'], $_POST['report_sql'], isset($_POST['paginate']));
			if ($status == 0)
				$this->default_action($lang['strreportcreated']);
			else
				$this->create($lang['strreportcreatedbad']);
		}
	}

	/**
	 * Show confirmation of drop and perform actual drop
	 */
	function drop() {
		global $reportsdb, $misc;
		global $lang;

		$confirm = false;
		if (isset($_REQUEST['confirm'])) $confirm = true;

		$misc->printHeader($lang['strreports']);
		$misc->printBody();
		
		if (isset($_REQUEST['cancel'])) {
			$this->default_action();
			exit;
		}

		if ($confirm) {
			// Fetch report from the database
			$report = $this->reportsdb->getReport($_REQUEST['report_id']);

			$_REQUEST['report'] = $report->fields['report_name'];
			$misc->printTrail('report');
			$misc->printTitle($lang['strdrop']);

			echo "<p>", sprintf($lang['strconfdropreport'], $misc->printVal($report->fields['report_name'])), "</p>\n";

			echo "<form action=\"plugin.php?plugin={$this->name}\" method=\"post\">\n";
			echo $misc->form;
			echo "<input type=\"hidden\" name=\"action\" value=\"drop\" />\n";
			echo "<input type=\"hidden\" name=\"report_id\" value=\"", htmlspecialchars($_REQUEST['report_id']), "\" />\n";
			echo "<input type=\"submit\" name=\"drop\" value=\"{$lang['strdrop']}\" />\n";
			echo "<input type=\"submit\" name=\"cancel\" value=\"{$lang['strcancel']}\" />\n";
			echo "</form>\n";
		} else {
			$status = $this->reportsdb->dropReport($_POST['report_id']);
			if ($status == 0)
				$this->default_action($lang['strreportdropped']);
			else
				$this->default_action($lang['strreportdroppedbad']);
		}

		$misc->printFooter();
	}

	/**
	 * Show default list of reports in the database
	 */
	function default_action($msg = '') {
		global $data, $misc, $reportsdb;
		global $lang;

		$misc->printHeader($lang['strreports']);
		$misc->printBody();
		$misc->printTrail('server');
		$misc->printTabs('server', 'report_plugin');
		$misc->printMsg($msg);
		
		$reports = $this->reportsdb->getReports();

		$columns = array(
			'report' => array(
				'title' => $lang['strreport'],
				'field' => field('report_name'),
				'url'   => "plugin.php?plugin={$this->name}&amp;action=properties&amp;{$misc->href}&amp;",
				'vars'  => array('report_id' => 'report_id'),
			),
			'database' => array(
				'title' => $lang['strdatabase'],
				'field' => field('db_name'),
			),
			'created' => array(
				'title' => $lang['strcreated'],
				'field' => field('date_created'),
			),
			'actions' => array(
				'title' => $lang['stractions'],
			),
			'comment' => array(
				'title' => $lang['strcomment'],
				'field' => field('descr'),
			),
		);
		
		$return_url = urlencode("plugin.php?plugin={$this->name}&amp;{$misc->href}");
		
		$actions = array(
			'run' => array(
				'title' => $lang['strexecute'],
				'url'   => "sql.php?subject=report&amp;{$misc->href}&amp;return_url={$return_url}&amp;return_desc=".urlencode($lang['strback'])."&amp;",
				'vars'  => array('report' => 'report_name', 'database' => 'db_name', 'reportid' => 'report_id', 'paginate' => 'paginate'),
			),
			'edit' => array(
				'title' => $lang['stredit'],
				'url'   => "plugin.php?plugin={$this->name}&amp;action=edit&amp;{$misc->href}&amp;",
				'vars'  => array('report_id' => 'report_id'),
			),
			'drop' => array(
				'title' => $lang['strdrop'],
				'url'   => "plugin.php?plugin={$this->name}&amp;action=drop&amp;confirm=true&amp;{$misc->href}&amp;",
				'vars'  => array('report_id' => 'report_id'),
			),
		);
		
		$misc->printTable($reports, $columns, $actions, 'reports-reports', $lang['strnoreports']);
		
		$navlinks = array (
			array (
				'attr'=> array (
					'href' => array (
						'url' => 'plugin.php',
						'urlvars' => array (
							'plugin' => $this->name, 
							'server' => field('server'),
							'action' => 'create')
					)
				),
				'content' => $lang['strcreatereport']
			)
		);
		$misc->printNavLinks($navlinks, 'reports-reports');
		$misc->printFooter();
	}
	
}
?>
