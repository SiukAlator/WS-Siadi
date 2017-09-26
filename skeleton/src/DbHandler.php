<?php

class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        include_once dirname(__FILE__) . '/Config.php';
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    public function isValidApiKey($key){
      $retorno = array();
      $stmt = $this->conn->prepare("SELECT count(*) as result, id_tabla, id_usuario
                                  FROM usuarios_token
                                  WHERE token = ?");
      $stmt->bind_param("s", $key);
      if ($stmt->execute()) {
          $cantidad = array();
          $existeU = 0;
          $id_tabla;
          $id_usuario;
          $stmt->bind_result(
              $cantidad['result'],
              $cantidad['id_tabla'],
              $cantidad['id_usuario']
          );
          while ($stmt->fetch()) {
              $existeU = $cantidad['result'];
              $id_tabla = $cantidad['id_tabla'];
              $id_usuario = $cantidad['id_usuario'];
          }

          if($existeU == 1)
          {
              $stmt->close();
              $retorno["code"] = COD_OK;
              $retorno["id_tabla"] = $id_tabla;
              $retorno["id_usuario"] = $id_usuario;
          }
          else {
            $stmt->close();
            $retorno["code"] = COD_ERROR_SE;
          }
      }
      else {
        $stmt->close();
        $retorno["code"] = COD_ERROR_GENERICO;
      }
      return $retorno;


    }


    public function fromtablaUser($userid){
      $retorno = array();
      $stmt = $this->conn->prepare("SELECT count(*) as result
                                  FROM super_usuarios
                                  WHERE user = ?");
      $stmt->bind_param("s", $userid);
      if ($stmt->execute()) {
          $cantidad = array();
          $existeU = 0;
          $id_tabla;
          $id_usuario;
          $stmt->bind_result(
              $cantidad['result']
          );
          while ($stmt->fetch()) {
              $existeU = $cantidad['result'];
          }

          if($existeU == 1)
          {
              $stmt->close();
              $retorno["code"] = COD_OK;
              $retorno["id_tabla"] = 1;
          }
          else {
              $stmt = $this->conn->prepare("SELECT count(*) as result
                                          FROM usuarios_administracion
                                          WHERE email = ?");
              $stmt->bind_param("s", $userid);
              if ($stmt->execute()) {
                  $cantidad = array();
                  $existeU = 0;
                  $id_tabla;
                  $id_usuario;
                  $stmt->bind_result(
                      $cantidad['result']
                  );
                  while ($stmt->fetch()) {
                      $existeU = $cantidad['result'];
                  }

                  if($existeU == 1)
                  {
                      $stmt->close();
                      $retorno["code"] = COD_OK;
                      $retorno["id_tabla"] = 2;
                  }
                  else {
                      $stmt = $this->conn->prepare("SELECT count(*) as result
                                                  FROM usuarios_empleados
                                                  WHERE email = ?");
                      $stmt->bind_param("s", $userid);
                      if ($stmt->execute()) {
                          $cantidad = array();
                          $existeU = 0;
                          $id_tabla;
                          $id_usuario;
                          $stmt->bind_result(
                              $cantidad['result']
                          );
                          while ($stmt->fetch()) {
                              $existeU = $cantidad['result'];
                          }

                          if($existeU == 1)
                          {
                              $stmt->close();
                              $retorno["code"] = COD_OK;
                              $retorno["id_tabla"] = 3;
                          }
                          else {
                              $stmt = $this->conn->prepare("SELECT count(*) as result
                                                          FROM usuarios_condominio
                                                          WHERE email = ?");
                              $stmt->bind_param("s", $userid);
                              if ($stmt->execute()) {
                                  $cantidad = array();
                                  $existeU = 0;
                                  $id_tabla;
                                  $id_usuario;
                                  $stmt->bind_result(
                                      $cantidad['result']
                                  );
                                  while ($stmt->fetch()) {
                                      $existeU = $cantidad['result'];
                                  }

                                  if($existeU == 1)
                                  {
                                      $stmt->close();
                                      $retorno["code"] = COD_OK;
                                      $retorno["id_tabla"] = 4;
                                  }
                                  else {
                                      $stmt->close();
                                      $retorno["code"] = COD_ERROR_UNE;
                                  }
                              }
                        }
                  }
                  }
              }
          }
      }

      else {
        $stmt->close();
        $retorno["code"] = COD_ERROR_GENERICO;
      }
      return $retorno;


    }

    public function getUserId($key){
        $retorno = array();
        $stmt = $this->conn->prepare("SELECT user, id_perfil
                                    FROM dl_usuarios
                                    WHERE token = ?");
        $stmt->bind_param("s", $key);
        if ($stmt->execute()) {
            $user = array();
            $stmt->bind_result(
                $user['user'],
                $user['id_perfil']
            );
            while ($stmt->fetch()) {
                if ($user['user'] != "")
                {
                    $retorno["code"] = 200;
                    $retorno["id"] = $user['user'];
                    $retorno["perfil"] = $user['id_perfil'];
                }
                else {
                    $retorno["code"] = COD_ERROR_UNE;
                    $retorno["id"] = "User not found";
                }
            }


        }
        else {
            return NULL;
        }
        return $retorno;

    }

    public function crearSuperUsuario($user_v, $pass, $name, $last_name, $email, $fono){
        $retorno = array();

        $stmt = $this->conn->prepare("SELECT count(*) as result
                                      FROM super_usuarios
                                      WHERE user = ?");
        $stmt->bind_param("s", $user_v);
        if ($stmt->execute()) {
            $cantidad = array();
            $existeU = 0;
            $stmt->bind_result(
                $cantidad['result']
            );
            while ($stmt->fetch()) {
                $existeU = $cantidad['result'];
            }

            if($existeU == 0)
            {
                $passConcat = $pass.HASHTAB;
                $passEncrypt = sha1($passConcat);
                $stmt = $this->conn->prepare("INSERT INTO super_usuarios(user, pass, name, last_name, num_intentos, habilitado, email, fono)
                                              VALUES(?,?,?,?, 0, 1,?,?)");
                $stmt->bind_param("ssssss", $user_v, $passEncrypt, $name, $last_name, $email, $fono);
                if ($stmt->execute()) {
                    $stmt->close();
                    $retorno["code"] = 200;
                }
                else {
                    $stmt->close();
                    $retorno["code"] = COD_ERROR_GENERICO;
                }
            }
            else {
                $stmt->close();
                $retorno["code"] = COD_ERROR_UE;
            }
        }
        else{
            $stmt->close();
            $retorno["code"] = COD_ERROR_GENERICO;
        }
        return $retorno;
    }


    public function validaLoginSUsers($user_i, $pass_i) {
        $retorno = array();
        $stmt = $this->conn->prepare("SELECT count(*) as result, num_intentos, habilitado
                                      FROM super_usuarios
                                      WHERE user = ?");
        $stmt->bind_param("s", $user_i);
        if ($stmt->execute()) {
            $cantidad = array();
            $existeU = 0;
            $num_intentos = 0;
            $habilitado = 0;
            $stmt->bind_result(
                $cantidad['result'],
                $cantidad['num_intentos'],
                $cantidad['habilitado']
            );
            while ($stmt->fetch()) {
                $existeU = $cantidad['result'];
                $num_intentos = $cantidad['num_intentos'];
                $habilitado =   $cantidad['habilitado'];
            }

            if($existeU == 1)
            {
                if (  $habilitado == 1)
                {
                $passEncode = sha1($pass_i.HASHTAB);
                $stmt = $this->conn->prepare("SELECT count(*) as result, name , last_name
                                            FROM super_usuarios
                                            WHERE user = ? and pass = ?");
                $stmt->bind_param("ss", $user_i, $passEncode);
                if ($stmt->execute()) {
                    $resultado = array();
                    $existeU = 0;
                    $name = "";
                    $last_name = "";
                    $stmt->bind_result(
                        $resultado['result'],
                        $resultado['name'],
                        $resultado['last_name']
                    );
                    while ($stmt->fetch()) {
                        $existeU = $resultado['result'];
                        $name = $resultado['name'];
                        $last_name = $resultado['last_name'];
                    }

                    if($existeU == 1)
                    {
                        $random = rand( 1000 , 9000 );
                        $token = sha1($random.HASHTAB);

                        $stmt = $this->conn->prepare("UPDATE super_usuarios
                                                      SET token = ?,
                                                          num_intentos = 0
                                                      WHERE user = ?");
                        $stmt->bind_param("ss", $token, $user_i);
                        if($stmt->execute())
                        {
                            $stmt = $this->conn->prepare("SELECT count(*) as result
                                                        FROM usuarios_token
                                                        WHERE id_usuario = ?");
                            $stmt->bind_param("s",$user_i);
                            if($stmt->execute())
                            {

                                $existeU = 0;
                                $stmt->bind_result(
                                    $resultado['result']
                                );
                                while ($stmt->fetch()) {
                                    $existeU = $resultado['result'];
                                }

                                if($existeU == 1)
                                {
                                      $stmt = $this->conn->prepare("UPDATE usuarios_token
                                                                    SET token = ?,
                                                                        id_tabla = 1
                                                                    WHERE id_usuario = ?");
                                      $stmt->bind_param("ss",$token, $user_i);
                                      if($stmt->execute())
                                      {
                                          $stmt->close();
                                          $retorno['code'] = COD_OK;
                                          $retorno['data'] = array(
                                            'token' => $token,
                                            'name' => $name,
                                            'last_name' => $last_name
                                          );
                                      }
                                }
                                else {
                                    $id_tabla = 1;
                                    $stmt = $this->conn->prepare("INSERT INTO usuarios_token(token, id_tabla, id_usuario)
                                                                VALUES(?,?,?)");
                                    $stmt->bind_param("sis",$token,$id_tabla,$user_i);
                                    if($stmt->execute())
                                    {
                                        $stmt->close();
                                        $retorno['code'] = COD_OK;
                                        $retorno['data'] = array(
                                          'token' => $token,
                                          'name' => $name,
                                          'last_name' => $last_name
                                        );
                                    }
                                }

                            }

                        }
                        else {
                            $stmt->close();
                            $retorno['code'] = COD_ERROR_GENERICO;
                        }

                    }
                    else {
                      $num_intentos = (int)$num_intentos + 1;
                      $error_cod = COD_ERROR_PASS;
                      if($num_intentos> CANT_INTENTOS_LOGIN)
                      {
                          $habilitado = 0;
                          $error_cod = COD_ERROR_UI;
                      }
                      else {
                          $habilitado = 1;
                      }
                      $stmt = $this->conn->prepare("UPDATE super_usuarios
                                                    SET num_intentos = ?,
                                                        habilitado = ?
                                                    WHERE user = ?");

                      $stmt->bind_param("iis",$num_intentos, $habilitado, $user_i);
                      if($stmt->execute())
                      {
                          $stmt->close();
                          $retorno['code'] = $error_cod;
                          $retorno['data'] = array(
                            'num_intentos' => $num_intentos
                          );
                      }else {
                          $stmt->close();
                          $retorno['code'] = COD_ERROR_GENERICO; //Error consulta
                      }
                    }
                    }else {
                        $stmt->close();
                        $retorno['code'] = COD_ERROR_GENERICO; //Error consulta
                    }
                }
                else {
                    $stmt->close();
                    $retorno['code'] = COD_ERROR_UI;
                }
        }
        else {
            $stmt->close();
            $retorno['code'] = COD_ERROR_UNE;
        }

    }
    return $retorno;

  }

  public function getDashboard($userid, $idtabla) {
      $retorno = array();
      if ($idtabla == 1)
      {
          $stmt = $this->conn->prepare("SELECT name, cant_cuentas, cant_espacios_comunes, logo, direccion, ciudad
                                        FROM condominio");
          if ($stmt->execute()) {
            $resultado = array();
            $condominios = array();

            $stmt->bind_result(
                $resultado['name'],
                $resultado['cant_cuentas'],
                $resultado['logo'],
                $resultado['cant_espacios_comunes'],
                $resultado['direccion'],
                $resultado['ciudad']

            );

            $i = 0;
            while ($stmt->fetch()) {
                $condominios[$i] = array(
                    'name' => $resultado['name'],
                    'cant_cuentas' => $resultado['cant_cuentas'],
                    'logo' => $resultado['logo'],
                    'cant_espacios_comunes' => $resultado['cant_espacios_comunes'],
                    'direccion' => $resultado['direccion'],
                    'ciudad' => $resultado['ciudad']
                );
                $i = $i + 1;
            }
            $retorno['code'] = COD_OK;
            if($i == 0)
            {
              $retorno['data'] = array(
                    'idtabla' => $idtabla
              );
            }
            else {
              $retorno['data'] = array(
                    'idtabla' => $idtabla,
                    'condominios' => $condominios
              );
            }


          }
      }


      return $retorno;
  }

  public function editarSuperUsuario($user_v, $pass, $name, $last_name, $email, $fono){
      $retorno = array();

      $stmt = $this->conn->prepare("SELECT count(*) as result
                                    FROM super_usuarios
                                    WHERE user = ?");
      $stmt->bind_param("s", $user_v);
      if ($stmt->execute()) {
          $cantidad = array();
          $existeU = 0;
          $stmt->bind_result(
              $cantidad['result']
          );
          while ($stmt->fetch()) {
              $existeU = $cantidad['result'];
          }
          if($existeU == 1)
          {
              $passConcat = $pass.HASHTAB;
              $passEncrypt = sha1($passConcat);
              $stmt = $this->conn->prepare("UPDATE super_usuarios
                                            SET pass = COALESCE(?, pass),
                                                name = COALESCE(?, name),
                                                last_name = COALESCE(?, last_name),
                                                email = COALESCE(?, email),
                                                fono = COALESCE(?, fono)
                                            WHERE user = ?");
              $stmt->bind_param("ssssss", $passEncrypt, $name, $last_name, $email, $fono, $user_v);
              if($stmt->execute())
              {
                  $stmt->close();
                  $retorno["code"] = COD_OK;
                  $retorno["data"] = "";
              }
              else{
                  $stmt->close();
                  $retorno["code"] = COD_ERROR_GENERICO;
              }
          }
          else {
              $stmt->close();
              $retorno["code"] = COD_ERROR_UNE;
          }
      }
      else{
          $stmt->close();
          $retorno["code"] = COD_ERROR_GENERICO;
      }
      return $retorno;
  }

  public function datosUsuario_SU($userid){
      $retorno = array();
      $stmt = $this->conn->prepare("SELECT user, name, last_name, email, fono
                                    FROM super_usuarios
                                    WHERE user = ?");
      $stmt->bind_param("s", $userid);
      if ($stmt->execute()) {
          $resultado = array();
          $usuarios = array();

          $stmt->bind_result(
              $resultado['user'],
              $resultado['name'],
              $resultado['last_name'],
              $resultado['email'],
              $resultado['fono']
          );
          $retorno['code'] = COD_OK;
          while ($stmt->fetch()) {
              $retorno['data'] = array(
                'user' => $resultado['user'],
                'name' => $resultado['name'],
                'last_name' => $resultado['last_name'],
                'email' => $resultado['email'],
                'fono' => $resultado['fono']
              );
          }
          $stmt->close();
      }
      else{
          $stmt->close();
          $retorno["code"] = COD_ERROR_GENERICO;
      }
      return $retorno;
  }

  public function listarSuperUsuario(){
      $retorno = array();

      $stmt = $this->conn->prepare("SELECT user, name, last_name, email, fono
                                    FROM super_usuarios");
      if ($stmt->execute()) {
          $resultado = array();
          $usuarios = array();

          $stmt->bind_result(
              $resultado['user'],
              $resultado['name'],
              $resultado['last_name'],
              $resultado['email'],
              $resultado['fono']
          );
          $i = 0;
          while ($stmt->fetch()) {
              $usuarios[$i] = array(
                  'user' => $resultado['user'],
                  'name' => $resultado['name'],
                  'last_name' => $resultado['last_name'],
                  'email' => $resultado['email'],
                  'fono' => $resultado['fono'],
                  'categoria' => 1
              );
              $i = $i + 1;
          }
          $retorno['code'] = 200;
          $retorno['data'] = $usuarios;
      }
      else{
          $stmt->close();
          $retorno["code"] = COD_ERROR_GENERICO;
      }
      return $retorno;
  }


    /******************FIN********************/

    public function asginarProyecto($user_v, $id_proyecto, $id_planificacion){
        $retorno = array();
        $nuevoProyecto = array();
        $stmt = $this->conn->prepare("SELECT id
                                      FROM dl_usuarios
                                      WHERE user = ?");
        $stmt->bind_param("s", $user_v);
        if ($stmt->execute()) {
            $id_usuarioA = array();
            $id_usuario = "";
            $stmt->bind_result(
                $id_usuarioA['id']
            );
            while ($stmt->fetch()) {
                $id_usuario = $id_usuarioA['id'];
            }

            $stmt = $this->conn->prepare("INSERT INTO dl_proy_plan(id_usuario, id_proyecto, id_planificacion)
                                        VALUES(?,?,?)");
            $stmt->bind_param("iii", $id_usuario, $id_proyecto, $id_planificacion);
            if ($stmt->execute()) {
                  $stmt->close();
                  $retorno["code"] = 200;
            }
            else {
                $stmt->close();
                $retorno["code"] = COD_ERROR_GENERICO;
            }
        }
        else {
          $stmt->close();
          $retorno['code'] = COD_ERROR_UNE;

        }
        return $retorno;
    }





    public function creaUsuario($user_i, $pass_i) {
        $retorno = array();
        $stmt = $this->conn->prepare("SELECT count(*) as result
                                    FROM dl_usuarios
                                    WHERE user = ?");
        $stmt->bind_param("s", $user_i);
        if ($stmt->execute()) {
            $cantidad = array();
            $existeU = 0;
            $stmt->bind_result(
                $cantidad['result']
            );
            while ($stmt->fetch()) {
                $existeU = $cantidad['result'];
            }

            if($existeU == 0)
            {
                $passEncode = sha1($pass_i);
                $stmt = $this->conn->prepare("INSERT INTO dl_usuarios(user,pass)
                                            VALUES(?,?)");
                $stmt->bind_param("ss", $user_i, $passEncode);
                if($stmt->execute())
                {
                    $stmt->close();
                    $retorno['code'] = COD_OK;

                }
                else {
                    $stmt->close();
                    $retorno['code'] = COD_ERROR_GENERICO;

                }

            }
            else {
                $stmt->close();
                $retorno['code'] = COD_USER_FAKE;

            }
        }
        else {
            $stmt->close();
            $retorno['code'] = COD_ERROR_GENERICO;

        }
        return $retorno;
    }






    public function cambiarPass($user_i, $newpass_i) {
        $retorno = array();

        if ($newpass_i != "")
        {
            $newpass = sha1($newpass_i);
            $stmt = $this->conn->prepare("UPDATE dl_usuarios
                                          SET pass = ?
                                          WHERE user = ?");
            $stmt->bind_param("ss", $newpass, $user_i);
            if($stmt->execute())
            {
                $stmt->close();
                $retorno['code'] = COD_OK;

            }
            else {
                $stmt->close();
                $retorno['code'] = COD_ERROR_GENERICO;

            }
        }
        else {
              $retorno['code'] = COD_ERROR_PE;
        }


        return $retorno;



      }


      public function getProjectList($id_user){
          $retorno = array();
          $stmt = $this->conn->prepare("SELECT b.id as id_proyecto, c.nombre AS planificacion, b.logo, b.nombre AS proyecto
                                        FROM dl_proy_plan a, dl_proyectos b, dl_planificacion c
                                        WHERE a.id_usuario = (SELECT id FROM dl_usuarios WHERE user = ?) AND a.id_proyecto = b.id
                                        AND a.id_planificacion = c.id
                                        ORDER BY a.id_proyecto");
          $stmt->bind_param("s", $id_user);
          if ($stmt->execute()) {
              $resultado = array();
              $proyectos = array();

              $stmt->bind_result(
                  $resultado['id_proyecto'],
                  $resultado['planificacion'],
                  $resultado['logo'],
                  $resultado['proyecto']
              );
              $i = 0;
              while ($stmt->fetch()) {
                  $proyectos[$i] = array(
                      'id_proyecto' => $resultado['id_proyecto'],
                      'proyecto' => $resultado['proyecto'],
                      'planificacion' => $resultado['planificacion'],
                      'logo' => $resultado['logo']
                  );
                  $i = $i + 1;
              }
              $retorno['code'] = 200;
              $retorno['data'] = $proyectos;

          }
          else {
              return NULL;
          }

          return $retorno;
      }

      public function getPerfilBasico($id_user){
          $retorno = array();
          $stmt = $this->conn->prepare("SELECT name, last_name, fono, email
                                        FROM dl_usuarios
                                        WHERE user = ?");
          $stmt->bind_param("s", $id_user);
          if ($stmt->execute()) {
              $resultado = array();
              $name = "";
              $last_name = "";
              $fono = "";
              $email = "";
              $stmt->bind_result(
                  $resultado['name'],
                  $resultado['last_name'],
                  $resultado['fono'],
                  $resultado['email']
              );

              while ($stmt->fetch()) {
                      $name = $resultado['name'];
                      $last_name = $resultado['last_name'];
                      $fono = $resultado['fono'];
                      $email = $resultado['email'];
              }
              $retorno['code'] = 200;
              $retorno['data'] = array(
                  'name' => $name,
                  'last_name' =>$last_name,
                  'fono' => $fono,
                  'email' => $email
              );
          }
          else {
            $retorno['code'] = COD_ERROR_GENERICO;;
          }

          return $retorno;
      }

      public function updateDataNewUser($userid, $name, $last_name, $fono, $email ){
          $retorno = array();
          $query = "UPDATE dl_usuarios SET newuser = 0";
          if ( $name != "" ){
             $query = $query.", name = '".$name."'";
          }
          else {
            $stmt = $this->conn->prepare("SELECT name FROM dl_usuarios WHERE user = ?");
            $stmt->bind_param("s", $userid);
            if ($stmt->execute()) {
                $resultado = array();

                $stmt->bind_result(
                    $resultado['name']
                );
                while ($stmt->fetch()) {
                    $name = $resultado['name'];
                }
              }
          }
          if ( $last_name != "" ){
             $query = $query.", last_name = '".$last_name."'";
          }
          else {
            $stmt = $this->conn->prepare("SELECT last_name FROM dl_usuarios WHERE user = ?");
            $stmt->bind_param("s", $userid);
            if ($stmt->execute()) {
                $resultado = array();

                $stmt->bind_result(
                    $resultado['last_name']
                );
                while ($stmt->fetch()) {
                    $last_name = $resultado['last_name'];
                }
              }
          }

          if ( $fono !="" ){
             $query = $query.", fono = '".$fono."'";
          }

          if ( $email != "" ){
             $query = $query.", email = '".$email."'";
          }

          $query = $query." WHERE user = '".$userid."'";
          $stmt = $this->conn->prepare($query);

          if ($stmt->execute()) {
              $stmt->close();
              $retorno['code'] = COD_OK;
              $retorno['data'] = array(
                'name' => $name,
                'last_name' => $last_name
              );


          }
          else {
              $retorno['code'] = COD_ERROR_GENERICO;
          }
          return $retorno;

      }
}
?>
