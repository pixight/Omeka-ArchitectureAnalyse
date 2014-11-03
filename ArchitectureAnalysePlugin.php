<?php
/**
* @version $Id$
* @copyright pxg, 2014
* @license http://www.gnu.org/licenses/gpl-3.0.txt
* @package ArchitectureAnalyse
*/


/**
* *
* @package Omeka\Plugins\ArchitectureAnalyse
*/

if (!defined('ARCHITECTUREANALYSE_PLUGIN_DIR')) {
    define('ARCHITECTUREANALYSE_PLUGIN_DIR', dirname(__FILE__));
}

require_once ARCHITECTUREANALYSE_PLUGIN_DIR . '/helpers/ArchitectureAnalyseFunctions.php';

class ArchitectureAnalysePlugin extends Omeka_Plugin_AbstractPlugin
{
    
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array('install', 'uninstall','uninstall_message','admin_head','define_routes');
   
    
    protected $_filters = array('admin_navigation_main','admin_items_form_tabs','custom_items_batch_form_tabs');
        

    
    /**
    * Installs the plugin
    **/
    public function hookInstall()
    {
        $db = $this->_db;
        $sql = "
        CREATE TABLE IF NOT EXISTS `{$db->prefix}architecture_analyses` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,          
            `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            `description` text COLLATE utf8_unicode_ci,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name` (`name`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $db->query($sql);
        
        $sql = "
        CREATE TABLE IF NOT EXISTS `{$db->prefix}architecture_analyses_elements` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `architecture_analyse_id` int(10) unsigned NOT NULL,
            `element_id` int(10) unsigned NOT NULL,
            `order` int(10) unsigned DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `architecture_analyse_id_element_id` (`architecture_analyse_id`,`element_id`),
        KEY `architecture_analyse_id` (`architecture_analyse_id`),
        KEY `element_id` (`element_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $db->query($sql);

        $sql = "INSERT INTO `{$db->ElementSets}` (`record_type`, `name`, `description`) 
                VALUES (?, ?, ?)";
                $this->_db->query($sql, array('Item', 'Architecture Analyse Metadata', 'Champs personnalisable'));
                
        $arch = new ArchitectureAnalyse;
        $arch->name = 'test';
        $arch->description = 'test desc';
        $arch->save();
        
        $this->_installOptions();        
    }
    
    /**
    * Uninstalls the plugin. This will remove the
    * element set created during installation.
     */
    public function hookUninstall()
    { 
        // Drop element, elementtext link to arch. analyse.
        $db = $this->_db;
        $tabarch = $db->getTable('ArchitectureAnalyse')->findAll();
        foreach($tabarch as $arch){
            $arch->delete();
        }
        //drop table
        $sql = "DROP TABLE IF EXISTS `{$db->prefix}architecture_analyses`,`{$db->prefix}architecture_analyses_elements`";
        $db->query($sql);

        $sql = "DELETE FROM `{$db->ElementSets}` WHERE `{$db->ElementSets}`.`name` LIKE 'Architecture Analyse Metadata'";
                $this->_db->query($sql);
                
        $this->_uninstallOptions();
    }
    
    /**
    * Appends a warning message to the uninstall confirmation page.
    *
     * Display the uninstall message.
     */
    public function hookUninstallMessage()
    {
       
        echo '<p><strong>Desinstallation</strong></p>';
    }
    
    /**
     * Configure admin theme header.
     *
     * @param array $args
     */
    public function hookAdminHead($args)
    {
        
    }
    
    function hookDefineRoutes($args)
    {
        $router = $args['router'];
        $router->addConfig(new Zend_Config_Ini(ARCHITECTUREANALYSE_PLUGIN_DIR .
        '/routes.ini', 'routes'));    
    }      
     
    /**
     * Gets the element sets for the 'Item' record type.
     * 
     * @return array The element sets for the 'Item' record type
     */
    protected function _getItemElementSets()
    {
        return get_db()->getTable('ElementSet')->findByRecordType('Item');
    }
    
    /**
     * Gets the element from his id.
     * 
     * @return Element The element 
     */
    protected function _getElement($id)
    {
        return get_db()->getTable('Element')->find($id);
    }
    
    /**
     * Add the link to the admin main navigation.
     * 
     * @param array Navigation array.
     * @return array Filtered navigation array.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Arch. Analyses'),
            'uri' => url('architecture-analyse/browse')
        );
        return $nav;
    }
    
    public function filterAdminItemsFormTabs($tabs,$tabitem)
    {
        //on récupère le tab archi analyse element set
        
        $archs = get_db()->getTable('ArchitectureAnalyse')->findAll();
       
        
        $html = '<script> jQuery(function() {
                                jQuery( "#accordion" ).accordion({
                                    heightStyle: "content"
                                });
                            });
                 </script>';
        $html .= '<div id="accordion">';
        foreach($archs as $arch){
            $html .= '<h3>'.$arch->name.'</h3>';
            $html .= '<div>';
            $html .= '<p class="element-set-description" id="';
            $html .= html_escape(text_to_id($arch->name) . '-description') . '">';
            $html .= __($arch->description) . '</p>' . "\n\n";            
             $archelements = $arch->Elements;
             foreach($archelements as $archelement){
                 $element = get_db()->getTable('Element')->find($archelement->id);                 
                 $html .= get_view()->elementForm($element, $tabitem['item']); 
             }
             $html .= '</div>';
        }
        $html .= '</div>';
        $tabs[ArchitectureAnalyse::ARCHITECTURE_ANALYSE_NAME] = $html;
        return $tabs;
    }
    
    public function filterCustomItemsBatchFormTabs($tabs,$args)
    {
        //on récupère le tab archi analyse element set
       
        $recordType = get_class($args['item']);
        
        $archs = get_db()->getTable('ArchitectureAnalyse')->findAll();
        unset($tabs[ArchitectureAnalyse::ARCHITECTURE_ANALYSE_NAME]);
        if(count($archs) > 0){            
            foreach($archs as $arch){                
                $html = '';
                $html .= '<fieldset id="item-fields-'.$arch->id.'">';
                $html .= '<p class="element-set-description" id="';
                $html .= html_escape(text_to_id($arch->name) . '-description') . '">';
                $html .= __($arch->description) . '</p>' . "\n\n";            
                 $archelements = $arch->Elements;
                 foreach($archelements as $archelement){
                     $element = get_db()->getTable('Element')->find($archelement->id);    
                     add_filter(array('ElementForm', $recordType, ArchitectureAnalyse::ARCHITECTURE_ANALYSE_NAME, $element->name),array('CustomItemsBatchFormPlugin','renameItem'));
                     $html .= get_view()->elementForm($element, $args['item']); 
                 }
                 $html .= '</fieldset>';
                 $tabs['Metadata personnalisée : '.$arch->name] = $html;
            }
        }
        return $tabs;
    }
}