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
 * Defines the version of tincan
 *
 * This code fragment is called by moodle_needs_upgrading() and
 * /admin/index.php
 *
 * @package report_tincan
 * @copyright  2014 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2014031700;      // The current plugin version (Date: YYYYMMDDXX)
$plugin->requires  = 2013050100;      // Requires this Moodle version
$plugin->cron      = 0;               // Period for cron to check this plugin (secs)
$plugin->component = 'report_tincan'; // To check on upgrade, that plugin sits in correct place
$plugin->maturity = MATURITY_ALPHA;
$plugin->release = '0.01 (Build: 2014031700)';
