<?php
/**
 * @package    local_up1_metadata
 * @copyright  2012-2021 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * return a metadata up1 as text
 * @global type $DB
 * @param int $courseid
 * @param string $field UP1 metadata text, ex. complement
 * @param bool $error : if set, throw an exception if $field isn't found ; otherwise return an empty string
 */
function up1_meta_get_text($courseid, $field, $error=false)
{
    global $DB;

    $prefix = 'up1';
    if (substr($field, 0, 3) !== 'up1') {
        $field = $prefix . $field;
    }
    $sql = "SELECT cd.value FROM {customfield_field} cf "
         . "JOIN {customfield_data} cd ON (cf.id = cd.fieldid) "
         . "WHERE cf.shortname=? AND cd.instanceid=?";
    $res = $DB->get_field_sql($sql, [$field, $courseid]);
    if ($error && !$res) {
        throw new coding_exception('Erreur ! champ "' . $field . '" absent');
        return '';
    }
    if (!$res) {
        return '';
    }
    return $res;
}

/**
 * return an html string <span title="...">...</span> for easy display of multiple metadata values
 * displays the main value, while the title tooltip displays the whole list on mouseover
 * @param int $courseid
 * @param string $field UP1 metadata text, ex. composante
 * @param bool $error : if set, throw an exception if $field isn't found ; otherwise return an empty string
 * @param bool $prefix if set, prefixes each item of the list with the given string
 * @return (html) string
 */
function up1_meta_html_multi($courseid, $field, $error=false, $prefix = '')
{
    $text = up1_meta_get_text($courseid, $field, $error);
    $items = array_filter(array_unique(explode(';', $text)));

    if (count($items) == 0) {
        return '—';
    }
    $first = reset($items);
    if (count($items) == 1) {
        return '<span>' . $prefix . $first . '</span>';
    }
    $brief = $prefix . $first . ' +';
    $long = $prefix . join(', ' . $prefix, $items);
    return '<span title="' . $long . '">' . $brief . '</span>';
}

/**
 * return a multiple metadata up1 as a formatted list ; ex. "UFR02-... / UFR04-..."
 * identic values are merged
 * @param int $courseid
 * @param string $field UP1 metadata text, ex. composante
 * @param bool $error : if set, throw an exception if $field isn't found ; otherwise return an empty string
 * @param string $separator
 * @param bool $prefix if set, prefixes the list by the field name, ex. "Niveau : L1 / L2"
 */
function up1_meta_get_list($courseid, $field, $error=false, $separator=' / ', $prefix = false)
{
    global $DB;

    $text = up1_meta_get_text($courseid, $field, $error);
    $items = array_unique(explode(';', $text));
    $res = join($separator, $items);
    if ($res) {
        if ($prefix) {
            $fieldname = $DB->get_field('customfield_field', 'name', ['shortname' => $field], MUST_EXIST);
            $res = $fieldname . ' : ' . $res;
        }
        return $res;
    }
}


/**
 * return a metadata up1 as date
 * @global type $DB
 * @param int $courseid
 * @param type $field UP1 metadata date, ex. datedemande
 */
function up1_meta_get_date($courseid, $field)
{
    $dtime = up1_meta_get_text($courseid, $field);
    if ($dtime == 0) {
        return ['date' => false, 'datetime' => false, 'datefr' => false];
    }
    return  [
        'date' => userdate($dtime, '%Y-%m-%d'),
        'datetime' => userdate($dtime, '%Y-%m-%d %H:%M:%S'),
        'datefr' => userdate($dtime, '%d/%m/%Y'),
    ];
}

/**
 * return a metadata up1 as (id, name) assoc. array
 * @global type $DB
 * @param int $courseid
 * @param string $field UP1 metadata userid, among (demandeurid, approbateurpropid, approbateureffid)
 * @param bool $username : if set, append the username after the fullname
 * @return array('id' => ..., 'name' => ...)
 */
