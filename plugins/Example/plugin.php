<?php
class Example {

	/**
	 * Attributes
	 */
	private $name = 'Example';
	private $lang;

	/**
	 * Constructor
	 * Register the plugin's functions in hooks of PPA.
	 * @param $plugin_manager - Instance of plugin manager
	 * @param $language Current phpPgAdmin language. If it was not found in the plugin, English will be used.
	 */
	function __construct($plugin_manager, $language) {
		// Set the plugin's language
		$lang_directory = dirname(__FILE__)."/lang/recoded";
		require_once("$lang_directory/english.php");
		if (file_exists("$lang_directory/$language.php")) {
			include_once("$lang_directory/$language.php");
		}
		$this->lang = $plugin_lang;
		
		$plugin_manager->add_plugin($this, $this->get_hooks(), $this->get_actions());
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
			'toplinks' => array('add_plugin_toplinks')
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
			'show_page'
		);
		return $actions;
	}

	/**
	 * Get the plugin name, that will be used as identification
	 * @return $plugin_name
	 */
	 function get_name() {
	 	 return $this->name;
	 }

	/**
	 * Add plugin in the top links
	 * @param $toplinks_operations
	 */
	function add_plugin_toplinks(&$plugin_functions_parameters) {
		global $misc;

		$href = "plugin.php?".$plugin_functions_parameters['href'];
		$href.= "&amp;plugin=".urlencode($this->name);
		$href.= "&amp;action=show_page";

		$link = "<a class=\"toplink\" href=\"$href\">";
		$link.= $this->lang['strdescription'];
		$link.= "</a>";

		//Add the link in the toplinks array
		$plugin_functions_parameters['toplinks'][] = $link;
	}

	/**
	 * Show a simple page
	 * This function will be used as an action
	 *
	 * TODO: make a style for this plugin, as an example of use of own css style.
	 */
	function show_page() {
		global $lang;

		echo "<div>{$this->lang['strdescription']}</div>";
		echo "<br>";

		$url = "<a href=\"servers.php\">";
		$url.= $lang['strback'];
		$url.= "</a>";
		echo $url;
	}
}
?>
