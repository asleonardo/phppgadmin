<?php

/**
 * A class that implements the plugin's system
 */

class PluginManager {

	/**
	 * Attributes
	 */
	private $plugins_list = array();
	private $functions_list = array();

	/**
	 * Register the plugins
	 * @param $language - Language that have been used.
	 */
	function __construct($language) {
		global $conf, $lang;

		// Get the activated plugins
		$plugins = $conf['plugins'];

		foreach ($plugins as $activated_plugin) {
			$plugin_file = './plugins/'.$activated_plugin.'/plugin.php';

			// Verify is the activated plugin exists
			if (file_exists($plugin_file)) {
				include_once($plugin_file);
				$plugin = new $activated_plugin($this, $language);
			} else {
				printf($lang['strpluginnotfound']."\t\n", $activated_plugin);
				exit;
			}
		}
	}

	/**
	 * Add a plugin in the list of plugins to manage
	 * @param $plugin - Instance from plugin
	 * @param $hooks - Array with functions and the places where they will hook. The default value is an empty array.
	 * @param $actions - Array with functions that the plugin will use as actions. The default value is an empty array.
	 */
	function add_plugin($plugin, $hooks = array(), $actions = array()) {
		//The $name is the identification of the plugin.
		//Example: PluginExample is the identification for PluginExample
		//It will be used to get a specific plugin from the plugins_list.
		$plugin_name = $plugin->get_name();
		$this->plugins_list[$plugin_name] = $plugin;

		//Register the plugin's functions
		foreach ($hooks as $hook => $functions) {
			$this->functions_list['hooks'][$hook][$plugin_name] = $functions;
		}

		//Register the plugin's actions
		$this->functions_list['actions'][$plugin_name] = $actions;
	}

	/**
	 * Get a plugin from the $plugins_list by the plugin's identification.
	 * @param $name - the plugin's name as identification. Exemple: PluginExample.
	 */
	function get_plugin($name) {
		global $lang;

		if (isset($this->plugins_list[$name])) {
			return $this->plugins_list[$name];
		} else {
			// Show an error and stop the application
			printf($lang['strpluginnotfound']."\t\n", $name);
			exit;
		}
	}

	/**
	 * Execute the plugins functions according some moment.
	 * @param $hook - The place where the function will be called
	 * @param $function_args - The reference to arguments of the called function
	 *
	 * TODO: check the supported entries (browser tree, tabs, trailer, navigation links, action buttons, top links)
	 */
	function execute_plugin_funtions($hook, &$function_args) {
		if (isset($this->functions_list['hooks'][$hook])) {
			foreach ($this->functions_list['hooks'][$hook] as $plugin_name => $functions) {
				foreach ($functions as $function) {
					$plugin = $this->get_plugin($plugin_name);
					if (method_exists($plugin, $function)) {
						call_user_func_array(array($plugin, $function), array(&$function_args));
					}
				}
			}
		}
	}

	/**
	 * Get the plugin translations
	 * @param $name - Plugin's name. Example: PluginExample, Crud, etc...
	 * @param $language - Current phpPgAdmin language. If it was not found in the plugin, English will be used.
	 *
	 * TODO: check if an english translation file exists. If not, to think a way to alert about it.
	 */
	function get_transalation($name, $language) {
		require_once("./plugins/{$name}/lang/recoded/english.php");
		if (file_exists("./plugins/{$name}/lang/recoded/{$language}.php")) {
			include_once("./plugins/{$name}/lang/recoded/{$language}.php");
		}
		return $plugin_lang;
	}

	/**
	 * Execute a plugin's action
	 * @param $action - action that will be executed. The action is the name of a plugin's function.
	 */
	function do_action($plugin_name, $action) {
		global $lang;

		$plugin = $this->get_plugin($plugin_name);

		// Check if the plugin's method exists and if this method is an declareted action.
		// The actions are declared in the plugins' constructors.
		if (method_exists($plugin, $action) and in_array($action, $this->functions_list['actions'][$plugin_name])) {
			call_user_func(array($plugin, $action));
		} else {
			// Show an error and stop the application
			printf($lang['stractionnotfound']."\t\n", $action, $plugin_name);
			exit;
		}
	}
}
?>
