<?php
require_once '_inc.php';
require_once '_variables.php';


$module = MK_RecordModuleManager::getFromType('image'); //Image details

print '<?xml version="1.0" encoding="UTF-8" ?>';
print '<rss version="2.0">';
print '<channel>';
?>

	<title><?php print $langscape["Media"] . ' '  . $langscape["records for"] . ' ' . $config->site->title; ?></title>
	<link><?php print MK_Utility::serverUrl('/'); ?></link>
	<description><?php print $module->getName() . ' '  . $langscape["record list"]; ?></description>
	<image><?php echo $config->site->url . $config->site->logo; ?></image>
  
<?php
// We get an instance of the image & gallery module
$paginator = new MK_Paginator( MK_Request::getQuery('page', 1), MK_Request::getQuery('per_page', 5000) );

if ($config->site->media->enable_approval) {
		$search_criteria[] = array(
	    	'field' => 'approved', 'value' => '1'
	    );
		$records = $module->searchRecords($search_criteria, $paginator);
	
} else {

	$records = $module->getRecords($paginator);

}

foreach($records as $record)
{

$gallery_id = $record->getGallery() ? $record->objectGallery()->getId() : ''; //Gallery name the item belongs to.
$author_name = $record->getUser() ? $record->objectUser()->getDisplayName() : '';
$image_type = $record->getMetaValue('type_gallery');

//GET IMAGE INFO
$myFile = $record->getImage();
$ext = pathinfo($myFile , PATHINFO_EXTENSION);
$fs = filesize(dirname($_SERVER['SCRIPT_FILENAME']) . '/' . urldecode($myFile));

?>
	<item>
	    <title><?php print $record->getTitle(); ?></title>
	    <pubDate><?php echo $record->getDateAdded(); ?></pubDate>
	    <author><?php echo $author_name; ?></author>
	    <category domain="<?php echo $config->site->url . 'gallery/' . $gallery_id; ?>"></category>  
	    <link><?php echo $config->site->url . getImageTypeName($image_type) . '/' .$record->getImageSlug(); ?></link>
	    <enclosure url="<?php echo $config->site->url . $myFile; ?>" length="<?php echo $fs; ?>" type="image/<?php echo $ext; ?>" />
	    <description><?php $desc = $record->getDescription(); print ($desc) ? substr($record->getDescription(), 0, 250) . "...": ""; ?></description>
	</item>

<?php
}
print '</rss>';
print '</channel>';
?>