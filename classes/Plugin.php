<?php
abstract class Plugin {

	/**
	 * Constructor
	 * Register the plugin's functions in hooks of PPA.
	 * @param $language Current phpPgAdmin language.
	 * @param $plugin_directory Plugin's path.
	 */
	function __construct($language, $plugin_directory) {
		// Set the plugin's language
		require_once("{$plugin_directory}/lang/recoded/english.php");
		if (file_exists("{$plugin_directory}/lang/recoded/{$language}.php")) {
			include_once("{$plugin_directory}/lang/recoded/{$language}.php");
		}
		$this->lang = $plugin_lang;
	}

	abstract function get_hooks();

	abstract function get_actions();

	abstract function get_name();
}
?>
