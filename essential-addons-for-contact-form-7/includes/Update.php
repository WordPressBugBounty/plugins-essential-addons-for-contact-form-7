<?php

namespace EACF7;

defined('ABSPATH') || exit;

class Update {

    /**
     * The upgrades
     *
     * @var array
     */
    private static $upgrades = array('1.0.1');

    public function installed_version() {
        return get_option('eacf7_version');
    }

    /**
     * Check if the plugin needs any update
     *
     * @return boolean
     */
    public function needs_update() {

        // may be it's the first install
        if (empty($this->installed_version())) {
            return false;
        }

        //if previous version is lower
        if (version_compare($this->installed_version(), EACF7_VERSION, '<')) {
            return true;
        }

        return false;
    }

    /**
     * Perform all the necessary upgrade routines
     *
     * @return void
     */
    public function perform_updates() {

        foreach (self::$upgrades as $version) {
            if (version_compare($this->installed_version(), $version, '<')) {
                $file = EACF7_INCLUDES . "/updates/Update-$version.php";

                if (file_exists($file)) {
                    include_once $file;
                }

                update_option('eacf7_version', $version);
            }
        }
        
        update_option('eacf7_db_version', EACF7_DB_VERSION);
    }
}
