<?php
	session_start();
	include('conexion.php');
	/*
	if(isset($_SESSION['nombre_usuario'])){
		$home = "";
		switch ($_SESSION['tipo']){
			 	case (5) :
                        $home="home_integrante.php";
                        break; 
	            case (4) :
	                	$home="home_grupo.php";
	                    break;
	            case (3) :
	                	$home="home_consultor.php";
	                    break;
	            case (2) :
	                	$home="home_consultor_jefe.php";
	                    break;
	            case (1) :
	                    $home="home_admin.php";
	                    break;                                                             		
	          }   
		header("Location: ../".$home);
	}
	*/
	//include ('verificarSegunCaso.php');
	$id_gestion = -1;
	//include('verificar_gestion.php');

	
	$nombre = trim($_POST['username']);
	$clave  = trim($_POST['password']);
	$tipo   = $_POST['tipo'];
	
	//Aqui poner el codigo para ignorar las partes en que se ignora mayusculas y minusculas.
	$consulta_sql = "SELECT * 
					 FROM usuario 
					 WHERE nombre_usuario COLLATE utf8_bin = '$nombre' and clave COLLATE utf8_bin = '$clave'";
	/*
	if ($tipo == 3 || $tipo == 2) {
		$consulta_sql = $consulta_sql."(tipo_usuario = 2 OR tipo_usuario = 3) AND gestion = 1";
	}else{
		if($tipo == 4) {
			$consulta_sql = $consulta_sql."(tipo_usuario = 4 OR tipo_usuario=5) AND gestion = $id_gestion";
		}else{
			if($tipo == 1){
				$consulta_sql=$consulta_sql."tipo_usuario = 1";
			}else{
				header('location:../index.php');
			}
		}
	}
	*/

	$consulta_sql = $consulta_sql." AND habilitado = 1";
	$consulta     = mysql_query($consulta_sql,$conn)or die("Could not execute the select query.");
	$resultado    = mysql_fetch_assoc($consulta);
	
	if(is_array($resultado) && !empty($resultado)){	
			$_SESSION['id']   = $resultado['id_usuario'];
			$_SESSION['tipo'] = $resultado['tipo_usuario'];
			$nombre_valido    = $resultado['nombre_usuario'];
			$_SESSION['nombre_usuario'] = $nombre_valido;//nombre del usuario						
	}else{	
			/*ACA MANEJAR LOS ERRORES SI ES ADMIN MOSTRAR ACCESO DENEGADO
			SI ES GRUPO EMPRESA O CONSULTOR MOSTRAR EL ERROR EN LA MISMA PAGINA*/
			/*NO EXISTE EL USUARIO*/
		echo "<center><h1>Acceso denegado</h1></center>"."<br />";
		echo "<center><h3>Por favor espera 3 segundos mientras te redirigimos al inicio.</h3></center>"."<br />";
		header('Refresh: 3; url=../index.php');
	}

	if(isset($_SESSION['nombre_usuario'])){
		$bitacora = mysql_query("INSERT into bitacora_sesion(usuario,fecha_hora,operacion)
								VALUES (".$_SESSION['id'].",CURRENT_TIMESTAMP,0)",$conn)
		or die("Error en la bitacora.");
		$home = "home_admin.php";
		if ($_SESSION['tipo']=1) {
			header("Location: ../".$home);		
			mysql_free_result($consulta);
		}else{
			echo "<center><h1>ERROR EN EL ACCESO</h1></center>"."<br />";
			echo "<center><h3>Por favor espera 3 segundos mientras te redirigimos al inicio.</h3></center>"."<br />";
			header('Refresh: 3; url=../index.php');
		}
	}
?>