<?php


require __DIR__ . '/../vendor/autoload.php';
require_once '../src/DbHandler.php';
require_once '../src/middleware.php';
require_once '../src/Config.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;




$app = new \Slim\App;


/*
{
  "status": {
    "code": "200",
    "message": "Success",
    "str": "OK"
  },
  "response": {
    "count": 1,
    "data": [
      {
        "code": "APP_CONTACT_PHONE",
        "name": "Mobile Contact Phone",
        "values": {
          "contact_phone": "*0123"
        }
      }
    ]
  }
}
*/


$mw = function ($request, $response, $next) {
      $headers = apache_request_headers();
      $retorno = array();
    	if (isset($headers['token']))
      {
      		$db = new DbHandler();
      		$api_key = $headers['token'];
          $user = $db->isValidApiKey($api_key);

      		if (!($user["code"] == COD_OK)) {
        			$retorno["code"] = $user["code"];
        			$retorno["message"] = "Access Denied. Invalid Api key";
              $retorno["data"] = "";
      		}
          else
          {
                $retorno["code"] = 200;
          			$retorno["message"] = "Ok";
                $retorno["id_usuario"] = $user['id_usuario'];
                $retorno["id_tabla"] = $user['id_tabla'];
      		}
    	}

      if($retorno["code"] == 200)
      {
          $request = $request->withAttribute('userid', $retorno["id_usuario"]);
          $request = $request->withAttribute('id_tabla', $retorno["id_tabla"]);
          $response = $next($request, $response);
      }
      else
      {
          $response->getBody()->write(json_encode($retorno));
      }
      return $response;
};

function formatResponse($code, $Msg, $data, $encode)
{
    $return = array();
    $status = array();
    $response = array();

    $status['code'] = $code;
    $status['message'] = $Msg;


    $response['count'] = count($data);
    $response['data'] = $data;

    $return['status'] = $status;
    $return['response'] = $response;
    if($encode == 1)
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    elseif ($encode == 2) {
        return json_encode($return, JSON_UNESCAPED_SLASHES);
    }

}

$app->post('/crearsusuario', function (Request $request, Response $response) {
    $headers = apache_request_headers();
    $user_v = $headers['user'];
    $pass = $headers['pass'];
    $name = $headers['name'];
    $last_name = $headers['last_name'];
    $email = $headers['email'];
    $fono = $headers['fono'];

    $db = new DbHandler();

    $result = $db->crearSuperUsuario($user_v, $pass, $name, $last_name, $email, $fono);
    $Msg = 'Crear super usuario';

    $code = $result['code'];
    if(array_key_exists('data', $result))
    {
        $data = $result['data'];
    }
    else {
        $data = NULL;
    }
    $encode = 1;
    $response = formatResponse($code, $Msg, $data, $encode);

    return $response;
});

/*
$app->post('/crearsusuario/{user}', function (Request $request, Response $response) {
    $user_v = $request->getAttribute('user');
    $headers = apache_request_headers();
    $pass = $headers['pass'];
    $name = $headers['name'];
    $last_name = $headers['last_name'];

    $db = new DbHandler();

    $result = $db->crearSuperUsuario($user_v, $pass, $name, $last_name);
    $Msg = 'Crear super usuario';

    $code = $result['code'];
    if(array_key_exists('data', $result))
    {
        $data = $result['data'];
    }
    else {
        $data = NULL;
    }
    $encode = 1;
    $response = formatResponse($code, $Msg, $data, $encode);

    return $response;
});
*/

$app->get('/loginsuser/{user}', function (Request $request, Response $response) {

    $user_v = $request->getAttribute('user');
    $headers = apache_request_headers();
    $pass = $headers['pass'];
    $db = new DbHandler();

    $result = $db->validaLoginSUsers($user_v, $pass);
    $Msg = 'Valida Login SU';

    $code = $result['code'];
    if(array_key_exists('data', $result))
    {
        $data = $result['data'];
    }
    else {
        $data = NULL;
    }
    $enconde = 1;
    $response = formatResponse($code, $Msg, $data, $enconde);

    return $response;
});

