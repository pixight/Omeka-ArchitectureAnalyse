<?php
/**
 * Retrieve the set of ArchitectureAnalyses that are being looped.
 *
 * @since 1.1
 * @return array
 */
function get_architecture_analyse_for_loop()
{
    return __v()->architectureanalyses;
}

/**
 * @since 1.1
 * @param array $itemtypes Set of ArchitectureAnalyse records to loop.
 * @return void
 */
function set_architecture_analyse_for_loop($itemtypes)
{
    __v()->architectureanalyses = $itemtypes;
}

/**
 * Loops through ArchitectureAnalyses assigned to the current view.
 *
 * @since 1.1
 * @return mixed The current ArchitectureAnalyse in the loop.
 */
function loop_architecture_analyse()
{
    return loop_records('architecture_analyse', get_architecture_analyse_for_loop(), 'set_current_architecture_analyse');
    
}

/**
 * @since 1.1
 * @param ArchitectureAnalyse
 * @return void
 */
function set_current_architecture_analyse($itemtype)
{
    __v()->architectureanalyse = $itemtype;
}

/**
 * @since 1.1
 * @return ArchitectureAnalyse|null
 */
function get_current_architecture_analyse()
{
    return __v()->architectureanalyse;
}

/**
 * Determine whether there are any ArchitectureAnalyse to loop through.
 *
 * @since 1.0
 * @see has_items_for_loop()
 * @return boolean
 */
function has_architecture_analyse_for_loop()
{
    $view = __v();
    return $view->itemtypes && count($view->itemtypes);
}

/**
 * Retrieve a full set of ArchitectureAnalyse objects currently available to Omeka.
 *
 * Keep in mind that the $params and $limit arguments are in place for the sake
 * of consistency with other data retrieval functions, though in this case
 * they don't have any effect on the number of results returned.
 *
 * @since 0.10
 * @param array $params
 * @param integer $limit
 * @return array
 */
function get_architecture_analyse($params = array(), $limit = 10)
{
    return get_db()->getTable('ArchitectureAnalyse')->findAll();
}

/**
 * Retrieve the set of values for architecture analyse elements.
 * @param Item|null Check for this specific item record (current item if null).
 * @return array
 */
function architecture_analyse_elements($item=null)
{
    if (!$item) {
        $item = get_current_item();
    }
    $elements = $item->getArchitectureAnalysesElements();
    foreach ($elements as $element) {
        $elementText[$element->name] = item(ELEMENT_SET_ARCHITECTURE_ANALYSE, $element->name);
    }
    return $elementText;
}
