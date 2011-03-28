<?php
$site_title = 'Scrabble words';

# let mysql_connect() decide how to connect, for speed
$db_host = '';
# define MySQL database connection information
$db_database = 'mysql_database';
$db_table = 'mysql_words_table';
$db_username = 'mysql_username';
$db_password = 'mysql_password';

# get username from Apache authentication
$user_name = ucfirst($_SERVER['REMOTE_USER']);
if(!$user_name) $user_name = 'James';
?>
