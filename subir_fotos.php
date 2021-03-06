<?php

//TODO: Pulir estetica
require ("inc/verify_login.php");
head("Subir fotos");
require ("upload/subida.html");
?>
</head>
<body>
	<?php
	require ("inc/estructura.inc.php");
	?>
	<div class="barra_full">
	<div class="marco">
		<?php
		if ($_POST['subir_fotos']) {
			$count = 0;
			foreach ($_POST as $name => $value) {
				if (preg_match("/tmpname/", $name, $algohaykeponer) == 1) {
					$count++;
					if($_POST['idalbums']=="")
						$_POST['idalbums'] = "NULL";
					
					mysqli_query($link,"INSERT INTO fotos (titulo,archivo,uploader,fecha,albums_idalbums) VALUES ('" . $_POST['titulo'] . "','user_fotos/" . $global_idusuarios . "-" . limpia_texto($global_nombrefull) . "/" . $value . "','" . $global_idusuarios . "',now()," . $_POST['idalbums'] . ")");
					error_mysql("exit");
				}
			}

			if ($count > 0) {
				//Si ha subido fotos recientemente hacemos update sino insert
				$q_novedades = mysqli_query($link,"SELECT idnovedades,datos FROM novedades WHERE propietario = {$global_idusuarios} AND tipo = 'subida_fotos' AND fecha > ADDTIME(now(), '-7 0:0:0') LIMIT 1");
				if(mysqli_num_rows($q_novedades)==1){
					$r_novedades=mysqli_fetch_assoc($q_novedades);
					$suma = $r_novedades['datos'] + $count;
					mysqli_query($link,"UPDATE novedades SET datos='{$suma}' WHERE idnovedades={$r_novedades['idnovedades']}");
					echo mysqli_error($link);
				}else{
					mysqli_query($link,"INSERT INTO novedades (fecha,tipo, propietario, datos) VALUES (now(),'subida_fotos','{$global_idusuarios}','{$count}')");
				}
				?>
				
				<div class='centrar'>
					<div class="ok_ajustable">
					Fotos subidas con exito, puedes verlas en tus <a href='albums.php' class='link'>albums</a>
					</div>
				</div>
				
				<?php
			} else {
				echo "<div class='centrar'>
					<div class='error_ajustable'>
						Faltan las imagenes
					</div>
				</div>";
			}
		}
		?>
		<form method="post" action="subir_fotos.php" enctype="multipart/form-data">
			<b>Titulo(s):</b>			
				<div class="input">
					<span>
						<input type="text" name="titulo" placeholder="Escribe un titulo para las fotos" size='55'>
					</span>
				</div>
			<br />
			<b>Album:</b>
			<?php
			$albums = mysqli_query($link,"SELECT * FROM `albums` WHERE usuarios_idusuarios='" . $global_idusuarios . "'");
			if (mysqli_num_rows($albums) > 0) {
				print "<div class='input'>
						<span class='select'>
							<select name='idalbums'><option value='NULL'>Ninguno</option>";
				while ($row = mysqli_fetch_assoc($albums)) {
					echo "<option value='" . $row['idalbums'] . "'>" . $row['album'] . "</option>";
				}
				echo "</select></span></div>";
			} else {
				echo "<span class='ayuda' title='Primero debes crear un album'>Ninguno</span>";
			}
			?>
			</select>
			<br />
			<b>Imagenes:</b>
			<br />
			<div id="uploader"></div>
			<input type="hidden" name="subir_fotos" value="1" />
			<button type='submit' class="azul"><span><b>Subir fotos</b></span></button>
		</form>

<?php
require ("inc/chat.php");
?>
</body>
</html>