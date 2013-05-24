<div class='row'>
    <?php if($_CONTROL->blnUseAddress){ ?>
        <div class='span4 offset2'>

            <h3>Address: </h3>
            <?php $_CONTROL->txtAddress1->Render(); ?>
            <?php $_CONTROL->txtAddress2->Render(); ?>
            <?php $_CONTROL->txtCity->Render(); ?>
            <?php $_CONTROL->txtState->Render(); ?>
            <?php $_CONTROL->txtZip->Render(); ?>
            <div class='alert alert-info'>
                Sorry we are only accepting signups in the USA right now
            </div>
        </div>
    <?php }else{ ?>
        <div class='span4'></div>
    <?php } ?>
    <div class='span4'>
        <h3>Payment Info: </h3>
        <?php $_CONTROL->txtCardNum ->Render(); ?>
        <?php $_CONTROL->txtCvc ->Render(); ?>
        <div class="input-append">
            <?php $_CONTROL->lstExpMonth ->Render(); ?>
            &nbsp;
            <?php $_CONTROL->lstExpYear ->Render(); ?>
        </div>
        <div class="control-group">
            <?php $_CONTROL->txtDiscount->Render(); ?>
        </div>
        <div class='control-group'>
            <?php $_CONTROL->lnkSubmit ->Render(); ?>
        </div>
    </div>
</div>


