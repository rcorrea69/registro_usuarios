<?php 
require 'funcs/funcs.php';
require 'enviar_mail_usuario.php';
enviarmail('rcorrea@derecho.uba.ar','ruben',' mi prode', 'este es elcuerpo del mail');
echo "Se envio el vendito mail";
?>