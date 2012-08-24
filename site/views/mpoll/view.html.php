<?php

jimport( 'joomla.application.component.view');


class MPollViewMPoll extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		$params = $mainframe->getPageParameters();
		$model =& $this->getModel();
		$pollid = JRequest::getVar( 'poll' );
		$task = JRequest::getVar('task');
		$jumptask = JRequest::getVar('jumptask');
		
		if ($task == "gotopoll") {
			$mainframe->redirect(JRoute::_("index.php?option=com_mpoll&task=".$jumptask."&poll=".$pollid,false));
		}
		
		if (empty($pollid)) $pollid = $params->get('poll');
		if (empty($task))$task=$params->get('task');
		$showlist = $params->get('showlist');
		$showstats = $params->get('showstats');
		$rtmpl = $params->get('rtmpl');
		$itemid = JRequest::getVar( 'Itemid' );
		$casting = JRequest::getVar( 'casting' );
		//check if logged in, logging in is REQUIRED
		$user =& JFactory::getUser();
		$guest = $user->guest ? true : false;
		$pdata=$model->getPoll($pollid); 
		$polllist = $model->getPolls($pdata['poll_cat']);
		if (empty($pdata)) { $task='notfound'; $msg='This Poll is currently unavailble.'; }
		else {
			//date stuff
			if ((strtotime($pdata['poll_start']) > strtotime(date("Y-m-d H:i:s"))) && $pdata['poll_start'] != '0000-00-00 00:00:00') { $task='notfound'; $msg='This Poll is currently unavailble.'; }
			if ((strtotime($pdata['poll_end']) < strtotime(date("Y-m-d H:i:s"))) && $pdata['poll_start'] != '0000-00-00 00:00:00') { $task='notfound'; $msg='This Poll is currently unavailble.'; }
			
		}
		$qdata=$model->getQuestions($pollid);
		if ($pdata['poll_only']) {
			if (!$guest) $casted=$model->getCasted($pollid);
			else $casted=false;
		} else {
			$casted=false;
		}
		$this->assignRef('task',$task);
		$this->assignRef('pdata',$pdata);
		$this->assignRef('qdata',$qdata);
		$this->assignRef('guest',$guest);
		$this->assignRef('polllist',$polllist);
		$this->assignRef('showlist',$showlist);
		$this->assignRef('showstats',$showstats);
		$this->assignRef('listloc',$listloc);
		if (!$casting && $task=='ballot' && !$casted) {
			parent::display($tpl);
		} else if ($casting && $task=='ballot' && !$casted) {
			//save vote results
			$se=$model->saveBallot($pollid);
			$url = 'index.php?option=com_mpoll&task=results&poll='.$pollid;
			if ($rtmpl) $url .= '&tmpl='.$rtmpl;
			$mainframe->redirect(JRoute::_($url,false));
		} else if (($casted && $task=='ballot') || $task=='results') { 
			$task='results';
			$fcast = $model->getFirstCast($pollid);
			$this->assignRef('fcast',$fcast);
			$lcast = $model->getLastCast($pollid);
			$this->assignRef('lcast',$lcast);
			$ncast = $model->getNumCast($pollid);
			$this->assignRef('ncast',$ncast);
			parent::display($tpl);
			
		} else {
			echo '<h3>'.$msg.'</h3>';
		}
	}
}
?>
