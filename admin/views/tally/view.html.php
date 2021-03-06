<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class MPollViewTally extends JView
{
	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'MPoll Polls Manager' ), 'continued' );
		JToolBarHelper::back('Polls','index.php?option=com_mpoll');
		$model = $this->getModel('tally');
		// Get data from the model
		$pollid = JRequest::getVar( 'poll' );
		$qdata=$model->getQuestions($pollid);
		$pdata=$model->getPoll($pollid); 
		$this->qdata = $qdata;
		$this->pdata = $pdata;
		
		parent::display($tpl);
	}
}
