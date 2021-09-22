<?php
	$config = MK_Config::getInstance();
?>
<div class="block clear-fix">
    <h2>Dashboard</h2>
    <p>Use the links above to navigate the CMS.</p>
    <h3>System Information</h3>
    <dl class="data-list data-list-wide clear-fix">
    	<dt>post_max_size</dt>
        <dd><?php print MK_Utility::formatBytes( $config->site->settings->post_max_size ); ?></dd>
    	<dt>upload_max_filesize</dt>
        <dd><?php print MK_Utility::formatBytes( $config->site->settings->upload_max_filesize ); ?></dd>
    	<dt>memory_limit</dt>
        <dd><?php print MK_Utility::formatBytes( $config->site->settings->memory_limit ); ?></dd>
    </dl>
<?php
if( count($this->message_list) > 0 )
{
	print '<h3>Notifications</h3>';
	foreach( $this->message_list as $message )
	{
		print '<p class="simple-message simple-message-'.$message['type'].'">'.$message['message'].'</p>';
	}
}
?>

<?php
// Action Log
if( count($this->action_log) > 0 )
{
?>
    <h3>Action Log <a rel="record delete" class="mini-button" title="Are you sure you want to delete all log entries" href="<?php print $this->uri(array('controller' => 'dashboard', 'section' => 'action-log', 'method' => 'delete')); ?>">Clear Log</a></h3>
<?php
	foreach( $this->action_log as $message )
	{
		print '<p class="simple-message simple-message-'.$message->getType().'">'.$message->getMessage().'</p>';
	}

	print '<div class="paginator">'.$this->action_log_paginator.'</div>';
}

// Additional Components & Updates
/*$components = MK_ComponentManager::getComponents(  );

$config = MK_Config::getInstance();
$installed_components = $config->db->components;
$installed_components_by_id = array();

foreach( $installed_components as $installed_component )
{
	$installed_component = $components[$installed_component];
	if( $installed_component['id'] )
	{
		$installed_components_by_id[$installed_component['id']] = $installed_component;
	}
}

$modules = MK_Utility::getResource('http://marketplace.mattlowden.com/api.php');
$modules = json_decode($modules, true);

print '<h3>Additional Components & Updates</h3>';
print '<ul class="products">';

$html_installed = '';
$html_out_of_date = '';
$html_not_installed = '';

if( $modules )
{
	foreach( $modules['items'] as $item )
	{
		$installed = array_key_exists($item['id'], $installed_components_by_id);

		$status = 'not-installed';
		$status_text = 'Install Now';
		if( $installed )
		{
			$installed_component = $installed_components_by_id[$item['id']];
			$installed_component_version = (float) $installed_component['version'];
			$component_version = (float) $item['version'];

			if( $installed_component_version < $component_version )
			{
				$status = 'out-of-date';
				$status_text = 'Out of Date';
			}
			else
			{
				$status = 'installed';
				$status_text = 'Installed';
			}
		}

		$price = (float) $item['price'];

		$item_html = '<li class="status-'.$status.'"><a class="clear-fix"'.( $status != 'installed' ? ' href=""' : '' ).'>';
		$item_html.= '<h5>$'.number_format($price, 2).'</h5>';
		$item_html.= '<span class="mini-button">'.$status_text.'</span>';
		$item_html.= '<img src="http://marketplace.mattlowden.com/'.$item['image'].'" />';
		$item_html.= '<h4>'.$item['name'].'</h4>';
		$item_html.= $item['short_description'];
		$item_html.= '</a></li>';
		
		if( $status == 'not-installed' )
		{
			$html_not_installed.= $item_html;
		}
		elseif( $status == 'installed' )
		{
			$html_installed.= $item_html;
		}
		elseif( $status == 'out-of-date' )
		{
			$html_out_of_date.= $item_html;
		}
	}
}

print $html_out_of_date;
print $html_not_installed;
print $html_installed;

print '</ul>';*/
?>

</div>