function up1_meta_get_user($courseid, $field, $username=true)
{
    global $DB;

    $userid = up1_meta_get_text($courseid, $field);
    if ($userid) {
        $dbuser = $DB->get_record('user', ['id' => $userid]);
        if ($dbuser) {
            $fullname = $dbuser->firstname .' '. $dbuser->lastname . ($username ? ' ('.$dbuser->username. ')' : '');
            return ['id' => $userid, 'name' => $fullname];
        } else {
            return ['id' => $userid, 'name' => '(id=' . $userid . ')'];
        }
    } else {
        return ['id' => false, 'name' => ''];
    }
}

/**
 * get the id in table customfield_data for a given (course id, field shortname)
 * @global type $DB
 * @param in $courseid
 * @param string $field (shortname)
 * @return type
 */
function up1_meta_get_id($courseid, $field)
{
    global $DB;

    $prefix = 'up1';
    if (substr($field, 0, 3) !== 'up1') {
        $field = $prefix . $field;
    }
    $sql = "SELECT cd.id FROM {customfield_data} cd "
         . " JOIN {customfield_field} cf ON (cd.fieldid = cf.id) "
         . " WHERE cf.shortname=? AND cd.instanceid=?";
    $id = $DB->get_field_sql($sql, [$field, $courseid], IGNORE_MISSING);

    //echo $sql ."\n -> $id\n";
    return $id;
}

/**
 *
 * @param array(string) $fields ex. array('up1complement', 'up1diplome', 'up1cycle')
 */
function up1_meta_gen_sql_query($fields)
{
    global $DB;

    $sql = "SELECT shortname, id FROM {customfield_field} WHERE shortname IN ('"
        . implode("' ,'", $fields) . "')" ;
    $fieldids = $DB->get_records_sql_menu($sql);

    $select = "SELECT c.id " ;
    $from = "FROM {course} c ";
    foreach ($fields as $field) {
        $fid = $fieldids[$field];
        $table = "cid" . $fid;
        $select = $select . ", ${table}.value AS $field ";
        $from = $from . "\n  JOIN {customfield_data} AS ${table} "
                    . " ON ( ${table}.fieldid = $fid AND ${table}.instanceid = c.id )" ;
    }
    $sql = $select . $from;
    return $sql;
}

/**
 * update or initializes a course metadata for a given course and fieldname
 * @param int $courseid
 * @param string $fieldname ex. 'rofid', 'datearchiv' ...
 * @param string $data field value
 * @return bool (on update) or int (inserted id, on insert)
 */
function up1_meta_set_data($courseid, $fieldname, $data)
{
    global $DB;
    
    $fieldrecord = $DB->get_record('customfield_field', ['shortname' => $fieldname], '*', MUST_EXIST);
    $fieldc = \core_customfield\field_controller::create($fieldrecord->id);
    
    $fielddata = $DB->get_record('customfield_data', ['fieldid' => $fieldrecord->id, 'instanceid' => $courseid]);
    $datafieldid = $fielddata ? $fielddata->id : 0;
    
    $datac = \core_customfield\data_controller::create($datafieldid, null, $fieldc);
    if (!$datac->get('id')) {
        $datac->set('contextid', context_course::instance($courseid)->id);
        $datac>set('instanceid', $courseid);
    }

    if ($fieldc->get('type') == 'date' && $data == '') {
        $data = $fieldc->get_configdata_property('defaultvalue');
    }

    $datac->set($datac->datafield(), $data);
    $datac->set('value', $data);
    $datac->save();

    if ($fielddata) {
        return true;
    } else {
        return $DB->get_field('customfield_data', 'id', ['fieldid' => $fieldrecord->id, 'instanceid' => $courseid]);
    }
}

/**
 * search all objects  matching a specific customfield data of type text (charvalue)
 * @param string $shortname, ex. 'up1urlfixe' or 'up1semestre'
 * @param string $needle the searched value
 * @return array(integer $id)
 */
function up1_meta_get_instanceid_by_field_text($shortname, $needle)
{
    global $DB;

    $sql = "SELECT cd.instanceid "
        . "FROM {customfield_data} cd "
        . "JOIN {customfield_field} cf ON (cd.fieldid = cf.id) "
        . "WHERE cd.value = :charvalue and cf.shortname = :sname ";
    return $DB->get_fieldset_sql($sql, ['sname' => $shortname, 'charvalue' => $needle]);
}
