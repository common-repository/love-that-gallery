<?php

//If on the settings options page...
if (isset($_GET['page']) && $_GET['page'] == 'Combined-Options') 
{ 
	?>
	<div id="adminPage">
    <h1>Options Page</h1>
    Choose a Gallery to load and edit:
    <form method="post" action="">
    <?php
	$galNames = getGalleryNames();
	$selectInput = generateSelect('IDS', $galNames);
	?>
    Gallery: <?=$selectInput;?>  
    <input name="Submit1" type="submit" class="button-primary" value="Load Gallery Options" />
    </form>
    <br>
    <br>
    
    <!-- retrieve all the settings for the chosen gallery -->
    <?php 
	if(isset($_POST['Submit2'])) //if any of the fields are changed, save to db
	{
			$gal=$_POST['IDS'];
			$close=$_POST['closeMethods'];
			$fontStyles=$_POST['fontStyles'];
			$finalFont = performStylesCheck($fontStyles);
			$fontColor=$_POST['colorFont'];
			$bgColor=$_POST['color'];
			$overColor=$_POST['colorOver'];
			$titlePos=$_POST['titlePos'];
			$showOver=$_POST['showOver'];
			$opac=$_POST['opacity'];
			$gridHeight=$_POST['gridHeight'];
			$gridWidth=$_POST['gridWidth'];
			saveSettings($gal, $fontColor, $finalFont, $bgColor, $overColor, $close, $titlePos, $showOver, $opac, $gridHeight, $gridWidth);	
	}
	if(isset($_POST['Submit1']))
	{
		$fontNames = array("Arial", "Bookman", "Comic Sans MS", "Courier New", "Georgia", "Helvetica", "Tahoma", "Times New Roman",
		"Verdana");
		$galleryId=$_POST['IDS'];
	
		if($galleryId) //if the drop down for the gallerys has been picked, load the form for that gallery
		{
		
			  $settings = getGallerySettings($galleryId);
			  $fontStyle = generateFont('fontStyles', $fontNames, $settings);
			  ?>
	 
	     
		  <form method="post" action="">
          <input type="hidden" name="IDS" value="<?php echo $_POST['IDS']; ?>" /> 
		  <table>
		  <tr>
		  <td><b>Options</b></td><td><b>Gallery Values</b></td>
		  </tr>
          <div class="color-picker" style="position: relative;">
		 <tr>
		  <td>Background Color</td><td>
				<input type="text" name="color" id="color" value="<?php echo "$settings->bgColor" ?>" />
			  <div style="position: absolute;" id="colorpicker"></div></td>
		   </tr>
		   <tr>
		   <td>Font Color</td><td>
			<input type="text" name="colorFont" id="colorFont" value="<?php echo "$settings->fontColor" ?>" />
			  <div style="position: absolute;" id="colorpickerF"></div></td>
			</tr>
            <tr>
		   <td>Image Overlay Color</td><td><input type="text" name="colorOver" id="colorOver" value="<?php echo "$settings->overColor" ?>" />
			  <div style="position: absolute;" id="colorpickerO"></div></td>

			</tr>
            </div>
			<tr>
			<td>Font Style</td><td><?=$fontStyle;?></td>
			</tr>
			<tr>
			<td>Exit Image via</td>
			<td><Input type = 'Checkbox' Name ='closeMethods[]' value ="Esc" <?php if($settings->escKey =="true"){ echo "checked='checked'"; } ?>> Esc key
				  <Input type = 'Checkbox' Name ='closeMethods[]' value ="X Button" <?php if($settings->closeButton =="true"){ echo "checked='checked'"; } ?>> Close button
				  <Input type = 'Checkbox' Name ='closeMethods[]' value ="Click off image" <?php if($settings->clickOver =="true"){ echo "checked='checked'"; } ?>> Click off image
                  <Input type = 'Checkbox' Name ='closeMethods[]' value ="Click on image" <?php if($settings->clickOn =="true"){ echo "checked='checked'"; } ?>>Click on image</td>
			</tr>
            <tr>
            <td>Caption Position</td>
            <td><Input type = 'radio' Name ='titlePos[]' value ="float" <?php if($settings->titlePos =="float"){ echo "checked='checked'"; } ?>>Float
				  <Input type = 'radio' Name ='titlePos[]' value ="inside" <?php if($settings->titlePos =="inside"){ echo "checked='checked'"; } ?>>Inside
				  <Input type = 'radio' Name ='titlePos[]' value ="outside" <?php if($settings->titlePos =="outside"){ echo "checked='checked'"; } ?>>Outside
                    <Input type = 'radio' Name ='titlePos[]' value ="over" <?php if($settings->titlePos =="over"){ echo "checked='checked'"; } ?>>Over</td>
            </tr>
            <tr>
            <td>Show Overlay</td>
            <td><Input type = 'radio' Name ='showOver[]' value ="true" <?php if($settings->showOver =="true"){ echo "checked='checked'"; } ?>>True
				  <Input type = 'radio' Name ='showOver[]' value ="false" <?php if($settings->showOver =="false"){ echo "checked='checked'"; } ?>>False</td>
            </tr>  
            <tr>
            <td>Overlay Opacity</td>
            <td><Input type='text' name="opacity" id="opacity" value="<?php echo "$settings->overOpac" ?>" /></td>
            </tr>
            <tr>
            <td></td><td>Enter values 0.0 - 1.0</td>
            </tr>
            <tr>
            <td>Grid Thumbnail Height</td>
            <td><Input type='text' name="gridHeight" id="gridHeight" value="<?php echo "$settings->imgHGrid"?>"/></td>
            </tr>  
             <tr>
            <td>Grid Thumbnail Width</td>
            <td><Input type='text' name="gridWidth" id="gridWidth" value="<?php echo "$settings->imgWGrid"?>"/></td>
            </tr>                  
		   </table>   
			  <p class="submit">
		  <input name="Submit2" type="submit" class="button-primary" value="Save Changes" />
			  </p >
		</form>
	  <?php
			}		
	}
	?>  
    </div>	
<?php 
}


