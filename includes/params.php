<?php
	define('DB_TYPE', 'mysql');
	define('DB_HOST', 'mysql');
	define('DB_PORT', 3306);
	define('DB_NAME', 'supra');
	define('DB_USER', 'supra');
	define('DB_PASS', 'unrobe.luzon.sage');
	define('PASSWORD_MIN_LEN', 8);
	define('PASSWORD_MAX_LEN', 32);
	define('TOKEN_NAME', 'supratok');// Example: xxxtok
	define('TOKEN_EXPIRATION', 60*60);      // Token valid 1H
	define('ECHO_DB_LOGS', FALSE);	// TRUE = echo DB logs, FALSE = no echo, log in file only
	define('LOG_FILE', __DIR__ . '/../logs/api.log');
	define('LOG_DB', __DIR__ . '/../logs/db.log');
?>