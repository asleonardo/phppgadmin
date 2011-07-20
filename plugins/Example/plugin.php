<?php
require_once('classes/Plugin.php');

class Example extends Plugin {

	/**
	 * Attributes
	 */
	protected $name = 'Example';
	protected $lang;

	/**
	 * Constructor
	 * Call parent constructor, passing the language that will be used.
	 * @param $language Current phpPgAdmin language. If it was not found in the plugin, English will be used.
	 */
	function __construct($language) {
		$plugin_directory = dirname(__FILE__);
		parent::__construct($language, $plugin_directory);
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
			'toplinks' => array('add_plugin_toplinks'),
			'tabs' => array('add_plugin_tabs'),
			'trail' => array('add_plugin_trail'),
			'navlinks' => array('add_plugin_navlinks'),
			'actionbuttons' => array('add_plugin_actionbuttons'),
			'displaybuttons' => array('add_plugin_displaybuttons')
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
			'show_page',
			'show_level_2',
			'show_level_3',
			'show_level_4',
			'show_display_extension',
			'show_databases_extension',
			'show_display_example',
			'show_schema_extension',
			'show_schema_extension_level_1',
			'show_schema_extension_level_2',
			'show_schema_extension_level_2_1',
			'show_schema_extension_level_2_2',
			'tree',
			'sub_tree'
		);
		return $actions;
	}

	/**
	 * Add plugin in the top links
	 * @param $plugin_functions_parameters
	 */
	function add_plugin_toplinks(&$plugin_functions_parameters) {
		global $misc;

		$href = "plugin.php?".$misc->href;
		$href.= "&plugin=".urlencode($this->name);
		$href.= "&subject=server";
		$href.= "&action=show_page";

		$toplink = array (
			'attr' => array (
				'href' => $href,
				'class' => 'toplink'
			),
			'content' => $this->lang['strdescription']
		);

		//Add the link in the toplinks array
		$plugin_functions_parameters['toplinks'][] = $toplink;
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
				$tabs['show_page'] = array (
					'title' => $this->lang['strdescription'],
					'url' => 'plugin.php',
					'urlvars' => array('subject' => 'server', 'action' => 'show_page', 'plugin' => urlencode($this->name)),
					'hide' => false,
					'icon' => 'Plugins'
				);
				break;
			case 'schema':
				$tabs['show_schema_extension'] = array (
					'title' => $this->lang['strdescription'],
					'url' => 'plugin.php',
					'urlvars' => array(
						'subject' => 'server', 
						'database' => $_REQUEST['database'],
						'schema' => $_REQUEST['schema'],
						'action' => 'show_schema_extension', 
						'plugin' => urlencode($this->name)),
					'hide' => false,
					'icon' => 'Plugins'
				);
				break;
			case 'show_schema_extension':
				$tabs['show_schema_extension_level_1'] = array (
					'title' => $this->lang['strlinklevel1'],
					'url' => 'plugin.php',
					'urlvars' => array(
						'subject' => 'show_schema_extension', 
						'action' => 'show_schema_extension_level_1', 
						'plugin' => urlencode($this->name)
					),
					'level' => 'show_schema_extension_level_1',
					'icon' => 'Plugins',
				);
				$tabs['show_schema_extension_level_2'] = array (
					'title' => $this->lang['strlinklevel2'],
					'url' => 'plugin.php',
					'urlvars' => array(
						'subject' => 'show_schema_extension', 
						'action' => 'show_schema_extension_level_2', 
						'plugin' => urlencode($this->name)
					),
					'level' => 'show_schema_extension_level_2',
					'icon' => 'Plugins',
				);
				break;
			case 'show_schema_extension_level_2':
				$tabs['show_schema_extension_level_2_1'] = array (
					'title' => $this->lang['strlinklevel2s1'],
					'url' => 'plugin.php',
					'urlvars' => array(
						'subject' => 'show_schema_extension_level_2', 
						'action' => 'show_schema_extension_level_2_1', 
						'plugin' => urlencode($this->name)
					),
					'icon' => 'Plugins',
				);
				$tabs['show_schema_extension_level_2_2'] = array (
					'title' => $this->lang['strlinklevel2s2'],
					'url' => 'plugin.php',
					'urlvars' => array(
						'subject' => 'show_schema_extension_level_2', 
						'action' => 'show_schema_extension_level_2_2', 
						'plugin' => urlencode($this->name)
					),
					'icon' => 'Plugins',
				);
				break;
		}
	}

	/**
	 * Add plugin in the trail
	 * @param $plugin_functions_parameters
	 */
	function add_plugin_trail(&$plugin_functions_parameters) {
		global $misc;
		$trail = &$plugin_functions_parameters['trail'];
		$done = false;
		$subject = '';

		if (isset($_REQUEST['subject'])) {
			$subject = $_REQUEST['subject'];
		}

		if (in_array($subject, array('show_page', 'show_level_2', 'show_level_3'))) {
			$trail['show_page'] = array(
				'title' => $this->lang['strlinktoplevel'],
				'text'  => $this->lang['strlinktoplevel'],
				'url'   => "plugin.php?".$misc->href."&plugin=".urlencode($this->name)."&action=show_page&subject=server",
				'icon' => 'Plugins'
			);

			if ($subject == 'show_page') $done = true;

			if (!$done) {
				$trail['show_level_2'] = array(
					'title' => $this->lang['strlinklevel2'],
					'text'  => $this->lang['strlinklevel2'],
					'url'   => "plugin.php?".$misc->href."&plugin=".urlencode($this->name)."&action=show_level_2&subject=show_page",
					'icon' => array('plugin' => 'Example', 'image' => 'Level2')
				);
			}

			if ($subject == 'show_level_2') $done = true;

			if (!$done) {
				$trail['show_level_3'] = array(
					'title' => $this->lang['strlinklevel3'],
					'text'  => $this->lang['strlinklevel3'],
					'url'   => "plugin.php?".$misc->href."&plugin=".urlencode($this->name)."&action=show_level_3&subject=show_level_2",
					'icon' => array('plugin' => 'Example', 'image' => 'Level3')
				);
			}
		}

		//schema extension 
		if (in_array($subject, array('show_schema_extension', 'show_schema_extension_level_2'))) {
				$trail['show_schema_extension'] = array(
				'title' => $this->lang['strschemaext'],
				'text'  => $this->lang['strschemaext'],
				'url'   => "redirect.php?server=".urlencode($_REQUEST['server'])."&database=".urlencode($_REQUEST['database'])."&schema=".urlencode($_REQUEST['schema'])."&plugin=".urlencode($this->name)."&action=show_schema_extension&subject=show_schema_extension",
				'icon' => 'Plugins'
			);

			if ($subject == 'show_schema_extension') $done = true;

			if (!$done) {
				$trail['show_schema_extension_level_2'] = array(
					'title' => $this->lang['strlinklevel2'],
					'text'  => $this->lang['strlinklevel2'],
					'url'   => "redirect.php?server=".urlencode($_REQUEST['server'])."&database=".urlencode($_REQUEST['database'])."&schema=".urlencode($_REQUEST['schema'])."&plugin=".urlencode($this->name)."&action=show_schema_extension_level_2&subject=show_schema_extension_level_2",
					'icon' => 'Plugins'
				);
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
		switch ($plugin_functions_parameters['place']) {

			case 'display-browse':
				$href = "plugin.php?".$misc->href;
				$href.= "&amp;plugin=".urlencode($this->name);
				$href.= "&amp;subject=show_page";
				$href.= "&amp;action=show_display_extension";
				$href.= "&amp;database=".urlencode($_REQUEST['database']);
				$href.= "&amp;table=".urlencode($_REQUEST['table']);

				$navlinks[] = array (
					'attr'=> array ('href' => $href),
					'content' => $this->lang['strdisplayext']
				);
				break;

			case 'all_db-databases':
				$href = "plugin.php?".$misc->href;
				$href.= "&amp;plugin=".urlencode($this->name);
				$href.= "&amp;subject=show_page";
				$href.= "&amp;action=show_databases_extension";

				$navlinks[] = array (
					'attr'=> array ('href' => $href),
					'content' => $this->lang['strdbext']
				);
				break;
		}

		if (count($navlinks) > 0) {
			//Merge the original navlinks array with Examples' navlinks 
			$plugin_functions_parameters['navlinks'] = array_merge($plugin_functions_parameters['navlinks'], $navlinks);
		}
	}

	/**
	 * Add plugin in the action buttons
	 * @param $plugin_functions_parameters
	 */
	function add_plugin_actionbuttons(&$plugin_functions_parameters) {
		global $misc;

		$actionbuttons = array();
		switch ($plugin_functions_parameters['place']) {

			case 'all_db-databases':
				$href = "plugin.php?".$misc->href;
				$href.= "&amp;plugin=".urlencode($this->name);
				$href.= "&amp;subject=show_page";
				$href.= "&amp;action=show_databases_extension&amp;";

				$actionbuttons['extraaction'] = array (
					'title' => $this->lang['strextraaction'],
					'url'   => $href,
					'vars'  => array('database' => 'datname')
				);
				break;
		}

		if (count($actionbuttons) > 0) {
			//Merge the original actionbuttons array with Examples' actionbuttons 
			$plugin_functions_parameters['actionbuttons'] = array_merge($plugin_functions_parameters['actionbuttons'], $actionbuttons);
		}
	}

	/**
	 * Add plugin in the action buttons of display.php
	 * @param $plugin_functions_parameters
	 */
	function add_plugin_displaybuttons(&$plugin_functions_parameters) {
		global $misc;

		$buttons = array();
		
		$href = "plugin.php?".$misc->href;
		$href.= "&amp;plugin=".urlencode($this->name);
		$href.= "&amp;subject=show_page";
		$href.= "&amp;action=show_display_example";
		$href.= "&amp;table=".urlencode($_REQUEST['table']);

		$buttons['extraaction'] = array (
			'title' => $this->lang['strextraaction'],
			'url'   => $href,
		);

		if (count($buttons) > 0) {
			//Merge the original actionbuttons array with Examples' actionbuttons
			$plugin_functions_parameters['displaybuttons'] = array_merge($plugin_functions_parameters['displaybuttons'], $buttons);
		}
	}

	/**
	 * Show a simple page
	 * This function will be used as an action
	 *
	 * TODO: make a style for this plugin, as an example of use of own css style.
	 */
	function show_page() {
		global $lang, $misc;

		$misc->printHeader($lang['strdatabase']);
		$misc->printBody();
		$misc->printTrail($_REQUEST['subject']);

		echo "<div>{$this->lang['strdescription']}</div>\n";
		echo "<br/>\n";

		//link to level 2
		$level3 = array (
			'url' => 'plugin.php',
			'urlvars' => array (
				'plugin' => $this->name,
				'subject' => 'show_page',
				'action' => 'show_level_2'
			),
		);
		echo "<a {$misc->printActionUrl($level3, $_REQUEST, 'href')}> {$this->lang['strlinklevel2']}</a>\n";

		echo "<br/>\n";
		echo "<br/>\n";

		$back = array ('url' => 'servers.php');
		echo "<a {$misc->printActionUrl($back, $_REQUEST, 'href')}> {$lang['strback']}</a>\n";

		$misc->printFooter();
	}

	/**
	 * Show the second level of pages
	 */
	function show_level_2() {
		global $lang, $misc;

		$misc->printHeader($lang['strdatabase']);
		$misc->printBody();
		$misc->printTrail($_REQUEST['subject']);

		echo "<div>{$this->lang['strdesclevel2']}</div>\n";
		echo "<br/>\n";

		//level 3
		$level3 = array (
			'url' => 'plugin.php',
			'urlvars' => array (
				'plugin' => $this->name,
				'subject' => 'show_level_2',
				'action' => 'show_level_3'
			),
		);
		echo "<a {$misc->printActionUrl($level3, $_REQUEST, 'href')}> {$this->lang['strlinklevel3']}</a>\n";

		echo "<br/>\n";
		echo "<br/>\n";

		$back = array (
			'url' => 'plugin.php',
			'urlvars' => array (
				'plugin' => $this->name,
				'subject' => 'server',
				'action' => 'show_page'
			),
		);
		echo "<a {$misc->printActionUrl($back, $_REQUEST, 'href')}> {$lang['strback']}</a>\n";

		$misc->printFooter();
	}

	/**
	 * Show the third level of pages
	 */
	function show_level_3() {
		global $lang, $misc;

		$misc->printHeader($lang['strdatabase']);
		$misc->printBody();
		$misc->printTrail($_REQUEST['subject']);

		echo "<div>{$this->lang['strdesclevel3']}</div>";
		echo "<br/>\n";

		//level 4
		$level4 = array (
			'url' => 'plugin.php',
			'urlvars' => array (
				'plugin' => $this->name,
				'subject' => 'show_level_3',
				'action' => 'show_level_4'
			),
		);
		echo "<a {$misc->printActionUrl($level4, $_REQUEST, 'href')}> {$this->lang['strlinklevel4']}</a>\n";

		echo "<br/>\n";
		echo "<br/>\n";

		$back = array (
			'url' => 'plugin.php',
			'urlvars' => array (
				'plugin' => $this->name,
				'subject' => 'show_page',
				'action' => 'show_level_2'
			),
		);
		echo "<a {$misc->printActionUrl($back, $_REQUEST, 'href')}> {$lang['strback']}</a>\n";

		$misc->printFooter();
	}

	/**
	 * Show the fourth level of pages
	 */
	function show_level_4() {
		global $lang, $misc;

		$misc->printHeader($lang['strdatabase']);
		$misc->printBody();
		$misc->printTrail($_REQUEST['subject']);

		echo "<div>{$this->lang['strdesclevel4']}</div>\n";
		echo "<br/>\n";

		$back = array (
			'url' => 'plugin.php',
			'urlvars' => array (
				'plugin' => $this->name,
				'subject' => 'show_level_2',
				'action' => 'show_level_3'
			),
		);
		echo "<a {$misc->printActionUrl($back, $_REQUEST, 'href')}> {$lang['strback']}</a>\n";

		$misc->printFooter();
	}
	
	/**
	 * Simple example of how to put a hook in the display page.
	 */
	function show_display_extension() {
		global $lang, $misc;

		$misc->printHeader($lang['strdatabase']);
		$misc->printBody();
		$misc->printTrail($_REQUEST['subject']);

		echo "<div>{$this->lang['strdisplayext']}</div>\n";
		echo "<br/>\n";

		$back = array (
			'url' => 'display.php',
			'urlvars' => array (
				'schema' => field('schema'),
				'table' => field('table'),
				'subject' => 'table'
			),
		);
		echo "<a {$misc->printActionUrl($back, $_REQUEST, 'href')}> {$lang['strback']}</a>\n";
		

		$misc->printFooter();
	}

	/**
	 * Simple example of how to put a hook in the databases list page.
	 */
	function show_databases_extension() {
		global $lang, $misc;

		$misc->printHeader($lang['strdatabase']);
		$misc->printBody();
		$misc->printTrail($_REQUEST['subject']);

		echo "<div>{$this->lang['strdbext']}</div>\n";
		echo "<br/>\n";

		$back = array (
			'url' => 'all_db.php',
			'urlvars' => array ('server' => field('server'))
		);
		echo "<a {$misc->printActionUrl($back, $_REQUEST, 'href')}> {$lang['strback']}</a>\n";

		$misc->printFooter();
	}
	
	/**
	 * Simple example of how to put a hook in the display page.
	 */
	function show_display_example() {
		global $lang, $misc;

		$misc->printHeader($lang['strdatabase']);
		$misc->printBody();
		$misc->printTrail($_REQUEST['subject']);

		echo "<div>{$this->lang['strextraaction']}</div>\n";
		echo "<br/>\n";

		$back = array (
			'url' => 'display.php',
			'urlvars' => array (
				'schema' => field('schema'),
				'table' => field('table'),
				'subject' => 'table'
			),
		);
		echo "<a {$misc->printActionUrl($back, $_REQUEST, 'href')}> {$lang['strback']}</a>\n";


		$misc->printFooter();
	}



	function show_schema_extension() {
		global $lang, $misc;

		$misc->printHeader($lang['strdatabase']);
		$misc->printBody();
		$misc->printTrail('schema');
		$misc->printTabs('schema','show_schema_extension');

		echo "<div>{$this->lang['strschemaext']}</div>\n";
		echo "<br/>\n";

		//link to schema level 1
		$level1 = array (
			'url' => 'plugin.php',
			'urlvars' => array (
				'schema' => field('schema'),
				'plugin' => $this->name,
				'subject' => 'show_schema_extension', 
				'action' => 'show_schema_extension_level_1'
			),
		);
		echo "<a {$misc->printActionUrl($level1, $_REQUEST, 'href')}> {$this->lang['strlinklevel1']}</a>\n";

		echo "<br />\n";

		//link to schema level 2
		$level2 = array (
			'url' => 'plugin.php',
			'urlvars' => array (
				'schema' => field('schema'),
				'plugin' => $this->name,
				'subject' => 'show_schema_extension', 
				'action' => 'show_schema_extension_level_2'
			),
		);
		echo "<a {$misc->printActionUrl($level2, $_REQUEST, 'href')}> {$this->lang['strlinklevel2']}</a>\n";

		$misc->printFooter();
	}

	function show_schema_extension_level_1() {
		global $lang, $misc;

		$misc->printHeader($lang['strdatabase']);
		$misc->printBody();
		$misc->printTrail('show_schema_extension');
		$misc->printTabs('show_schema_extension','show_schema_extension_level_1');

		echo "<div>{$this->lang['strlinklevel1']}</div>\n";

		echo "<br/>\n";
		echo "<br/>\n";

		$back = array (
			'url' => 'plugin.php',
			'urlvars' => array (
				'schema' => field('schema'),
				'plugin' => $this->name,
				'subject' => 'schema',
				'action' => 'show_schema_extension'
			),
		);
		echo "<a {$misc->printActionUrl($back, $_REQUEST, 'href')}> {$lang['strback']}</a>\n";

		$misc->printFooter();
	}

	function show_schema_extension_level_2() {
		global $lang, $misc;

		$misc->printHeader($lang['strdatabase']);
		$misc->printBody();
		$misc->printTrail('show_schema_extension');
		$misc->printTabs('show_schema_extension','show_schema_extension_level_2');

		echo "<div>{$this->lang['strlinklevel2']}</div>\n";

		//link to schema level 2.1
		$level2_1 = array (
			'url' => 'plugin.php',
			'urlvars' => array (
				'schema' => field('schema'),
				'plugin' => $this->name,
				'subject' => 'show_schema_extension_level_2', 
				'action' => 'show_schema_extension_level_2_1'
			),
		);
		echo "<a {$misc->printActionUrl($level2_1, $_REQUEST, 'href')}> {$this->lang['strlinklevel2s1']}</a>\n";
		echo "<br/>\n";

		//link to schema level 2.2
		$level2_2 = array (
			'url' => 'plugin.php',
			'urlvars' => array (
				'schema' => field('schema'),
				'plugin' => $this->name,
				'subject' => 'show_schema_extension_level_2',
				'action' => 'show_schema_extension_level_2_2'
			),
		);
		echo "<a {$misc->printActionUrl($level2_2, $_REQUEST, 'href')}> {$this->lang['strlinklevel2s2']}</a>\n";
		echo "<br/>\n";
		echo "<br/>\n";

		$back = array (
			'url' => 'plugin.php',
			'urlvars' => array (
				'schema' => field('schema'),
				'plugin' => $this->name,
				'subject' => 'schema', 
				'action' => 'show_schema_extension'
			),
		);
		echo "<a {$misc->printActionUrl($back, $_REQUEST, 'href')}> {$lang['strback']}</a>\n";

		$misc->printFooter();
	}

	function show_schema_extension_level_2_1() {
		global $lang, $misc;

		$misc->printHeader($lang['strdatabase']);
		$misc->printBody();
		$misc->printTrail('show_schema_extension_level_2');
		$misc->printTabs('show_schema_extension_level_2','show_schema_extension_level_2_1');

		echo "<div>{$this->lang['strlinklevel2s1']}</div>\n";

		echo "<br/>\n";
		echo "<br/>\n";

		$back = array (
			'url' => 'plugin.php',
			'urlvars' => array (
				'schema' => field('schema'),
				'plugin' => $this->name,
				'subject' => 'show_schema_extension',
				'action' => 'show_schema_extension_level_2'
			),
		);
		echo "<a {$misc->printActionUrl($back, $_REQUEST, 'href')}> {$lang['strback']}</a>\n";

		$misc->printFooter();
	}

	function show_schema_extension_level_2_2() {
		global $lang, $misc;

		$misc->printHeader($lang['strdatabase']);
		$misc->printBody();
		$misc->printTrail('show_schema_extension_level_2');
		$misc->printTabs('show_schema_extension_level_2','show_schema_extension_level_2_2');

		echo "<div>{$this->lang['strlinklevel2s2']}</div>\n";

		echo "<br/>\n";
		echo "<br/>\n";

		$back = array (
			'url' => 'plugin.php',
			'urlvars' => array (
				'schema' => field('schema'),
				'plugin' => $this->name,
				'subject' => 'show_schema_extension',
				'action' => 'show_schema_extension_level_2'
			),
		);
		echo "<a {$misc->printActionUrl($back, $_REQUEST, 'href')}> {$lang['strback']}</a>\n";

		$misc->printFooter();
	}

	function tree() {
		global $misc;

		$reqvars = $misc->getRequestVars('show_schema_extension');
		$tabs = $misc->getNavTabs('show_schema_extension');
		$items = $misc->adjustTabsForTree($tabs);
		
		$attrs = array(
			'text'   => noEscape(field('title')),
			'icon'   => field('icon'),
			'action' => url(
				field('url'),
				$reqvars,
				field('urlvars', array())
			),
			'branch' => url('plugin.php',
				$reqvars,
				array (
					'action' => 'sub_tree',
					'plugin' => urlencode($this->name),
					'level' => field('level')
				)
			)
		);
		
		$misc->printTreeXML($items, $attrs);
		exit;
	}

	function sub_tree() {
		global $misc;

		$reqvars = $misc->getRequestVars($_REQUEST['level']);
		$tabs = $misc->getNavTabs($_REQUEST['level']);
		$items = $misc->adjustTabsForTree($tabs);
		$attrs = array(
			'text' => noEscape(field('title')),
			'icon' => field('icon'),
			'action' => url(
				field('url'),
				$reqvars,
				field('urlvars', array())
			),
		);
		$misc->printTreeXML($items, $attrs);
		exit;
	}
}
?>
