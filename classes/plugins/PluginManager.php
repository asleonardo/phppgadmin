<?php

/**
 * A class that implements the plugin's system
 *
 * $Id:
 */

class PluginManager {

	/**
	 * Attributes
	 */
	public $plugins_list = array();
	public $functions_list = array();

	/**
	 * Add a plugin in the manager
	 * @param $plugin
	 */
	function add_plugin($plugin) {
		$this->plugins_list[$plugin->get_plugin_index()] = $plugin;
	}

	/**
	 * Get a plugin from the $plugins_list by the plugin's name.
	 * @param $plugin_index
	 */
	function get_plugin($plugin_index) {
		return $this->plugins_list[$plugin_index];
	}

	/**
	 * Add a function in the $functions_list list, with the information when this function will be used by the
	 * phppgadmin core.
	 * @param $plugin_index - Index that identify the plugin. Example the plugin_example's index is plugin_example :-)
	 * @param $when - This identify when the added function will be called
	 * @param $function - The name of the function. It will be called by callback;
	 */
	function add_plugin_functions($plugin_index, $when, $function) {
		$this->functions_list[$when][] = array('plugin_index' => $plugin_index, 'function' => $function);
	}

	/**
	 * Execute the plugins functions according some moment.
	 * @param $when - When the function will be called
	 * @param $function_args - The reference to arguments of the called function
	 *
	 * TODO: check the supported entries (browser tree, tabs, trailer, navigation links, action buttons, top links)
	 */
	function execute_plugin_funtions($when, &$function_args) {
		if (isset($this->functions_list[$when])) {
			foreach ($this->functions_list[$when] as $node) {
				$plugin_index = $node['plugin_index'];
				$function = $node['function'];
				$plugin = $this->get_plugin($plugin_index); 
				//
				if (method_exists($plugin, $function)) {
					call_user_func_array(array($plugin, $function), array(&$function_args));
				}
			}
		}
	}

	/**
	 * Get the plugin translations
	 * @param $plugin_index - Name of the plugin index. Example: PluginExample, Crud, etc...
	 * @param $language - Current phpPgAdmin language. If it was not found in the plugin, English will be used.
	 *
	 * TODO: check if an english translation file exists. If not, to think a way to alert about it.
	 */
	function get_transalation($plugin_index, $language) {
		require_once("./plugins/{$plugin_index}/lang/recoded/english.php");
		require_once("./plugins/{$plugin_index}/lang/translations.php");
		if (isset($pluginLangFiles[$language])) {
			include_once("./plugins/{$plugin_index}/lang/recoded/{$language}.php");
		}
		return $plugin_lang;
	}
}
?>
