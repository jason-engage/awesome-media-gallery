
<div class="block">
	<ul class="tab-list tab-list-right">
<?php
	foreach( $this->fieldsets as $fieldset_uri => $fieldset_name )
	{
		print '<li'.( $this->selected_fieldset == $fieldset_uri ? ' class="selected"' : '' ).'><a href="'.$this->uri(array('form' => $fieldset_uri)).'">'.$fieldset_name.'</a></li>';
	}
?>
	</ul>
    <h2>Dashboard / Settings</h2>
<?php
	print $this->form;
?>
</div>