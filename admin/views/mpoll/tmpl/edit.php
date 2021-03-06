<?php

// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$params = $this->form->getFieldsets('params');
?>
<form action="<?php echo JRoute::_('index.php?option=com_mpoll&layout=edit&poll_id='.(int) $this->item->poll_id); ?>" method="post" name="adminForm" id="mpoll-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_MPOLL_MPOLL_SETUP' ); ?></legend>
			<ul class="adminformlist">
<?php foreach($this->form->getFieldset('setup') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
<?php endforeach; ?>
			</ul>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_MPOLL_MPOLL_DETAILS' ); ?></legend>
<?php foreach($this->form->getFieldset('details') as $field): ?>
				<?php echo '<div>'.$field->label.'<div class="clr"></div>'.$field->input.'</div>';?>
<?php endforeach; ?>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_MPOLL_MPOLL_PUBLISHING' ); ?></legend>
			<ul class="adminformlist">
<?php foreach($this->form->getFieldset('publishing') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
<?php endforeach; ?>
			</ul>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_MPOLL_MPOLL_CONFIRMATION' ); ?></legend>
			<ul class="adminformlist">
<?php foreach($this->form->getFieldset('confirmation') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
<?php endforeach; ?>
			</ul>
			<?php foreach($this->form->getFieldset('confcontent') as $field): ?>
				<?php echo '<div>'.$field->label.'<div class="clr"></div>'.$field->input.'</div>';?>
			<?php endforeach; ?>
			<div style="clear:both">use {i##} with quesion id as ## for form content<br />{name} for users full name<br />{email} for users email<br />{username} for users username</div>
		</fieldset>
	</div>



	<div>
		<input type="hidden" name="task" value="mpoll.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

