<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library of interface functions and constants for module tincan
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the tincan specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package report_tincan
 * @copyright  2014 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_tincan;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require ($CFG->dirroot .'/report/tincan/vendor/autoload.php');


//Reviewer - Can I put this inside my class rather than making it global? How? 
try {
	$lrs = report_tincan::tincan_setup_lrs();
}
catch (Exception $e) {
	$lrs;
}

class report_tincan {
	
	//TODO: validate endpoint is not empty
	public static function tincan_setup_lrs(){
		$version = get_config('report_tincan', 'lrsversion');
		if (empty($version)){
			$version = '1.0.0'; //default TODO: put this default somewhere central
		}
		
		$lrs = new \TinCan\RemoteLRS();
		$lrs
			->setEndPoint(get_config('report_tincan', 'lrsendpoint'))
		   	->setAuth(get_config('report_tincan', 'lrslogin'), get_config('report_tincan', 'lrspass'))
			->setversion($version);
		return $lrs;
	}
	
	public static function tincan_quiz_attempt_started($event){
		global $CFG, $DB;
		//not all of these will be used TODO: remove those which aren't used once all is said and done!
		$course  = $DB->get_record('course', array('id' => $event->courseid));
	    $attempt = $event->get_record_snapshot('quiz_attempts', $event->objectid);
	    $quiz    = $event->get_record_snapshot('quiz', $attempt->quiz);
	    $cm      = get_coursemodule_from_id('quiz', $event->get_context()->instanceid, $event->courseid);
		
		$lrs = self::tincan_setup_lrs();

		
		//print_error('quiz: '. json_encode($quiz) .'  <br/><br/>attempt: '. json_encode($attempt).'  <br/><br/>cm: '. json_encode($cm).'  <br/><br/>course: '. json_encode($course));		
		//quiz: {"id":"1","course":"2","name":"Tin Can enabled quiz","intro":"<p>x<\/p>","introformat":"1","timeopen":"0","timeclose":"0","timelimit":"0","overduehandling":"autoabandon","graceperiod":"0","preferredbehaviour":"deferredfeedback","attempts":"0","attemptonlast":"0","grademethod":"1","decimalpoints":"2","questiondecimalpoints":"-1","reviewattempt":"65536","reviewcorrectness":"0","reviewmarks":"0","reviewspecificfeedback":"0","reviewgeneralfeedback":"0","reviewrightanswer":"0","reviewoverallfeedback":"0","questionsperpage":"1","navmethod":"free","shufflequestions":"0","shuffleanswers":"1","questions":"1,0,2,0","sumgrades":"2.00000","grade":"10.00000","timecreated":"0","timemodified":"1395439265","password":"","subnet":"","browsersecurity":"-","delay1":"0","delay2":"0","showuserpicture":"0","showblocks":"1","enabletincan":"1","tincanactivityid":"http:\/\/localhost\/moodle\/quiz1","tincanlrsendpoint":"http:\/\/localhost\/learninglocker\/public\/data\/xAPI\/","tincanlrslogin":"b8801ad982a3854ad58f2892e9f009a8cf81e378","tincanlrspass":"48fa05d1d9a7d21266c6577bcbea62a81f8804fa","tincanlrsversion":"1.0.0","cmid":"5"}
		//attempt: {"quiz":"1","userid":"4","preview":0,"layout":"1,0,2,0","attempt":34,"timestart":1395440014,"timefinish":0,"timemodified":1395440014,"state":"inprogress","timecheckstate":null,"uniqueid":41,"id":41}
		//cm: {"id":"5","course":"2","module":"25","instance":"1","section":"3","idnumber":"","added":"1394914233","score":"0","indent":"0","visible":"1","visibleold":"1","groupmode":"0","groupingid":"0","groupmembersonly":"0","completion":"1","completiongradeitemnumber":null,"completionview":"0","completionexpected":"0","availablefrom":"0","availableuntil":"0","showavailability":"0","showdescription":"0","name":"Tin Can enabled quiz","modname":"quiz"} 
		//course: {"id":"2","category":"1","sortorder":"10001","fullname":"test","shortname":"test","idnumber":"","summary":"","summaryformat":"1","format":"weeks","showgrades":"1","newsitems":"5","startdate":"1394924400","marker":"0","maxbytes":"0","legacyfiles":"0","showreports":"0","visible":"1","visibleold":"1","groupmode":"0","groupmodeforce":"0","defaultgroupingid":"0","lang":"","calendartype":"","theme":"","timecreated":"1394881703","timemodified":"1394881703","requested":"0","enablecompletion":"1","completionnotify":"0","cacherev":"1395439927"}
		
		$statement = array( 
			'actor' => self::tincan_getactor(), 
			'verb' => array(
				'id' => 'http://adlnet.gov/expapi/verbs/attempted',
				'display' => array(
					'en-US' => 'attempted',
					'en-GB' => 'attempted',
					),
				),
			'object' => array(
				'id' =>  $CFG->wwwroot . '/mod/quiz/view.php?id='. $quiz->id, 
				'definition' => array(
					'name' => array(
						'en-US' => $quiz->name,
						'en-GB' => $quiz->name,
					),
					'description' => array(
						'en-US' => $quiz->intro,
						'en-GB' => $quiz->intro,
					), 
				),
			),
		);
		
		try {
			$lrs->saveStatement($statement);
		}
		catch (Exception $e) {
			//TODO: handle error
		}
			
		return true;
	}
	
