<?php
$type_name = strip_formatting($architecture_analyse->name);
if ($type_name != '') {
    $type_name = ': &quot;' . html_escape($type_name) . '&quot; ';
} else {
    $type_name = '';
}
$title = __('Architecture Analyse #%s', $architecture_analyse->id) . $type_name;
echo head(array('title'=> $title,'bodyclass'=>'item-types'));
echo flash();
?>
<section class="seven columns alpha">
    <div id="type-info">
        <h2><?php echo __('Description'); ?></h2>
        <p><?php echo html_escape($architecture_analyse->description); ?></p>
        <h2><?php echo __('Elements'); ?></h2>
        <?php if ($architecture_analyse->Elements): ?>
        <dl class="type-metadata">
            <?php foreach($architecture_analyse->Elements as $element): ?>
            <dt><?php echo html_escape($element->name); ?></dt>
            <dd><?php echo html_escape($element->description); ?></dd>
            <?php endforeach; ?>
        </dl>
        <?php else: ?>
        <p><?php echo __('There are no elements.'); ?></p>
        <?php endif; ?>
    </div>

    <div id="type-items">
        <h2><?php echo __('Recently Added Items'); ?></h2>
        <?php if($architecture_analyse->Items != null): ?>
        <ul>
        <?php set_loop_records('items', $architecture_analyse->Items); ?>
        <?php foreach (loop('items') as $item): ?>
        <li><span class="date"><?php echo format_date(metadata('item', 'Added')); ?></span> <?php echo link_to_item('<span class="title">' . metadata('item', array('Dublin Core', 'Title')) . '</span>') ?></li>
        <?php endforeach;?>
        </ul>
        <?php else: ?>
        <p><?php echo __('There are no recently added items.'); ?></p>
        <?php endif;?>
        
    </div>
</section>

<section class="three columns omega">
    <div id="edit" class="panel">
    <?php if ( is_allowed('ItemTypes','edit') ): ?>
    <a class="edit big green button" href="<?php echo html_escape(url('architecture-analyse/edit/' . $architecture_analyse->id));//record_url($architecture_analyse, 'edit', 'architecture-analyse')); ?>"><?php echo __('Edit'); ?></a>
    <?php endif; ?>
    <?php if ( is_allowed('ItemTypes','delete') ): ?>
    <a href="<?php echo html_escape(url('architecture-analyse/delete-confirm/' . $architecture_analyse->id));?>" class="edit big red button delete-confirm"><?php echo __('Delete') ?></a>
    <?php endif; ?>
    </div>
</section>
<?php echo foot();?>
