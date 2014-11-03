<?php 

/**
 * Build an item type.
 * 
 * @package ArchitectureAnalyse\models\Builder
 */
class Builder_ArchitectureAnalyse extends Omeka_Record_Builder_AbstractBuilder
{
    protected $_recordClass = 'ArchitectureAnalyse';
    
    protected $_settableProperties = array('name', 'description');
    
    private $_elements = array();
        
    /**
     * Set the elements that will be attached to the built ArchitectureAnalyse record.
     * 
     * @param array $elementMetadata
     * @return void
     */
    public function setElements(array $elementMetadata)
    {
        $this->_elements = $elementMetadata;
    }
    
    /**
     * Add elements to be associated with the ArchitectureAnalyse.
     */
    protected function _beforeBuild()
    {        
        $this->_record->addElements($this->_elements);
    }
}