	public static function tincan_quiz_attempt_submitted($event){
		global $CFG, $DB;
		//not all of these will be used TODO: remove those which aren't used once all is said and done!
		$course  = $DB->get_record('course', array('id' => $event->courseid));
	    $attempt = $event->get_record_snapshot('quiz_attempts', $event->objectid);
	    $quiz    = $event->get_record_snapshot('quiz', $attempt->quiz);
	    $cm      = get_coursemodule_from_id('quiz', $event->get_context()->instanceid, $event->courseid);
		
		$lrs = self::tincan_setup_lrs();

		
		//print_error('quiz: '. json_encode($quiz) .'  <br/><br/>attempt: '. json_encode($attempt).'  <br/><br/>cm: '. json_encode($cm).'  <br/><br/>course: '. json_encode($course));		
		// quiz: {"id":"1","course":"2","name":"Tin Can enabled quiz","intro":"<p>x<\/p>","introformat":"1","timeopen":"0","timeclose":"0","timelimit":"0","overduehandling":"autoabandon","graceperiod":"0","preferredbehaviour":"deferredfeedback","attempts":"0","attemptonlast":"0","grademethod":"1","decimalpoints":"2","questiondecimalpoints":"-1","reviewattempt":"65536","reviewcorrectness":"0","reviewmarks":"0","reviewspecificfeedback":"0","reviewgeneralfeedback":"0","reviewrightanswer":"0","reviewoverallfeedback":"0","questionsperpage":"1","navmethod":"free","shufflequestions":"0","shuffleanswers":"1","questions":"1,0,2,0","sumgrades":"2.00000","grade":"10.00000","timecreated":"0","timemodified":"1395439265","password":"","subnet":"","browsersecurity":"-","delay1":"0","delay2":"0","showuserpicture":"0","showblocks":"1","enabletincan":"1","tincanactivityid":"http:\/\/localhost\/moodle\/quiz1","tincanlrsendpoint":"http:\/\/localhost\/learninglocker\/public\/data\/xAPI\/","tincanlrslogin":"b8801ad982a3854ad58f2892e9f009a8cf81e378","tincanlrspass":"48fa05d1d9a7d21266c6577bcbea62a81f8804fa","tincanlrsversion":"1.0.0","cmid":"5"} 
		//attempt: {"id":"42","quiz":"1","userid":"4","attempt":"35","uniqueid":"42","layout":"1,0,2,0","currentpage":"0","preview":"0","state":"finished","timestart":"1395440160","timefinish":1395440168,"timemodified":1395440168,"timecheckstate":null,"sumgrades":0,"needsupgradetonewqe":"0"}
		// cm: {"id":"5","course":"2","module":"25","instance":"1","section":"3","idnumber":"","added":"1394914233","score":"0","indent":"0","visible":"1","visibleold":"1","groupmode":"0","groupingid":"0","groupmembersonly":"0","completion":"1","completiongradeitemnumber":null,"completionview":"0","completionexpected":"0","availablefrom":"0","availableuntil":"0","showavailability":"0","showdescription":"0","name":"Tin Can enabled quiz","modname":"quiz"} 
		//course: {"id":"2","category":"1","sortorder":"10001","fullname":"test","shortname":"test","idnumber":"","summary":"","summaryformat":"1","format":"weeks","showgrades":"1","newsitems":"5","startdate":"1394924400","marker":"0","maxbytes":"0","legacyfiles":"0","showreports":"0","visible":"1","visibleold":"1","groupmode":"0","groupmodeforce":"0","defaultgroupingid":"0","lang":"","calendartype":"","theme":"","timecreated":"1394881703","timemodified":"1394881703","requested":"0","enablecompletion":"1","completionnotify":"0","cacherev":"1395439927"}
		
		//TODO: add id, context and timestamp
		$statement = array( 
			'actor' => self::tincan_getactor(), 
			'verb' => array(
				'id' => 'http://adlnet.gov/expapi/verbs/completed',
				'display' => array(
					'en-US' => 'completed',
					'en-GB' => 'completed',
					),
				),
			'object' => array(
				'id' =>  $CFG->wwwroot . '/mod/quiz/view.php?id='. $quiz->id, 
				'definition' => array(
					'name' => array(
						'en-US' => $quiz->name,
						'en-GB' => $quiz->name,
					), 
				'description' => array(
						'en-US' => $quiz->intro,
						'en-GB' => $quiz->intro,
					), 
				),
			), 
			'result' => array( 
				//TODO: can we calculate sucecss by looking at the moodle completion status?
				//TODO: add completion
				//TODO: can we get duration based on time started and time completed? Does this include time between sessions? - it looks like it does
				//TODO: consideer using result:response to store feedback. All feedback or just quiz level feedback? 
				'score' => array(
					'min' => 0, //TODO: is this right? 
					'max' => floatval($quiz-> grade), //TODO: using the quiz grade will mean gardes dont match up with question level reporting. is this ok?
					'raw' => (($attempt-> sumgrades)/($quiz-> sumgrades))*($quiz-> grade),
					'scaled' => (($attempt-> sumgrades)/($quiz-> sumgrades)),
				),
			),
		);
	
		//send it
		try {
			$lrs->saveStatement($statement);
		}
		catch (Exception $e) {
			//TODO: handle error
		}
				
		return true;
	}

	public static function tincan_getactor()
	{
		global $USER, $CFG;
		if ($USER->email){
			return new \TinCan\Agent([
				"name" => fullname($USER),
				"mbox" => "mailto:".$USER->email,
				"objectType" => "Agent"
			]);
		}
		else{
			return new \TinCan\Agent([
				"name" => fullname($USER),
				"account" => array(
					"homePage" => $CFG->wwwroot,
					"name" => $USER->id
				),
				"objectType" => "Agent"
			]);
		}
	}
}



