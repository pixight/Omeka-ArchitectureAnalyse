<?php
$type_name = strip_formatting($architecture_analyse->name);
if ($type_name != '') {
    $type_name = ': &quot;' . html_escape($type_name) . '&quot; ';
} else {
    $type_name = '';
}
$title = __('Edit Architecture Analyse #%s', $architecture_analyse->id) . $type_name;

echo head(array('title'=> $title,'bodyclass'=>'item-types'));
echo flash();
?>

<form method="post" action="">
    <?php include 'form.php';?>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $form->getElement(ArchitectureAnalyse_Form_ArchitectureAnalyse::SUBMIT_EDIT_ELEMENT_ID); ?>
            <?php if (is_allowed('ItemTypes', 'delete')): ?>
                <a href="<?php echo html_escape(url('architecture-analyse/delete-confirm/' . $architecture_analyse->id));?>" class="big red button delete-confirm"><?php echo __('Delete') ?></a><?php // echo link_to($architecture_analyse, 'delete-confirm', __('Delete'), array('class' => 'big red button delete-confirm')); ?>
            <?php endif; ?>
        </div>
    </section>
</form>

<script type="text/javascript">
Omeka.addReadyCallback(Omeka.ArchitectureAnalyse.enableSorting);
Omeka.addReadyCallback(Omeka.ArchitectureAnalyse.addHideButtons);
</script>
<?php echo foot(); ?>
