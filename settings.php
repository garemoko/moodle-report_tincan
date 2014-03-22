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
 * The main tincan configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package report_tincan
 * @copyright  2014 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/lib.php');

if ($hassiteconfig) {
    // New settings page
    $page = new admin_settingpage('tincan', get_string('pluginname', 'report_tincan'));
		
	//Add the LRS settings header
	$page->add(new admin_setting_heading('report_tincan/tincanlrsheader', get_string('tincanlrsheader', 'report_tincan'), ''));
	
	//Add LRS endpoint
	$page->add(new admin_setting_configtext('report_tincan/lrsendpoint',
        get_string('tincanlrsendpoint', 'report_tincan'), get_string('tincanlrsendpoint_help', 'report_tincan'), 'http://example.com/endpoint/', PARAM_TEXT, 64));
	
	//Add basic authorisation login. TODO: OAuth
	$page->add(new admin_setting_configtext('report_tincan/lrslogin',
        get_string('tincanlrslogin', 'report_tincan'), get_string('tincanlrslogin_help', 'report_tincan'), '', PARAM_TEXT, 64));
	
	//Add basic authorisation pass. TODO: OAuth
	$page->add(new admin_setting_configtext('report_tincan/lrspass',
        get_string('tincanlrspass', 'report_tincan'), get_string('tincanlrspass_help', 'report_tincan'), '', PARAM_TEXT, 64));
	
	$page->add(new admin_setting_configtext('report_tincan/lrsversion',
        get_string('tincanlrsversion', 'report_tincan'), get_string('tincanlrsversion_help', 'report_tincan'), '1.0.0', PARAM_TEXT, 5));

	// Add settings page to navigation tree
    $ADMIN->add('reports', $page);
}
