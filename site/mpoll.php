<?php
// no direct access
defined('_JEXEC') or die('Restricted access');


// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

// Require specific controller if requested
if($controller = JRequest::getVar('controller')) {
	require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
}
// Create the controller

$doc = &JFactory::getDocument();
$doc->addStyleSheet('media/com_mpoll/mpoll.css');


$classname	= 'MPollController'.$controller;
$controller = new $classname( );
		
// Perform the Request task
$controller->execute( JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();
//if (JRequest::getVar('view') != 'certif') echo '<h5>ContinuEd FrontEnd v0.6.3.24beta &#8226; &copy;2008 Corona Productions &#8226; For Development Use Only &#8226; Not for review &#8226; Do not take internally</h5>';
?>

