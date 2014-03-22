<?php
$observers = array(
    array(
        'eventname'   => '\mod_quiz\event\attempt_started',
        'callback'    => '\report_tincan\report_tincan::tincan_quiz_attempt_started',
        'includefile' => 'report\tincan\lib.php',
        'internal' => false,
    ),
    array(
        'eventname'   => '\mod_quiz\event\attempt_submitted',
        'callback'    => '\report_tincan\report_tincan::tincan_quiz_attempt_submitted',
        'includefile' => 'report\tincan\lib.php',
        'internal' => false,
    ),
);