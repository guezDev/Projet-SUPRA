<?php
require_once __DIR__ . '/../db.php';

$__token_ok = FALSE;
$__user_id = -1;
$__user_login = '';
$__token = $_COOKIE[TOKEN_NAME];
if (empty($__token)) {
   $__token = $_GET[TOKEN_NAME];
}git 

function setTheCookie($val)
{
   global   $__token;

   setcookie(TOKEN_NAME, $val, 0, '/');
   $__token = $val;
}

function deleteTheCookie()
{
   global   $__token;

   setcookie(TOKEN_NAME, '', 0, '/');
   $__token = '';
}

function checkToken()
{
   global   $__token, $__token_ok, $__user_id, $__user_login;

   $pdo = getPDO();
   $stmt = $pdo->prepare('SELECT `u`.`id`, `u`.`login` FROM `tokens` AS `t` INNER JOIN `users` AS `u` ON `u`.`id` = `t`.`user_id` WHERE `t`.`token` = :token AND `t`.`expiration` > NOW() AND `u`.`enabled` = "y"');
   $stmt->execute(array('token' => $__token));
   if ($row = $stmt->fetchObject()) {
      $stmt = $pdo->prepare('UPDATE `tokens` SET `expiration` = DATE_ADD(NOW(), INTERVAL :expiration SECOND) WHERE `token` = :token');
      $stmt->execute(array('token' => $__token, 'expiration' => TOKEN_EXPIRATION));
      $__user_login = $row->login;
      $__user_id = $row->id;
      setTheCookie($__token);
      return TRUE;
   } else {
      $__user_login = '';
      $__user_id = -1;
      deleteTheCookie();
      return FALSE;
   }
};

$app->get('/api/1.0/check', function ($req, $resp, $args) {
   global   $__user_id;

   try {
      if (!checkToken()) {
         return $resp->withStatus(401);   // Unauthorized
      } else {
         $ret = [
            'id' => $__user_id,
         ];
         return buildResponse($resp, $ret);
      }
   } catch (Exception $e) {
      __logException('Pb Check token', $e);
      return $resp->withStatus(500);   // Internal Server Error
   }
});
