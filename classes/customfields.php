<?php
/**
 * Installe les champs personnalisés de cours, au format interne de Moodle 
 * concerne : tables customfield_category, customfield_field 
 * ne modifie pas : customfield_data
 *
 * L'API officielle Customfields est légère et n'est pas prévue pour la création automatique de champs ;
 * elle permet surtout la création de nouveaux types de champs et de formulaires, ce qui n'est pas notre besoin
 * https://moodledev.io/docs/4.4/apis/core/customfields
 * 
 * @package    local_up1_metadata
 * @copyright  2021-2026 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_up1_metadata;

class customfields
{
    public $verbose;

    public $diagmessage;
    
    const TEMPLATE_CATEGORY = [
        'id' => null,
        'name' => '', // à remplir
        'description' => '', // à remplir
        'descriptionformat' => 0,
        'sortorder' => null, // à remplir
        'timecreated' => null, // à remplir
        'timemodified' => null, // à remplir
        'component' => 'core_course',
        'area' => 'course',
        'itemid' => 0,
        'contextid' => 1
    ];
        
    const TEMPLATE_FIELD = [
        'id' => null,
        'shortname' => '',
        'name' => '',
        'type' => '', // text | textarea | checkbox | ( date | int...)
        'description' => '<p></p>',
        'descriptionformat' => 1,
        'sortorder' => null,
        'categoryid' => null,
        'configdata' => '',
        'timecreated' => null,
        'timemodified' => null,        
    ];
    
    // customfield_field.configdata dépend du type déclaré dans customfield_field.type
    // modèle simplifié, on garde la version sérialisée
    const TEMPLATE_CONFIGDATA = [
        'checkbox' => '{"required":"0","uniquevalues":"0","checkbydefault":"0","locked":"0","visibility":"2"}',
        'text' => '{"required":"0","uniquevalues":"0","defaultvalue":"","displaysize":50,"maxlength":1333,"ispassword":"0","link":"","locked":"0","visibility":"2"}',
        'textarea' => '{"required":"0","uniquevalues":"0","locked":"0","visibility":"2","defaultvalue":"","defaultvalueformat":"1"}'        
    ];

    // Référence https://tickets.silecs.info/view.php?id=5883
    private $CustomFields = [
        'Syllabus' => [ // category
            'syl_obligatoire' => [
                'name' => 'Obligatoire',
                'type' => 'checkbox',
                'description' => "<p>A récupérer du SI ; sinon à remplir par l'enseignant.e à l'étape de rattachement</p>",
            ],
            'syl_reference' => [
                'name' => 'Syllabus de référence',
                'type' => 'checkbox',
                'description' => "<p>Cet EPI présente le syllabus de référence pour cette matière devant figurer dans Ametys ou sur le site web de l'université.</p>",
            ],
             'syl_ects' => [
                'name' => "Nombre d'ECTS",
                'type' => 'text',
                'description' => "<p>A récupérer du SI.</p>",
            ],
            'syl_volume' => [
                'name' => 'Volume horaire',
                'type' => 'text',
                'description' => "<p>Volume horaire (CM, TD) : à récupérer du SI.</p>",
            ],
            'syl_elpcode' => [
                'name' => 'Code APOGEE',
                'type' => 'text',
                'description' => "<p>(ELP matière) : à récupérer avec le rattachement</p>",
            ],
            'syl_elpintitule' => [
                'name' => 'Intitulé matière',
                'type' => 'text',
                'description' => "<p>A récupérer avec le rattachement</p>",
            ],
            'syl_langue' => [
                'name' => "Langue d'enseignement",
                'type' => 'text',
                'description' => "<p>A remplir par l'enseignant.e à l'étape de rattachement</p>",
            ],
            'syl_objectifs' => [
                'name' => 'Objectifs pédagogiques',
                'type' => 'textarea',
                'description' => "<p>A remplir par l'enseignant.e à la nouvelle étape Syllabus </p>",
            ],
            'syl_plan' => [
                'name' => 'Plan du cours',
                'type' => 'textarea',
                'description' => "<p>A remplir par l'enseignant.e à la nouvelle étape Syllabus</p>",
            ],
            'syl_prerequis' => [
                'name' => 'Prérequis',
                'type' => 'textarea',
                'description' => "<p>A remplir par l'enseignant.e à la nouvelle étape Syllabus</p>",
            ],
            'syl_evaluation' => [
                'name' => "Modalités d'évaluation",
                'type' => 'textarea',
                'description' => "A remplir par l'enseignant.e à la nouvelle étape Syllabus</p>",
            ],
            'syl_bibliographie' => [
                'name' => 'Bibliographie',
                'type' => 'textarea',
                'description' => "<p>A remplir par l'enseignant.e à la nouvelle étape Syllabus</p>",
            ],
            'syl_responsables' => [
                'name' => 'Responsable(s)',
                'type' => 'textarea',
                'description' => "<p>Responsable(s) du ou des diplômes concernés : à remplir via un champ de recherche à la nouvelle étape Syllabus (1 nom + si possible email)</p>",
            ],
            'syl_contacts' => [
                'name' => 'Contacts',
                'type' => 'textarea',
                'description' => "<p>Contacts (enseignant.es responsables) : A récupérer du statut Responsable EPI (1 nom)</p>",
            ],           
        ], // category
    ];

    
    function __construct(int $verbose)
    {   
        $this->verbose = $verbose;
    }
    
    public function update_customfields()
    {
        foreach ($this->CustomFields as $categoryname => $fields) {
            echo "$categoryname... \n";
            $catid = $this->insert_or_find_category($categoryname);
            echo $this->diagmessage;
        }
    }

    /**
     * 
     * @global \moodle_database $DB
     */
    private function insert_or_find_category(string $categoryname): int
    {
        global $DB;
        $table = 'customfield_category';
        $catid = $DB->get_field($table, 'id', ['name' => $categoryname], IGNORE_MISSING);
        if ($catid) {
            $this->diagmessage = "La catégorie $categoryname existe déjà.\n";
            return $catid;
        }
        $record = (object)self::TEMPLATE_CATEGORY;
        $record->name = $categoryname;
        $record->sortorder = 1 + $DB->get_field_sql('SELECT MAX(sortorder) FROM {' . $table . '}');
        $inserttime = time();
        $record->timecreated = $inserttime;
        $record->timemodified = $inserttime;
        $this->diagmessage = "La catégorie $categoryname a été créée.\n";
        return $DB->insert_record($table, $record, true);
    }
    
    private function insert_or_find_field(array $field): int
    {
        
    }
}