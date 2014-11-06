<?php
/**
 * Zen Animate
 * @version 1.1
 * @author Joomla Bamboo
 * http://www.joomlabamboo.com
 * Based on JW AllVideos Plugin by Joomlaworks.gr and Xtypo from www.templateplazza.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgSystemzenshortcodes extends JPlugin {

	function onAfterRender() {

		// Get Plugin info
		jimport( 'joomla.html.parameter' );
		$plugin		= JPluginHelper::getPlugin('system','zenshortcodes');
		$app        = JFactory::getApplication();
		$document   = JFactory::getDocument();
		$doctype    = $document->getType();
		$output     = JResponse::getBody();
		$urlOption  = JRequest::getVar('option','none');
		$urlTask    = JRequest::getVar('task','none');
		
		$param		= new JForm( $plugin->params );
		$enabled   = $this->params->get('enabled', 1);

		if($app->isAdmin()) {
			return;
		}

		if($doctype !== 'html') {
			return;
		}

		if(($urlOption == 'com_content') and ($urlTask == 'edit')) {
			return;
		}

		unset($app, $doctype, $urlTask, $urlOption, $param, $plugin);

		// Array to store items in
		$regex = array();
		
		// icons
		include('includes/icons.php');
		
		foreach ($icons as $key => $icon) {
			$regex[$icon] = array('<span class="zen-icon zen-icon-'.$icon.'">***code***</span>', '#{zen-'.$icon.'}(.*?){/zen-'.$icon.'}#s');	
		}
		
		
		
		// icons
		include('includes/grids.php');
		
		foreach ($grids as $key => $grid) {
			$regex[$grid] = array('<div class="zg-col zg-col-'.$grid.'">***code***</div>', '#{zen-'.$grid.'}(.*?){/zen-'.$grid.'}#s');	
		}
		
		
		$regex['row'] = array('<div class="zen-row no-row-margin">***code***</div>', '#{zen-row}(.*?){/zen-row}#s');
		

		// Parse Codes
		$startcode       = '';
		$endcode         = '';

		foreach ($regex as $key => $value) {

			if (preg_match_all($value[1], $output, $matches, PREG_PATTERN_ORDER) > 0) {
				foreach ($matches[1] as $match) {

					$classes[] = $key;

					$code = str_replace("***code***", $match, $value[0]);
					$output = str_replace("{zen-".$key."}".$match."{/zen-".$key."}", $startcode.$code.$endcode , $output);
					
				}
		 	}
		}
		unset(
			$key,
			$value,
			$regex,
			$match,
			$matches,
			$code,
			$startcode,
			$endcode
			);

		

		unset(
			$document
			);

		JResponse::setBody($output);

		unset($output);

		return true;
	}
}
?>
