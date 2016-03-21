<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_custom
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$moduleIntro = $params->get ('module-intro');
?>
<script src="http://a.vimeocdn.com/js/froogaloop2.min.js"></script>

<?php  if($moduleIntro) : ?> 
	<div class="module-intro text-center"><?php echo $moduleIntro; ?></div>
<?php endif; ?>

<div class="custom<?php echo $moduleclass_sfx ?>" <?php if ($params->get('backgroundimage')): ?> style="background-image:url(<?php echo $params->get('backgroundimage');?>)"<?php endif;?> >
	<?php echo $module->content;?>
</div>
