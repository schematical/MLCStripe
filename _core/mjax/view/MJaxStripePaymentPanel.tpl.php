
<?php $_CONTROL->txtCardNum ->Render(); ?>
<?php $_CONTROL->txtCvc ->Render(); ?>
<div class="input-append">
	<?php $_CONTROL->lstExpMonth ->Render(); ?>
	&nbsp;
	<?php $_CONTROL->lstExpYear ->Render(); ?>
</div>
<?php if($_CONTROL->blnUseAddress){ ?>
	<h3>Address: </h3>
	<?php $_CONTROL->txtAddress1->Render(); ?>
	<?php $_CONTROL->txtAddress2->Render(); ?>
	<?php $_CONTROL->txtCity->Render(); ?>
	<?php $_CONTROL->txtState->Render(); ?>
	<?php $_CONTROL->txtZip->Render(); ?>
	<div class='alert alert-info'>
		Sorry we are only accepting signups in the USA right now
	</div>
    <div class="control-group">
      <?php $_CONTROL->txtDiscount->Render(); ?>
    </div>
<?php } ?>
<div class='clear'></div>
<?php $_CONTROL->lnkSubmit ->Render(); ?>
