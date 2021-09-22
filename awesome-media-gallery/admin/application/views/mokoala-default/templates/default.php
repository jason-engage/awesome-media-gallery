<?php
	$config = MK_Config::getInstance();
?>
<!DOCTYPE html>
<html id="large" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php print $this->getHead()->render(); ?>
        <link rel="shortcut icon" type="image/x-icon" href="<?php print $this->getThemeDirectory(); ?>img/icon.ico" />
		<link type="text/css" media="screen" rel="stylesheet" href="<?php print $this->getThemeDirectory(); ?>css/reset.css" />
		<link type="text/css" media="screen" rel="stylesheet" href="<?php print $this->getThemeDirectory(); ?>css/screen.css" />
		<link type="text/css" media="screen" rel="stylesheet" href="<?php print $this->getThemeDirectory(); ?>js/jquery.uploadify/jquery.uploadify.css" />
		<script language="javascript" type="text/javascript" src="<?php print $this->getThemeDirectory(); ?>js/swfobject.js"></script>
		<script language="javascript" type="text/javascript" src="<?php print $this->getThemeDirectory(); ?>js/jquery.js"></script>
		<script language="javascript" type="text/javascript" src="<?php print $this->getThemeDirectory(); ?>js/jquery.uploadify/jquery.uploadify.js"></script>
		<script language="javascript" type="text/javascript" src="<?php print $this->getThemeDirectory(); ?>js/jquery.tinymce/jquery.tinymce.js"></script>
		<script language="javascript" type="text/javascript" src="<?php print $this->getThemeDirectory(); ?>js/main.js"></script>
		<script language="javascript" type="text/javascript">
        	mokoala.template_folder = '<?php print $this->getThemeDirectory(); ?>';
        	mokoala.base_href = '<?php print $config->site->base_href; ?>';
        	mokoala.site_href = '<?php print $config->site->url; ?>';
        	mokoala.settings_upload_max_filesize = '<?php print $config->site->settings->upload_max_filesize; ?>';
        </script>
	</head>
	
	<body>
        <div id="user-bar" class="clear-fix">
            <div class="inner">
            	<a rel="core navigation-toggle" class="navigation-toggle"></a>
            
                <h1><a href=""><?php print $config->site->name; ?></a></h1>
    
                <ul id="user">
                    <li>Welcome <?php print $this->getUser()->getDisplayName(); ?></li>
                    <li class="my-account"><a href="<?php print $this->uri( array('controller' => 'users', 'method' => 'edit', 'id' => $this->getUser()->getId())); ?>">My account</a></li>
                    <li class="log-out"><a href="<?php print $this->uri( array('controller' => 'account', 'section' => 'log-out' )); ?>">Log out</a></li>
                    <li class="view-site"><strong><a target="_blank" href="<?php print $config->site->url; ?>">View Site</a></strong></li>
                </ul>
			</div>
		</div>
        <div id="navigation-wrapper-background"></div>
        <div id="navigation-wrapper">
            <ul id="navigation-main" class="clear-fix">
                <li class="<?php print MK_Request::getParam('controller') === 'dashboard' ? 'selected ' : null; ?>first">
                    <a href="<?php print $this->uri(array('controller' => 'dashboard')); ?>" class="main">Dashboard</a>
                    <ul id="navigation-sub" class="clear-fix">
                        <li class="first<?php print MK_Request::getParam('section') == 'settings' ? ' selected' : ''; ?>"><a href="<?php print $this->uri( array('controller' => 'dashboard', 'section' => 'settings' ) ); ?>">Settings</a></li>
                        <li class="<?php print MK_Request::getParam('section') == 'email-users' ? ' selected' : ''; ?>"><a href="<?php print $this->uri( array('controller' => 'dashboard', 'section' => 'email-users' ) ); ?>">Email Users</a></li>
                        <li class="<?php print MK_Request::getParam('section') == 'backup' ? ' selected' : ''; ?>"><a href="<?php print $this->uri( array('controller' => 'dashboard', 'section' => 'backup' ) ); ?>">Backup</a></li>
                        <li class="<?php print MK_Request::getParam('section') == 'file-manager' ? ' selected' : ''; ?>"><a href="<?php print $this->uri( array('controller' => 'dashboard', 'section' => 'file-manager' ) ); ?>">File Manager</a></li>
                        <!--<li class="<?php print MK_Request::getParam('section') == 'installed-components' ? ' selected' : ''; ?>"><a href="<?php print $this->uri( array('controller' => 'dashboard', 'section' => 'installed-components' ) ); ?>">Installed Components</a></li>-->
                    </ul>
                </li>
<?php
    foreach($this->modules as $module)
    {
		if( $module->isHidden() || ( $config->core->mode !== MK_Core::MODE_FULL && $module->isCoreModule() ) )
		{
			continue;
		}
        print '<li class="'.(MK_Request::getParam('controller') == $module->getSlug() ? 'selected ' : null).'">';
        print '<a href="'.$this->uri( array('controller' => $module->getSlug()) ).'" class="main">'.$module->getName().'</a>';
        if( count($module->getSubModules()) > 0 )
        {
            print '<ul id="navigation-sub" class="clear-fix">';
            $counter = 0;
            foreach( $module->getSubModules() as $sub_module )
            {
                $class = array();
				$counter++;
                if($counter === 1) $class[] = 'first';
                if(MK_Request::getParam('section') === $sub_module->getSlug()) $class[] = 'selected';
                print '<li class="'.(implode(' ', $class)).'"><a href="'.$this->uri( array('controller' => $module->getSlug(), 'section' => $sub_module->getSlug() ) ).'">'.$sub_module->getName().'</a></li>';
            }
            print '</ul>';
        }
        print '</li>';
    }
?>
            </ul>
		</div>

        <div id="container" class="clear-fix">
			<?php print $this->getDisplayOutput(); ?>
		</div>
		
        <div id="footer">
        	<ul class="clear-fix">
            	<li><a target="_blank" href="<?php print $config->core->url; ?>using">Using</a></li>
                <li><a target="_blank" href="<?php print $config->core->url; ?>developing">Developing</a></li>
                <li><a target="_blank" href="<?php print $config->core->url; ?>contact">Contact</a></li>
                <li class="version"><a target="_blank" href="<?php print $config->instance->url; ?>"><?php print $config->instance->name; ?> v<?php print $config->instance->version; ?></a><br /><a target="_blank" class="core" href="<?php print $config->core->url; ?>">Running on <?php print $config->core->name; ?> v<?php print $config->core->version; ?></a></li>
            </ul>
        </div>
	</body>
</html>
