<?php


$num_items = ($deviceType == 'phone')?24:20;

$total_records        = $user_module->getTotalRecords();
$user_catgegory_list  = $users_types_array_combined;
$paginator            = new MK_Paginator( MK_Request::getQuery('page', 1), $num_items );
$user_search_criteria = array(); //Setup the search criteria array


if ($config->site->members->sort_by == 'ALPHA') {	

	//ORDER BY DISPLAY NAME
	$order_by_field = $field_module->searchRecords(array(
	    array('field' => 'module', 'value' => $user_module->getId()),
	    array('field' => 'name', 'value' => 'display_name')
	));
	$direction = "ASC";

} else {

	//ORDER BY MEDIA COUNT
	$order_by_field = $field_module->searchRecords(array(
	    array('field' => 'module', 'value' => $user_module->getId()),
	    array('field' => 'name', 'value' => 'media_count')
	));
	$direction = "DESC";
	
}

$order_by_field = array_pop( $order_by_field );
            
$options              = array(
                            'order_by' => array(
                                'field' => $order_by_field->getId(),
                                'direction' => $direction
                            )
                        );

                        
if( !empty( $section ) ) { //Specified user type.
    
    $key = array_search(ucfirst($section), $user_catgegory_list); //Convert section to category id.
    
    $user_search_criteria[] = array(
        'literal' => "(`category` = " . $key . ")"
    );
    
    $active = '';
    
    $member_type = ucfirst($section);
    
} else { //All.

    $active = 'pure-button-active';
    
    $member_type = ''.$langscape["Members"].'';

}

$users = $user_module->searchRecords($user_search_criteria, $paginator, $options); 

$plural = new Inflect; ?>

<?php

	if ( ( ($deviceType <> 'phone') || !$config->site->mobile->enable_responsive_phone) && ($config->site->ads->enable_members_top) ) { ?>

	<!-- 728x90 Ad Banner -->
	<?php include ('includes/ad-top.php'); ?> 

<?php 
	}
?>

<section class="content members pure-g-r"> 


<div class="members-nav">
    
    <a href="members" title="<?php echo $langscape["All"];?>">
        <div class="pure-button pure-button-primary  <?php echo $active; ?>">
            <span><?php echo $langscape["All"];?> <?php //echo '(' . $total_records . ')'; ?></span>
        </div>
    </a><?php

    foreach ( $user_catgegory_list as $key => $value ) {
    
        if ($value == ucfirst($section)) { //Set active class for button state. DH
        
            $active = 'pure-button-active';
        
        } else {
        
            $active = ''; //Reset the active class for the next loop. DH
        
        }
    
        $total_records = $user_module->getTotalRecords(array(
            array('field' => 'category', 'value' => $key)
        )); 
        
        if($total_records > 0) { ?>
       
            <a href="members/<?php echo strtolower($value); ?>" title="<?php echo $value; ?>">
                <div class="pure-button pure-button-primary <?php echo $active; ?>">
                    <span><?php echo $plural->pluralize($value); //. ' (' . $total_records . ')'; ?></span>
                </div>
            </a><?php
        
        }
    } ?>
    
</div>     

<!-- Members grid-cs-cs Starts Here -->
<?php

if( !empty($users) ) { 

    $count = $paginator->getTotalRecords();?>
    
  <h2 class="sub-title"><?php echo $plural->pluralize_if($count, $member_type); ?></h2>
  
    <ul data-autoload="true" class="members" data-page-type="members"><?php
        
        foreach( $users as $member ) 	{
            
            include ('includes/member-box.php');
        
        } ?> 
        
    </ul>

	<?php 
		if ($config->site->grid->pagination_type == 0 || $config->site->grid->pagination_type == 2) { //LOAD MORE BUTTON & INFINITE SCROLL
	?>

    <div class="paginator clear-fix">
    
        <div class="pure-button pure-button-primary load-more ladda-button">
            <span class="ladda-label"><?php 
                echo $paginator->render('members.php?page={page}'.(!empty($section) ? '&amp;section='.$section : '')); ?>
            </span>
        </div>
        
    </div>
	<?php 
		} elseif ($config->site->grid->pagination_type == 1) { //PAGE NUMBERS
	?>
	
    <div class="paginator2 clear-fix"><?php echo $paginator->render('members.php?page={page}'.(!empty($section) ? '&amp;section='.$section : '')); ?></div>

	<?php 
		}

} else { ?>

  	<p class="alert alert-warning"><?php echo $langscape["There are no members"];?></p><?php
  
} ?>

</section>