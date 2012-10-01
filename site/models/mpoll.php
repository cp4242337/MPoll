<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class MPollModelMPoll extends JModel
{

	function getPoll($pollid)
	{
		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();
		$query = 'SELECT * FROM #__mpoll_polls WHERE poll_id = '.$pollid.' && published > 0 && access IN ('.implode(",",$user->getAuthorisedViewLevels()).')';
		$db->setQuery( $query );
		$pdata = $db->loadAssoc();
		return $pdata;
	}
	function getQuestions($courseid)
	{
		$db =& JFactory::getDBO();
		$query = 'SELECT * FROM #__mpoll_questions ';
		$query .= 'WHERE published > 0 && q_poll = '.$courseid.' ORDER BY ordering ASC';
		$db->setQuery( $query );
		$qdata = $db->loadObjectList();
		return $qdata;
	}
	function saveBallot($pollid) {
		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();
		$userid = $user->id;
		$pollinfo=$this->getPoll($pollid);
		$email = '';
		//save completed
		$qc = 'INSERT INTO #__mpoll_completed (cm_user,cm_poll) VALUES ('.$userid.','.$pollid.')';
		$db->setQuery( $qc );
		$db->query();
		$lastid = $db->insertid();
		//saev answers
		$query = 'SELECT * FROM #__mpoll_questions WHERE published > 0 && q_poll = '.$pollid;
		$db->setQuery( $query );
		$qdata = $db->loadAssocList(); 
		foreach ($qdata as $ques) {
			if ($pollinfo['poll_emailto']) $email .= '<b>'.$ques['q_text'].'</b>';
			$otherans=$db->getEscaped(JRequest::getVar('q'.$ques['q_id'].'o'));
			if ($ques['q_type'] != 'mcbox') {
				$ans = JRequest::getVar('q'.$ques['q_id']);
				if ($pollinfo['poll_emailto']) {
					if ($ques['q_type'] == "multi") {
						$qo = 'SELECT opt_txt FROM #__mpoll_questions_opts WHERE published > 0 && opt_id = '.$ans;
						$db->setQuery($qo); $result = $db->loadResult();
						$email .= '<br />'.$result.'<br /><br />';
					} else {
						$email .= '<br />'.$ans.'<br /><br />';
					}
				}
				$q = 'INSERT INTO #__mpoll_results	(res_user,res_poll,res_qid,res_ans,res_ans_other,res_cm) VALUES ("'.$userid.'","'.$pollid.'","'.$ques['q_id'].'","'.$ans.'","'.$otherans.'","'.$lastid.'")';
				$db->setQuery( $q );
				$db->query();
			} else {
				$ansarr = JRequest::getVar('q'.$ques['q_id']); 
				$ans = implode(' ',$ansarr);
				if ($pollinfo['poll_emailto']) {
					$qo = 'SELECT opt_txt FROM #__mpoll_questions_opts WHERE published > 0 && opt_id IN ('.implode(',',$ansarr).')';
					$db->setQuery($qo); $result = $db->loadResultArray();
					$email .= '<br />'.implode("; ",$result).'<br /><br />';
				}
				$q = 'INSERT INTO #__mpoll_results	(res_user,res_poll,res_qid,res_ans,res_ans_other,res_cm) VALUES ("'.$userid.'","'.$pollid.'","'.$ques['q_id'].'","'.$ans.'","'.$otherans.'","'.$lastid.'")';
				$db->setQuery( $q );
				$db->query();
			}
			
		}
		
		if ($pollinfo['poll_emailto']) {
			$mail = &JFactory::getMailer();
			$mail->IsHTML(true);
			$emllist = Array();
			$emllist = explode(",",$pollinfo['poll_emailto']);
			foreach ($emllist as $e) {
				$mail->addRecipient($e,$e);
			}
			$mail->setSender($emllist[0],$emllist[0]);
			$mail->setSubject("MPoll Results: ".$pollinfo['poll_name']);
			$mail->setBody( $email );
			$sent = $mail->Send();
		}
		return 0;
	}
	
	function getCasted($pollid) {
		$db =& JFactory::getDBO();
		//$sewn = JFactory::getSession();
		//$sessionid = $sewn->getId();
		$user =& JFactory::getUser();
		$userid = $user->id;
		$query = 'SELECT * FROM #__mpoll_completed WHERE cm_user="'.$userid.'" && cm_poll="'.$pollid.'"';
		$db->setQuery($query);
		$data = $db->loadAssoc();
		if (count($data) > 0) return true;
		else return false;
	}
	
	function getPolls($catid) {
		$query  = ' SELECT * ';
		$query .= ' FROM #__mpoll_polls';
		$query .= ' WHERE published = 1 && poll_cat = '.$catid;
		$query .= ' ORDER BY poll_name ASC';
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$data = $db->loadObjectList();
		return $data;
	}
	
	function getFirstCast($pollid) {
		$q = 'SELECT cm_time FROM #__mpoll_completed WHERE cm_poll = '.$pollid.' ORDER BY cm_time ASC LIMIT 1';
		$db =& JFactory::getDBO();
		$db->setQuery($q); 
		$data = $db->loadAssoc(); 
		return $data['cm_time'];
	}
	function getLastCast($pollid) {
		$q = 'SELECT cm_time FROM #__mpoll_completed WHERE cm_poll = '.$pollid.' ORDER BY cm_time DESC LIMIT 1';
		$db =& JFactory::getDBO();
		$db->setQuery($q); 
		$data = $db->loadAssoc(); 
		return $data['cm_time'];
	}
	function getNumCast($pollid) {
		$q = 'SELECT count(*),cm_poll FROM #__mpoll_completed WHERE cm_poll = '.$pollid.' GROUP BY cm_poll';
		$db =& JFactory::getDBO();
		$db->setQuery($q); 
		$data = $db->loadAssoc();
		if ($data) return $data['count(*)'];
		else return 0;
	}
	

}
