<?php
/* 
Plugin Name: Love That Photo Gallery
Plugin URI: http://www.lovethatplugin.com
Version: first release. 0.0
Author: <a href="http://www.lovethatplugin.com/">Danielle Johnston</a>
Description: A gallery plugin for senior research
 
Copyright 2012 Danielle Johnston (email : dl2465@cs.ship.edu)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
	
	This program uses FancyBox 
	
	FancyBox
	Version: 1.3.4
	© 2008 - 2012 / fancybox.net
	

*/
if (!class_exists("LoveThatPhotoGallery")) 
{
	class LoveThatPhotoGallery 
	{
	//	var $adminOptionsName = "LoveThatPhotoGalleryAdminOptions";
		function LoveThatPhotoGallery() { //constructor
			
		}
			
		//creates the shortcode for the grid gallery
		//[addGallery]
		function addGridContent($atts) 
		{ 	//default id = 1;
			 extract(shortcode_atts(array(  
   				     "id" => '1'  
   				 ), $atts));  
			
			$params = $this->getGalleryParams($id);	
			$sql=$this->getGalleryImages($id);
			$num = count($sql);			
		
		   $str = '<div id="gridGallery"><center><table border="0" cellspacing="0" cellpadding="0">';  
          		
					$i = 0;
					for($tempRows = 0; $tempRows < $params['rows']; $tempRows++)
					{ 
                    	$str.='<tr><td>';
						for($tempCols = 0; $tempCols < $params['cols']; $tempCols++)
						{
							 if($i < $num)	
							 {
                   			  $str.='<a class="grid_'.$sql[$i]['galID'].'" rel="'.$sql[$i]['galID'].'" href="'.$sql[$i]['image'].'" title="'.$sql[$i]['imgtitle'].'"><img src="'.$sql[$i]['image'].'"/></a>';
                             
							  $i++;
							 }
						}
						$str.='</td></tr>';
			    	}            
              $str.='</table></center></div>';
			  $case= "grid_";
			   $script = loadJQueryOptions($id, $case);
				   $str.=$script;
			
			  
				return $str;
		}
		
		//adds the slider gallery for the shortcode
		//[addSlider]
		function addSlider($atts) { 	
				//default id = 1;
			 extract(shortcode_atts(array(  
   				     "id" => '1'  
   				 ), $atts)); 
			
			$sql= $this->get_slider_images($id);
		     
              $str = '<div id="containerSettings"><button id="leftButton" onclick=\'moveLeft()\'>&lt;</button><button id="rightButton" onclick=\'moveRight()\'>&gt;</button><div id="gallery">';
                    	
					foreach($sql as $img)
					{   	
					
                 $str.='<a class="slider_'.$img->galID.'" rel="'.$img->galID.'" title="'.$img->imgtitle.'" href="'.$img->image.'"><img src="'.$img->image.'"/></a>';
				
					} 
                  $str.='</div></div>';
				  $case= "slider_";
				   $script = loadJQueryOptions($img->galID, $case);
				   $str.=$script;
       		return $str;		  
		}
		
		//gets the images for [all] sliders
		function get_slider_images($id)
		{
			global $wpdb;
			$table_name=$wpdb->prefix."loveThatGallery_Images";
			$query="select * from $table_name WHERE galID = $id";
			$sql=$wpdb->get_results($query);
			return $sql;
		}	
		
		//gets the images from the gallery
		function getGalleryImages($id)
		{
			global $wpdb;
			$table_name=$wpdb->prefix."loveThatGallery_Images";
			$query="SELECT * FROM $table_name WHERE galID = $id";
			$sql=$wpdb->get_results($query, ARRAY_A);
			return $sql;
		}
		
		//gets the parameters from a specific gallery 
		//@param - $id: the id of the gallery you want
		//returns: the number of rows and cols this gallery has
		function getGalleryParams($id)
		{
			global $wpdb;
			$table_gallerySet=$wpdb->prefix."loveThatGallery_Params";
			$query2="SELECT * FROM $table_gallerySet WHERE galleryId = $id";
			$rowsCols = $wpdb->get_row($query2);
			$array['rows'] = $rowsCols->galNumRows;
			$array['cols'] = $rowsCols->galNumCols;
			return $array;
		}
		
		//creates two tables Params and Images
		//params holds rows and columns for each gallery
		//images holds all the images the user has uploaded so far and each images attrs
		function gallery_install()			// Function for installation
		{
		  global $wpdb;
		  //gallery	
		  $table_name = $wpdb->prefix . "loveThatGallery_Params"; 
		  $table_images = $wpdb->prefix . "loveThatGallery_Images";

		  $sql = "CREATE TABLE $table_name (
			galleryId int(11) NOT NULL auto_increment,
			galNumRows int(2) default 4,
			galNumCols int(2) default 3,
			galtitle varchar(255) default NULL,
			PRIMARY KEY  (galleryId)
		  )";
			  
		  $sql1 = "CREATE TABLE $table_images (
			imgId int(11) NOT NULL auto_increment,
			image varchar(255) default NULL,
			postedtime datetime default NULL,
			imgtitle varchar(255) default NULL,
			imgdesc text,
			galleryId int(11) default 1,
			PRIMARY KEY  (imgId)
		  )";
		
		  //allows wordpress to create these tables into the database	  
		  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		  dbDelta($sql);
		  dbDelta($sql1);
	   }
		
		// Function for uninstallation
		function gallery_uninstall()
		{
			global $wpdb;
			$table_name = $wpdb->prefix . "loveThatGallery_Params"; 
		    $table_images = $wpdb->prefix . "loveThatGallery_Images";
			$sql="DROP TABLE IF EXISTS $table_name;";
			$sql1="DROP TABLE IF EXISTS $table_settings;";
			$wpdb->query($sql);
			$wpdb->query($sql1);
		}
		
		//load the css style sheets and javascript files
		function ltpLoadScripts()
		{
			if (!is_admin()) 
			{
				//Header
			//add the stylesheet			
			wp_enqueue_style("slider", plugins_url( 'slider.css' , __FILE__ ), false, "1.0");
			wp_enqueue_style("fancyboxCss", plugins_url( 'fancyboxCss.css' , __FILE__ ), false, "1.0");
			//register scripts
			wp_register_script("fancybox", plugins_url('fancybox.js', __FILE__ ), array('jquery'), ""  ,false);
			wp_register_script("imageResize", plugins_url('imageResize.js' , __FILE__ ), array("jquery"), "",false);
			wp_register_script("resizeInit", plugins_url('resizeInit.js' , __FILE__ ), array("imageResize"), "",false);
					//slider:
			wp_register_script("jquerySlider2", plugins_url( 'jquerySlider2.js' , __FILE__ ), array("jquery"), "1.0", false);
			wp_register_script("jquery.jswipe-0.1.2", plugins_url( 'jquery.jswipe-0.1.2.js' , __FILE__ ), array("jquery",		
			"jquerySlider"), "",false);
			//enqueue scripts
			wp_enqueue_script("jquery");
			wp_enqueue_script("fancybox");
			wp_enqueue_script("resizeInit");
					//slider:
			wp_enqueue_script("jquerySlider2");
			wp_enqueue_script("jquery.jswipe-0.1.2");
			}
		}
		
		//tell wordpress which function to call to load the admin options page
		function admin_menu_options()
		{
			// Add a new top-level menu:
			//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function);
  			  add_menu_page(__('LTGallery','menu-test'), __('LTGallery','menu-test'), 'manage_options', 'manage-gallery','ltg_admin_options');

   			// Add a submenu to the custom top-level menu:
			//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function	
   			 add_submenu_page('manage-gallery', __('Settings','menu-test'), __('Settings','Combined-Options'), 'manage_options', 'Combined-Options', 'ltg_admin_options_grid');
		}
				
		//tells wordpress to use its own media uploader, and the js file used to handle it
		function my_admin_scripts() 
		{
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_enqueue_script('farbtastic');
			wp_register_script('lTGupload', plugins_url('lTGupload.js' , __FILE__ ), array("jquery","media-upload","thickbox"));
			wp_register_script('colorPicker2', plugins_url('colorPicker2.js', __FILE__), array("farbtastic", "jquery" ));
			wp_enqueue_script('lTGupload');			
  			wp_enqueue_script('colorPicker2');
		}
		
		//style used for the uploader on the admin page
		function my_admin_styles() 
		{
			wp_enqueue_style('thickbox');
			wp_enqueue_style('farbtastic');	
		}


	}
} //End Class of LoveThatPhotoGallery

