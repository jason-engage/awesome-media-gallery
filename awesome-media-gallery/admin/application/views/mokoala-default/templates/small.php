<?php
	$config = MK_Config::getInstance();
?>
<!DOCTYPE html>
<html id="small" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php print $this->getHead()->render(); ?>
        <link rel="shortcut icon" type="image/x-icon" href="<?php print $this->getThemeDirectory(); ?>img/icon.ico" />
		<link type="text/css" media="screen" rel="stylesheet" href="<?php print $this->getThemeDirectory(); ?>css/reset.css" />
		<link type="text/css" media="screen" rel="stylesheet" href="<?php print $this->getThemeDirectory(); ?>css/screen.css" />
		<script language="javascript" type="text/javascript" src="<?php print $this->getThemeDirectory(); ?>js/jquery.js"></script>
		<script language="javascript" type="text/javascript" src="<?php print $this->getThemeDirectory(); ?>js/jquery.tinymce/jquery.tinymce.js"></script>
		<script language="javascript" type="text/javascript" src="<?php print $this->getThemeDirectory(); ?>js/main.js"></script>
	</head>
	
	<body>
        <div id="header">
            <h1>
                <a href=""><?php print $config->site->name; ?></a>
            </h1>
        </div>
        <div id="container">
            <?php print $this->getDisplayOutput(); ?>
            <!--<p id="footer"><a target="_blank" class="core" href="<?php print $config->core->url; ?>">Running on <?php print $config->core->name; ?> v<?php print $config->core->version; ?></a></p>-->
        </div>

	</body>
</html>
