<?php foreach($_CONTROL->arrCards as $intIndex => $arrCard){ ?>
    <div class='row-fluid'>
        <div class='span4'>
            <?php echo $arrCard['type']; ?>
        </div>
        <div class='span4'>
            <b>Last 4:</b> <?php echo $arrCard['last4']; ?>
        </div>
        <div class='span4'>
            <?php $_CONTROL->arrRadioBoxes[$intIndex]->Render(); ?>
        </div>
    </div>

<?php } ?>
<hr />
<!--<div class='row-fluid'>
    <div class='span4 offset4'>
        <?php /*$_CONTROL->lnkAddNewCard->Render(); */?>
    </div>
</div>
<hr/>-->
<div class='pull-right'>
    <?php $_CONTROL->lnkSubmit->Render(); ?>
</div>
<div style='clear:both;'></div>
