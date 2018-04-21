<?php
/**
 * Helper class for Table Data module
 * 
 * @package    Table Data
 * @subpackage Modules
 * @link http://docs.joomla.org/J3.x:Creating_a_simple_module/Developing_a_Basic_Module
 * @license        GNU/GPL, see LICENSE.php
 * mod_helloworld is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
class ModTableDataHelper
{
    /**
     * Retrieves one row of record column data and outputs a table with the column data filled into the user template
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */    
    public static function getTableData($params)
    {
        // Obtain a database connection
        $db = JFactory::getDbo();

        // Retrieve the various parameters
        $fieldnames = explode(",",preg_replace("/\s+/",",",$params->get('fieldnames')));
        $tablequery = $params->get('tablequery');
        $errormessage = $params->get('errormessage');
        $tablehtml = $params->get('tablehtml');

        // Trim any trailing semicolon or LIMIT clause from it. We can only handle one returned record here.
        $tablequery = rtrim($tablequery,";");
        $limitpos = strpos($tablequery,'LIMIT');
        if ($limitpos !== FALSE) $tablequery = substr($tablequery,0,$limitpos);

        // Now force the returned record set to be a single record.
        $tablequery .= " LIMIT 1;";

        // Prepare the query
        $db->setQuery($tablequery);

        // Load the row.
        try
        {
            $record = $db->loadObject();
        }
        catch (Exception $e)
        {
            $tablehtml = $errormessage;
        }
        if ($record)
        {
            // Go through the list of field names and replace each {fieldname} in the user-provided HTML.
            foreach ($fieldnames as $fieldname)
            {
                $tablehtml = html_entity_decode(str_replace("{".$fieldname."}",$record->$fieldname,$tablehtml),ENT_QUOTES);
            }
        }
        // Return the table with all fieldname substitutions made
        return $tablehtml;
    }
}
?>