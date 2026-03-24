<?php
/**
 * Plugin upgrade code.
 *
 * @package    local_up1_metadata
 * @copyright  2012-2026 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/../obsolete/datalib.php');
require_once(__DIR__ . '/../obsolete/insertlib.php');
require_once(__DIR__ . '/../../roftools/roflib.php');
require_once(__DIR__ . '/../obsolete/libupgrade.php');

use \local_up1_metadata\customfields;

function xmldb_local_up1_metadata_upgrade($oldversion)
{
    global $CFG, $DB;


    if ($oldversion < 2013070300) { // on peut faire cette mise à jour inconditionnellement
        $metadata = up1_course_metadata();

        echo "Création des catégories :<br />\n";
        insert_metadata_categories($metadata, 'course');

        echo "<br />\n<br />\n";
        echo "Création des champs :<br />\n";
        insert_metadata_fields($metadata, 'course');
    }

    if ($oldversion < 2013070301) { // initialisation de categoriesbisrof
        echo "Initialisation de categoriesbisrof :<br />\n";
        update_categoriesbisrof();
    }

    if ($oldversion < 2016051101) {
        echo "Initialisation de up1urlfixe.<br />\n";
        add_urlfixe();
    }

    if ($oldversion < 2026031300) {
        $customfields = new customfields(1);
        $customfields->update_customfields();
        echo "OK.\n";
    }

    return true;
}
