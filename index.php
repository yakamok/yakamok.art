<?php

#check variables are sanitized
function variable_check($input_check){
	if (ctype_alnum(str_replace("-","",str_replace("/","",$input_check)))){
		return $input_check;
	}else{
		header('location: /?page=404');
	}
}

## this function checks the image folder to see what folders already exist and return an array
function get_folders($foldername){

	$images_find = array();
	foreach (glob($foldername . "/*") as $filename) {
		if (is_dir($filename)){
			$images_find[str_replace($foldername . "/", "", $filename)] = $filename;
		}
	}
	return $images_find;
}

#get a list of images from a folder
function get_image_list($foldername){

	$images_find = array();
	foreach (glob($foldername . "/*.jpg") as $filename) {
		$images_find[] = $filename;
	}
	return $images_find;
}

#this builds up a menu from the list of folders containing  images
function create_main_menu($activelink, $sublink){
	$active = 'id="active"';
	echo '<ul>';
	foreach (get_folders("images") as $key => $value) {
		echo '<li class="dropdown">';
		if ($activelink === $key) {
			echo '<a ' . $active . ' href="#" class="dropbtn">' . $key . '</a>';

		}else {
			echo '<a href="#" class="dropbtn">' . $key . '</a>';		
		}
		echo '<div class="dropdown-content">';
		foreach (get_folders("images/" . $key) as $subkey => $value) {
			if ($sublink == $subkey) {
				echo '<a ' . $active . ' href=/?page=' . $key . "/" . $subkey . "&sub=" . $key . '&gal=1>' . $subkey . '</a>';
			}else {
				echo '<a href=/?page=' . $key . "/" . $subkey . "&sub=" . $key . '&gal=1>' . $subkey . '</a>';
			}
		}
		echo '</div>';
	}

	if ($activelink === "blog") {
		echo'<li><a ' . $active . ' href="/?page=blog&sub=blog" class="dropbtn">blog</a></li>';
	}else {
		echo'<li><a href="/?page=blog&sub=blog" class="dropbtn">blog</a></li>';
	}
	echo'<li><a href="/?sub=home" class="dropbtn">home</a></li>';
	echo '</li></ul>';
}


function get_sort_image_array($page_name) {

	$images_list = get_image_list("images/" . $page_name);
	$image_column_array = array(array(), array(), array(), array());
	$county = 0;
	for ($i=0; $i < count($images_list); $i++) {
		if (strpos($images_list[$i], "th.jpg") === False ) {
			$image_column_array[$county][$i] = $images_list[$i];
			$county++;
			if ($county == 4) {
				$county = 0;
			}
		}
	}
	return $image_column_array;
}

#this function displays the images in the folders that are found and pulls exif data from each image
function display_images($page_name){

	for ($i=0; $i < 4; $i++) {
		echo '<div class="column">';
		foreach (get_sort_image_array($page_name)[$i] as $key => $value) {
			$imSize = getimagesize(str_replace(".jpg", ".th.jpg", $value));
			echo '<a class="hovclass" href="' . $value . '" data-lightbox="set" >';
			echo '<img src="' . str_replace(".jpg", ".th.jpg", $value) . '" loading="lazy"' . ' width="' . $imSize[0] . '" height="' . $imSize[1] . '"></a>';
		}
		echo '</div>';
	}
}

#sanitize incoming data
if (isset($_GET['sub'])) {
	$subcat = variable_check($_GET['sub']);
}else {
	$subcat = NULL;
}

if (isset($_GET['page'])) {
	$pagecat = variable_check($_GET['page']);
}else {
	$pagecat = NULL;
}

if (isset($_GET['gal'])) {
	$gallset = variable_check($_GET['gal']);
}else {
	$gallset = NULL;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name=viewport content="width=device-width, initial-scale=1">
<title>Photography Portfolio</title>
<link rel="stylesheet" type="text/css" href="style.css"/>
<link rel="preload" href="/font/CormorantGaramond-Light.ttf" as="font" type="font/ttf" crossorigin>
<?php
if ($gallset === "1") {
	echo '<link rel="stylesheet" href="lightbox2-2.11.3/dist/css/lightbox.min.css">';
}
?>
</head>
<body oncontextmenu="return false;">
<div id="wrapper">
<div id="header">


<?php
if ($pagecat == "blog") {
	echo '<div id="title">Blog</div>';
}elseif ($pagecat != NULL){
	echo '<div id="title">' . ucfirst(str_replace('/', '', strstr($pagecat, '/'))) . '</div>';
}
if ($pagecat == NULL) {
	echo '<div id="title">Yakamok</div>';
}	
?>
<div id="nav">
<?php create_main_menu($subcat,str_replace('/', '', strstr($pagecat, '/'))); ?>
</div>
<div class="clear"></div>
</div>
<?php

if ($pagecat == "404") {
	echo "nothing to see here";
}


if ($subcat == "blog"){
	echo '<div class="blog">';
	require_once 'Parsedown.php';

	foreach (glob("posts/*.md") as $filename) {
		$findPosts[] = $filename;
	}

	natsort($findPosts);
	$orgArray = array_slice(array_reverse($findPosts), 0, 4);

	foreach ($orgArray as $key => $value) {
		echo Parsedown::instance()->text(file_get_contents($value));
		echo '<div class="divide"></div>';
	}
	echo '</div>';
}

if ($gallset === "1"){
	$bannerinfo = "images/".$pagecat."/info.txt";
	if (file_exists($bannerinfo) !== False){
		echo '<div id="details">';
		include($bannerinfo);
		echo '</div>';
	}
	echo '<div class="row">';
	display_images($pagecat);
	echo '</div>';
	echo '<script src="lightbox2-2.11.3/dist/js/lightbox-plus-jquery.min.js"></script>';
}

if ($subcat == NULL || $subcat == "home") {
	echo '<div class="blog">';
	echo '<br /><img src="home.jpg">';
	echo "</div>";
}
?>
<div class="clear"></div>
<div id="footer">
	Copyright on all Images <?php echo date("Y"); ?>
</div>

</div>
</body>
</html>
