<?php


if ( (basename($_SERVER['SCRIPT_NAME']) == $member_page) && !empty($user_id) && ($allow_contact == 1)) { ?>
<!-- Hire Me Modal -->
<div class="en-modal en-effect-<?php echo $config->site->style->modal_effect; ?>" id="modal-contact-user">
  <div class="en-content">
    <div class="modal-title"><img src="<?php echo $config->site->logo_modal; ?>" alt="<?php echo $config->site->name; ?>"></div>
    <div class="contact-user-container">
      <iframe id="ContactUserFrame" src="contact-user.php?user=<?php echo $user_id; ?>" seamless="seamless"></iframe>
	  <a class="en-close circled-cross icon"></a>
    </div>
  </div>
</div><?php
} ?>

<?php if ( !$config->site->wordpress->strict_login && !$user->isAuthorized() ) { ?>
<div class="en-modal en-effect-<?php echo $config->site->style->modal_effect; ?>" id="modal-sign-in">
  <div class="en-content">
    <div class="modal-title"><img src="<?php echo $config->site->logo_modal; ?>" alt="<?php echo $config->site->name; ?>"></div>
    <div class="sign-in-container">
      <iframe id="SignInFrame" src="sign-in.php" seamless="seamless"></iframe>
      <a class="en-close circled-cross icon"></a>
	</div>
  </div> 
</div><!-- END EN-MODAL -->

<div class="en-modal en-effect-<?php echo $config->site->style->modal_effect; ?>" id="modal-sign-up">
  <div class="en-content">
    <div class="modal-title"><img src="<?php echo $config->site->logo_modal; ?>" alt="<?php echo $config->site->name; ?>"></div>
    <div class="sign-up-container">
      <iframe id="SignUpFrame" src="sign-up.php" seamless="seamless"></iframe>
      <a class="en-close circled-cross icon"></a>
	</div>
  </div> 
</div><!-- END EN-MODAL -->
<?php } ?>

<div class="en-modal en-effect-<?php echo $config->site->style->modal_effect; ?>" id="modal-contact">
  <div class="en-content">
    <div class="modal-title"><img src="<?php echo $config->site->logo_modal; ?>" alt="<?php echo $config->site->name; ?>"></div>
    <div class="contact-container">
      <iframe id="ContactFrame" src="contact.php" seamless="seamless"></iframe>
      <a class="en-close circled-cross icon"></a>
	</div>
  </div> 
</div><!-- END EN-MODAL -->

<div class="en-modal en-effect-<?php echo $config->site->style->modal_effect; ?>" id="modal-about">
  <div class="en-content">
    <div class="modal-title"><img src="<?php echo $config->site->logo_modal; ?>" alt="<?php echo $config->site->name; ?>"></div>
    <div class="about-container">
      <iframe id="AboutFrame" src="about.php" seamless="seamless"></iframe>
      <a class="en-close circled-cross icon"></a>
	</div>
  </div> 
</div><!-- END EN-MODAL -->

<div class="en-modal en-effect-<?php echo $config->site->style->modal_effect; ?>" id="modal-terms">
  <div class="en-content">
    <div class="modal-title"><img src="<?php echo $config->site->logo_modal; ?>" alt="<?php echo $config->site->name; ?>"></div>
    <div class="terms-container">
      <iframe id="TermsFrame" src="terms.php" seamless="seamless"></iframe>
      <a class="en-close circled-cross"><button class="pure-button pure-button-primary"><?php echo $langscape["Close"];?></button></a>
	</div>
  </div> 
</div><!-- END EN-MODAL -->

<div class="en-modal en-effect-<?php echo $config->site->style->modal_effect; ?>" id="modal-privacy">
  <div class="en-content">
    <div class="modal-title"><img src="<?php echo $config->site->logo_modal; ?>" alt="<?php echo $config->site->name; ?>"></div>
    <div class="privacy-container">
      <iframe id="PrivacyFrame" src="privacy-policy.php" seamless="seamless"></iframe>
      <a class="en-close circled-cross"><button class="pure-button pure-button-primary"><?php echo $langscape["Close"];?></button></a>
	</div>
  </div> 
</div><!-- END EN-MODAL -->

<div class="en-modal en-effect-<?php echo $config->site->style->modal_effect; ?>" id="modal-change-password">
  <div class="en-content">
    <div class="modal-title"><img src="<?php echo $config->site->logo_modal; ?>" alt="<?php echo $config->site->name; ?>"></div>
    <div class="password-container">
      <iframe id="ChangePasswordFrame" src="change-password.php" seamless="seamless"></iframe>
      <a class="en-close circled-cross icon"></a>
	</div>
  </div> 
</div><!-- END EN-MODAL -->

<div class="en-modal en-effect-<?php echo $config->site->style->modal_effect; ?>" id="modal-choose">
  <div class="en-content">
    <div class="modal-title"><img src="<?php echo $config->site->logo_modal; ?>" alt="<?php echo $config->site->name; ?>"></div>
    <div class="choose-container">
    <iframe id="ChooseFrame" src="upload-choose.php" data-src="upload-choose.php" seamless="seamless"></iframe>
	</div>
	<a class="en-close circled-cross icon"></a>
  </div> 
</div><!-- END EN-MODAL -->