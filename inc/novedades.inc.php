<h2>Novedades</h2>
<?php
// USUARIOS CON NOVEDADES
$q_users = mysqli_query($link, "SELECT MAX(novedades.fecha) as fecha,propietario, idusuarios, nombre, apellidos, archivo
									FROM novedades, amigos, usuarios
									LEFT JOIN fotos
									ON idfotos_princi=idfotos
									WHERE {$global_idusuarios} = user1 AND user2 = idusuarios AND user2 = propietario OR
										{$global_idusuarios} = user2 AND user1 = idusuarios AND user1 = propietario
									GROUP BY propietario ORDER BY fecha DESC;");
echo mysqli_error($link);

if(mysqli_num_rows($q_users)>0){
	echo "<div id='seccion_novedades'>";
	while ($r_users = mysqli_fetch_assoc($q_users)) {
		print "	<div class='user'>
					<div class='encabezado' style='display: inline-block;'> 
						<img src='{$r_users['archivo']}' class='imagen_perfil'>
						<div style='display: inline-block;'> 
							<div> 
								<div class='foto_come_titu'><a href='perfil.php?id={$r_users['idusuarios']}'>{$r_users['nombre']} {$r_users['apellidos']}</a></div> 
							</div>
						</div>
					</div>
					<div class='novedades'>";
	
		//novedades
		$q_novedades = mysqli_query($link, "SELECT * FROM novedades WHERE propietario = {$r_users['propietario']} AND fecha > ADDTIME(now(), '-17 0:0:0') ORDER BY fecha DESC");
		//TODO reducir tiempo de busqueda
		while ($r_novedades = mysqli_fetch_assoc($q_novedades)) {
			echo "<div class='novedad'>";
	
			if ($r_novedades['tipo'] == "amistad") {
				if($r_novedades['visitante'] != $global_idusuarios){
					$q_novedad = mysqli_query($link, "SELECT * FROM usuarios WHERE idusuarios = {$r_novedades['visitante']}");
					$r_novedad = mysqli_fetch_assoc($q_novedad);
					echo "agreg&oacute; a " . NombreApellido($r_novedad['nombre']." ".$r_novedad['apellidos']);
				}else{
					echo "es ahora tu amigo!";
				}
				
				echo " <div class='fecha'>" . fecha($r_novedades['fecha'], "now") . "</div>";
			}
	
			if ($r_novedades['tipo'] == "subida_fotos") {
				if($r_novedades['datos']>5){
					$limite = 5;
				}else{
					$limite = $r_novedades['datos'];
				}
				$q_novedad = mysqli_query($link, "SELECT * FROM fotos WHERE uploader = {$r_novedades['propietario']} ORDER BY idfotos DESC LIMIT {$limite}");
				if (mysqli_num_rows($q_novedad) > 0) {
					echo "subi&oacute; {$r_novedades['datos']} fotos <div class='fecha'>" . fecha($r_novedades['fecha'], "now") . "</div><div class='imagenes'>";
					while ($r_novedad = mysqli_fetch_assoc($q_novedad)) {
						echo "<a href='fotos.php?iduser={$r_users['idusuarios']}&idalbum=subidas&idfotos={$r_novedad['idfotos']}'><img src='" . $r_novedad['archivo'] . "' class='imagen'></a>";
					}
					echo "</div>";
				}
			}
	
			if ($r_novedades['tipo'] == "foto_principal") {
				$q_novedad = mysqli_query($link, "SELECT * FROM fotos, usuarios WHERE idfotos = {$r_novedades['datos']} AND idusuarios={$r_novedades['propietario']}");
				echo mysqli_error($link);
				$r_novedad = mysqli_fetch_assoc($q_novedad);					
				echo "cambi&oacute; su foto principal <div class='fecha'>" . fecha($r_novedades['fecha'], "now") . "</div><br><img src='" . $r_novedad['archivo'] . "' style='max-height:100px'>";
			}
	
			if ($r_novedades['tipo'] == "estado") {
				echo "cambi&oacute; su estado a <i>" . $r_novedades['datos'] . "</i> <div class='fecha'>" . fecha($r_novedades['fecha'], "now") . "</div>";
			}
	
			if ($r_novedades['tipo'] == "tablon") {
				$q_novedad = mysqli_query($link, "SELECT * FROM usuarios WHERE idusuarios = {$r_novedades['propietario']} OR idusuarios = {$r_novedades['visitante']}");
				$r_novedad = mysqli_fetch_assoc($q_novedad);
				echo NombreApellido($r_novedad['nombre']." ".$r_novedad['apellidos']) . " coment&oacute; su tabl&oacute;n <div class='fecha'>" . fecha($r_novedades['fecha'], "now") . "</div>";
			}
			echo "</div>";
		}
		echo "</div></div>";
	}
	echo "</div>";
}
?>