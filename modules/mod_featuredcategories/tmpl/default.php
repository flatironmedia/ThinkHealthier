<?php
// no direct access
defined('_JEXEC') or die('Restricted accessd');

$class = "active";
if (isset($_GET['cat'])) $class = "inactive";

?>
<div class="mod_featuredcategories">
	<a class="<?php echo $class; ?>" href="/health-news"> All News </a><br>

	<?php

	foreach ($categories as $key => $category) {
		$class = "inactive";
	    if (isset($_GET['cat'])) if ($category->id == $_GET['cat']) $class = "active";
	    echo '<a class="'.$class.'" href="/health-news?cat='.$category->id.'"> '.$category->title.' </a><br>';
	}

	?>
</div>
