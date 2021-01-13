<?php
require_once __DIR__ . '/../db.php';

$app->get('/api/1.0/login', function ($req, $resp, $args) {
   try {
      global $__user_id, $__user_login;

      /** SECURITY CHECK - MANDATORY */
      $pdo = getPDO();
      /** END OF SECURITY CHECK */
   
      $password = $_GET['password'];
      $login = $_GET['login'];
      $versions = $_GET['ver'];

      $stmt = $pdo->prepare('SELECT `id`,`login` FROM `users` AS `u` WHERE `u`.`login` = :login AND `u`.`password` = make_password(:password) AND `u`.`enabled` = "y"');
      $stmt->execute(array('login' => $login, 'password' => $password));
      if (!($row = $stmt->fetchObject())) {
         deleteTheCookie();
         __log('Connection ERR for "' . $login . '"');
         return $resp->withStatus(401);   // Unauthorized
      }

      $token = openssl_random_pseudo_bytes(16);
      $token = bin2hex($token);
      $__user_id = $row->id;
      $__user_login = $login;
      $stmt2 = $pdo->prepare('INSERT INTO `tokens` SET `user_id` = :id, `token` = :token, `expiration` = DATE_ADD(NOW(), INTERVAL :expiration SECOND)');
      $stmt2->execute(array('id' => $__user_id, 'token' => $token, 'expiration' => TOKEN_EXPIRATION));

      setTheCookie($token);

      __log('Connection OK "' . $__user_login . '" (ID #' . $__user_id . ')');

      $ret = array(
         'id' => (int)$__user_id,
      );
      return buildResponse($resp, $ret);
   } catch (Exception $e) {
      __logException('Pb login', $e);
      return $resp->withStatus(500);   // Internal Server Error
   }
});

$app->post('/api/1.0/password', function ($req, $resp, $args) {
   global   $__user_id;
   try {
      /** SECURITY CHECK - MANDATORY */
      $pdo = getPDO();
      if (!checkToken()) {
         return $resp->withStatus(401);   // Unauthorized
      }
      /** END OF SECURITY CHECK */
   
      $params = $req->getParsedBody();
      if ((strlen($params['new_password']) < PASSWORD_MIN_LEN) || (strlen($params['new_password']) > PASSWORD_MAX_LEN)) {
         __log('Pb de longueur de mdp');
         return $resp->withStatus(416);   // Range Not Satisfiable
      } else {
         $stmt = $pdo->prepare('UPDATE `users` SET `password` = :new_password WHERE (`password` IS NULL OR `password` = make_password(:old_password)) AND `enabled` = "y"');
         $stmt->execute(array('old_password' => $params['old_password'], 'new_password' => $params['new_password']));
         $ret = (($stmt->rowCount() > 0) ? 1 : 0);
         __log('Changement mdp : Count = ' . $stmt->rowCount());
      }
      if ($ret > 0) {
         return $resp->withStatus(200);   // OK
      } else {
         return $resp->withStatus(304);   // Not Modified
      }
   } catch (Exception $e) {
      __logException('Pb password', $e);
      return $resp->withStatus(500);   // Internal Server Error
   }
});

$app->get('/api/1.0/logout', function ($req, $resp, $args) {
   global   $__token;
   try {
      /** SECURITY CHECK - MANDATORY */
      $pdo = getPDO();
      if (!checkToken()) {
         return $resp->withStatus(401);   // Unauthorized
      }
      /** END OF SECURITY CHECK */
   
      $stmt = $pdo->prepare('DELETE FROM tokens WHERE token = :token');
      $stmt->execute(array('token' => $__token));
      deleteTheCookie();
      return $resp->withStatus(200);   // OK
   } catch (Exception $e) {
      __logException('Pb Logout', $e);
      return $resp->withStatus(500);   // Internal Server Error
   }
});
?>