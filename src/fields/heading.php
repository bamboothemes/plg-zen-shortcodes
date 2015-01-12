<?php
/**
 * @package		Joomla Bamboo Zen Grid Framework
 * @Type        Core Framework Files
 * @version		v1.0
 * @author		Joomal Bamboo http://www.joomlabamboo.com
 * @copyright 	Copyright (C) 2007 - 2010 Joomla Bamboo
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );
jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Renders a editors element
 *
 * @package 	Joomla.Framework
 * @subpackage		Form
 * @since		1.6
 */

class JFormFieldHeading extends JFormField
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	
	protected $type = 'heading';

	protected function getInput()
	{
		
		
		
	}
	
	public function getLabel() {
		$label     = (string) $this->element['label'];
		$desc     = (string) $this->element['desc'];
		return '<h3 style="border-top:1px solid #eee;padding-top:20px;margin-top:20px">'.$label.'</h3><p style="max-width:800px;background:#fafafa;padding:20px; border:1px solid #eee;font-weight:300;line-height:1.8em">' . $desc . '</span>';
	}

}
?>