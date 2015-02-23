<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * TtdPluginClass
 *
 * @author		Geraint Palmer
 * @version 	1.0.1 	   
 */
	class TtdPluginClass
	{
		const VERSION = '1.0.1';
		// variable
		protected $options;

		
		// Initialize the plugin
		public function __construct() 
		{
			$this->install_error_database();
		}
		
		/**
		 * Delete a file, or a folder and its contents (recursive algorithm)
		 *
		 * @author      Aidan Lister <aidan@php.net>
		 * @version     1.0.3
		 * @since 		0.2
		 * @link        http://aidanlister.com/repos/v/function.rmdirr.php
		 * @param       string   $dirname    Directory to delete
		 * @return      bool     Returns TRUE on success, FALSE on failure
		 */
		function rmdirr($dirname)
		{
		    // Sanity check
		    if (!file_exists($dirname)) {
		        return false;
		    }

		    // Simple delete for a file
		    if (is_file($dirname) || is_link($dirname)) {
		        return unlink($dirname);
		    }

		    // Loop through the folder
		    $dir = dir($dirname);
		    while (false !== $entry = $dir->read()) {
		        // Skip pointers
		        if ($entry == '.' || $entry == '..') {
		            continue;
		        }

		        // Recurse
		        $this->rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
		    }

		    // Clean up
		    $dir->close();
		    return rmdir($dirname);
		}

		/**
		 * Fetches the value of an option. Returns `null` if the option is not set.
		 */
		public function get_option($option, $default=null)
		{
			return $this->options->get_option($option, $default);
		}
		
		/**
		 * Removes an option.
		 */
		public function delete_option($option)
		{
			$this->options->delete_option($option);
		}
		
		/**
		 * Updates the value of an option.
		 */
		public function update_option($option, $value)
		{
			$this->options->update_option($option, $value);
		}
		
		/**
		 * Sets an option if it doesn't exist.
		 */
		public function add_option($option, $value)
		{
			$this->options->add_option($option, $value);
		}
		
		/**
		 * Clears the option cache
		 */
		public function flush_options()
		{
			$this->options->flush_options();
		}
		
		/**
		 * Executes a MySQL query with exception handling.
		 */
		protected function safe_query($sql)
		{
			global $wpdb;
	
			$result = $wpdb->query($sql);
			if ($result === false)
			{
				if ($wpdb->error)
				{
					$reason = $wpdb->error->get_error_message();
				}
				else
				{
					$reason = 'Unknown SQL Error';
				}
				$this->log_error($reason , "safe_query", $sql );
				throw new TTD_Error( $reason);
			}
			return $result;
		}
		
		

		protected function log_error($message, $feed_id=null, $trace=null)
		{
			global $wpdb;
	
			if ($feed_id)
			{
				$result = $wpdb->query($wpdb->prepare("INSERT INTO `".$wpdb->prefix."pluginapi_error_log` (`feed_id`, `message`, `timestamp`) VALUES (%s, %s, %d)", $wpdb->escape($feed_id), $wpdb->escape($message), time()));
			}
			else
			{
				$result = $wpdb->query($wpdb->prepare("INSERT INTO `".$wpdb->prefix."pluginapi_error_log` (`feed_id`, `message`, `timestamp`) VALUES (NULL, %s, %d)", $wpdb->escape($message), time()));
			}
		}
		
		protected function install_error_database(){
			global $wpdb;
			
			$this->safe_query("CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."pluginapi_error_log` (
			  `id` int(11) NOT NULL auto_increment,
			  `message` varchar(255) NOT NULL,
			  `trace` text NULL,
			  `feed_id` int(11) NULL,
			  `timestamp` int(11) NOT NULL,
			  `has_viewed` tinyint(1) default 0 NOT NULL,
			  INDEX `feed_id` (`feed_id`, `has_viewed`),
			  INDEX `has_viewed` (`has_viewed`),
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM;");
		}
	}
	
	class TTD_Error extends Exception { }
?>