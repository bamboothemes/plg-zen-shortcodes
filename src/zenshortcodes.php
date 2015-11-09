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


	function onAfterRoute() {
		
		$app = JFactory::getApplication();
		if($app->isAdmin()) {
			return;
		}
		
		$fa_css = $this->params->get('fa_css');
	
		if($fa_css) {
			$document = JFactory::getDocument();
			
			if($fa_css =="cdn") {
				$document->addStyleSheet('//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css');
			} else {
				$document->addStyleSheet(JURI::base() . 'media/zenshortcode/fontawesome/css/font-awesome.min.css');
			}
		}
	}
	
	
	
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
		$advanced_delim = $this->params->get('advanced_delim', 0);

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
		
		
		// Determine the prefix to use for the icons
		$fa_css = $this->params->get('fa_css');
		
		if($fa_css =="cdn" || $fa_css =="local") {
			$prefix = 'fa fa-';
		} else {
			$prefix = 'zen-icon zen-icon-';
		}
		
		
		// icons
		if($this->params->get('icon_select')) {
			
			include('includes/icons.php');
			
			foreach ($icons as $key => $icon) {
			
				$regex[$icon] = array('<span class=\''.$prefix.$icon.'\'></span>***code***', '#{zen-'.$icon.'}(.*?){/zen-'.$icon.'}#s');	
			}
		}
		
		
		// Custom Items
		$custom = $this->params->get('custom_icons');
		$custom_prefix = $this->params->get('custom_prefix', 'zen-icon zen-icon-');
		
		if($custom !=="") {
			$custom = explode(',', $custom);
			
			foreach ($custom as $icon){
			 	$regex[$icon] = array('<span class=\''.$custom_prefix.$icon.'\'></span>***code***', '#{zen-'.$icon.'}(.*?){/zen-'.$icon.'}#s');	
			}
		}
		
		
		// Custom Syntax
		$custom = $this->params->get('custom_syntax');
		
		if($custom !=="") {
			$custom = explode(',', $custom);
			
			foreach ($custom as $icon){
			 	$regex[$icon] = array('<span class=\''.$icon.'\'>***code***</span>', '#{zen-'.$icon.'}(.*?){/zen-'.$icon.'}#s');	
			}
		}
		
		
		// grids
		$grids = array('1','2','3','4','5','6','7','8','9','10','11','12');
		
		foreach ($grids as $key => $grid) {
			$regex[$grid] = array('<div class="zg-col zg-col-'.$grid.'">***code***</div>', '#{zen-'.$grid.'}(.*?){/zen-'.$grid.'}#s');	
		}
		
		
		$regex['row'] = array('<div class="zen-row no-row-margin clearfix">***code***</div>', '#{zen-row}(.*?){/zen-row}#s');
		

		// Button
		$regex['btn'] = array('***code***', '#{zen-btn}(.*?){/zen-btn}#s');
		
		// Button
		$regex['pre'] = array('<pre data-codetype="HTML">***code***</pre>', '#{zen-pre}(.*?){/zen-pre}#s');

		// Quote
		$regex['quote'] = array('<blockquote><p>***code***</p></blockquote>', '#{zen-quote}(.*?){/zen-quote}#s');
		
		// Author
		$regex['author'] = array('<small class="author">***code***</small>', '#{zen-author}(.*?){/zen-author}#s');
		
		// Line Break
		$regex['br'] = array('<br />', '#{zen-br}(.*?){/zen-br}#s');
		
		// Parse Codes
	

		foreach ($regex as $key => $value) {

			if (preg_match_all($value[1], $output, $matches, PREG_PATTERN_ORDER) > 0) {
				
				foreach ($matches[1] as $match) {

					$classes[] = $key;
					
					if($key == "btn" || $key == "mini") {
						$data = explode('|', $match);
						$link = $data[1];
						$text = $data[0];
						$code = '<a class=\''.$key.'\' href=\''.$link.'\'>'.$text.'</a>';
						
					} elseif($key == "pre") {
						
							$data = explode('|', $match);
							$content = $data[1];
							$title = $data[0];
							$code = '<pre data-type="'.$title.'"><span class="code-title">'.$title.'</span>'.$content.'</pre>';
							
						}
					
					else {
											
						$code = str_replace("***code***", $match, $value[0]);
						
						// See if we are adding any effects
						
						if($advanced_delim) {
							$delim = '|||';
						} else {
							$delim = '|';
						}
						
						$effects = strpos($match, $delim);
							
						if($effects) {
						
							$effects = explode($delim, $match);
							
							// Ok so we have some effects lets get the content first
							$content = explode(':', $match);
							
							if(isset($content[1])) {
								$code .= $content[1];
							}
							
							$effects = array_filter($effects);
							$code = str_replace($delim, '', $code);
							
							foreach ($effects as $effect) {
								$code = str_replace($effect, '', $code);
								$code = str_replace("<span class='", "<span class='zen-icon-".$effect." ", $code);
							}
						}	
					}				
					$output = str_replace("{zen-".$key."}".$match."{/zen-".$key."}", $code , $output);
				}
		 	}
		}
		unset(
			$key,
			$value,
			$regex,
			$match,
			$matches,
			$code
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
