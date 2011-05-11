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
	 */
	function __construct($plugin_manager, $language) {
		$this->manage_transalation($language);
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

	/**
	 * Manage the plugins translations
	 * TODO: to put this function in the PluginManager class
	 */
	function manage_transalation($language) {
		require_once("./plugins/{$this->plugin_index}/lang/recoded/english.php");
		include_once("./plugins/{$this->plugin_index}/lang/translations.php");
		if (isset($pluginLangFiles[$language])) {
			include_once("./plugins/{$this->plugin_index}/lang/recoded/{$language}.php");
		}
		$this->plugin_lang = $plugin_lang;
	}
}
?>
