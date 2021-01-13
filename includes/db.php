<?php
	require_once __DIR__ . '/params.php';
	require_once __DIR__ . '/utils.php';

	$__pdo = NULL;

	function getPDO() {
		global $__pdo;

		if ($__pdo == NULL) {
			$__pdo = new PDO(DB_TYPE . ':dbname=' . DB_NAME . ';host=' . DB_HOST . ';port=' . DB_PORT, DB_USER, DB_PASS);
			$__pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		return $__pdo;
	}
?>
