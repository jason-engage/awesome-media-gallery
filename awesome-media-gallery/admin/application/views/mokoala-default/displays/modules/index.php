<?php
	$user = MK_Authorizer::authorize();
?>
<div class="block">
    <h2>Managing <?php print $this->module->getName(); ?> <small>(<?php print number_format($this->module->getTotalRecords()); ?> record<?php print $this->module->getTotalRecords() == 1 ? '' : 's'; ?> in total)</small></h2>
    <div class="inner-block inner-block-search">
<?php
print $this->search_form;
if(  MK_Request::getParam('method') === 'search' )
{
	print '<p class="module-search-expand module-search-contract"><span>&ndash;</span><a href="'.$this->uri().'">Fewer options</a></p>';
}
else
{
	print '<p class="module-search-expand"><span>+</span><a href="'.$this->uri( array('method' => 'search') ).'">More options</a></p>';
}
?>
	</div>

<div class="module-records-title-container">
<div class="module-export-container">
    <p class="module-export module-export-mini"><span>&#9660;</span><a href="<?php print $this->uri( array_merge_replace($this->page_params, array('export' => 'csv')) , false ); ?>">Export as CSV</a></p>
    <p class="module-export module-export-full"><span>&#9650;</span><a href="<?php print $this->uri( array_merge_replace($this->page_params, array('export' => 'csv')) , false ); ?>">Export as CSV</a></p>
    <form class="module-export-full">
        <ul class="clear-fix">
<?php
foreach( $this->all_fields as $field )
{
	if( in_array($field->getType(), array('password', 'hidden', 'file', 'file_image')) )
	{
		continue;
	}
?>
            <li><input type="checkbox"<?php print $field->getDisplayWidth() ? ' checked="checked"' : ''; ?> value="<?php print $field->getName(); ?>" id="field_<?php print $field->getName(); ?>" /><label for="field_<?php print $field->getName(); ?>" title="<?php print MK_Utility::removeHTML($field->getTooltip()); ?>"><?php print $field->getLabel(); ?></label></li>
<?php
}
?>
        </ul>
        <div class="clear-fix form-buttons form-field-link">
            <a class="input-submit" href="<?php print $this->uri( array_merge_replace($this->page_params, array('export' => 'csv')) , false ); ?>">Export these Fields</a>
        </div>
    </form>
</div>
<h3>Records</h3>
</div>
<?php
if( !empty($this->message) )
{
	foreach( $this->messages as $message )
	{
		print '<p class="simple-message simple-message-'.$message->getType().'">'.$message->getMessage().'</p>';
	}
}
else
{
?>
<form id="module-browse" class="clear-fix" action="<?php print $this->uri(); ?>" enctype="multipart/form-data" method="post">
<table class="table-data" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <th class="first center field-checkbox" style="width:5%;"><div><input type="checkbox" value="0" /></div></th>
<?php
	$field_count = 0;
	$total_fields = count($this->fields) + 1;
	if($this->module->getManagementWidth())
	{
		$total_fields++;
	}

	foreach($this->fields as $field)
	{
		$field_count++;
		$class = array(
			'field-'.$field->getName(),
			'field-type-'.( $field->getType() ? $field->getType() : 'text' )
		);
		
		if( $this->module->getFieldTitle() == $field->getId() )
		{
			$class[] = 'field-slug';
		}

		if($field_count === 1) $class[] = 'first';
		if($field_count === $total_fields) $class[] = 'last';
		if($field->getId() == MK_Request::getParam('order_by'))
		{
			if(MK_Request::getParam('order_by_direction') === 'DESC') $class[] = 'desc';
			else $class[] = 'asc';
		}

		$field_uri = $this->uri( array('order_by' => $field->getId(), 'order_by_direction' => ( $field->getId() == MK_Request::getParam('order_by') && MK_Request::getParam('order_by_direction') ) == 'ASC' ? 'DESC' : 'ASC' ) );

    	print '<th class="'.implode(' ', $class).'" style="width:'.$field->getDisplayWidth().'">';
		print '<a href="'.$field_uri.'">';
		print '<span class="wrap">';
		print $field->getLabel();
		if( $tooltip = $field->getTooltip() )
		{
			print '<p class="tooltip"><span class="arrow"></span>'.$tooltip.'</p>';
		}
		print '</span>';
		print '</a>';
		print '</th>';
	}
	
	if($this->module->getManagementWidth())
	{
		print '<th class="last options" style="width:'.$this->module->getManagementWidth().'"><div>Options</div></th>';
	}
?>
		</tr>
	</thead>
    <tbody>
<?php
	$counter = 0;
	foreach($this->records as $record)
	{
		$user_class = get_class($user);
					
		$counter++;
		$text_indent = '';
		$can_edit = $record->canEdit( $user );
		$can_delete = $record->canDelete( $user );

		for($i = 0; $i < $record->getNestedLevel(); $i++)
		{
			$text_indent.='&nbsp;&nbsp;&nbsp;';
		}

		print '<tr class="'.( is_int($counter / 2) ? 'odd' : 'even' ).'">';
		print '<td class="first center field-checkbox">';
		if( $can_edit )
		{
            print '<input'.( !$can_delete ? ' disabled="disabled"' : '' ).' name="module-select[]" type="checkbox" value="'.$record->getId().'" />';
		}
		print '</td>';

		foreach($this->fields as $field)
		{
			$classes = array(
				'field-'.$field->getName(),
				'field-type-'.( $field->getType() ? $field->getType() : 'text' )
			);

			if( $this->module->getFieldTitle() == $field->getId() )
			{
				$classes[] = 'field-slug';
			}

			$get_method = 'get'.MK_Utility::stringToReference($field->getName());
			print '<td class="'.implode(' ', $classes).'">';
			if($field->getId() === $this->module->getFieldTitle())
			{
				print $text_indent.$record->$get_method($field);
			}
			else
			{
				print $record->$get_method($field);
			}
            print '</td>';
		}
		
		if($this->module->getManagementWidth())
		{
			print '<td class="last options">';
			foreach( $this->options_list as $title => $attributes )
			{
				$attributes['class'] = 'mini-button mini-button-'.MK_Utility::getSlug($title);
				if( $can_edit != false )
				{
					if( $title == 'Delete' && !$can_delete )
					{
						print '<span title="You can\'t delete your own account" class="'.$attributes['class'].'">'.$title.'</span> ';
					}
					else
					{
						$attributes['href'] = str_replace('{record_id}', $record->getId(), $attributes['href']);
						print '<a'.MK_Utility::getAttributes($attributes).'>'.$title.'</a> ';
					}
				}
				else
				{
					print '<span title="This record cannot be edited" class="'.$attributes['class'].'">'.$title.'</span> ';
				}
			}
			print '</td>';
		}
?>
		</tr>
<?php
	}

	if(count($this->records) === 0)
	{
?>
		<tr class="no-records">
        	<td colspan="<?php print $total_fields; ?>">Sorry, your search returned no results!</td>
        </tr>
<?php
	}

?>
	</tbody>
</table>

<?php
	if(count($this->records) > 0)
	{
		print '<div class="paginator clear-fix">'.$this->paginator.'</div>';
	}

	print implode('', $this->options_list_global);
?>

</form>
<?php
}
?>
</div>