if (class_exists("LoveThatPhotoGallery")) {
	$ltp_gallery = new LoveThatPhotoGallery();
}


//Actions and Filters and Header/Footer
if (isset($ltp_gallery)) {
		
	//if the admin page is being viewed, load the scripts and styles for it 
		if (isset($_GET['page']) && $_GET['page'] == 'manage-gallery') 
		{
			add_action('admin_print_scripts',  array(&$ltp_gallery,'my_admin_scripts'));
			add_action('admin_print_styles',  array(&$ltp_gallery,'my_admin_styles'));
		}
	
		if (isset($_GET['page']) && $_GET['page'] == 'Combined-Options') 
		{
			add_action('admin_print_scripts',  array(&$ltp_gallery,'my_admin_scripts'));
			add_action('admin_print_styles',  array(&$ltp_gallery,'my_admin_styles'));
		}
	
	//Actions
	add_action('init', array(&$ltp_gallery, 'ltpLoadScripts'), 1);
	add_action('admin_menu', array(&$ltp_gallery, 'admin_menu_options'), 1);

	//special calls
	add_shortcode('addGrid', array(&$ltp_gallery, 'addGridContent'));
	add_shortcode('addSlider', array(&$ltp_gallery, 'addSlider'));
	
	register_activation_hook(__FILE__,array(&$ltp_gallery, 'gallery_install'));	
	register_deactivation_hook(__FILE__,array(&$ltp_gallery, 'gallery_uninstall'));
}
//provide the link to displaying the upload images/create gallery page
function ltg_admin_options()
{
   include('adminPages/adminOptions.php');
}

