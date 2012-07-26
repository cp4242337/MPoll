<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
$db =& JFactory::getDBO();
?>
<form name="mpollf<?php echo $pdata['poll_id']; ?>">

<table width="95%" border="0" cellspacing="0" cellpadding="1" align="center" class="poll<?php echo $params->get('moduleclass_sfx'); ?>">
<thead>
	<tr>
		<td style="font-weight: bold;">
			<?php if ($showtitle) echo $pdata['poll_name']; ?>
		</td>
	</tr>
</thead>
	<tr>
		<td align="left"><div id="mpollmod<?php echo $pdata['poll_id']; ?>">
			<?php
				if ($status != 'closed' && $status != 'done') {
					foreach ($qdatap as $qdata) {
						if ($qdata->q_req && $qdata->q_type != 'mcbox') { 
							$req_q[] = 'q'.$qdata->q_id;
							$req_t[] = $qdata->q_type;
						}
						//Question #
						echo '<div class="mpollmod-question">';
					
						//Question text if not a single checkbox
						if ($qdata->q_type != 'cbox') {
							echo '<div class="mpollmod-question-text">'.$qdata->q_text.'</div>';
							
						}
						
						//output checkbox
						if ($qdata->q_type == 'cbox') { 
							echo '<div class="mpollmod-answer"><label><input type="checkbox" size="40" name="q'.$qdata->q_id.'" id="q'.$qdata->q_id.'"> '.$qdata->q_text.'</label></div>';
						}
						
						//verification msg area
						echo '<div id="'.'q'.$qdata->q_id.'_msg" class="mpollmod-error_msg"></div>';
						
						//output radio select
						if ($qdata->q_type == 'multi') {
							$query = 'SELECT * FROM #__mpoll_questions_opts WHERE opt_qid = '.$qdata->q_id.' && published = 1 ORDER BY ordering ASC';
							$db->setQuery( $query );
							$qopts = $db->loadAssocList();
							$numopts=0;
							foreach ($qopts as $opts) {
								echo '<div class="mpollmod-answer"><label><input type="radio" name="q'.$qdata->q_id.'" value="'.$opts['opt_id'].'" id="q'.$qdata->q_id.'"> '.$opts['opt_txt'].'</label></div>';
								$numopts++;
							}
						} 
					
						/* disabled for module
						//output multi checkbox
						if ($qdata->q_type == 'mcbox') {
							//echo '<em>(check all that apply)</em><br />';
							$query = 'SELECT * FROM #__mpoll_questions_opts WHERE opt_qid = '.$qdata->q_id.' ORDER BY opt_order ASC';
							$db->setQuery( $query );
							$qopts = $db->loadAssocList();
							foreach ($qopts as $opts) {
								echo '<label><input type="checkbox" name="q'.$qdata->q_id.'[]" value="'.$opts['opt_id'].'" id="q'.$qdata->q_id.'">'.$opts['opt_txt'].'</label><br>';
							}
						} 
						*/
						//text boxes disabled for module
						/*//output text field
						if ($qdata->q_type == 'textbox') { echo '<input type="text" size="20" name="q'.$qdata->q_id.'" id="q'.$qdata->q_id.'"><br>'; }*/
						
						//add in verification if nedded
						if ($qdata->q_req && $qdata->q_type != 'mcbox') { $req_o[] = $numopts;}
						echo '</div>';
					}
					if ($status == 'open') {
						echo '<p align="center">';
						echo '<a href="javascript:checkRq'.$pdata['poll_id'].'();" class="button">Submit</a>';
						echo '</p>';
					} else { 
						echo '<p align="center">Log in to Vote</p>'; 
					}
					$cnt = count($req_q);
					?>
					<script type='text/javascript'>
					function checkRq<?php echo $pdata['poll_id']; ?>() {
						ev = document.mpollf<?php echo $pdata['poll_id']; ?>;
						erMsg = '<span style="color:#800000"><b>Answer is Required</b></span>';
						cks = false; errs = false;
					<?
					for ($i=0; $i<$cnt; $i++) {
						if ($req_t[$i] == 'textbox') { echo "	if(isEmpty".$pdata['poll_id']."(ev.".$req_q[$i].", erMsg,'".$req_q[$i]."'+'_msg')) { errs=true; }\n"; }
						if ($req_t[$i] == 'multi') { echo "	if(isNCheckedR".$pdata['poll_id']."(ev.".$req_q[$i].", erMsg,".$req_o[$i].",'".$req_q[$i]."'+'_msg')) { errs=true; }\n"; }
						if ($req_t[$i] == 'cbox') { echo "	if(isChecked".$pdata['poll_id']."(ev.".$req_q[$i].", erMsg,'".$req_q[$i]."'+'_msg')) { errs=true; }\n"; }
						
					} 
				
				?>
					if (!errs) MPollAJAX<?php echo $pdata['poll_id']; ?>();
					}
					
					function isEmpty<?php echo $pdata['poll_id']; ?>(elem, helperMsg,msgl){
						if(elem.value.length == 0){
							document.getElementById(msgl).innerHTML = helperMsg;
							document.getElementById(msgl).style.display='block';
							elem.focus(); // set the focus to this input
							return true;
						}
						document.getElementById(msgl).innerHTML ='';
						document.getElementById(msgl).style.display='none';
							return false;
					}
					
					function isNCheckedR<?php echo $pdata['poll_id']; ?>(elem, helperMsg,cnt,msgl){
						var isit = false;
						for (var i=0; i<cnt; i++) {
							if(elem[i].checked){ isit = true; }
						}
						if (isit == false) {
							document.getElementById(msgl).innerHTML = helperMsg;
							document.getElementById(msgl).style.display='block';
							elem[0].focus(); // set the focus to this input
							return true;
						}
						document.getElementById(msgl).innerHTML = '';
						document.getElementById(msgl).style.display='none';
							return false;
					}
					function isChecked<?php echo $pdata['poll_id']; ?>(elem, helperMsg,msgl) {
						if (elem.checked) {
							document.getElementById(msgl).innerHTML = '';
							document.getElementById(msgl).style.display='none';
							return false;
						} else { 
							document.getElementById(msgl).innerHTML = helperMsg;
							document.getElementById(msgl).style.display='block';
							elem.focus(); // set the focus to this input
							return true; 
						}
					}
					function getCheckedValue<?php echo $pdata['poll_id']; ?>(radioObj) {
						if(!radioObj)
							return "";
						var radioLength = radioObj.length;
						if(radioLength == undefined)
							if(radioObj.checked)
								return radioObj.value;
							else
								return "";
						for(var i = 0; i < radioLength; i++) {
							if(radioObj[i].checked) {
								return radioObj[i].value;
							}
						}
						return "";
					}
	
					function MPollAJAX<?php echo $pdata['poll_id']; ?>(){
						var ajaxRequest;  // The variable that makes Ajax possible!
						
						try{
							// Opera 8.0+, Firefox, Safari
							ajaxRequest = new XMLHttpRequest();
						} catch (e){
							// Internet Explorer Browsers
							try{
								ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
							} catch (e) {
								try{
									ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
								} catch (e){
									// Something went wrong
									alert("Your browser broke!");
									return false;
								}
							}
						}
						// Create a function that will receive data sent from the server
						ajaxRequest.onreadystatechange = function(){
							if(ajaxRequest.readyState == 4){
								var ajaxDisplay = document.getElementById('mpollmod<?php echo $pdata['poll_id']; ?>');
								ajaxDisplay.innerHTML = ajaxRequest.responseText;
							}
						}
						var queryString = "?";
						<?php
						echo "queryString += 'poll=' + '".$pdata['poll_id']."';\n";
						foreach ($qdatap as $qdata) {
							if ($qdata->q_type = 'multi') {
								echo "\t\t\t\t\tqueryString += '&q".$qdata->q_id."=' + getCheckedValue".$pdata['poll_id']."(ev.q".$qdata->q_id.");\n";
							}
							else echo "\t\t\t\t\tqueryString += '&q".$qdata->q_id."=' + ev.q".$qdata->q_id.".value;\n";
						} 
						?> 
						ajaxRequest.open("GET", "modules/mod_mpoll/mod_mpoll_ajax.php" + queryString, true);
						ajaxRequest.send(null); 
					}
					</script><?php 
				} else if ($status == 'done') {
					if ($pdata['poll_results_msg_before']) echo $pdata['poll_results_msg_before'];
					if ($pdata['poll_showresults']) {
						foreach ($qdatap as $q) {
							echo '<div class="mpollmod-question">';
							$anscor=false;
							echo '<div class="mpollmod-question-text">'.$q->q_text.'</div>';
							switch ($q->q_type) {
								case 'multi':
									$qnum = 'SELECT count(res_qid) FROM #__mpoll_results WHERE res_qid = '.$q->q_id.' GROUP BY res_qid';
									$db->setQuery( $qnum );
									$qnums = $db->loadAssoc();
									$numr=$qnums['count(res_qid)'];
									$query  = 'SELECT o.* FROM #__mpoll_questions_opts as o ';
									$query .= 'WHERE o.opt_qid = '.$q->q_id.' ORDER BY ordering ASC';
									$db->setQuery( $query );
									$qopts = $db->loadObjectList();
									$tph=0;
									foreach ($qopts as &$o) {
										$qa = 'SELECT count(*) FROM #__mpoll_results WHERE res_qid = '.$q->q_id.' && res_ans = '.$o->opt_id.' GROUP BY res_ans';
										$db->setQuery($qa);
										$o->anscount = $db->loadResult();
										if ($o->anscount == "") $o->anscount = 0;
									}
									$gper=0; $ansper=0; $gperid = 0;
									foreach ($qopts as $opts) {
										if ($numr != 0) $per = ($opts->anscount+$opts->prehits)/($numr+$tph); else $per=1;
										if ($qans == $opts->id && $opts->correct) {
											$anscor=true;
										}
										echo '<div class="mpollmod-opt">';
										
										echo '<div class="mpollmod-opt-text">';
										if ($opts->opt_correct) echo '<div class="mpollmod-opt-correct">'.$opts->opt_txt.'</div>';
										else echo $opts->opt_txt;
										echo '</div>';
										echo '<div class="mpollmod-opt-count">';
										echo ($opts->anscount);
										echo '</div>';
										echo '<div class="mpollmod-opt-bar-box"><div class="mpollmod-opt-bar-bar" style="background-color: '.$opts->opt_color.'; width:'.($per*100).'%"></div></div>';
										echo '</div>';
										if ($gper < $per) { $gper = $per; $gperid = $opts->id; }
										if ($qans==$opts->opt_id) {
											if ($qdata->q_expl) $expl=$qdata->q_expl;
											else $expl=$opts->opt_expl;
										}
									}
									break;
									
							}
							
							echo '</div>';
						}
					}
					if ($pdata['poll_results_msg_mod']) echo $pdata['poll_results_msg_mod'];
				}
				
				
				
				?>
				
		</div></td>
	</tr>
</table>

	

</form>