$app->get('/obtieneDashboard', function (Request $request, Response $response) {

    $userid = $request->getAttribute('userid');
    $idtabla = $request->getAttribute('id_tabla');
    $db = new DbHandler();
    $result = $db->getDashboard($userid, $idtabla);
    $code = $result['code'];
    $Msg = 'Obtiene dashboard';
    if(array_key_exists('data', $result))
    {
        $data = $result['data'];
    }
    else {
        $data = NULL;
    }
    $enconde = 1;
    $response = formatResponse($code, $Msg, $data, $enconde);

    return $response;
})->add($mw);

$app->put('/editarusuario', function (Request $request, Response $response) {
    $headers = apache_request_headers();
    $userid = $request->getAttribute('userid');
    $idtabla = $request->getAttribute('id_tabla');
    $db = new DbHandler();
    if($idtabla == 1)
    {
        $userEdit = $headers['useredit'];
        $pass = "";
        $name = "";
        $last_name = "";
        $email = "";
        $fono = "";
        if(array_key_exists('pass', $headers))
        {
            $pass = $headers['pass'];
        }
        if(array_key_exists('name', $headers))
        {
            $name = $headers['name'];
        }
        if(array_key_exists('last_name', $headers))
        {
            $last_name = $headers['last_name'];
        }
        if(array_key_exists('fono', $headers))
        {
            $fono = $headers['fono'];
        }
        if(array_key_exists('email', $headers))
        {
            $email = $headers['email'];
        }


        $resultUserEdit = $db->fromtablaUser($userEdit);

        if($resultUserEdit['code'] == COD_OK)
        {
                $db = new DbHandler();
                if($resultUserEdit['id_tabla'] == 1)
                {
                      $result = $db->editarSuperUsuario($userEdit, $pass, $name, $last_name, $email, $fono);
                }

        }
    }
    /*Agregar otros tipos de usuario*/

    $Msg = 'Editar usuario';

    $code = $result['code'];
    if(array_key_exists('data', $result))
    {
        $data = $result['data'];
    }
    else {
        $data = NULL;
    }
    $encode = 1;
    $response = formatResponse($code, $Msg, $data, $encode);

    return $response;
})->add($mw);

$app->get('/obtienedatos_eusuario', function (Request $request, Response $response) {
    $headers = apache_request_headers();
    $idtabla = $request->getAttribute('id_tabla');
    $userid = $request->getAttribute('userid');
    $useredit = $headers['useredit'];
    $db = new DbHandler();

    $resultUserEdit = $db->fromtablaUser($useredit);

    if($resultUserEdit['code'] == COD_OK)
    {
        if($idtabla == 1)
        {
            $db = new DbHandler();
            if($resultUserEdit['id_tabla'] == 1)
            {
                $result = $db->datosUsuario_SU($useredit);
            }
        }
        else
        {
            $result['code'] = COD_ERROR_ANA;
        }
    }
    else
    {
        $result['code'] = COD_ERROR_ANA;
    }

    $Msg = 'Datos de usuario a editar';

    $code = $result['code'];
    if(array_key_exists('data', $result))
    {
        $data = $result['data'];
    }
    else {
        $data = NULL;
    }
    $encode = 1;
    $response = formatResponse($code, $Msg, $data, $encode);

    return $response;
})->add($mw);

$app->get('/listarusuarios', function (Request $request, Response $response) {
    $headers = apache_request_headers();
    $idtabla = $request->getAttribute('id_tabla');
    $userid = $request->getAttribute('userid');
    $db = new DbHandler();
    if($idtabla == 1)
    {

        $result = $db->listarSuperUsuario();
    }
    else
    {
        $result['code'] = COD_ERROR_ANA;
    }

    $Msg = 'Lista de super usuarios';

    $code = $result['code'];
    if(array_key_exists('data', $result))
    {
        $data = $result['data'];
    }
    else {
        $data = NULL;
    }
    $encode = 1;
    $response = formatResponse($code, $Msg, $data, $encode);

    return $response;
})->add($mw);

/******************FIN********************/

$app->get('/login/{user}/{pass}', function (Request $request, Response $response) {

    $user_v = $request->getAttribute('user');
    $pass_v = $request->getAttribute('pass');

    $db = new DbHandler();

    $result = $db->validaLogin($user_v, $pass_v);
    $Msg = 'Valida Login';

    $code = $result['code'];
    if(array_key_exists('data', $result))
    {
        $data = $result['data'];
    }
    else {
        $data = NULL;
    }
    $enconde = 1;
    $response = formatResponse($code, $Msg, $data, $enconde);

    return $response;
});


