<?php
/*------------------------------------------------------------------------
# com_yoorecipe -  YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2012 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

abstract class JHtmlDateTimeUtils
{
	
	/**
	* getDate00h00m00s
	*/
	public static function getDate00h00m00s($date_obj) {
		return $date_obj->format('Y-m-d 00:00:00');
	}
	
	/**
	* getDate23h59m59s
	*/
	public static function getDate23h59m59s($date_obj) {
		return $date_obj->format('Y-m-d 23:59:59');
	}
	
	/**
	 * getTimeIntervalInDaysBetween
	 */
	public static function getTimeIntervalInDaysBetween($firstDateObj, $secondDateObj) {
	
		$dateInterval = date_diff($firstDateObj, $secondDateObj);
		return (int) $dateInterval->format('%R%a')+1;
	}

	/**
	 * formatTime
	 */
	public static function formatTime($duration, $D = null, $H = null, $M = null) {

		if ($D == null) {
			$D = JText::_('COM_YOORECIPE_DAY').' ';
		}
		if ($H == null) {
			$H = JText::_('COM_YOORECIPE_HOUR').' ';
		}
		if ($M == null) {
			$M = JText::_('COM_YOORECIPE_MIN');
		}
		
		if ($duration == 0) {
			return '0 min';
		}
		
		$d = floor ($duration / 1440);
		$h = floor ( ($duration - ($d * 1440)) / 60);
		$m = $duration - $d * 1440 - $h * 60;
		
		$result = '';
		
		if ($d > 0) {
			$result .= $d.$D ;
		}
		if ($h > 0) {
			$result .= $h.$H;
		}
		if ($m > 0) {
			$result .= $m.$M;
		}
		return $result;
	}
	
	/**
	* getFirstDayOfWeek
	*/
	public static function getFirstDayOfWeek ($start_date_obj) {
		
		$day_of_week = $start_date_obj->format('w');
		$interval = 0;
		
		switch ($day_of_week) {
			case 1:
			return $start_date_obj;
			break;
			
			case 0: // Sunday
				$interval = 6 ;
				$start_date_obj->sub(new DateInterval('P'.$interval.'D'));
			break;
			
			default:
				$interval = $day_of_week-1;
				$start_date_obj->sub(new DateInterval('P'.$interval.'D'));
			break;
				
		}
			
		return $start_date_obj;
	}
}