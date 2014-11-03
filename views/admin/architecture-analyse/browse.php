<?php 
$pageTitle = __('Browse architecture analyse') . ' ' . __('(%s total)', $total_results);
echo head(array('title' => $pageTitle,'bodyclass' => 'item-types')); ?>

<div class="table-actions">
    <?php if (is_allowed('ItemTypes', 'add')): ?>
    <?php echo link_to('architecture-analyse', 'add', __('Add an architecture analyse'), array('class'=>'add green button')); ?>
    <?php endif ?>
</div>

<table>
    <thead>
        <tr>
            <th><?php echo __('Type Name'); ?></th>
            <th><?php echo __('Description'); ?></th>
        </tr>
    </thead>
    <tbody>        
        <?php foreach (loop('ArchitectureAnalyse') as $archi): ?>
        <tr class="itemtype">
            <td class="itemtype-name">
                <a href="<?php echo html_escape(url('architecture-analyse/show/' . $archi->id));//record_url($archi, 'show', 'architecture-analyse')); ?>"><?php echo html_escape($archi->name); ?></a>
                <ul class="action-links group">
                <?php if (is_allowed('ItemTypes', 'edit')): ?>
                    <li><a class="edit" href="<?php echo html_escape(url('architecture-analyse/edit/' . $archi->id)); ?>"><?php echo __('Edit'); ?></a></li>
                <?php endif; ?>        
                </ul>                
            </td>
            <td class="itemtype-description"><?php echo html_escape($archi->description); ?></td>
            
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="table-actions">
    <?php if (is_allowed('ItemTypes', 'add')): ?>
    <?php echo link_to('architecture-analyse', 'add', __('Add an architecture analyse'), array('class'=>'add green button')); ?>
    <?php endif ?>
</div>


<?php echo foot(); ?>