$app->get('/loginstatus', function (Request $request, Response $response) {

    $dashboard = array();
    $dashboard['avance_general'] = array(
        'e1' => 40.2,
        'e1Text' => 'Avance ac. programado',
        'e2' => 35.6,
        'e2Text' => 'Avance ac. real'
    );
    $dashboard['avance_semanal'] = array(
        'e1' => 5.2,
        'e1Text' => 'Avance programado',
        'e2' => 1.8,
        'e2Text' => 'Avance real',
        'e3' => 0.9,
        'e3Text' => 'Avance planificado'
    );
    $dashboard['proyeccion_real'] = '06/03/2018';
    $code = 200;
    $Msg = "Ok";
    $data = $dashboard;
    $enconde = 2;
    $response = formatResponse($code, $Msg, $data, $enconde);
    return $response;
})->add($mw);



$app->post('/nuevousuario/{user}/{pass}', function (Request $request, Response $response) {
    $user_v = $request->getAttribute('user');
    $pass_v = $request->getAttribute('pass');

    $db = new DbHandler();
    $result = $db->creaUsuario($user_v, $pass_v);
    $Msg = 'Registro de usuario';

    $code = $result['code'];
    $data = NULL;

    $enconde = 1;
    $response = formatResponse($code, $Msg, $data, $enconde);

    return $response;
});

$app->put('/cambiarcontrasena', function (Request $request, Response $response, $args) {
    //$user_v = $request->getAttribute('nombre');
    $userid = $request->getAttribute('userid');
    $headers = apache_request_headers();
    $newpass = $headers['pass'];

    $db = new DbHandler();
    $result = $db->cambiarPass($userid, $newpass);
    $code = $result['code'];
    $Msg = 'Cambiar contraseña';
    if(array_key_exists('data', $result))
    {
        $data = $result['data'];
    }
    else {
        $data = NULL;
    }
    $enconde = 1;
    $response = formatResponse($code, $Msg, $data, $enconde);

    return $response;
})->add($mw);


$app->get('/listadoproyectos', function (Request $request, Response $response, $args) {
    //$user_v = $request->getAttribute('nombre');
    $userid = $request->getAttribute('userid');
    $db = new DbHandler();
    $result = $db->getProjectList($userid);
    $code = $result['code'];
    $Msg = 'Listado de proyectos';
    if(array_key_exists('data', $result))
    {
        $data = $result['data'];
    }
    else {
        $data = NULL;
    }
    $enconde = 1;
    $response = formatResponse($code, $Msg, $data, $enconde);

    return $response;
})->add($mw);

$app->get('/obtiene_perfilbasico', function (Request $request, Response $response, $args) {
    //$user_v = $request->getAttribute('nombre');
    $userid = $request->getAttribute('userid');
    $db = new DbHandler();
    $result = $db->getPerfilBasico($userid);
    $code = $result['code'];
    $Msg = 'Obtiene datos de perfil de usuario';
    if(array_key_exists('data', $result))
    {
        $data = $result['data'];
    }
    else {
        $data = NULL;
    }
    $enconde = 1;
    $response = formatResponse($code, $Msg, $data, $enconde);

    return $response;
})->add($mw);



$app->put('/nuevo_usuario_update', function (Request $request, Response $response, $args) {
    $headers = apache_request_headers();

    $name = $headers['name'];
    $last_name = $headers['last_name'];
    $fono = $headers['fono'];
    $email = $headers['email'];

    $userid = $request->getAttribute('userid');
    $db = new DbHandler();
    $result = $db->updateDataNewUser($userid,$name, $last_name, $fono, $email );
    $code = $result['code'];
    $Msg = 'Actualiza data nuevo usuario';
    if(array_key_exists('data', $result))
    {
        $data = $result['data'];
    }
    else {
        $data = NULL;
    }
    $enconde = 1;
    $response = formatResponse($code, $Msg, $data, $enconde);

    return $response;
})->add($mw);


$app->run();

?>
