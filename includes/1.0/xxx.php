<?php
// Les lignes à adapter sont entre des /*** ADAPT... FIN_ADAPT ***/ ou avec juste un // ADAPT en bout de ligne
// Il suffit d'adapter/remplacer les xxx et XXX
// On suppose que l'ID d'un record est nommé `id`

namespace xxx;    // ADAPT

function checkRecordExists($id) {
   // Pas de try...catch, c'est déjà fait dans l'appelant...
   $pdo = getPDO();
   /*** ADAPT */
   $stmt = $pdo->prepare('SELECT 1 FROM `xxx` WHERE `id` = :id');
   /*** FIN_ADAPT */
   $stmt->execute(array('id' => $id));
   if ($row = $stmt->fetchObject()) {
      return TRUE;
   } else {
      return FALSE;
   }
}

$app->get('/api/1.0/xxx', function ($req, $resp, $args) {   // ADAPT
   try {
      /** SECURITY CHECK - MANDATORY */
      $pdo = getPDO();
      if (!checkToken()) {
         return $resp->withStatus(401);   // Unauthorized
      }
      /** END OF SECURITY CHECK */
      
      $ret = array();
      $xxx = [];    // ADAPT
      
      /*** ADAPT */
      $stmt = $pdo->query('SELECT `x`.`id`, `x`.`xxx` FROM `xxx` AS `x`');
      while ($row = $stmt->fetchObject()) {
         $groupes[] = [
            'id' => (int)$row->id,
            'xxx' => $row->xxx,
            //...etc
         ];
      }

      $ret = array(
         'xxx' => (array)$xxx, // on (caste) en "array" par sécurité
      );
      /*** FIN_ADAPT */
      return buildResponse($resp, $ret);
   } catch (Exception $e) {
      __logException('Pb GET xxx', $e);   // ADAPT
      return $resp->withStatus(500);   // Internal Server Error
   }
});

function getRecordById($id) {
   // Pas de try...catch, c'est déjà fait dans l'appelant...
   $pdo = getPDO();
   /*** ADAPT */
   $stmt = $pdo->prepare('SELECT `x`.`id`, `x`.`xxx` FROM `xxx` AS `x` WHERE `x`.`id` = :id');
   $stmt->execute(array('id' => $id));
   if ($row = $stmt->fetchObject()) {
      $ret = [
         'id' => (int)$row->id,
         'xxx' => $row->xxx,
      ];
   /*** FIN_ADAPT */
   } else {
      $ret = [];  // On retourne quand même un array
   }
   return $ret;
}

$app->get('/api/1.0/xxx/{id}', function ($req, $resp, $args) {   // ADAPT
   $id = $args['id'];
   try {
      /** SECURITY CHECK - MANDATORY */
      if (!checkToken()) {
         return $resp->withStatus(401);   // Unauthorized
      }
      /** END OF SECURITY CHECK */
   
      if (!checkRecordExists($id)) {
         __log('Pb GET XXX #' . $id . ' - Inconnu');    // ADAPT
         return $resp->withStatus(404);   // Not found
      }

      $ret = getRecordById($id);
      return buildResponse($resp, $ret);
   } catch (Exception $e) {
      __logException('Pb GET xxx #' . $id, $e);    // ADAPT
      return $resp->withStatus(500);   // Internal Server Error
   }
});

$app->post('/api/1.0/xxx', function ($req, $resp, $args) {    // ADAPT
   $params = $req->getParsedBody();
   $xxx = $params['xxx'];     // ADAPT
   // etc...

   try {
      /** SECURITY CHECK - MANDATORY */
      $pdo = getPDO();
      if (!checkToken()) {
         return $resp->withStatus(401);   // Unauthorized
      }
      /** END OF SECURITY CHECK */
   
      /*** ADAPT */
      $stmt = $pdo->prepare('INSERT INTO `xxx` SET `xxx` = :xxx, etc...');
      $stmt->execute(array('xxx' => $xxx, /* etc...*/));
      $id = $pdo->lastInsertId();
      /*** FIN_ADAPT */
   
      $ret = getRecordById($id);
      return buildResponse($resp, $ret);
   } catch (Exception $e) {
      __logException('Pb POST xxx', $e);    // ADAPT
      return $resp->withStatus(500);   // Internal Server Error
   }
});

$app->put('/api/1.0/xxx/{id}', function ($req, $resp, $args) {      // ADAPT
   $params = $req->getParsedBody();
   $id = $args['id'];
   try {
      /** SECURITY CHECK - MANDATORY */
      $pdo = getPDO();
      if (!checkToken()) {
         return $resp->withStatus(401);   // Unauthorized
      }
      /** END OF SECURITY CHECK */
   
      if (!checkRecordExists($id)) {
         __log('Pb PUT xxx #' . $id . ' - Inconnu');
         return $resp->withStatus(404);   // Not found
      }

      $fields = [];  // Liste des champs à updater. 'key' (champ) => 'val' (valeur)
      /*** ADAPT */
      if (isset($params['nom'])) $fields['nom'] = $params['nom'];
      // etc...
      /*** FIN_ADAPT */

      $upd_fields = makeUpdateFields($fields);  // Dans utils.php, crée une chaine de "`field`= :field"...
      $req = 'UPDATE `xxx` SET ' . $upd_fields . ' WHERE `id` = :id';     // ADAPT
      $stmt = $pdo->prepare($req);
      $stmt->execute(array_merge($fields, ['id' => $id]));

      $ret = getRecordById($id);
      return buildResponse($resp, $ret);
   } catch (Exception $e) {
      __logException('Pb PUT xxx #' . $id, $e);    // ADAPT
      return $resp->withStatus(500);   // Internal Server Error
   }
});

$app->delete('/api/1.0/xxx/{id}', function ($req, $resp, $args) {      // ADAPT
   $params = $req->getParsedBody();
   $id = $args['id'];
   try {
      /** SECURITY CHECK - MANDATORY */
      $pdo = getPDO();
      if (!checkToken()) {
         return $resp->withStatus(401);   // Unauthorized
      }
      /** END OF SECURITY CHECK */
   
      if (!checkRecordExists($id)) {
         __log('Pb DELETE xxx #' . $id . ' - Inconnu');     // ADAPT
         return $resp->withStatus(404);   // Not found
      }

      $stmt = $pdo->prepare("DELETE FROM `xxx` WHERE `id` = :id");      // ADAPT
      $stmt->execute(['id' => $id]);
      return $resp->withStatus(200);
   } catch (Exception $e) {
      __logException('Pb DELETE xxx #' . $id, $e);    // ADAPT
      return $resp->withStatus(500);   // Internal Server Error
   }
});
?>