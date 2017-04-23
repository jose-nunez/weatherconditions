<?php

class AdminPage{
	function AdminPage(){
		if(!isset( $_REQUEST['updated'])) $_REQUEST['updated'] = false;

		$bcwc_use_html5_location = get_option('bcwc_use_html5_location');
		$bcwc_demoId=get_option('bcwc_demoId');

		?>

		<div class="wrap">
		<h2><img src="<?php echo BCWC_URL.'img/icon.png'; ?>" style="width:40px;"/> Bicicultura Weather Conditions</h2>
		<p>
		<form action="options.php" method="post">
		<?php 

			// wp_nonce_field('update-options');
			settings_fields('bcwc_setting');
			// do_settings_sections('bcwc_setting');
		?>
			<table class="table">
				<tr valign="middle">
					<label for="bcwc_use_html5_location">
						<input name="bcwc_use_html5_location" type="checkbox" <?php echo $bcwc_use_html5_location?'checked="checked"':''; ?> />
						Usar localización HTML5
					</label>
				</tr>
				<!-- <tr valign="middle">
					<th scope="row">ID de la página que mostrará la Demo</th>
					<td><input type="text" name="bcwc_demoId" value="<?php echo $bcwc_demoId; ?>" /></td>
				</tr> -->
			</table>
			<?php submit_button();?>
		</form>
		</p>
		<?php 
		global $bcwc_plugin;$bcwc_plugin->loadAdminServices();
		global $bcwc_calidadAire;	
		global $bcwc_radiacionSolar;
		?>
		
		<h3>Estaciones de monitoreo de calidad del aire</h3>
		<input type="button" value="Actualizar listado" class="button" onclick="bcwc_updateStations();">
		<span id="update_message"></span>
		<p id="stations_list"><?php echo $bcwc_calidadAire->printStations(); ?></p>
		
		<h3>Estaciones de monitoreo de radiación solar</h3>
		<p><?php echo $bcwc_radiacionSolar->printStations(); ?></p>
		<?php
			//AQUI RELLENO NECESARIO
	}
}
?>



	