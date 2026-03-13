<?php
/**
 * @package    local_up1_metadata
 * @copyright  2012-2021 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


function up1_course_metadata()
{
    // nota : 'init' is NOT a custom meta-field ; it is the value which will be initialized (if not null)
    // for all object records (all courses or all users)

    $res = [
        'Identification' => [
            'complement' => ['name' => 'Complément intitulé', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'nomnorme' => ['name' => 'Nom normé', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'abregenorme' => ['name' => 'Nom abrégé normé', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'rofpath' => ['name' => 'Chemin ROF', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'rofpathid' => ['name' => 'Chemin ROFid', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'code' => ['name' => 'Code Apogée', 'datatype' => 'text', 'locked' => 1,  'init' => null],
            'rofid' => ['name' => 'RofId', 'datatype' => 'text', 'locked' => 1,  'init' => null],
            'rofname' => ['name' => 'Nom ROF', 'datatype' => 'text', 'locked' => 0,  'init' => null],
        ],
        'Indexation' => [
            'periode' => ['name' => 'Période', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'composante' => ['name' => 'Composante', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'semestre' => ['name' => 'Semestre', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'niveau' => ['name' => 'Niveau', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'niveaulmda' => ['name' => 'Niveau LMDA', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'niveauannee' => ['name' => 'Niveau année', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'composition' => ['name' => 'Composition', 'datatype' => 'text', 'locked' => 0],
            'categoriesbis' => ['name' => 'Catégories de cours supplémentaires hors ROF', 'datatype' => 'text', 'locked' => 0,  'init' => ''],
            'categoriesbisrof' => ['name' => 'Catégories de cours supplémentaires rattachements ROF', 'datatype' => 'text', 'locked' => 0,  'init' => ''],
        ],
        'Diplome' => [
            'diplome' => ['name' => 'Diplôme', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'domaine' => ['name' => 'Domaine ROF', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'type' => ['name' => 'Type ROF', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'nature' => ['name' => 'Nature ROF', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'cycle' => ['name' => 'Cycle ROF', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'rythme' => ['name' => 'Rythme ROF', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'langue' => ['name' => 'Langue', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'acronyme' => ['name' => 'Acronyme', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'mention' => ['name' => 'Mention', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'specialite' => ['name' => 'Spécialité', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'parcours' => ['name' => 'Parcours', 'datatype' => 'text', 'locked' => 0],
        ],
        'Cycle de vie - création' => [
            'avalider' => ['name' => 'Attente de validation', 'datatype' => 'checkbox', 'locked' => 0,  'init' => null],
            'responsable' => ['name' => 'Responsable enseignement (ROF)', 'datatype' => 'text', 'locked' => 0,  'init' => null], // d'après le ROF
            'demandeurid' => ['name' => 'Demandeur Id', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'datedemande' => ['name' => 'Date demande', 'datatype' => 'datetime', 'locked' => 0,  'init' => null],
            'approbateurpropid' => ['name' => 'Approbateur proposé Id', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'approbateureffid' => ['name' => 'Approbateur effectif Id', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'datevalid' => ['name' => 'Date validation', 'datatype' => 'datetime', 'locked' => 0,  'init' => null],
            'commentairecreation' => ['name' => 'Commentaire creation', 'datatype' => 'text', 'locked' => 0,  'init' => null],
        ],
        'Cycle de vie - gestion' => [
            'dateprevarchivage' => ['name' => 'Date prévis. archivage', 'datatype' => 'datetime', 'locked' => 0,  'init' => null],
            'datearchivage' => ['name' => 'Date archivage', 'datatype' => 'datetime', 'locked' => 0,  'init' => null],
        ],
        'Cycle de vie - Informations techniques' => [
            'generateur' => ['name' => 'Générateur', 'datatype' => 'text', 'locked' => 0,  'init' => null],
            'modele' => ['name' => 'Modèle', 'datatype' => 'text', 'locked' => 0, 'init' => null],
            'urlfixe' => ['name' => 'Url fixe', 'datatype' => 'text', 'locked' => 0, 'init' => null],
        ],
    ];
    return $res;
}
