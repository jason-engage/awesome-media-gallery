
<div class="block">
    <h2>Dashboard / Installed Components</h2>
<?php
	foreach( $this->messages as $message )
	{
		print '<p class="simple-message simple-message-'.$message->getType().'">'.$message->getMessage().'</p>';
	}
?>
</div>