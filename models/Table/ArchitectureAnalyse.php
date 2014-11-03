<?php 

/**
 * @package ArchitectureAnalyse\models\Table
 */
class Table_ArchitectureAnalyse extends Omeka_Db_Table
{
    protected function _getColumnPairs()
    {
        return array($this->_name . '.id', $this->_name . '.name');
    }
    
    public function findByName($itemTypeName) 
    {
        $select = $this->getSelect();
        $select->where($this->_name . '.name = ?', $itemTypeName);
        return $this->fetchObject($select);
    }
    /**
     * Retrieve a set of Element records that belong to a specific Architecture Analyse.
     * 
     * @see Item::getItemTypeElements()
     * @param integer
     * @return array Set of element records.
     */
    public function findByArchitectureAnalyse($id)
    {        
        
        //$select = $this->getSelect();
        $db = $this->getDb();
        $select = new Omeka_Db_Select($db->getAdapter());
        $select->from(array('e'=>$db->Elements), "e.*");
        $select->joinInner(array('architecture_analyses_elements'=>$db->ArchitectureAnalysesElements), 'architecture_analyses_elements.element_id = e.id', array());
        $select->where('architecture_analyses_elements.architecture_analyse_id = ?');
        $select->order('architecture_analyses_elements.order ASC');
       
        $elements = $this->fetchObjects($select, array($id)); 

       return $elements;
    }
    
}
