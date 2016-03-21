<?php

/*
* @version        3.1.0
* @package      rightgrouprules
* @copyright     Copyright (C) 2014. All rights reserved.
* @license         GNU General Public License version 2 or later; see LICENSE.txt
* @author         Andrej,Simo, Vlado <andrejb86@gmail.com> - http://www.ogosense.com
*/


defined('_JEXEC') or die;

class plgContentArticleLinker extends JPlugin
{
    
    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);
    }
    public function onContentPrepare($context, &$article, &$params, $page)
    {


          // Getting text
            $text=$article->text;
            //echo('<pre>'.print_r($text, true).'</pre>'); 
            

            $matches=array();
             
            //Replacing
             $text = preg_replace_callback('/adam_id="(\d+),(\d+)"/', function($matches){
                
                
                $IDs = explode('"',$matches[0]);
                $num = explode(',',$IDs[1]);
                $prog = $num[0];
                $content = $num[1];


                $db = JFactory::getDbo();
                $query = $db->getQuery(true);

                $query->select(array('b.id','b.catid'))
                      ->from($db->quoteName('#__adam_mapping','a'))
                      ->join('INNER',$db->quoteName('#__content', 'b').' ON (' . $db->quoteName('a.content_id') . ' = ' . $db->quoteName('b.id') . ')')
                      ->where($db->quoteName('a.projectTypeID').' = '.$prog.' AND '.$db->quoteName('a.genContentId').' = '.$content);

                $db->setQuery($query);
                $temp = $db->loadObjectList();
                
           



                if (! empty($temp[0])) { 
                       
                $id = $temp[0]->id;
                $cat = $temp[0]->catid;
                   
                $url = JRoute::_(ContentHelperRoute::getArticleRoute($id, $cat));
                $url = 'href="'.$url.'"';
                //$url= JRoute::_("index.php?view=article&id=".$id."&catid=".$cat);
                } else {
                    $url = ''; 
                }

                 

                return $url;
            }
            , $text);
            

            $article->text=$text;

            
        }

        
} 