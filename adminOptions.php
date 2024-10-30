<?php
//if the page is the uploader page
if (isset($_GET['page']) && $_GET['page'] == 'manage-gallery') 
{ 
	//Grab the image's filepath, the gallery to save it in,  the image title, and description
	$newGallery=$_POST['newGalTitle'];
	$filepath=$_POST['upload_image'];
	$galleryId=$_POST['IDS'];
	$filepath=trim($filepath);
	$cols=ltrim(rtrim(strip_tags($_POST['colNum'])));
	$rows=ltrim(rtrim(strip_tags($_POST['rowNum'])));
	//if the gallery is being created, create it first before saving the image
	if($newGallery)
	{
		if(!$cols)
		{
			$cols = 4;
		}
		if(!rows)
		{
			$rows = 10;
		}
		createNewGallery($newGallery, $rows, $cols);
	}	
	//save the image if there is a filepath
	if($filepath)
	{
		$title=ltrim(rtrim(strip_tags($_POST['imgTitle'])));
		$desc=ltrim(rtrim(strip_tags($_POST['imgDesc'])));

		save($filepath,$galleryId,$title,$desc);
	}
	
	$galNames = getGalleryNames();

	$selectInput = generateSelect('IDS', $galNames);

	?>
	<!-- The forms for uploading an image and putting in the details -->
	<div id="adminPage">
    <form action="" method="post" id="uploadForm" name="uploadForm">
	<tr valign="top">
	<th scope="row">Upload Image</th>
	<td><label for="upload_image">
	<input id="upload_image" type="text" size="36" name="upload_image" value="" />
	<input id="upload_image_button" type="button" value="Upload Image" />
	<br />Enter an URL or upload an image by uploading, then "insert into post".
	</label>
	<br />
	<br />
	<label>Enter the Image's Information Below</label>
	<br />
	<br />
	<table width = "600">
	<tr>
	<td>
	<label>Image Title:</label>
	<input type="text" name="imgTitle" id="imgTitle" size="20px" /><br />
	</td>
	<td>
	<label>Image Desc:</label>
	<input type="text" name="imgDesc" id="imgDesc" size="30px"  />
	</td>
    <tr>
    <td>
    <br />
    <br />
    <label>Choose which gallery to put it in: </label>
   	<?=$selectInput;?>  
    </td>
    </tr>
	<tr>
	<td>
    <label>-----------------------------------------------------</label>
    <br />
    <br />
       <label>Create a new gallery:</label>
    <input type="text" name="newGalTitle" id="newGalTitle" size="20px" />
    <br />
    <label>Number of Rows, Cols:</label>
    <input type="text" name="rowNum" id="rowNum" size="5px" />
    <input type="text" name="colNum" id="colNum" size="5px" />
    <br/>
    <br/>
	<input type="submit" class="button-primary" name="submit" value="Save" style="" />
	</form>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</div>
<?php 
} 


//gets all the galleries
function getGalleryNames()
{
	global $wpdb;
	$table_name=$wpdb->prefix."loveThatGallery_Params";
	$query="SELECT galtitle FROM $table_name";
	$sql=$wpdb->get_col($query);
	return $sql;
}

//generate the drop down box of gallery names
function generateSelect($name, $options) 
{
	$html = '<select name="'.$name.'">';
	$counter = 1;
	foreach ($options as $option) {
		$html .= '<option value='.$counter.'>'.$option.'</option>';
		$counter++;
	}
	$html .= '</select>';
	return $html;
}

//used to save the newly uploaded image into the database
function save($filepath, $galleryId, $imgTitle, $imgDesc)
{
	global $wpdb;
	$table_name=$wpdb->prefix."loveThatGallery_Images";		
	if($table_name)
	{
		//give a default gallery if none provided
		if(!$galleryId)
		{
			$galleryId = 1;
		}
		$rows_affected = $wpdb->query(
             $wpdb->prepare("
                             INSERT INTO $table_name
                             (image, postedtime, imgtitle, imgdesc, galID)
						      VALUES (%s, %s,%s, %s, %d)",
                             $filepath, date("y-m-d h:m:s"), $imgTitle, $imgDesc, $galleryId));
	}
}

//create a new gallery with a set amount of rows and cols
function createNewGallery($newGallery, $rows, $cols)
{
	global $wpdb;
	$table_name=$wpdb->prefix."loveThatGallery_Params";		
	$rows_affected = $wpdb->query(
             $wpdb->prepare("
                             INSERT INTO $table_name
                             (galNumRows, galNumCols, galtitle)
						      VALUES (%d,%d, %s)",
                              $rows, $cols, $newGallery));
	
}
?>