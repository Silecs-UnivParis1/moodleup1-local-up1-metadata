<?php                                                                                                                                                                                                                                                   
/**
 * 
 * @package    local_up1_metadata
 * @copyright  2021-2026 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \local_up1_metadata\customfields;

define('CLI_SCRIPT', true);
require(dirname(__FILE__, 4) . '/config.php'); // global moodle config file.
require_once($CFG->libdir . '/clilib.php');      // cli only functions
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('error_reporting', E_ALL);

// now get cli options
list($options, $unrecognized) = cli_get_params(
        ['help' => false, 'verbose' => 1,
         'run' => false, 'display-code' => false, 'display-base' => false,
        ],
    );

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help = <<<EOHELP
Création des champs personnalisés de cours standard.

Options:
--help           Affiche cette aide
--verbose=N      Verbosité (0 à 3), 1 par défaut, inopérant.

--run            Lance la création
--display-code   Affiche la liste des champs définis dans le code
--display-base   Affiche la liste des champs enregistrés dans la base de données

EOHELP;

if ( ! empty($options['help']) ) { 
    echo $help;
    return 0;
}

// Ensure errors are well explained
$CFG->debug = DEBUG_NORMAL;

if ( ! empty($options['run']) ) { 
    $customfields = new customfields($options['verbose']);
    $customfields->update_customfields();
    echo "OK.\n";
    return 0;
}

if ( ! empty($options['display-code']) ) {
    $customfields = new customfields($options['verbose']);
    echo $customfields->get_code_fields();
    echo "OK.\n";
    return 0;
}

if ( ! empty($options['display-base']) ) {
    $customfields = new customfields($options['verbose']);
    echo $customfields->get_database_fields();
    echo "OK.\n";
    return 0;
}