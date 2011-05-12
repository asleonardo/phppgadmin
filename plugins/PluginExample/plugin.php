<?php
class PluginExample {

	/**
	 * Attributes
	 */
	public $plugin_name = 'Plugin example';
	public $plugin_index = __CLASS__;
	public $plugin_lang = '';

	/**
	 * Constructor
	 * Register the plugin's functions in hooks of PPA.
	 * @param $plugin_manager - Instance of plugin manager
	 * @param $language Current phpPgAdmin language. If it was not found in the plugin, English will be used.
	 */
	function __construct($plugin_manager, $language) {
		$this->plugin_lang = $plugin_manager->get_transalation($this->plugin_index, $language);
		$plugin_manager->add_plugin_functions($this->plugin_index, 'toplinks', 'add_plugin_toplinks');
		
		/* Register more functions here */
	}

	/**
	 * Get the $plugin_index
	 * @return $plugin_index
	 */
	 function get_plugin_index() {
	 	 return $this->plugin_index;
	 }

	/**
	 * Add plugin in the top links
	 * @param $toplinks_operations
	 */
	function add_plugin_toplinks(&$toplinks_operations) {
		$toplinks_operations[$this->plugin_index] = "<a class=\"toplink\" href=\"#\">{$this->plugin_lang['plugin_toplink']}</a>";
	}
}
?>
