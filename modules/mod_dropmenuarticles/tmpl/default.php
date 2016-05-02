<div class="menu-module-articles">

<?php
// no direct access
defined('_JEXEC') or die('Restricted accessd');


//echo('<pre>'.print_r($articles, true).'</pre>');
//$article = $articles[0];
foreach ($articles as $key => $article) {
    $images = json_decode($article->images);
    $image = "";
    if (isset($images->image_intro) and !empty($images->image_intro)) $image = $images->image_intro;
    else if (isset($images->image_fulltext) and !empty($images->image_fulltext)) $image = $images->image_fulltext;
    else $image = "images/default_image.jpg";
    //echo('<pre>'.print_r($images, true).'</pre>');
?>


<div class="item">
    <div class="padding clearfix">

       

        <a href="<?php
              require_once (JPATH_SITE . '/components/com_content/helpers/route.php');
            if(class_exists ("ContentHelperRoute"))
            {
               $url =  JRoute::_(ContentHelperRoute::getArticleRoute($article->id.':'.$article->alias, $article->catid, ''));
               
            }
                    
                    echo $url; ?>" target="_self">

        <img class="top" src="<?php echo '/slir/w183-h122/'.htmlspecialchars($image); ?>" alt="image">
        


        <h4>
        

        <?php echo $article->title; ?>
        </a>
        </h4>


        <div class="xs_intro">
            <?php //echo strip_tags($article->introtext); 
            $article_desc = strip_tags($article->introtext);
            $article_desc = str_replace("\n", "", $article_desc);
            $article_desc = str_replace("\r", "", $article_desc);
            if (isset($article->introtext)) 
                if (! empty($article->introtext)) 
                    if (strlen($article_desc)>55) { 
                        echo substr($article_desc, 0, strpos($article_desc, ' ', 50))."...";
                    } else echo $article_desc;

            ?>
        </div>

    </div>
</div>

<?php } ?>

</div>