<?php
/**
 * Plugin Name: Wordpress Twitter Timeline API
 * Plugin URI: https://github.com/ElMijo/wp-twitter-timeline-api
 * Description: Un pequeño plugin de wordpress que permite obtener el user_timeline de la cuenta de un usuario Twitter, utilizando el REST APIs de Twitter.
 * Author: jerry Anselmi <janselmi@mppi.gob.ve>
 * License: MIT
 * Version: 1.0
 * Text Domain: wttapi
 * Domain Path: /languages
 */

define('WTTAPI_DOMAIN', 'wttapi');
define('WTTAPI_DIR', dirname(__FILE__));
define('WTTAPI_URL', plugin_dir_url(__FILE__));


// include_once WTTAPI_DIR.'/src/WTwitterTimelineAPI/WTTAPISettings.php';
// include_once WTTAPI_DIR.'/src/WTTAPI/WTTAPI.php';
include_once WTTAPI_DIR.'/src/WTwitterTimelineAPI/WTwitterTimelineAPISettingsFactory.php';
include_once WTTAPI_DIR.'/src/WTwitterTimelineAPI/WTwitterTimelineAPISettingsInterface.php';
include_once WTTAPI_DIR.'/src/WTwitterTimelineAPI/WTwitterTimelineAPI.php';
include_once WTTAPI_DIR.'/src/WTwitterTimelineAPI/WTwitterTimelineAPIWidget.php';
?>