//retrieve all the settings from the gallery selected to be used in the form
function getGallerySettings($galleryId)
{
	global $wpdb;
	$table_name=$wpdb->prefix."loveThatGallery_Params";
	$query="SELECT * FROM $table_name WHERE galleryId = $galleryId";
	$sql=$wpdb->get_row($query);
	return $sql;	
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

//creates the drop down box for the font styles
function generateFont($styleName, $fontNames, $settings)
{
	$html = '<select name="'.$styleName.'">';	
	foreach ($fontNames as $fontName)
	{
		if($settings->fontStyle == $fontName)
		{
			$html .= '<option value='.$fontName.'>'.$fontName.'</option>';
		}		
	}
	
	foreach ($fontNames as $fontName)
	{
	
	 if($settings->fontStyle != $fontName)
		{
			$html .= '<option value='.$fontName.'>'.$fontName.'</option>';
		}
	}
	$html .= '</select>';
	return $html;
}

//creates the drop down box for each gallery
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

//save all the settings in the form
function saveSettings($galleryId, $fontColor, $fontStyles, $bgColor, $overColor, $close, $titlePos, $showOver, $opac, $gridHeight, $gridWidth)
{
	if($opac > 1.0 || $opac < 0.0)
	{
		$opac = 0.3;	
	}
	$escVar = "false";
	$closeBVar = "false";
	$clickOffVar = "false";
	$clickOnVar = "false";
	foreach($close  as  $value) 
	{
		if($value == "Esc")
		{
			$escVar = "true";
		}else if($value == "X Button")
		{
			$closeBVar = "true";
		}else if($value == "Click off image")
		{
			$clickOffVar = "true";
		}else if($value == "Click on image")
		{
			$clickOnVar = "true";	
		}
	}
	echo $check_msg;
	echo "Changes have been saved.";
	global $wpdb;
	$table_name=$wpdb->prefix."loveThatGallery_Params";		
	if($table_name)
	{
		$wpdb->update($table_name, array( 'bgColor' => $bgColor,
										   'fontColor' => $fontColor,
										   'fontStyle' => $fontStyles,
										   'overColor' => $overColor,
										   'escKey'    => $escVar,
										   'closeButton' => $closeBVar,
										   'clickOver' => $clickOffVar,
										   'clickOn' => $clickOnVar,
										   'titlePos' => $titlePos[0],
										   'showOver' => $showOver[0],
										   'overOpac' => $opac,
										   'imgHGrid' => $gridHeight,
										   'imgWGrid' => $gridWidth),
            array( 'galleryId' => $galleryId ));
	}	
}

//to make sure the full name of the font gets to the database
function performStylesCheck($fontStyles)
{
	if($fontStyles == 'Courier')
	{
		$fontStyles = "Courier New";
	}else if($fontStyles == 'Times')
	{
		$fontStyles = "Times New Roman";
	}else if($fontStyles == 'Comic')
	{
		$fontStyles = "Comic Sans MS";
	}
	
	return $fontStyles;
}

?>