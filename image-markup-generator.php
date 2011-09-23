<?php
/**
 * Image markup
 *
 * Adds textfields to the image page, to allow you to copy and paste image links to forums, other websites, etc
 *
 * @author Marcus Wong (wongm)
 * @package plugins
 */

$plugin_description = gettext("Adds textfields to the image page, to allow you to copy and paste image links to forums, other websites, etc.");
$plugin_author = "Marcus Wong (wongm)";
$plugin_version = '1.0.0'; 
$plugin_URL = "http://code.google.com/p/wongm-zenphoto-plugins/";
$option_interface = 'imageMarkupOptions';

/**
 * Plugin option handling class
 *
 */
class imageMarkupOptions {
	
	function imageMarkupOptions() {
		setOptionDefault('imageMarkup_fields', "%IMAGE_TITLE%\n[url=%IMAGE_PAGE_URL%][img]%IMAGE_SIZED_URL%[/img][/url]");
		setOptionDefault('imageMarkup_permission', "admin");
		setOptionDefault('imageMarkup_size_default', 'default');
		setOptionDefault('imageMarkup_size_value', '');
	}

	function getOptionsSupported() {
		$sizelist = array(gettext("Default") => "default",gettext("Custom") => "custom");
		$permissionlist = array(gettext("Admin only") => "admin",gettext("Everyone") => "everyone");
		return array(	gettext('Custom image size') => array('key' => 'imageMarkup_size_value', 'type' => OPTION_TYPE_TEXTBOX,
										'order' => 3,
										'desc' => gettext("Size in pixels for IMAGE_SIZED_URL, if you are not using the values set elsewhere in Zenphoto.")),
									gettext('Permission') => array('key' => 'imageMarkup_permission', 'type' => OPTION_TYPE_RADIO, 'buttons' => $permissionlist,
										'order' => 1,
										'desc' => gettext("Who can see the fields on the image page.")),
									gettext('Image size') => array('key' => 'imageMarkup_size_default', 'type' => OPTION_TYPE_RADIO, 'buttons' => $sizelist,
										'order' => 2,
										'desc' => gettext("What size images should be used.")),
									gettext('Markup to be generated') => array('key' => 'imageMarkup_fields', 'type' => OPTION_TYPE_TEXTAREA,
										'order' => 4,
										'desc' => gettext("String that specifies the format of the markup to be generated. Multiple items are possible, semi-colon delimited. Allowable fields:<br/>%IMAGE_TITLE%<br/>%IMAGE_DESCRIPTION%<br/>%IMAGE_PAGE_URL%<br/>%IMAGE_DATE%<br/>%IMAGE_THUMBNAIL_URL%<br/>%IMAGE_FULLSIZE_URL%<br/>%IMAGE_SIZED_URL%<br/>%ALBUM_TITLE%"))
		);
	}
}

function printImageMarkupFields() {

	if (zp_loggedin() OR getOption('imageMarkup_permission') == 'everyone') 
	{
		global $_zp_current_image;
		
		$path = str_replace($_zp_current_image->filename, '', $_zp_current_image->webpath);
		$path = str_replace('albums/', '', $path);
		$i = 1;
		
		$markupBases = split(";",getOption('imageMarkup_fields'));
?>		<div class="generatedMarkupBoxes">
		<script type="text/javascript">
		function SelectAllGeneratedMarkup(id)
		{
    		document.getElementById(id).focus();
    		document.getElementById(id).select();
		}
		</script>
<?	
		foreach ($markupBases AS $markupStringToMerge)
		{
			if (strlen($markupStringToMerge) > 0)
			{
				if (getOption('imageMarkup_size_default') == 'default')
				{
					$size = getOption('image_size');
				}
				else
				{
					$size = getOption('imageMarkup_size_value');
				}
				
				$markupStringToMerge = str_replace('%IMAGE_TITLE%', $_zp_current_image->getTitle(), $markupStringToMerge);
				$markupStringToMerge = str_replace('%IMAGE_DESCRIPTION%', $_zp_current_image->getDesc(), $markupStringToMerge);
				$markupStringToMerge = str_replace('%IMAGE_PAGE_URL%', "http://".$_SERVER['HTTP_HOST'].getImageLinkURL(), $markupStringToMerge);
				$markupStringToMerge = str_replace('%IMAGE_DATE%', getImageDate(), $markupStringToMerge);
				$markupStringToMerge = str_replace('%IMAGE_THUMBNAIL_URL%', "http://".$_SERVER['HTTP_HOST'] . $_zp_current_image->getThumb(), $markupStringToMerge);
				$markupStringToMerge = str_replace('%IMAGE_FULLSIZE_URL%', "http://".$_SERVER['HTTP_HOST'] . $_zp_current_image->getFullImage(), $markupStringToMerge);
				$markupStringToMerge = str_replace('%IMAGE_SIZED_URL%', "http://".$_SERVER['HTTP_HOST'] . $_zp_current_image->getSizedImage($size), $markupStringToMerge);
				$markupStringToMerge = str_replace('%ALBUM_TITLE%', $_zp_current_image->getAlbum()->getTitle(), $markupStringToMerge);
			
				echo '<textarea name="markup' . $i . '" id="markup' . $i . '" cols="100" rows="2" onClick="SelectAllGeneratedMarkup(\'markup' . $i . '\')">'.$markupStringToMerge.'</textarea><br/>';
				$i++;
			}
		}
		
		echo "</div>";
	}
}

?>