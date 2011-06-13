<?php
require_once(PPA_CLASS_PATH.'/Plugin.php');

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
			'show_level_4'
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

		$tabs = array();
		switch ($plugin_functions_parameters['section']) {
			case 'server':
				$tabs = array (
					'title' => $this->lang['strdescription'],
					'url' => 'plugin.php',
					'urlvars' => array('subject' => 'server', 'action' => 'show_page', 'plugin' => urlencode($this->name)),
					'hide' => false,
					'icon' => array('plugin' => 'Example', 'image' => 'Hook')
				);
				break;
		}
		//Add the link in the tabs array
		if (count($tabs) > 0) {
			$plugin_functions_parameters['tabs']['Example'] = $tabs;
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
				'icon' => array('plugin' => 'Example', 'image' => 'Hook')
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
	}

	/**
	 * Add plugin in the navlinks
	 * @param $plugin_functions_parameters
	 */
	function add_plugin_navlinks(&$plugin_functions_parameters) {
		global $misc;

		$href = "plugin.php?".$misc->href;
		$href.= "&plugin=".urlencode($this->name);
		$href.= "&subject=server";
		$href.= "&action=show_page";

		$navlinks  = array (
				'attr'=> array ('href' => $href),
				'content' => $this->lang['strdescription']
		);

		//Add the link in the toplinks array
		$plugin_functions_parameters['navlinks'][] = $navlinks;
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
		$link = "<a href=\"plugin.php?".$misc->href;
		$link.= "&amp;plugin=".urlencode($this->name);
		$link.= "&amp;action=show_level_2";
		$link.= "&amp;subject=show_page\">";
		$link.= $this->lang['strlinklevel2'];
		$link.= "</a>\n";
		echo $link;

		echo "<br/>\n";
		echo "<br/>\n";

		$back_link = "<a href=\"servers.php\">";
		$back_link.= $lang['strback'];
		$back_link.= "</a>\n";
		echo $back_link;

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
		$link = "<a href=\"plugin.php?".$misc->href;
		$link.= "&amp;plugin=".urlencode($this->name);
		$link.= "&amp;action=show_level_3";
		$link.= "&amp;subject=show_level_2\">";
		$link.= $this->lang['strlinklevel3'];
		$link.= "</a>\n";
		echo $link;

		echo "<br/>\n";
		echo "<br/>\n";

		$back_link = "<a href=\"plugin.php?".$misc->href;
		$back_link.= "&amp;plugin=".urlencode($this->name);
		$back_link.= "&amp;action=show_page"; 
		$back_link.= "&amp;subject=server\">";
		$back_link.= $lang['strback'];
		$back_link.= "</a>\n";
		echo $back_link;

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
		$link = "<a href=\"plugin.php?".$misc->href;
		$link.= "&amp;plugin=".urlencode($this->name);
		$link.= "&amp;action=show_level_4";
		$link.= "&amp;subject=show_level_3\">";
		$link.= $this->lang['strlinklevel4'];
		$link.= "</a>\n";
		echo $link;

		echo "<br/>\n";
		echo "<br/>\n";

		$back_link = "<a href=\"plugin.php?".$misc->href;
		$back_link.= "&amp;plugin=".urlencode($this->name);
		$back_link.= "&amp;action=show_level_2"; 
		$back_link.= "&amp;subject=show_page\">";
		$back_link.= $lang['strback'];
		$back_link.= "</a>\n";
		echo $back_link;

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

		$back_link = "<a href=\"plugin.php?".$misc->href;
		$back_link.= "&amp;plugin=".urlencode($this->name);
		$back_link.= "&amp;action=show_level_3";
		$back_link.= "&amp;subject=show_level_2\">";
		$back_link.= $lang['strback'];
		$back_link.= "</a>\n";
		echo $back_link;

		$misc->printFooter();
	}
}
?>
