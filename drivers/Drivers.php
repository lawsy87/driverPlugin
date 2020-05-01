<?php /*

**************************************************************************

Plugin Name:  Drivers
Description:  Drivers plugin for Douglas Digital
Version:      1.0.4
Author:       Scott Mokler
Author URI:   http://ibuildthings.xyz

**************************************************************************/

// Current version number
if (!defined('DRIVERS_VERSION')) {
    define('DRIVERS_VERSION', '1.0.4');
}

/**
 * @description Activation will force this to be set to 1.0.0 for testing purposes
 */
function drivers_activation()
{
    add_option('DRIVERS_VERSION', '1.0.0');
}
register_activation_hook(__FILE__, 'drivers_activation');


/**
 * @param null $version
 * @description Update wp_options to current version
 */
function drivers_update_db_version($version = null)
{
    // Installed version number
    if (!is_null($version)) {
        update_option('DRIVERS_VERSION', $version);
    } else {
        update_option('DRIVERS_VERSION', DRIVERS_VERSION);
    }
}

/**
 * @description Deactivate Plugin
 */
function drivers_deactivation()
{
    global $wpdb;
    // Delete option entry
    delete_option('DRIVERS_VERSION');
    // Drop tables used by plugin
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->base_prefix}drivers");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->base_prefix}cars");
}
register_deactivation_hook(__FILE__, 'drivers_deactivation');

/**
 * Check if update is needed
 */
function drivers_update_db_check()
{
    if (get_site_option('DRIVERS_VERSION') != DRIVERS_VERSION) {
        drivers_update();
    }
}

add_action('plugins_loaded', 'drivers_update_db_check');

/**
 * @description Update Plugin
 */
function drivers_update()
{
    global $wpdb;
    $db_version = drivers_get_db_version();

    // 1.0.1
    if ($db_version <= '1.0.1') {
        drivers_upgrade_101($wpdb);
        drivers_update_db_version('1.0.1');
    }
    // 1.0.2
    if ($db_version <= '1.0.2') {
        drivers_upgrade_102($wpdb);
        drivers_update_db_version('1.0.2');
        die();
    }
    // 1.0.3
    if ($db_version <= '1.0.3') {
        drivers_upgrade_103($wpdb);
        drivers_update_db_version('1.0.3');
    }
    // 1.0.4
    if ($db_version <= '1.0.4') {
        drivers_upgrade_104($wpdb);
        drivers_update_db_version('1.0.4');
    }
}

// Get DB version
function drivers_get_db_version()
{
    return get_version('DRIVERS_VERSION');
}

/**
 * @param $wpdb
 * @description Update plugin to 1.0.1
 */
function drivers_upgrade_101($wpdb)
{
    $sql = "CREATE TABLE `{$wpdb->base_prefix}cars` (
  `car_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`car_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * @param $wpdb
 * @description Update plugin to 1.0.2
 */
function drivers_upgrade_102($wpdb)
{
    $sql = "CREATE TABLE `{$wpdb->base_prefix}drivers` (
  `driver_id` int(11) NOT NULL AUTO_INCREMENT,
  `car_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`driver_id`),
  KEY `car_id` (`car_id`),
  CONSTRAINT `drivers_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `{$wpdb->base_prefix}cars` (`car_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * @param $wpdb
 * @description Update plugin to 1.0.3
 */
function drivers_upgrade_103($wpdb)
{
    $sql = "INSERT INTO `{$wpdb->base_prefix}cars` (`car_id`, `title`)
VALUES
	(1, 'Mercedes A Class'),
	(2, 'Ford GT 500'),
	(3, 'Mclaren P1');
";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * @param $wpdb
 * @description Update plugin to 1.0.4
 */
function drivers_upgrade_104($wpdb)
{
    $sql = "INSERT INTO `{$wpdb->base_prefix}drivers` (`driver_id`, `car_id`, `name`)
VALUES
	(1, 1, 'The Stig'),
	(2, 2, 'Jeremy Clarkson'),
	(3, 3, 'Richard Hammond');";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
