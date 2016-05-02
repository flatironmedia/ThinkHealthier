<?php

/**
* @version 		1.0.0
* @package 		com_content
* @copyright 	Copyright (C) 2014. All rights reserved.
* @license 		GNU General Public License version 2 or later; see LICENSE.txt
* @author 		Xander <avrhovac@ogosense.com> - http://www.ogosense.com
*/
$alphabet = array(
		'a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E', 'f' => 'F', 'g' => 'G', 'h' => 'H', 'i' => 'I', 'j' => 'J', 'k' => 'K', 'l' => 'L', 'm' => 'M', 'n' => 'N', 'o' => 'O', 'p' => 'P', 'q' => 'Q', 'r' => 'R', 's' => 'S', 't' => 'T', 'u' => 'U', 'v' => 'V', 'w' => 'W', 'x' => 'X', 'y' => 'Y', 'z' => 'Z'
	);
$juri = JUri::getInstance();
// no direct access
defined('_JEXEC') or die;

?>
<div class="azalphabet-wrapper">
	<div id="azalphabet_label_heading" class="azalphabet-label-heading"> 
		<div class="azalphabet-text">
			When you're looking for a trusted source on all things, medical, think healthier. 
			Our team of experts share the most up-to-date information on symptyoms, causes, diagnosis, treatment options and more.
		</div>
		<div class="azalphabet-adam-img">
			<img src="/images/powered-by-adam.png">
		</div>
		<div class="azalphabet-letters">
			<?php foreach ($alphabet as $ref => $letter) : 
				$path = $juri->getPath().'?'.$juri->getQuery();
				$pos = strpos($path, 'let=');
				if($pos !== false)
					$path = substr_replace($path, $ref, $pos + 4, 1);
				else
					$path.= '&let='.$ref;
			?>
				<span class="azalphabet-letter"><a href="<?php echo $path; ?>"><?php echo $letter; ?></a></span>
			<?php endforeach; ?>
		</div>
	</div>
	<?php if(!empty($this->cat->content)) : ?>
		<div id="azalphabet_most_viewed" class="azalphabet-most-viewed">
			<h2>Most Viewed</h2>
			<div class="azalphabet-mw-article-wrapper">
			<?php foreach($this->cat->content as $article) : ?>
				<div class="azalphabet-mw-article">
					<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->id.':'.strtolower(str_replace(' ', '-', $article->title)), $article->catid, '')); ?>">
						<?php echo $article->title; ?>
					</a>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if($this->content) : ?>
		<div id="azalphabet_letter_result" class="azalphabet-letter-result">
			<h3><?php echo strtoupper($this->letter); ?></h3>
			<div class="azalphabet-lr-article-wrapper">
			<?php foreach($this->content as $article) : ?>
				<div class="azalphabet-lr-article">
					<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->id.':'.strtolower(str_replace(' ', '-', $article->title)), $article->catid, '')); ?>">
						<?php echo $article->title; ?>
					</a>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
	<?php elseif($this->letter) : ?>
		<div id="azalphabet_letter_result" class="azalphabet-letter-result">
			<h3><?php echo strtoupper($this->letter); ?></h3>
			<div class="azalphabet-letter-result-final">
				No results found.
			</div>
		</div>
	<?php endif; ?>

</div>