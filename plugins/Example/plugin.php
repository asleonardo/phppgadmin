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
		$this->lang = $plugin_manager->get_transalation($this->name, $language);
		$plugin_manager->add_plugin($this, $this->get_hooks());
	}

	/**
	 * This method returns the functions that will hook in the phpPgAdmin core.
	 * To do include a function just put in the $functions array the follwing code:
	 * 'hook' => array('function1', 'function2').
	 *
	 * Example:
	 * $functions = array(
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
		//NOTE: What is the best way to return? $_SERVER['HTTP_REFERER'] cannot be trusted.
		$href.= "&amp;return_url=".urlencode($_SERVER['PHP_SELF']."?".$misc->getHREF());

		$link = "<a class=\"toplink\" href=\"$href\">";
		$link.= $this->lang['strdescription'];
		$link.= "</a>";

		//Add the link in the toplinks array
		$plugin_functions_parameters['toplinks'][] = $link;
	}

	/**
	 * Show a simple page
	 *
	 * TODO: make a style for this plugin, as an example of use of own css style.
	 */
	function show_page() {
		global $lang;

		echo "<div>{$this->lang['strdescription']}</div>";
		echo "<br>";

		$url = "<a href=\"{$_REQUEST['return_url']}\">";
		$url.= $lang['strback'];
		$url.= "</a>";
		echo $url;
	}
}
?>
