<?php


/**
 * ArchitectureAnalyse form.
 * Copied from item types form
 * @package ArchitectureAnalyse\Form
 */
class ArchitectureAnalyse_Form_ArchitectureAnalyse extends Omeka_Form
{
    // form id
    const FORM_ID = 'architecture-analyse-form';

    // form element ids
    const NAME_ELEMENT_ID = 'architectureanalyse_name';
    const DESCRIPTION_ELEMENT_ID = 'architectureanalyse_description';
    const REMOVE_HIDDEN_ELEMENT_ID = 'architectureanalyse_remove';
    const SUBMIT_EDIT_ELEMENT_ID = 'architectureanalyse_edit_submit';
    const SUBMIT_ADD_ELEMENT_ID = 'architectureanalyse_add_submit';
    
    // input names
    const ELEMENTS_INPUT_NAME = 'elements';
    const ELEMENTS_TO_ADD_INPUT_NAME = 'elements-to-add';
    const NEW_ELEMENTS_INPUT_NAME = 'new-elements';
    
    private $_architectureAnalyse;  // the item type ArchitectureAnalyse for the form
    
    /* 
       An info array for each ArchitectureAnalyse element in the ArchitectureAnalyse
       each elementInfo contains the following keys:
       
       'element' => the ArchitectureAnalyse element object
       'temp_id' => the temporary form element id for 
                    ArchitectureAnalyse elements that have not yet been added to the ArchitectureAnalyse
    */
    private $_elementInfos; 
    
    public function init()
    {
        parent::init();
        $this->setAttrib('id', self::FORM_ID);
    }
    
    public function setArchitectureAnalyse($itemType) 
    {
        $this->_architectureAnalyse = $itemType;                
        $this->_initElements();
    } 
    
    public function getElementInfos()
    {
        return $this->_elementInfos;
    }
        
    public function saveFromPost() 
    {
        if ($_POST) {            
            if (!$this->_architectureAnalyse) {
                $this->_architectureAnalyse = new ArchitectureAnalyse;
            }
            
            // get the ArchitectureAnalyse element infos from post
            $this->_elementInfos = $this->_getElementInfosFromPost();
            
            // make sure that there are no duplicates in ArchitectureAnalyse elements            
            $this->_checkForDuplicateElements();
            
            // remove old ArchitectureAnalyse elements from post
            $this->_removeElementsFromArchitectureAnalyse();
                        
            // add elements to the ArchitectureAnalyse
            $this->_addElementsToArchitectureAnalyse();
        
            // set the name and description of the ArchitectureAnalyse
            $this->_architectureAnalyse->name = $this->getValue(self::NAME_ELEMENT_ID);
            $this->_architectureAnalyse->description = $this->getValue(self::DESCRIPTION_ELEMENT_ID);
            
            // save the ArchitectureAnalyse
            if ($this->_architectureAnalyse->save()) {            
                // reorder the ArchitectureAnalyse's elements
                $this->_reorderArchitectureAnalysesElements();
            }
        }
        
        return $this->_architectureAnalyse;
    }
    
    private function _addElementsToArchitectureAnalyse()
    {
        $elements = array();
        foreach($this->_elementInfos as $elementInfo) {
            $elements[] = $elementInfo['element'];
        }

        $this->_architectureAnalyse->addElements($elements);
    }
    
    private function _reorderArchitectureAnalysesElements()
    {
        $elementOrders = array();
        foreach($this->_elementInfos as $elementInfo) {
            $elementOrders[$elementInfo['element']['id']] = $elementInfo['order'];
        }
        $this->_architectureAnalyse->reorderElements($elementOrders);
    }
    
