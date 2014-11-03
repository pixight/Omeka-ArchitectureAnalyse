<?php 
$pageTitle = __('Add Architecture Analyse');
echo head(array('title'=>$pageTitle,'bodyclass'=>'item-types'));
echo flash();
?>

<form method="post" action="">
    <?php include 'form.php'; ?>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $form->getElement(ArchitectureAnalyse_Form_ArchitectureAnalyse::SUBMIT_ADD_ELEMENT_ID); ?>           
        </div>
    </section>
</form>
<?php echo foot(); ?>