//points to the script for the admin options page
function ltg_admin_options_grid()
{
	include('adminPages/adminOptionsGrid.php');	
}

//get all the options for a specific id
function getOptions($galleryid)
{
	global $wpdb;
	$table =$wpdb->prefix."loveThatGallery_Params";
	$query2="SELECT * FROM $table WHERE galleryId = $galleryid";
	$options = $wpdb->get_row($query2);
	return $options;
}

//load all the lightbox/css options for each galleryid
function loadJQueryOptions($galleryId, $case)
{
	//pull all the options from the database for given id
	$options = getOptions($galleryId);
	$script = '<script> 
 
 var escapeB = '.$options->escKey.'; 
 var closeB = '.$options->closeButton.';
 var overlay = '.$options->showOver.';
 var titlePos = "'.$options->titlePos.'";
 var hideOnOverlay = '.$options->clickOver.';
 var hideOnContent = '.$options->clickOn.';
 var overlayCol = "'.$options->overColor.'";
 var opac = '.$options->overOpac.';
 var bgCol = "'.$options->bgColor.'";
 var fColor = "'.$options->fontColor.'";
 var heightV = '.$options->imgHGrid.';
 var widthV = '.$options->imgWGrid.';
 var fStyle = "'.$options->fontStyle.'";
 var cols = '.$options->galNumCols.';
 jQuery("a.'.$case.$galleryId.'").fancybox(
											{
						\'titlePosition\' :  titlePos,
						\'overlayShow\'  :   overlay,
						\'overlayOpacity\':   opac,							 
						\'overlayColor\'  :   overlayCol,
						\'showCloseButton\' :  closeB,
						\'enableEscapeButton\' : escapeB,
						\'hideOnOverlayClick\' : hideOnOverlay,
						\'fontColor\' : fColor,
						\'fontStyle\' : fStyle,
						\'hideOnContentClick\' : hideOnContent
											}	);

 	var widthGrid = widthV * cols + 5;
	jQuery(\'#gridGallery img\').height(heightV);
	jQuery(\'#gridGallery img\').width(widthV);	
 	jQuery(\'#gridGallery\').css(\'background\', bgCol);
	jQuery(\'#gridGallery\').css(\'width\', widthGrid);
	jQuery(\'#containerSettings\').css(\'background\', bgCol);
						   
</script>';
	return $script;
}


?>