<?php

require 'funcs/conexion.php';
require 'funcs/funcs.php';
require 'enviar_mail_usuario.php';
$errors = array();

//Aqui va el código PHP del Vídeo
if (!empty($_POST)) {
	$nombre = $mysqli->real_escape_string($_POST['nombre']);
	$usuario = $mysqli->real_escape_string($_POST['usuario']);
	$password = $mysqli->real_escape_string($_POST['password']);
	$con_password = $mysqli->real_escape_string($_POST['con_password']);
	$email = $mysqli->real_escape_string($_POST['email']);

	$captcha = $mysqli->real_escape_string($_POST['g-recaptcha-response']);
	$secret = "6LcaRMkaAAAAAJn4HBrla8fg3cCBqOnlcB01bM-d";


	////////sitio web//////////////////
	// 6Lc83JkiAAAAAHg4Vj8ltWniC2Zd9TbVGINzvjoL

	// clave secreta
	// 6Lc83JkiAAAAAMlBt8WMedeGn5LwZOyzktQJJ11q

	$activo = 0;
	$tipo_usuario = 2;
	$secret = "";

	if (!$captcha) {
		$errors[] = " Por favor verifique el captcha";
	}

	if (isNull($nombre, $usuario, $password, $con_password, $email)) {
		$errors[] = "Debe llenar todos los campos";
	}

	if (!isEmail($email)) {
		$errors[] = "Debe ingresar un mail válido";
	}

	if (!validaPassword($password, $con_password)) {
		$errors[] = "Las contraseñas no coinciden";
	}

	if (usuarioExiste($usuario)) {
		$errors[] = "El usuario $usuario ya existe";
	}

	if (emailExiste($email)) {
		$errors[] = "El correo electronico $email ya existe";
	}

	if (count($errors) == 0) {
		$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha");

		$arr = json_decode($response, true);

		if ($arr['success']) {
			$pass_hash = hashPassword($password);
			$token = generateToken();

			$registro = registraUsuario($usuario, $pass_hash, $nombre, $email, $activo, $token, $tipo_usuario);


			if ($registro > 0) {
				$url = 'http://' . $_SERVER["SERVER_NAME"] . '/registro_usuarios/sistema/activar.php?id=' . $registro . '&val=' . $token;
				$asunto = "Activar Cuenta - Sistema de registro de usuarios";
				$cuerpo = "Estimado $nombre : <br/><br/> Para continuar con el proceso de registro, es indispensable de click en el siguiente link <a href=$url> Activar Cuenta </a>";

				if (enviarmail($email, $nombre, $asunto, $cuerpo)) {
					echo "Para terminar el proceso de registro siga las instrucciones que le hemos enviado la dirección de correo electrocino: $email";

					echo "<br><a href='index.php'>Iniciar Sesion </a>";
					exit;
				} else {
					$errors[] = "Eroor al enviar Email";
				}
			} else {
				$errors[] = "Error al registrar el usuario";
			}
		} else {
			$errors[] = "Error al Comprobar el Captcha";
		}
	}
}

?>

<!doctype html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Registro</title>

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css">
	<script src="js/bootstrap.min.js"></script>

	<script src="https://www.google.com/recaptcha/api.js?render=6Lc83JkiAAAAAHg4Vj8ltWniC2Zd9TbVGINzvjoL"></script>

	<script>
		$(document).ready(function() {
			$('#btn-signup').click(function() {
				grecaptcha.ready(function() {
					grecaptcha.execute('6Lc83JkiAAAAAHg4Vj8ltWniC2Zd9TbVGINzvjoL', {
						action: 'validarUsuario'
					}).then(function(token) {
						// Add your logic to submit to your backend server here.
						$('#signupform').prepend('<input type="hidden" name="token" value="'+token+'">');
						$('#signupform').prepend('<input type="hidden" name="action" value="validarUsuario"');
						$('#signupform').submit();
					});
				});

			})
		});


	</script>

</head>

<body>
	<div class="container">
		<div id="signupbox" style="margin-top:50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
			<div class="panel panel-info">
				<div class="panel-heading">
					<div class="panel-title">Reg&iacute;strate</div>
					<div style="float:right; font-size: 85%; position: relative; top:-10px"><a id="signinlink" href="index.php">Iniciar Sesi&oacute;n</a></div>
				</div>

				<div class="panel-body">

					<form id="signupform" class="form-horizontal" role="form" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" autocomplete="off">

						<div id="signupalert" style="display:none" class="alert alert-danger">
							<p>Error:</p>
							<span></span>
						</div>

						<div class="form-group">
							<label for="nombre" class="col-md-3 control-label">Nombre:</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="nombre" placeholder="Nombre" value="<?php if (isset($nombre)) echo $nombre; ?>" required>
							</div>
						</div>

						<div class="form-group">
							<label for="usuario" class="col-md-3 control-label">Usuario</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="usuario" placeholder="Usuario" value="<?php if (isset($usuario)) echo $usuario; ?>" required>
							</div>
						</div>

						<div class="form-group">
							<label for="password" class="col-md-3 control-label">Password</label>
							<div class="col-md-9">
								<input type="password" class="form-control" name="password" placeholder="Password" required>
							</div>
						</div>

						<div class="form-group">
							<label for="con_password" class="col-md-3 control-label">Confirmar Password</label>
							<div class="col-md-9">
								<input type="password" class="form-control" name="con_password" placeholder="Confirmar Password" required>
							</div>
						</div>

						<div class="form-group">
							<label for="email" class="col-md-3 control-label">Email</label>
							<div class="col-md-9">
								<input type="email" class="form-control" name="email" placeholder="Email" value="<?php if (isset($email)) echo $email; ?>" required>
							</div>
						</div>

						<div class="form-group">
							<label for="captcha" class="col-md-3 control-label"></label>
							<div class="g-recaptcha col-md-9" data-sitekey="6LcaRMkaAAAAAMhIhz0IHLwoM-BW0ujn6928TESn"></div>
						</div>

						<div class="form-group">
							<div class="col-md-offset-3 col-md-9">
								<button id="btn-signup" type="button" class="btn btn-info"><i class="icon-hand-right"></i>Registrar</button>
								<!-- <button class="g-recaptcha" data-sitekey="6Lc83JkiAAAAAHg4Vj8ltWniC2Zd9TbVGINzvjoL" data-callback='onSubmit' data-action='submit'>Registrar</button> -->
							</div>
						</div>
					</form>
					<?php echo resultBlock($errors); ?>
				</div>
			</div>
		</div>
	</div>
</body>

</html>