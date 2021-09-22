<div id="left">
	<div class="block">
        <h2><?php print $this->module->getName(); ?> / <?php print $this->title; ?></h2>
    <?php
    
        print $this->form;
    
    ?>
	</div>
</div>
<div id="right">
<?php
	$module_field_type = MK_RecordModuleManager::getFromType( 'module_field' );
	$module_type = MK_RecordModuleManager::getFromType( 'module' );
	foreach( $this->linked_module_data as $module_data )
	{
		$records = $module_data['records'];
		$module = $module_data['module'];
		$link_field = $module_data['link_field'];

		if( $parent_module = $module->getParentModule() )
		{
			$parent_module = $module->objectParentModule();
			$module_url = array( 'controller' => $parent_module->getSlug(), 'section' => $module->getSlug() );
		}
		else
		{
			$module_url = array( 'controller' => $module->getSlug(), 'section' => 'index' );
		}

		$slug_field = MK_RecordManager::getFromId( $module_field_type->getId(), $module->getFieldTitle() );
		print '<div class="block block-flushbottom">';
		print '<h3>Related '.$module->getName().'</h3>';
		print '<table class="table-data table-supplement">';
		print '<thead>';
		print '<tr><th style="width:100%;"><div>'.$slug_field->getLabel().'</div></th></tr>';
		print '</thead>';
		print '<tbody>';
		$counter = 0;
		foreach($records as $record)
		{
			$counter++;
			$record_url = $module_url;
			$record_url['id'] = $record->getId();
			$record_url['method'] = 'edit';
			print '<tr'.($counter == 1 ? ' class="first"' : '').'><td><a href="'.$this->uri($record_url).'">'.MK_Utility::removeHTML( $record->renderMetaValue($slug_field->getName()) ).'</a></td></tr>';
		}
		print '</tbody>';
		print '</table>';
		$view_all = $module_url;
		$view_all[$link_field] = $this->record->getId();
		$view_all['method'] = 'search';
		$add_new = $module_url;
		$add_new['method'] = 'add';
		print '<a class="mini-button mini-button-supplement" href="'.$this->uri($add_new).'">Add new</a><p class="bottom-text"><a href="'.$this->uri($view_all).'">View All</a> &raquo;</p>';
		print '</div>';
	}
?>
</div>