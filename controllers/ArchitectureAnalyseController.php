<?php
/**
 * @copyright PXG
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package ArchitectureAnalyse/Controller
 */

class ArchitectureAnalyse_ArchitectureAnalyseController extends Omeka_Controller_AbstractActionController
{
    const ELEMENTS_TO_REMOVE = 'elements-to-remove';

    const CURRENT_ELEMENT_ORDER_PREFIX = 'element-order-';

    const ADD_NEW_ELEMENT_NAME_PREFIX = 'add-new-element-name-';
    const ADD_NEW_ELEMENT_DATA_TYPE_ID_PREFIX = 'add-new-element-data-type-id-';
    const ADD_NEW_ELEMENT_DESCRIPTION_PREFIX = 'add-new-element-description-';
    const ADD_NEW_ELEMENT_ORDER_PREFIX = 'add-new-element-order-';

    const ADD_EXISTING_ELEMENT_ID_PREFIX = 'add-existing-element-id-';
    const ADD_EXISTING_ELEMENT_ORDER_PREFIX = 'add-existing-element-order-';

    public function init()
    {
        $this->_helper->db->setDefaultModelName('ArchitectureAnalyse');
        //$this->_modelClass = 'ArchitectureAnalyse';
        
    }

    public function addAction()
    {
        $arch = new ArchitectureAnalyse();
        $form = $this->_getForm($arch);
        
        if (isset($_POST[ArchitectureAnalyse_Form_ArchitectureAnalyse::SUBMIT_ADD_ELEMENT_ID])) {
            if ($form->isValid($_POST)) {
                try{
                    $arch = $form->saveFromPost();                    
                    $this->_helper->flashMessenger(__('The architecture analyse "%s" was successfully added.', $arch->name), 'success');
                    $this->_helper->redirector->gotoRoute(array('action' => 'show', 'id' => $arch->id), 'architectureAnalyseShow');
                    //redirector('show', 'ArchitectureAnalyse', 'architecture-analyse', array('id'=>$arch->id));
                } catch (Omeka_Validate_Exception $e) {
                    $arch->delete();
                    $this->_helper->flashMessenger($e);
                }                
            } else {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }
        
        // specify view variables
        $this->view->form = $form;
        $this->view->architecture_analyse = $arch;
    }
    
    public function editAction()
    {        
        // get the ArchitectureAnalyse
        $arch = $this->_helper->db->findById();
        
        // edit the ArchitectureAnalyse
        $form = $this->_getForm($arch);
        if (isset($_POST[ArchitectureAnalyse_Form_ArchitectureAnalyse::SUBMIT_EDIT_ELEMENT_ID])) {
            if ($form->isValid($_POST)) {
                try{                    
                    $form->saveFromPost();                    
                    $this->_helper->flashMessenger(__('The architecture analyse "%s" was successfully updated.', $arch->name), 'success');
                    $this->_helper->redirector->gotoRoute(array('action' => 'show', 'id' => $arch->id), 'architectureAnalyseShow');
                    //$this->_helper->redirector('show', 'ArchitectureAnalyse', 'architecture-analyse', array('id'=>$arch->id));//$this->_helper->redirector('show', null, null, array('id'=>$arch->id));
                } catch (Omeka_Validate_Exception $e) {
                    $this->_helper->flashMessenger($e);
                }                
            } else {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }
        
        // specify view variables
        $this->view->form = $form;
        $this->view->architecture_analyse = $arch;
    }
    

    public function addNewElementAction()
    {
        if ($this->_getParam('from_post') == 'true') {
            $elementTempId = $this->_getParam('elementTempId');
            $elementName = $this->_getParam('elementName');
            $elementDescription = $this->_getParam('elementDescription');
            $elementOrder = $this->_getParam('elementOrder');
        } else {
            $elementTempId = '' . time();
            $elementName = '';
            $elementDescription = '';
            $elementOrder = intval($this->_getParam('elementCount')) + 1;
        }

        require_once ARCHITECTUREANALYSE_PLUGIN_DIR . '/forms/ArchitectureAnalyse.php';
        $stem = ArchitectureAnalyse_Form_ArchitectureAnalyse::NEW_ELEMENTS_INPUT_NAME . "[$elementTempId]";
        $elementNameName = $stem . '[name]';
        $elementDescriptionName = $stem . '[description]';
        $elementOrderName = $stem . '[order]';

        $this->view->assign(array('element_name_name' => $elementNameName,
                                  'element_name_value' => $elementName,
                                  'element_description_name' => $elementDescriptionName,
                                  'element_description_value' => $elementDescription,
                                  'element_order_name' => $elementOrderName,
                                  'element_order_value' => $elementOrder,
                                   ));
    }

    public function addExistingElementAction()
    {
        if ($this->_getParam('from_post') == 'true') {
            $elementTempId = $this->_getParam('elementTempId');
            $elementId = $this->_getParam('elementId');            
            $element = $this->_helper->db->getTable('Element')->find($elementId);
            if ($element) {
                $elementDescription = $element->description;
            }
            $elementOrder = $this->_getParam('elementOrder');
        } else {
            $elementTempId = '' . time();
            $elementId = '';
            $elementDescription = '';
            $elementOrder = intval($this->_getParam('elementCount')) + 1;
        }

        
        require_once ARCHITECTUREANALYSE_PLUGIN_DIR . '/forms/ArchitectureAnalyse.php';
        $stem = ArchitectureAnalyse_Form_ArchitectureAnalyse::ELEMENTS_TO_ADD_INPUT_NAME . "[$elementTempId]";
        $elementIdName = $stem .'[id]';
        $elementOrderName = $stem .'[order]';

        $this->view->assign(array('element_id_name' => $elementIdName,
                                  'element_id_value' => $elementId,
                                  'element_description' => $elementDescription,
                                  'element_order_name' => $elementOrderName,
                                  'element_order_value' => $elementOrder,
                                  ));
    }

    public function changeExistingElementAction()
    {
        $elementId = $this->_getParam('elementId');
        $element = $this->_helper->db->getTable('Element')->find($elementId);

        $elementDescription = '';
        if ($element) {
            $elementDescription = $element->description;
        }

        $data = array();
        $data['elementDescription'] = $elementDescription;

        $this->_helper->json($data);
    }

    protected function _redirectAfterAdd($arch)
    {
        
        $this->_redirect("architecture-analyse/edit/{$arch->id}");
    }
    
    protected function _redirectAfterDelete($record)
    {  
        
        $this->_redirect("architecture-analyse/browse");
    }
    
    
    protected function _getDeleteConfirmMessage($arch)
    {
        return __('This will delete the "%s" arch. analyse but will not delete the '
             . 'elements assigned to the arch. analyse. Items that are assigned to '
             . 'this arch. analyse will lose all metadata that is specific to the '
             . 'arch. analyse.', $arch->name);
    }
    
    protected function _getDeleteForm()
    {
        $record = $this->_helper->db->findById();
        
        $form = new Zend_Form();
        $form->setElementDecorators(array('ViewHelper'));
        $form->removeDecorator('HtmlTag');
        $form->addElement('hash', 'confirm_delete_hash');
        $form->setAction(url(array('module'=>'architecture-anayse','controller'=>'architecture-analyse','action' => 'delete','id'=>$record->id),'architectureAnalyseDelete'));
        $form->addElement('submit', 'Delete', array('class' => 'delete red button'));

        return $form;
    }

    protected function _getAddSuccessMessage($arch)
    {
        return __('The arch. analyse "%s" was successfully added!  You may now add elements to your new arch. analyse.', $arch->name);
    }
    
    private function _getForm($arch)
    {        
        require_once ARCHITECTUREANALYSE_PLUGIN_DIR . '/forms/ArchitectureAnalyse.php';
        $form = new ArchitectureAnalyse_Form_ArchitectureAnalyse;
        $form->setArchitectureAnalyse($arch);
        return $form;
    }
}