    private function _initElements()
    {           
        // set the ArchitectureAnalyse name and description
        $archiName = '';
        $archiDescription = '';
        if ($this->_architectureAnalyse) {
             $archiName  = $this->_architectureAnalyse->name;
             $archiDescription  = $this->_architectureAnalyse->description;
        }
        
        // set the default element infos
        $this->_elementInfos = array();
        if ($this->_architectureAnalyse) {
            $elementOrder = 1;
            foreach($this->_architectureAnalyse->Elements as $element) {
                $elementInfo = array(
                    'element' => $element,
                    'temp_id' => null,
                    'order' => $elementOrder,
                );
                $this->_elementInfos[] = $elementInfo;
                $elementOrder++;
            }
        }
         
        $this->clearElements();
        
        $this->addElement('text', self::NAME_ELEMENT_ID,
            array(
                'label' => __('Name'),
                'description' => __('The name of the architecture analyse.'),
                'required' => true,
                'value' => $archiName,
            )
        );
        
        $this->addElement('textarea', self::DESCRIPTION_ELEMENT_ID,
            array(
                'label' => __('Description'),
                'description' => __('The description of the architecture analyse.'),
                'value' => $archiDescription,
                'cols' => 50,
                'rows' => 5,
            )
        );
        
        $this->addElement('hidden', self::REMOVE_HIDDEN_ELEMENT_ID, array('value' => ''));
        
        $this->addElement('submit', self::SUBMIT_ADD_ELEMENT_ID, array(
            'label' => __('Add Architecture Analyse'),
            'class' => 'big green button',
            'decorators' =>  array(
                        'ViewHelper',
                        'Errors',)
        ));
                
        $this->addElement('submit', self::SUBMIT_EDIT_ELEMENT_ID, array(
            'label' => __('Save Changes'),
            'class' => 'big green button',
            'decorators' =>  array(
                        'ViewHelper',
                        'Errors',)
        ));
    }
    
    private function _checkForDuplicateElements()
    {
        // Check for duplicate elements and throw an exception if a duplicate is found
        $elementIds = array();
        $elementNames = array();
        foreach($this->_elementInfos as $elementInfo) {
            $element = $elementInfo['element'];
            
            // prevent duplicate ArchitectureAnalyse element ids
            if ($element->id) {
                if (in_array($element->id, $elementIds)) {
                    throw new Omeka_Validate_Exception(__('The architecture analyse cannot have more than one "%s" element.', $element->name));
                } else {
                    $elementIds[] = $element->id;
                }
            }

            // prevent duplicate ArchitectureAnalyse element names
            if ($element->name) {
                if (in_array($element->name, $elementNames)) {
                    throw new Omeka_Validate_Exception(__('The architecture analyse cannot have more than one "%s" element.', $element->name));
                } else {
                    $elementNames[] = trim($element->name);
                }
            }
        }
    }
    
    private function _removeElementsFromArchitectureAnalyse()
    {        
        $elementTable = get_db()->getTable('Element');
        // get the ArchitectureAnalyse element ids to remove from the post and remove those elements from the ArchitectureAnalyse
        $elementIds = explode(',', $this->getValue(self::REMOVE_HIDDEN_ELEMENT_ID));         

        foreach($elementIds as $elementId) {
            $elementId = intval(trim($elementId));
            if ($elementId) {
                if ($element = $elementTable->find($elementId)) {
                    $this->_architectureAnalyse->removeElement($element);        
                }
            }
        }  
    }
    
    // get the elements to save from the post
    private function _getElementInfosFromPost()
    {
        $elementTable = get_db()->getTable('Element');
        $elementInfos = array();

        if (isset($_POST[self::ELEMENTS_INPUT_NAME])) {
            $currentElements = $_POST[self::ELEMENTS_INPUT_NAME];
            foreach ($currentElements as $elementId => $info) {
                $elementInfos[] = array(
                    'element' => $elementTable->find($elementId),
                    'temp_id' => null,
                    'order' => $info['order']
                );
            }
        }

        if (isset($_POST[self::ELEMENTS_TO_ADD_INPUT_NAME])) {
            $elementsToAdd = $_POST[self::ELEMENTS_TO_ADD_INPUT_NAME];
            foreach ($elementsToAdd as $tempId => $info) {
                if (empty($info['id'])) {
                    continue;
                }

                $elementInfos[] = array(
                    'element' => $elementTable->find($info['id']),
                    'temp_id' => $tempId,
                    'order' => $info['order']
                );
            }
        }

        if (isset($_POST[self::NEW_ELEMENTS_INPUT_NAME])) {
            $newElements = $_POST[self::NEW_ELEMENTS_INPUT_NAME];
            foreach ($newElements as $tempId => $info) {
                if (empty($info['name'])) {
                    continue;
                }

                $element = new Element;
                $element->setElementSet(ArchitectureAnalyse::ARCHITECTURE_ANALYSE_NAME);
                $element->setName($info['name']);
                $element->setDescription($info['description']);
                $element->order = null;
                                
                $elementInfos[] = array(
                    'element' => $element,
                    'temp_id' => $tempId,
                    'order' => $info['order']
                );
            }
        }

        return $elementInfos;
    }
}
