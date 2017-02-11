<?php
// ------ start specifics for EdX forums ------- 
/** Redirect edX url */
define('MY_EDX_URL','https://courses.edx.org/courses/MITTSL_UPDATE_THIS_VALUE');

// W3 total cache
define('WP_CACHE', true);

define("WP_DEBUG", false);
define("WP_DEBUG_LOG", true );
define('FORCE_SSL_ADMIN', true);
if (isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && ($_SERVER["HTTP_X_FORWARDED_PROTO"] == "https")) {
  $_SERVER["HTTPS"]="on";
}

/** Enable simple users to remove edit loging */
define('MY_USER_EDIT_LOG_OPTION',true);

/* Mark as unread plugin and other plugins */
// Taken from wp-content/plugins/bbpress-mark-as-read/bbp-mark-as-read.php
//define('DOING_AJAX', true);
/* Using DOING_AJAX is problematic because defining it makes the admin bar disappear */
// Inspired by the other pluging (such as the simple like plugin), which use ajax to update the server regardless 
// of the state of DOING_AJAX
define('DOING_AJAX_MARKASREAD', true);

/* Working groups */
/* Inspired by plugins that have config settings (e.g. openid, simple like plugin) */
/* How often (in days) we should create working groups */
define('MY_CREATE_WORKING_GROUP_EVERY_X_DAYS', 1);
/* Working group size */
define('MY_WORKING_GROUP_SIZE',5);
/* Time when the algorithm should run (US eastern time!) */
/* Hour MILITARY TIME!! */
/* NOTE: If you're planning to change the time when the algorithm should run, you should change it by at least 30 minutes. */
/* Otherwise the server may fail to detect that the time changed. */
define('MY_WORKING_GROUP_RUN_HOUR', 17);
/* Minute */
define('MY_WORKING_GROUP_RUN_MINUTE', 30);
/* Time zone see http://php.net/manual/en/function.timezone-abbreviations-list.php user contributed comment #2 */
/* and http://us.php.net/manual/en/timezones.others.php */
define('MY_WORKING_GROUP_TIME_ZONE', 'America/New_York');
/* The id of the user that should create the working groups */
define('MY_WORKING_GROUP_CREATING_USER_ID', 2);
/* Number of times to randomize the groups */
define('MY_NUMBER_OF_TIMES_TO_RANDOMIZE_GROUPS', 1000);
/* Source: https://wordpress.org/support/topic/maximum-execution-time-of-30-seconds-exceeded-workaround */
set_time_limit(2400);
/* The epsilon for the scheduled time to run the script */
/* The algorithm doesn't usually run at the exact same time. Run time - epsilon through time + epsilon will be considered */
/* the time of the day to run the algorithm */
define('MY_WORKING_GROUP_EPSILON', 10);
/* Specific days of the week on which to run the algorithm */
/* Options: Sunday, Monday, Tuesday, Wednesday, Thursday, Friday, Saturday */
/* The idea was taken from other scripts that use schedules like mmaker and Catme */
define('MY_WORKING_GROUP_RUN_SUNDAY', true);
define('MY_WORKING_GROUP_RUN_MONDAY', false);
define('MY_WORKING_GROUP_RUN_TUESDAY', false);
define('MY_WORKING_GROUP_RUN_WEDNESDAY', false);
define('MY_WORKING_GROUP_RUN_FRIDAY', false);

define('‘MY_WORKING_GROUP_RUN', false);

// ------ end specifics for EdX forums ------- 

?>