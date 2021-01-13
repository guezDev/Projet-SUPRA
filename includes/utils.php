<?php
	require_once __DIR__ . '/params.php';

	function formatLog($txt) {
		return date('Y-m-d H:i:s') . "\t" . trim($txt) . "\n";
	}

	function __log($txt) {
		$fd = @fopen(LOG_FILE, 'a+');

		@fputs($fd, formatLog($txt));
		@fclose($fd);
   }

	function __logException($txt, $exception) {
		$fd = @fopen(LOG_DB, 'a+');

		@fputs($fd, formatLog($txt . ': ' . $exception->getMessage() . ' (' . $exception->getCode() . ")\nStack:\n" . $exception->getTraceAsString() . "\n-------\n"));
		@fclose($fd);
		if (defined('ECHO_DB_LOGS') && ECHO_DB_LOGS) {
			echo $txt . ' - ' . $exception->getCode() . ' - ' . $exception->getMessage();
		}
	}

	function buildResponse($resp, $ret) {
		$newResponse = $resp->withHeader('Content-type', 'application/json');
		$newResponse = $newResponse->withAddedHeader('Access-Control-Allow-Origin', '*');
		$newResponse->getBody()->write(json_encode($ret));
		return $newResponse;
	}

	function makeUpdateFields($fields) {
		$ret = '';
		foreach ($fields as $a_field => $a_val) {
			$ret .= (empty($ret) ? '' : ', ') . '`' . $a_field. '` = :' . $a_field;
		}
		return $ret;
	}
?>
