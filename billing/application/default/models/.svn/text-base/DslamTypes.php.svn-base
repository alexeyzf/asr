<?php
/**
 * Model of dslam_types_view view
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class DslamTypes extends Zend_Db_Table
{
    protected $_name = 'dslam_types_view';
    protected $_primary = 'id';

    /**
     * Gets id => name associative array of dlam types
     */
    public function getOptions()
    {
        $dslamTypes = $this->fetchAll();
        $options = array();

        foreach ($dslamTypes as $dslamType)
        {
             $options[$dslamType->id] = $dslamType->name;
        }

        return $options;
    }
}