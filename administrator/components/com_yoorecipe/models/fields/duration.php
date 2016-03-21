<?php
/*------------------------------------------------------------------------
# com_yoorecipe -  YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2011 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
/**
* Form Field class for the Joomla Framework.
*
* @package      Joomla.Framework
* @subpackage   Form
* @since      1.6
*/
class JFormFieldDuration extends JFormField
{
   /**
    * The form field type.
    *
    * @var      string
    * @since   1.6
    */
   protected $type = 'duration';
   
   /**
    * Method to get the field input markup.
    *
    * @return   string   The field input markup.
    * @since   1.6
    */
	protected function getInput()
	{
		$html = array();
		
		$html[] = $this->selectDaysFromDuration($this->element['prefix'], $this->value);
		$html[] = $this->selectHoursFromDuration($this->element['prefix'], $this->value);
		$html[] = $this->selectMinutesFromDuration($this->element['prefix'], $this->value);
		
		return implode("\n", $html);
	}
   
	/**
	 * Generate a select list for duration in days
	 */
	private function selectDaysFromDuration($pfx_name, $duration) {
		
		$html = array();
		
		$nbDays = $this->getDaysFromDuration($duration);
		$html[] = '<div class="input-append">';
		$html[] = '<select class="input-mini" name="'.$pfx_name.'_days" id="'.$pfx_name.'_days">';
		for ($i=0 ; $i < 7 ; $i++) {
			if (strcmp($nbDays,$i) == 0) {
				$html[] = '<option value="'.$i.'" selected="selected">'.$i.'</option>';
			} else {
				$html[] = '<option value="'.$i.'">'.$i.'</option>';
			}
		}
		$html[] = '</select>';
		$html[] = '<div class="add-on">'.JText::_('COM_YOORECIPE_DAY').'</div>';
		$html[] = '</div>';
			
		return implode("\n", $html);
	}
	
	/**
	 * Generate a select list for duration in hours
	 */
	private function selectHoursFromDuration($pfx_name, $duration) {
		
		$html = array();
		
		$nbDays = $this->getHoursFromDuration($duration);
		$html[] = '<div class="input-append">';
		$html[] = '<select class="input-mini" name="'.$pfx_name.'_hours" id="'.$pfx_name.'_hours">';
		for ($i=0 ; $i < 24 ; $i++) {
			if (strcmp($nbDays,$i) == 0) {
				$html[] = '<option value="'.$i.'" selected="selected">'.$i.'</option>';
			} else {
				$html[] = '<option value="'.$i.'">'.$i.'</option>';
			}
		}
		$html[] = '</select>';
		$html[] = '<div class="add-on">'.JText::_('COM_YOORECIPE_HOUR').'</div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
	
	/**
	 * Generate a select list for duration in minutes
	 */
	private function selectMinutesFromDuration($pfx_name, $duration) {
		
		$html = array();
		
		$nbDays = $this->getMinutesFromDuration($duration);
		$html[] = '<div class="input-append">';
		$html[] = '<select class="input-mini" name="'.$pfx_name.'_minutes" id="'. $pfx_name.'_minutes">';
		for ($i=0 ; $i < 60 ; $i = $i+5) {
			if (strcmp($nbDays,$i) == 0) {
				$html[] = '<option value="'.$i.'" selected="selected">'.$i.'</option>';
			} else {
				$html[] = '<option value="'.$i.'">'.$i.'</option>';
			}
		}
		$html[] = '</select>';
		$html[] = '<div class="add-on">'.JText::_('COM_YOORECIPE_MIN').'</div>';
		$html[] = '</div>';
			
		return implode("\n", $html);
	}
	
	/**
	 * getDaysFromDuration
	 */
	private function getDaysFromDuration($duration) {
		return floor($duration / 1440);
	}
	
	/**
	 * getHoursFromDuration
	 */
	private function getHoursFromDuration($duration) {
		return floor ( ($duration - $this->getDaysFromDuration($duration) * 1440) / 60);
	}
	
	/**
	 * getMinutesFromDuration
	 */
	private function getMinutesFromDuration($duration) {
		return $duration % 60;
	}
}