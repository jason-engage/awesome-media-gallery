<div class="block">
    <h2>Dashboard / Email Users</h2>
<?php

	if( ($user_groups = MK_Request::getParam('user-groups')) && !empty($user_groups) )
	{
		if( is_string($user_groups) )
		{
			$user_groups = explode(',', $user_groups);
		}

		$settings = array(
			'attributes' => array(
				'class' => 'standard clear-fix',
				'action' => $this->uri( array('controller' => 'dashboard', 'section' => 'email-users', 'user-groups' => implode(',', $user_groups)) )
			),
		);

		$structure = array(
			'subject' => array(
				'type' => 'text',
				'label' => 'Subject',
				'validation' => array(
					'instance' => array()
				)
			),
			'message' => array(
				'type' => 'textarea',
				'label' => 'Message',
				'validation' => array(
					'instance' => array()
				)
			),
			'submit' => array(
				'type' => 'submit',
				'attributes' => array(
					'value' => 'Send Email'
				),
			),
		);

		$form = new MK_Form( $structure, $settings );
		
		if( $form->isSuccessful() )
		{
			$user_module = MK_RecordModuleManager::getFromType('user');

			$email = new MK_BrandedEmail();
			$email
				->setSubject( $form->getField('subject')->getValue() )
				->setMessage( $form->getField('message')->getValue() );

			$users = $user_module->searchRecords(array(
				array('literal' => " `group` IN ('".implode("','", $user_groups)."') ")
			));
			
			if( !empty($users) )
			{
				foreach( $users as $current_user )
				{
					if( $email_address = $current_user->getEmail() )
					{
						$email->send( $email_address );
					}
				}
			}
?>
	<p class="simple-message simple-message-success">You have successfully sent emails to all users in your selected group(s).</p>
<?php
		}
		else
		{
			$group_list = array();
			$group_module = MK_RecordModuleManager::getFromType('user_group');
			foreach( $user_groups as $group_id )
			{
				$group = MK_RecordManager::getFromId($group_module->getId(), $group_id);
				$group_list[] = $group->getName();
			}
?>
	<p>Emailing users in the group(s): <em><?php print implode('</em>, <em>', $group_list); ?></em>.</p>
<?php

			print $form->render();
		}
	}
	else
	{
?>
	<p>Select which groups you want to email. You can select multiple groups.</p>
	<form class="clear-fix email-users" action="<?php print $this->uri( array('controller' => 'dashboard', 'section' => 'email-users') ); ?>" enctype="multipart/form-data" method="post">
    <ul class="email-users-groups clear-fix">
<?php
		if( !empty($this->groups) )
		{
			foreach( $this->groups as $group )
			{
?>
		<li>
        	<a href="">
                <h3><?php print $group->getName(); ?></h3>
                <p><?php print number_format($group->getTotalUsers()); ?> users</p>
                <input type="checkbox" name="user-groups[]" value="<?php print $group->getId(); ?>" />
            </a>
        </li>
<?php
			}
		}
?>
    </ul>
    <div class="clear-fix form-field form-field-submit"><div class="input"><input type="submit" name="email_submit" id="email_submit" value="Next"></div></div>
    </form>
<?php
	}
?>
</div>