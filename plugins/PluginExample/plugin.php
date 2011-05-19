<?php
class PluginExample {

	/**
	 * Attributes
	 */
	private $name = 'PluginExample';
	private $description = 'Plugin Exemple';
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
	function add_plugin_toplinks(&$toplinks_operations) {
		$toplinks_operations[$this->name] = "<a class=\"toplink\" href=\"#\">{$this->lang['plugin_toplink']}</a>";
	}
}
?>
