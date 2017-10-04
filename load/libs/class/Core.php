<?php

/**
 * @name Php Hierarchical Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Core
{
    /* TODO */
	/*mapea las tablas de la base de datos*/

	private $table;
	private $fields = array();
	private $root_email = 'admin@getmapper.xyz';
	private $root_pass = 'Root@2016*';

	public static function run($overwrite_files=false){

		$obj = new self();

		/* Crea la tabla de grupos y permisos */
		$check_groups = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DBName . "' AND TABLE_NAME = 'admins_groups'";
		$check_groups = $obj->execute($check_groups);
		if(is_array($check_groups) && count($check_groups) == 0):
			$groups_table = "CREATE TABLE `admins_groups` (
			  `group_id` int(11) NOT NULL AUTO_INCREMENT,
			  `group_name` varchar(128) DEFAULT NULL,
			  `group_permission` text,
			  `group_isroot` tinyint(1) NOT NULL DEFAULT '0',
			  `group_status` tinyint(1) NOT NULL DEFAULT '1',
			  `group_hidden` tinyint(1) NOT NULL DEFAULT '0',
			  `group_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`group_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
			print("Creando tabla admins_groups... \n\n");
			$obj->execute($groups_table);
		endif;

		/* Crea la tabla de administradores*/
		$check_admins = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DBName . "' AND TABLE_NAME = 'admins'";
		$check_admins = $obj->execute($check_admins);
		if(is_array($check_admins) && count($check_admins) == 0):
			$admins_table = "CREATE TABLE `admins` (
			  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
			  `group_id` int(11) NOT NULL,
			  `admin_names` varchar(128) DEFAULT NULL,
			  `admin_email` varchar(128) DEFAULT NULL,
			  `admin_password` varchar(32) DEFAULT NULL,
			  `admin_random_key` varchar(16) DEFAULT NULL,
			  `admin_random_seed` text,
			  `admin_status` tinyint(1) NOT NULL DEFAULT '1',
			  `admin_hidden` tinyint(1) NOT NULL DEFAULT '0',
			  `admin_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `admin_last_login` timestamp NULL DEFAULT NULL,
			  PRIMARY KEY (`admin_id`),
			  KEY `fk_admins_admins_groups_idx` (`group_id`),
			  CONSTRAINT `fk_admins_admins_groups` FOREIGN KEY (`group_id`) REFERENCES `admins_groups` (`group_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
			print("Creando tabla admins... \n\n");
			$obj->execute($admins_table);
		endif;

		/*crea tabla de registro de intentos de login de administrador*/
		$check_admin_login_attempts = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DBName . "' AND TABLE_NAME = 'admin_login_attempts'";
		$check_admin_login_attempts = $obj->execute($check_admin_login_attempts);
		if(is_array($check_admin_login_attempts) && count($check_admin_login_attempts) == 0):
			$admin_login_attempts = "CREATE TABLE `admin_login_attempts` (
			  `admin_login_attempt_id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `admin_id` int(11) NOT NULL,
			  `admin_login_ip_address` varchar(16) NOT NULL,
			  `admin_login_response` text NOT NULL,
			  `admin_login_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`admin_login_attempt_id`),
			  KEY `fk_admin_login_attempts_admins1_idx` (`admin_id`),
			  CONSTRAINT `fk_admin_login_attempts_admins1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
			print("Creating table admins_login_attempts...\n\n");
			$obj->execute($admin_login_attempts);
		endif;

		/* Inserta grupo Administrador */
		$permissions = json_encode(array("access" => "full", "permissions" => "full"));
		$check_group_root = "SELECT group_name FROM admins_groups WHERE group_name = 'Administrador'";
		$check_group_root = $obj->execute($check_group_root);
		if(is_array($check_group_root) && count($check_group_root) == 0):
			$insert_root = "INSERT INTO admins_groups SET group_name = 'Administrador',
			group_isroot = 1";
			print("Creando grupos & permisos... \n\n");
			$obj->execute($insert_root);
		endif;

		/* Inserta admin root */
		$check_admin_root = "SELECT admin_email FROM admins WHERE admin_email = '" . $obj->root_email . "'";
		$check_admin_root = $obj->execute($check_admin_root);
		if(is_array($check_admin_root) && count($check_admin_root) == 0):
			$root_pass = $obj->root_pass;
			$random_key= uniqcode(16,16);
			$insert_root = "INSERT INTO admins SET admin_names = 'Root Admin',
			group_id = '1',
			admin_email = '" . $obj->root_email . "',
			admin_password = MD5('{$root_pass}_" . strtoupper(strrev($obj->root_email))."'),
			admin_random_key = '{$random_key}',
			admin_random_seed = '". Encryption::Encrypt($root_pass, strrev(md5($random_key)))."'";
			print("Creando usuario root... \n\n");
			$obj->execute($insert_root);
		endif;

		/*crea archivo para el menu*/
		$fcon_menu = '<ul>
	<li class="heading"><span>Sistema</span></li>
	<li class="hasSubmenu"> <a data-toggle="collapse" class="glyphicons cogwheels" href="#menu_config"><i></i><span>Configuraciones</span></a>
		<ul class="collapse" id="menu_config">
			<li><a href="" onclick="module(\'admins_groups\');return!1;"><i></i><span>Grupos y Permisos</span></a></li>
			<li><a href="" onclick="module(\'admins\');return!1;"><i></i><span>Administradores</span></a></li>
		</ul>
	</li>
	<li class="heading"><span>Contenidos</span></li>' . "\n";
		if(!is_file(pathToView . 'cms.menu_left.php') || $overwrite_files):
			$file_menu = @fopen( pathToView . "cms.menu_left.php", "w" );
		endif;
		/*trae tablas*/
		$tables = "SELECT TABLE_NAME, TABLE_COMMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DBName . "'";
		$tables = $obj->execute($tables);

		$override_tables = array(
			"admins_groups",
			"admins",
			"admin_login_attempts"
		);

		if(is_array($tables) && count($tables) > 0):

			/* PARA FORMULARIO CON PERMISOS DEL ADMINISTRADOR */
			$table_list = '$tables = array('."\n";

			foreach($tables as $table):
				if(!in_array($table['TABLE_NAME'], $override_tables)):

					$table_list_title = $table['TABLE_NAME'];
					if(strlen($table['TABLE_COMMENT']) > 0):
						$table_config_per = @json_decode("{". utf8_encode($table['TABLE_COMMENT']) ."}");
						if($table_config_per instanceof stdClass):
							$table_list_title = isset($table_config_per->title)  ? $table_config_per->title  : $table_list_title;
						endif;
					endif;

					$table_list .= '						"'.$table['TABLE_NAME'].'" => "'.ucwords($table_list_title).'",'."\n";
				endif;
			endforeach;

			$table_list .= '					);'."\n";

			if(file_exists(pathToView . "admins_groups.form.php")):
				@unlink(pathToView . "admins_groups.form.php");
			endif;

			if(!is_file( pathToView . "admins_groups.form.php" )):
				$file_admin_form = @fopen( pathToView . "admins_groups.form.php", "w+" );
			endif;

			$file_admin_cont = '<?php
$modulename	= "Grupos y Permisos";
$admin_id	= numParam(\'id\');
$title		= $admin_id > 0 ? "Modificar Registro" : "Nuevo Registro";
$data		= Admins_groups::select($admin_id);
if(is_array($data) && count($data) > 0 && empty($_POST)):
	$_POST = $data[0];
else:
	$fields = Admins_groups::getfields();
	foreach($fields as $k => $v):

		if(isset($_POST[$k])):
			$value = empty($_POST[$k]) ? NULL : $_POST[$k];
		else:
			$value = NULL;
		endif;

		$_POST[$k] = $value;
	endforeach;
	$_POST[\'group_status\'] = $_POST[\'group_status\'] ==  NULL ? 1 : $_POST[\'group_status\'];
endif;
$callback	= array(
	"success"	=> "admins_groups.view.php",
	"error"		=> "admins_groups.form.php"
);
?>
<ul class="breadcrumb">
	<li><a href="" class="glyphicons home" onclick="module(\'dashboard\');return!1;"><i></i> <?php echo sysName;?></a></li>
	<li class="divider"></li>
	<li><a href="" onclick="module(\'admins_groups&page=<?php echo pageNumber();?>\');return!1;"><?php echo $modulename;?></a></li>
	<li class="divider"></li>
	<li><?php echo $title; ?></li>
</ul>
<div class="separator"></div>
<div class="heading-buttons">
	<h3 class="glyphicons parents" style="width:400px !important;"><i></i> <?php echo $title;?></h3>
	<div class="buttons pull-right">
		<a href="" class="btn btn-primary btn-icon glyphicons circle_arrow_left" onclick="module(\'admins_groups&page=<?php echo pageNumber();?>\');return!1;"><i></i>Volver</a>
	</div>
</div>
<div class="separator"></div>
<form class="form-horizontal" style="margin-bottom: 0;" id="admins_groups_form" name="admins_groups_form" method="post" autocomplete="off" onsubmit="savedata(\'admins_groups\');return!1;">
	<div class="well" style="padding-bottom: 20px; margin: 0;">
		<h4>InformaciÃ³n del Registro</h4>
		<?php Message::alert();?>
		<hr class="separator" />
		<div class="row-fluid">
		<div class="span6">
				<div class="control-group<?php echo isset($error[\'group_name\']) ? " error" : "";?>">
					<label class="control-label" for="group_name">Nombre</label>
					<div class="controls">
						<input class="" id="group_name" name="group_name" value="<?php echo htmlspecialchars($_POST[\'group_name\']);?>" type="text" style="color:#000;" />

						<?php
						if(isset($error[\'group_name\'])):
						?>
						<p class="error help-block"><span class="label label-important"><?php echo $error[\'group_name\'];?></span></p>
						<?php
						endif;
						?>
					</div>
				</div>

				<?php
				if(Login::get("group_isroot")==1):

					'.$table_list.'

					$permission = @json_decode(stripslashes($_POST[\'group_permission\']));
					$option_disabled = $_POST[\'group_isroot\'] == 1 ? true : false;

				?>
				<div class="control-group<?php echo isset($error[\'group_permission\']) ? " error" : "";?>">
					<label class="control-label" for="group_permission">Permisos</label>
					<div class="controls" style="width:400px; border:1px solid #ccc; background-color:#fff;">

						<table width="400" border="0" cellspacing="0" cellpadding="5">
						<tr>
							<th style="border-bottom:1px solid #ccc;" width="202" scope="col">&nbsp;</th>
							<th style="border-bottom:1px solid #ccc;" width="66" scope="col">Crear</th>
							<th style="border-bottom:1px solid #ccc;" width="66" scope="col">Modificar</th>
							<th style="border-bottom:1px solid #ccc;" width="66" scope="col">Eliminar</th>
						</tr>
						<?php
						$t=0;
						foreach($tables as $tk => $tv):
							$t++;
							$insert_checked = false;
							$update_checked = false;
							$delete_checked = false;

							if($_POST[\'group_isroot\'] == 1):
								$insert_checked = true;
								$update_checked = true;
								$delete_checked = true;
							else:
								if($permission instanceof stdClass):
									eval(\'$insert_checked = $permission->\' . $tk . \'->insert == 1 ? true : false;\');
									eval(\'$update_checked = $permission->\' . $tk . \'->update == 1 ? true : false;\');
									eval(\'$delete_checked = $permission->\' . $tk . \'->delete == 1 ? true : false;\');
								endif;
							endif;

							$border = $t < count($tables) ? \'border-bottom:1px solid #ccc;\' : \'\';
						?>
						<tr>
							<th scope="row" style="<?php echo $border;?> text-align:left;"><?php echo htmlspecialchars($tv);?></th>
							<td style="<?php echo $border;?>" align="center"><input type="checkbox" name="<?php echo $tk;?>_permission_insert" id="<?php echo $tk;?>_permission_insert" value="1"<?php if($option_disabled){?> disabled="disabled"<?php } ?><?php if($insert_checked){?> checked="checked"<?php } ?> /></td>
							<td style="<?php echo $border;?>" align="center"><input type="checkbox" name="<?php echo $tk;?>_permission_update" id="<?php echo $tk;?>_permission_update" value="1"<?php if($option_disabled){?> disabled="disabled"<?php } ?><?php if($update_checked){?> checked="checked"<?php } ?> /></td>
							<td style="<?php echo $border;?>" align="center"><input type="checkbox" name="<?php echo $tk;?>_permission_delete" id="<?php echo $tk;?>_permission_delete" value="1"<?php if($option_disabled){?> disabled="disabled"<?php } ?><?php if($delete_checked){?> checked="checked"<?php } ?> /></td>
						</tr>
						<?php
						endforeach;
						?>
					</table>

						<?php
						if(isset($error[\'group_permission\'])):
						?>
						<p class="error help-block"><span class="label label-important"><?php echo $error[\'group_permission\'];?></span></p>
						<?php
						endif;
						?>
					</div>
				</div>
				<?php
				endif;
				?>
				<?php if(Login::get("group_isroot") == 1){?>
				<div class="control-group<?php echo isset($error[\'group_isroot\']) ? " error" : "";?>">
					<label class="control-label" for="group_isroot">Super Administrador</label>
					<div class="controls">
						<input class="" id="group_isroot" name="group_isroot" value="1" type="checkbox" style="color:#000;"<?php if($_POST[\'group_isroot\'] == 1){?> checked="checked"<?php } ?> />

						<?php
						if(isset($error[\'group_isroot\'])):
						?>
						<p class="error help-block"><span class="label label-important"><?php echo $error[\'group_isroot\'];?></span></p>
						<?php
						endif;
						?>
					</div>
				</div>
				<?php }?>
				<div class="control-group<?php echo isset($error[\'group_status\']) ? " error" : "";?>">
					<label class="control-label" for="group_status">Activo</label>
					<div class="controls">
						<input class="" id="group_status" name="group_status" value="1" type="checkbox" style="color:#000;"<?php if($_POST[\'group_status\'] == 1){?> checked="checked"<?php } ?> />

						<?php
						if(isset($error[\'group_status\'])):
						?>
						<p class="error help-block"><span class="label label-important"><?php echo $error[\'group_status\'];?></span></p>
						<?php
						endif;
						?>
					</div>
				</div>


						</div>
				</div>
		<hr class="separator" />
		<div class="form-actions">
			<input type="hidden" name="id" value="<?php echo $group_id;?>" />
			<input type="hidden" name="token" value="<?php echo token("Admins_groups::save(".$group_id.")");?>" />
			<input type="hidden" name="callback" value="<?php echo token(json_encode($callback));?>" />
			<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i>Aceptar</button>
			<button type="button" class="btn btn-icon btn-default glyphicons circle_remove" onclick="module(\'admins_groups&page=<?php echo pageNumber();?>\');return!1;"><i></i>Cancelar</button>
		</div>
	</div>
</form>';
			if(is_resource($file_admin_form)):
				fwrite($file_admin_form, $file_admin_cont);
				fclose($file_admin_form);
			endif;

			/* FIN PARA FORMULARIO CON PERMISOS DEL ADMINISTRADOR */

			$n=1;
			foreach($tables as $table):

				$file_class = false;
				$file_view = false;
				$file_form = false;

				if(!in_array($table['TABLE_NAME'], $override_tables)):
					if(!is_file( pathToController . $table['TABLE_NAME'] . ".class.php") || $overwrite_files ):
						$file_class = @fopen( pathToController . $table['TABLE_NAME'] . ".class.php", "w+" );
					endif;

					if(!is_file( pathToView . $table['TABLE_NAME'] . ".view.php") || $overwrite_files):
						$file_view = @fopen( pathToView . $table['TABLE_NAME'] . ".view.php", "w+" );
					endif;

					if(!is_file( pathToView . $table['TABLE_NAME'] . ".form.php") || $overwrite_files):
						$file_form = @fopen( pathToView . $table['TABLE_NAME'] . ".form.php", "w+" );
					endif;

					$status_class = $file_class == false ? '[** Controller FAIL **]' : '[Controller OK]';
					$status_view  = $file_view  == false ? '[** View FAIL **]' : '[View OK]';
					$status_form  = $file_form  == false ? '[** Form FAIL **]' : '[Form OK]';
					print('Mapping table: ' .$table['TABLE_NAME']. ' ['.$n.'/'.count($tables).']...' . $status_class . $status_view . $status_form . "\n\n");

				endif;


				$n++;

				$primaryKey = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DBName . "' AND TABLE_NAME = '{$table['TABLE_NAME']}' AND COLUMN_KEY = 'PRI'";
				$primaryKey = $obj->execute($primaryKey);
				$primaryKey = $primaryKey[0]['COLUMN_NAME'];
				$colprefix	= explode("_", $primaryKey);
				$colprefix	= $colprefix[0];

				$title = ucwords($table['TABLE_NAME']);
				$icon  = "globe";
				$login = false;
				$image = false;
				$file  = false;
				$imageh = 0;
				$imagew = 0;
				$table_parent = "none";
				$table_type = "normal";
				$table_menu = true;
				$parent_option = '';
				$tree_function = '';
				$get_function = '';

				$onExport = false;
				$export = '';
				$big_wh = '800,600';
				$small_wh = '150,150';
				$onForm = false;

				if(strlen($table['TABLE_COMMENT']) > 0):

					$table_config = @json_decode("{". utf8_encode($table['TABLE_COMMENT']) ."}");

					if($table_config instanceof stdClass):

						$title = isset($table_config->title)  ? $table_config->title  : $title;
						$icon  = isset($table_config->icon)	  ? $table_config->icon	  : $icon;

						$image = isset($table_config->images) ? (strtolower($table_config->images) == "true" ? true : false) : $image;
						$file  = isset($table_config->files) ? (strtolower($table_config->files) == "true" ? true : false) : $file;
						$table_type 	= isset($table_config->type)  ? strtolower($table_config->type) : $table_type;
						$table_menu 	= isset($table_config->menu)  ? (strtolower($table_config->menu)   == "true" ? true : false) : $table_menu;
						$table_parent 	= isset($table_config->parent) ? strtolower($table_config->parent) : $table_parent;

						$login = isset($table_config->login) ? (strtolower($table_config->login) == "true" ? true : false) : $login;
						$onExport  = isset($table_config->export) ? (strtolower($table_config->export) == "true" ? true : false) : $onExport;
						$onForm = isset($table_config->form) ? (strtolower($table_config->form) == "true" ? true : false) : $onForm;
					endif;
				endif;

				/*trae columnas de las tablas*/
				$columns = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DBName . "' AND TABLE_NAME = '{$table['TABLE_NAME']}'";
				$columns = $obj->execute($columns);
				foreach ($columns as $column):
					if(strlen($column['COLUMN_COMMENT']) > 0):
						$column_config = @json_decode("{" . utf8_encode($column['COLUMN_COMMENT']) . "}");
						if($column_config instanceof stdClass):
							$big_wh		= isset($column_config->big)? $column_config->big : $big_wh;
							$small_wh	= isset($column_config->small)? $column_config->small : $small_wh;
						endif;
					endif;
				endforeach;
				if($onExport):
					if(!is_dir("download")):
						@mkdir("download",0777);
					endif;
					$export = '
	<div class="buttons pull-right">
		<a href="./download?export='.$table['TABLE_NAME'].'" class="btn btn-primary btn-icon glyphicons circle_plus"><i></i>Exportar</a>
	</div>';
				endif;

				if($table_parent != "none"):

					$parents = explode(",", $table_parent);

					if(is_array($parents) && count($parents) > 0):

						foreach($parents as $parent_table):
							$parent_option .= '<a href="" class="btn-action glyphicons '.$icon.' btn-success" onclick="module(\'' . $parent_table . '&'. $primaryKey .'=<?php echo $list[\''.$primaryKey.'\'];?>&page=<?php echo $page;?>\');return!1;"><i></i></a>'."\n";
						endforeach;

						$parent_option .= '					';

					endif;
				endif;

				if($table_type == "category"):
					$category_process = '
		if(number($_POST[\'{$colprefix}_parent\']) == 1):
			$res = $obj->find($obj->tableName, "{$colprefix}_parent", 0);
		else:
			$res = $obj->select(number($_POST[\'{$colprefix}_parent\']));
		endif;

		$left	= $res[0][\'{$colprefix}_left\'];
		$right	= $res[0][\'{$colprefix}_right\'];

		if($id == 0):

			$limit_l = "UPDATE '.$table['TABLE_NAME'].' SET {$colprefix}_left	= {$colprefix}_left  + 2 WHERE {$colprefix}_left  > {$left}";
			$limit_r = "UPDATE '.$table['TABLE_NAME'].' SET {$colprefix}_right= {$colprefix}_right + 2 WHERE {$colprefix}_right > {$left}";
			$obj->execute($limit_l);
			$obj->execute($limit_r);

			$left  = $res[0][\'{$colprefix}_left\'] + 1;
			$right = $res[0][\'{$colprefix}_left\'] + 2;

		else:

			$res 	= self::select($id);
			$left	= $res[0][\'{$colprefix}_left\'];
			$right	= $res[0][\'{$colprefix}_right\'];

		endif;

		$obj->fields[\'{$colprefix}_left\'][\'value\']	= $left;
		$obj->fields[\'{$colprefix}_right\'][\'value\']	= $right;'."\n";

	$tree_function = '
	public static function tree($id){
		$obj = new self();
		$id = number($id);
		$sql = "
		SELECT c1.'.$colprefix.'_parent AS p1, c1.'.$colprefix.'_left AS l1, c1.'.$colprefix.'_right AS r1,
		c2.* FROM '.$table['TABLE_NAME'].' c1, '.$table['TABLE_NAME'].' c2
		WHERE c1.'.$colprefix.'_id = $id AND(
			c2.'.$colprefix.'_id = $id OR(
				c2.'.$colprefix.'_parent >= 0 AND
				c2.'.$colprefix.'_left < c1.'.$colprefix.'_left AND c2.'.$colprefix.'_right > c1.'.$colprefix.'_right
			)
		) ORDER BY c2.'.$colprefix.'_id ASC
		";
		return $obj->execute($sql);
	}';

	$get_function = '
	public static function get($parent=1){
		$obj = new self();
		$get = "SELECT *
		FROM '.$table['TABLE_NAME'].'
		WHERE '.$colprefix.'_parent = \'{$parent}\'
		AND '.$colprefix.'_status = 1
		AND '.$colprefix.'_hidden = 0
		ORDER BY '.$colprefix.'_nombre";
		$get = $obj->execute($get);
		return $get;
	}';

				else:
					$category_process = "";
	$get_function = '
	public static function get($where=null,$order=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$ord = $order == null ? "" : " ORDER BY {$order}";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}'.$colprefix.'_status = 1 AND '.$colprefix.'_hidden = 0{$ord}";
		return $obj->execute($sql);
	}';
				endif;

				if($image || $file):

					/*crea directorios para upload*/
					@mkdir(rootUpload . $table['table_name'], 0777);

					/*view*/
					$callback_success	= 'upload.success.php';
					$callback_error		= 'upload.error.php';
					$form_config		= ' action="js/save" target="upload_frame" enctype="multipart/form-data"';
					$uploader			= '<iframe name="upload_frame" id="upload_frame" style="border:none;width:10px; height:10px;"></iframe>';
					$upload_option		= '<input type="hidden" name="option" value="'.$table['TABLE_NAME'].'" id="option" />';
					$upload_error_handler = '
if(isset($_GET[\'error\'])):
	$error = array();
	$_POST = $_GET;
	foreach($_GET as $gk => $gv):
		if(strpos($gk,"error_") !== false):
			$error[str_replace("error_","", $gk)] = $gv;
		endif;
	endforeach;
endif;'."\n";
				if($image):
					if(!is_dir("upload")):
						@mkdir("upload",0777);
					endif;
					@mkdir("upload/".strtolower($table['TABLE_NAME']),0777);
					/*controller*/
					$image_process = "\n".'
		if(strlen($_FILES[\'{$field_file_name}\'][\'name\'])> 0):

			if($_FILES[\'{$field_file_name}\'][\'error\'] == 0):

				if($id > 0):
					$picture = self::select($id);
					if(count($picture) > 0):
						@unlink($picture[0][\'{$field_image_small_path}\']);
						@unlink($picture[0][\'{$field_image_big_path}\']);
					endif;
				endif;

				$sourceName	 = $_FILES[\'{$field_file_name}\'][\'name\'];
				$sourceImage = $_FILES[\'{$field_file_name}\'][\'tmp_name\'];
				$targetImage = strtoupper(uniqid(randomnumbers(6,8).\'_\'.randomnumbers(6,8).\'_\'));
				$size = getimagesize($sourceImage);
      			$size_w = $size[0]; // width
      			$size_h = $size[1]; // height

				/* sube la foto */
				$img = new ImageUpload();
				$img->setOutputFormat("JPG");
				$img->fileToResize($sourceImage);
				$img->setAlignment("center");
				$img->setBackgroundColor(array(255, 255, 255));

				$img->setOutputFile($targetImage . "_B");
				$img->setTarget(rootUpload . "{$target_folder_name}/");
				$img->setSize($size_w,$size_h);
				$img->Resize();
				$file_large = $img->getOutputFileName();

				$img->setOutputFile($targetImage . "_S");
				$img->setSize('.$small_wh.');
				$img->Resize();
				$file_small = $img->getOutputFileName();

				$obj->fields[\'{$field_file_name}\'][\'value\']			= $sourceName;
				$obj->fields[\'{$field_image_big_path}\'][\'value\']	= rootUpload . "{$target_folder_name}/" . $file_large;
				$obj->fields[\'{$field_image_big_url}\'][\'value\']		= uploadURL  . "{$target_folder_name}/" . $file_large;
				$obj->fields[\'{$field_image_small_path}\'][\'value\']	= rootUpload . "{$target_folder_name}/" . $file_small;
				$obj->fields[\'{$field_image_small_url}\'][\'value\']	= uploadURL  . "{$target_folder_name}/" . $file_small;

			else:
				Message::set("Por favor, elija una foto", MESSAGE_ERROR);
				return $obj->error;
			endif;

		else:

			if($id > 0):
				$picture = self::select($id);
				if(count($picture) > 0):
					$obj->fields[\'{$field_file_name}\'][\'value\']				= $picture[0][\'{$field_file_name}\'];
					$obj->fields[\'{$field_image_big_path}\'][\'value\']		= $picture[0][\'{$field_image_big_path}\'];
					$obj->fields[\'{$field_image_big_url}\'][\'value\']			= $picture[0][\'{$field_image_big_url}\'];
					$obj->fields[\'{$field_image_small_path}\'][\'value\']		= $picture[0][\'{$field_image_small_path}\'];
					$obj->fields[\'{$field_image_small_url}\'][\'value\']		= $picture[0][\'{$field_image_small_url}\'];
				endif;
			else:

				Message::set("Por favor, elija una imagen", MESSAGE_ERROR);
				return $obj->error;

			endif;


		endif;'."\n";
				else:
					$image_process = '';
				endif;

					$image_delete_on_error = '
			@unlink(rootUpload . "'.$table['TABLE_NAME'].'/" . $file_large);
			@unlink(rootUpload . "'.$table['TABLE_NAME'].'/" . $file_small);'."\n";
				else:
					$image_process = '';
					$image_delete_on_error = '';
					$callback_success	= $table['TABLE_NAME'].'.view.php';
					$callback_error		= $table['TABLE_NAME'].'.form.php';
					$form_config		= ' onsubmit="savedata(\''.$table['TABLE_NAME'].'\');return!1;"';
					$uploader			= '';
					$upload_option		= '';
					$upload_error_handler = '';
				endif;

				if($file):
					if(!is_dir("upload")):
						@mkdir("upload",0777);
					endif;
					@mkdir("upload/".strtolower($table['TABLE_NAME']),0777);
					$file_process = "\n".'
		/*sube archivos*/
		if(strlen($_FILES[\'{$field_file_name}\'][\'name\'])> 0):

			$sourceName	= $_FILES[\'{$field_file_name}\'][\'name\'];
			$sourceFile = $_FILES[\'{$field_file_name}\'][\'tmp_name\'];
			$fileExt	= explode(".", $sourceName);
			$fileExt	= strtolower($fileExt[count($fileExt) - 1]);
			$targetFile = strtoupper(uniqid(randomnumbers(6,8).\'_\'.randomnumbers(6,8).\'_\'));
			$file_name	= $targetFile . "." . $fileExt;

			if($_FILES[\'{$field_file_name}\'][\'error\'] == 0):

				if($id > 0):
					$file = self::select($id);
					if(count($file) > 0):
						@unlink($file[0][\'{$field_file_path}\']);
					endif;
				endif;

				$format = false;

				switch($_FILES[\'{$field_file_name}\'][\'type\']):
					case "application/pdf":
						$format = true;
						break;
				endswitch;

				if($format):
					$obj->fields[\'{$field_file_name}\'][\'value\']	= $sourceName;
					$obj->fields[\'{$field_file_path}\'][\'value\']	= rootUpload . "{$target_folder_name}/" . $file_name;
					$obj->fields[\'{$field_file_url}\'][\'value\']	= uploadURL  . "{$target_folder_name}/" . $file_name;
					@move_uploaded_file($sourceFile, rootUpload . "{$target_folder_name}/" . $file_name);
				else:
					Message::set("El formato del archivo que intenta subir es incorrecto", MESSAGE_ERROR);
					return $obj->error;
				endif;

			else:
				Message::set("Por favor, elija un archivo", MESSAGE_ERROR);
				return $obj->error;
			endif;

		else:

			if($id > 0):
				$file = self::select($id);
				if(count($file) > 0):
					$obj->fields[\'{$field_file_name}\'][\'value\']	= $file[0][\'{$field_file_name}\'];
					$obj->fields[\'{$field_file_path}\'][\'value\']	= $file[0][\'{$field_file_path}\'];
					$obj->fields[\'{$field_file_url}\'][\'value\']	= $file[0][\'{$field_file_url}\'];
				endif;
			else:

				Message::set("Por favor, elija un archivo", MESSAGE_ERROR);
				return $obj->error;

			endif;


		endif;'."\n";
				else:
					$file_process = '';
				endif;

				if($table['TABLE_NAME'] != "admins_groups" && $table['TABLE_NAME'] != "admins" && $table['TABLE_NAME'] != "admin_login_attempts" && $table_menu):
					$fcon_menu .= '<?php if(access(\'' . $table['TABLE_NAME'] . '\')): ?>'."\n";
					$fcon_menu .= '	<li class="glyphicons '.$icon.'"><a href="" onclick="module(\'' . $table['TABLE_NAME'] . '\');return!1;"><i></i><span>'.$title.'</span></a></li>'. "\n";
					$fcon_menu .= '<?php endif; ?>'."\n";
				endif;
				/*
				COMIENZA LA GENERACION PARA EL ARCHIVO Class
				*/
				$fcon_class = '<?php
class ' . ucfirst($table['TABLE_NAME']) . ' extends Mysql{
	protected $tableName	= "'.$table['TABLE_NAME'].'";' ."\n";

				$fcon_form = '<?php
clearBuffer();
$modulename	= "' .$title. '";
$'.$primaryKey.'	= numParam(\'id\');
$title		= $'.$primaryKey.' > 0 ? "Modificar" : "Nuevo";
$data		= '.ucfirst($table['TABLE_NAME']).'::select($'.$primaryKey.');
'.$upload_error_handler.'
if(is_array($data) && count($data) > 0 && empty($_POST)):
	$_POST = $data[0];
else:
	$fields = '.ucfirst($table['TABLE_NAME']).'::getfields();
	foreach($fields as $k => $v):
		if(isset($_POST[$k])):
			$value = empty($_POST[$k]) ? NULL : $_POST[$k];
		else:
			$value = NULL;
		endif;
		$_POST[$k] = $value;
	endforeach;
	$_POST[\''.$colprefix.'_status\'] = $_POST[\''.$colprefix.'_status\'] == NULL ? 1 : $_POST[\''.$colprefix.'_status\'];
endif;
$callback	= array(
	"success"	=> "'.$callback_success.'",
	"error"		=> "'.$callback_error.'"
);
?>
<ul class="breadcrumb">
	<li><a href="" class="glyphicons home" onclick="module(\'dashboard\');return!1;"><i></i> <?php echo sysName;?></a></li>
	<li class="divider"></li>
	<li><a href="" onclick="module(\'' .$table['TABLE_NAME']. '&page=<?php echo pageNumber();?>\');return!1;"><?php echo $modulename;?></a></li>
	<li class="divider"></li>
	<li><?php echo $title; ?></li>
</ul>
<div class="separator"></div>
<div class="heading-buttons">
	<h3 class="glyphicons '.$icon.'" style="width:50% !important;"><i></i><a href="" onclick="module(\''.$table['TABLE_NAME'].'&page=<?php echo pageNumber();?>\');return!1;"><?php echo htmlspecialchars($modulename);?></a> &gt; <?php echo $title;?></h3>
	<div class="buttons pull-right">
		<a href="" class="btn btn-primary btn-icon glyphicons circle_arrow_left" onclick="module(\'' . $table['TABLE_NAME'] . '&page=<?php echo pageNumber();?>\');return!1;"><i></i>Volver</a>
	</div>
</div>
<div class="separator"></div>
<form class="form-horizontal" style="margin-bottom: 0;" id="'.$table['TABLE_NAME'].'_form" name="'.$table['TABLE_NAME'].'_form" method="post" autocomplete="off"'.$form_config.'>
	<div class="well" style="padding-bottom: 20px; margin: 0;">
		<h4>Informaci&oacute;n de <?php echo $modulename;?></h4>
		<?php Message::alert();?>
		<hr class="separator" />
		<div class="row-fluid">
		<div class="span6">';

				/*trae columnas de las tablas*/
				$columns = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DBName . "' AND TABLE_NAME = '{$table['TABLE_NAME']}'";
				$columns = $obj->execute($columns);

				$fields 		= isset($fields) 		? $fields 		: NULL;
				$post_fields 	= isset($post_fields) 	? $post_fields 	: NULL;
				$tooltip	 	= isset($tooltip) 		? $tooltip	 	: NULL;
				$delete_action	= isset($delete_action) ? $delete_action: NULL;

				foreach($columns as $column):

					if($column['COLUMN_KEY'] == "PRI"):
						$primaryKey  = $column['COLUMN_NAME'];
						$fcon_class .= '	protected $primaryKey = "'.$column['COLUMN_NAME'].'";' . "\n";
					else:

						$name_col = '<strong><?php echo htmlspecialchars($list[\''.$colprefix.'_name\']);?></strong>';

						if(strpos($column['COLUMN_NAME'], "_hidden") !== false):
							$delete_action = '$obj->change($obj->tableName, "'.$colprefix.'_hidden", 1, $obj->primaryKey . " = {$id}");';
							/*
							$delete_action = '$delete = "UPDATE ' . $table['TABLE_NAME'] . ' SET ' . $column['COLUMN_NAME'] . ' = 1 WHERE " . $obj->primaryKey . " = {$id}";'."\n\t\t";
							$delete_action .= '$obj->Execute($delete);';
							*/
							if($image || $file):

								$name_col = '<img src="<?php echo $list[\''.$colprefix.'_image_small_url\'];?>" width="150" /></strong>';

								$delete_action .= '
		$picture = self::select($id);
		if(count($picture) > 0):
			@unlink($picture[0][\''.$colprefix.'_image_small_path\']);
			@unlink($picture[0][\''.$colprefix.'_image_big_path\']);
		endif;
';

							else:
								$name_col = '<strong><?php echo htmlspecialchars($list[\''.$colprefix.'_name\']);?></strong>';
							endif;

						else:


							if(strpos($column['COLUMN_NAME'], "_name") !== false):
								$name_column = $column['COLUMN_NAME'];
							endif;

							$tooltip = '';
							$field_type		= "text";
							$field_label	= $column['COLUMN_NAME'];
							$field_preview	= '';
							$field_value	= '<?php echo htmlspecialchars($_POST[\''. $column['COLUMN_NAME'] .'\']);?>';
							$field_input	= '<input class="" id="'.$column['COLUMN_NAME'].'" name="'.$column['COLUMN_NAME'].'" value="'.$field_value.'" type="text" style="color:#000;" />';
							$field_tooltip  = '';
							$field_visible	= true;
							$field_required	= $column['IS_NULLABLE'] == "YES" ? false : true;
							$field_validate = "none";
							$field_maxlen	= '';
							$field_minlen	= '';

							if(strlen($column['COLUMN_COMMENT']) > 0):

								$column_config = @json_decode("{" . utf8_encode($column['COLUMN_COMMENT']) . "}");

								if($column_config instanceof stdClass):

									$field_label	= isset($column_config->label)	 ? ucwords($column_config->label) 	: ucwords($field_label);
									$field_type 	= isset($column_config->type)	 ? $column_config->type 	: $field_type;
									$field_value	= isset($column_config->value)	 ? $column_config->value 	: $field_value;
									$field_tooltip	= isset($column_config->tooltip) ? $column_config->tooltip 	: $field_tooltip;
									$field_visible  = isset($column_config->visible) ? (strtolower($column_config->visible) == "false" ? false : true) : $field_visible;
									$field_type		= strtolower($field_type);
									$field_required = isset($column_config->required) ? (strtolower($column_config->required) == "false" ? false : true) : $field_required;
									$field_validate = isset($column_config->validation) ? (strlen($column_config->validation) > 0 ? strtolower($column_config->validation) : "none") : $field_validate;
									$field_maxlen	= isset($column_config->maxlen) ? (number($column_config->maxlen) > 0 ? ', "maxlen"=>"'.number($column_config->maxlen).'"' : '') : $field_maxlen;
									$field_minlen	= isset($column_config->minlen) ? (number($column_config->minlen) > 0 ? ', "minlen"=>"'.number($column_config->minlen).'"' : '') : $field_minlen;

									$length 	= number_format($column['CHARACTER_MAXIMUM_LENGTH'],0,"","") == 0 ? '' : ',	"length"=> '.$column['CHARACTER_MAXIMUM_LENGTH'];
									$required 	= $field_required ? ', "required" => "1"' : ', "required" => "0"';
									$fields    .= '		"' .$column['COLUMN_NAME']. '"	=> array("type" => "'.$column['DATA_TYPE'].'"'. $length . $required . $field_minlen . $field_maxlen . ', "validation" => "'.$field_validate.'"),' ."\n";

									switch($field_type):
										default:
										case "text":
										case "email":
											$field_input = $field_input;
											break;
										case "file":

										$field_preview = '<?php
				if($'.$primaryKey.' > 0):
				?>
				<div class="control-group">
					<label class="control-label" for="banner_href">Archivo actual</label>
					<div class="controls">
						<span><?php echo $_POST[\''.$colprefix.'_file_name\'];?></span>
					</div>
				</div>
				<?php
				endif;
				?>'."\n";
										$field_input = '<div class="fileupload fileupload-new" data-provides="fileupload">
						  	<div class="input-append">
						    	<div class="uneditable-input span3"><i class="icon-file fileupload-exists"></i> <span class="fileupload-preview"></span></div><span class="btn btn-file"><span class="fileupload-new">Buscar archivo</span><span class="fileupload-exists">Cambiar</span><input type="file" name="'.$column['COLUMN_NAME'].'" id="'.$column['COLUMN_NAME'].'" /></span><a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Quitar</a>
						  	</div>
						</div>';

											break;
										case "image":

										$field_preview = '
								<?php
				if($'.$primaryKey.' > 0):
				?>
				<div class="control-group">
					<label class="control-label" for="banner_href">Imagen actual</label>
					<div class="controls">
						<img src="<?php echo $_POST[\''.$colprefix.'_image_small_url\'];?>" />
					</div>
				</div>
				<?php
				endif;
				?>'."\n";
										$field_input = '<div class="fileupload fileupload-new" data-provides="fileupload">
						  	<div class="input-append">
						    	<div class="uneditable-input span3"><i class="icon-file fileupload-exists"></i> <span class="fileupload-preview"></span></div><span class="btn btn-file"><span class="fileupload-new">Buscar imagen</span><span class="fileupload-exists">Cambiar</span><input type="file" name="'.$column['COLUMN_NAME'].'" id="'.$column['COLUMN_NAME'].'" /></span><a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Quitar</a>
						  	</div>
						</div><label>(Dimensiones '.$big_wh.'px)</label>';

											break;
										case "checkbox":
											$field_input = '<input class="" id="'.$column['COLUMN_NAME'].'" name="'.$column['COLUMN_NAME'].'" value="'.$field_value.'" type="checkbox"<?php if($_POST[\''.$column['COLUMN_NAME'].'\'] == 1){?> checked="checked"<?php } ?> />';
											break;
										case "select":
											$field_data = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DBName . "' AND COLUMN_NAME = '" . $column['COLUMN_NAME'] . "' AND COLUMN_KEY = 'PRI'";
											$field_data = $obj->execute($field_data);

											if(is_array($field_data) && count($field_data) > 0):
												$field_data = $field_data[0];
												$field_input = '<?php '.ucfirst($field_data['TABLE_NAME']) . '::combobox($_POST[\''.$column['COLUMN_NAME'].'\']);' . '?>' . "\n";
											else:
												$field_input = '<select id="'.$column['COLUMN_NAME'].'" name="'.$column['COLUMN_NAME'].'" style="color:#000;">
													</select>';
											endif;
											break;
										case "textarea":
											$field_input = '<textarea class="" id="'.$column['COLUMN_NAME'].'" name="'.$column['COLUMN_NAME'].'" style="color:#000; width:500px; height:160px;">'.$field_value.'</textarea>';
											break;
										case 'password':
											$field_input = '<input type="password" id="'.$column['COLUMN_NAME'].'" name="'.$column['COLUMN_NAME'].'" style="color:#000;">';
											break;
										case 'maps':
											$field_input = '<div style="margin-bottom:5px;">
								                        <a class="btn-action glyphicons pin btn-primary" onclick="placeMarker();" title="Marcar ubicaciÃ³n"><i></i></a>
								                        <a class="divider"></a>
								                        <a class="btn-action glyphicons move btn-primary" onclick="" title="Mover"><i></i></a>
									                </div>
									                <div id="mapcanvas" style="width: 800px; height: 450px; margin-left:0; border:1px solid #ccc;"></div><br><br>
									                <input class="LatLng" id="'.$column['COLUMN_NAME'].'" name="'.$column['COLUMN_NAME'].'" value="'.$field_value.'" type="text" style="color:#000;" />';
											break;
										case 'email':
											$field_input = '<input type="email" id="'.$column['COLUMN_NAME'].'" name="'.$column['COLUMN_NAME'].'" style="color:#000;">';
											break;
										case 'number':
											$field_input = '<input type="number" id="'.$column['COLUMN_NAME'].'" name="'.$column['COLUMN_NAME'].'" style="color:#000;">';
											break;
									endswitch;

									if(strlen($field_tooltip) > 0):
										$tooltip = "\n" . '						<span style="margin: 0;" class="btn-action single glyphicons circle_question_mark" data-toggle="tooltip" data-placement="top" data-original-title="' .utf8_encode($field_tooltip). '"><i></i></span>';
									endif;


								else:

									$length 	= number_format($column['CHARACTER_MAXIMUM_LENGTH'],0,"","") == 0 ? '' : ',	"length"=> '.$column['CHARACTER_MAXIMUM_LENGTH'];
									$required 	= $column['IS_NULLABLE'] == "YES" ? ', "required" => "0"' : ', "required" => "1"';
									$fields    .= '		"' .$column['COLUMN_NAME']. '"	=> array("type" => "'.$column['DATA_TYPE'].'"'. $length . $required .', "validation" => "none"),' ."\n";

								endif;

							else:

								$length 	= number_format($column['CHARACTER_MAXIMUM_LENGTH'],0,"","") == 0 ? '' : ',	"length"=> '.$column['CHARACTER_MAXIMUM_LENGTH'];
								$required 	= $column['IS_NULLABLE'] == "YES" ? ', "required" => "0"' : ', "required" => "1"';
								$fields    .= '		"' .$column['COLUMN_NAME']. '"	=> array("type" => "'.$column['DATA_TYPE'].'"'. $length . $required .', "validation" => "none"),' ."\n";

							endif;

							$override_values = array(
								$colprefix . "_timestamp",
								$colprefix . "_image_small_path",
								$colprefix . "_image_small_url",
								$colprefix . "_image_big_path",
								$colprefix . "_image_big_url",
								$colprefix . "_timestamp",
								$colprefix . "_file_name"
							);

							if(!in_array($column['COLUMN_NAME'], $override_values)):
								$post_value   = strpos($column['COLUMN_NAME'], "_status") !== false || strpos($column['COLUMN_NAME'], "_hidden") !== false ? 'isset($_POST[\''.$column['COLUMN_NAME'].'\']) ? number($_POST[\''.$column['COLUMN_NAME'].'\']) : 0' : '$_POST[\''.$column['COLUMN_NAME'].'\']';
								if($field_type=='password'):
									$post_fields .= '       if($id>0): $password = self::select($id); $password = $password[0][\''.$column['COLUMN_NAME'].'\']; else: $password = null; endif;'."\n";
									$post_fields .= '		$obj->fields[\''.$column['COLUMN_NAME'].'\'][\'value\']	=	(!empty('.$post_value.'))? md5('.$post_value.') : $password;' ."\n";
								else:
									$post_fields .= '		$obj->fields[\''.$column['COLUMN_NAME'].'\'][\'value\']	=	'.$post_value.';' ."\n";
								endif;
							endif;

							if($field_visible && $column['COLUMN_NAME'] != $colprefix . "_timestamp"):
							$fcon_form .= $field_preview .'
				<div class="control-group<?php echo isset($error[\''. $column['COLUMN_NAME'] .'\']) ? " error" : "";?>">
					<label class="control-label" for="'.$column['COLUMN_NAME'].'">'.htmlspecialchars($field_label).'</label>
					<div class="controls">
						'.$field_input.'
						'.$tooltip.'
						<?php
						if(isset($error[\'' . $column['COLUMN_NAME'] . '\'])):
						?>
						<p class="error help-block"><span class="label label-important"><?php echo $error[\'' . $column['COLUMN_NAME'] . '\'];?></span></p>
						<?php
						endif;
						?>
					</div>
				</div>' ."\n";
							endif;

						endif;

					endif;
					unset($tooltip);
				endforeach;

				if(strlen($image_process) > 0):
					$matching = array(
						'{$field_image_small_path}',
						'{$field_image_small_url}',
						'{$field_image_big_path}',
						'{$field_image_big_url}',
						'{$field_file_name}',
						'{$target_folder_name}'
					);

					$replace  = array(
						$colprefix . '_image_small_path',
						$colprefix . '_image_small_url',
						$colprefix . '_image_big_path',
						$colprefix . '_image_big_url',
						$colprefix . '_file_name',
						$table['TABLE_NAME']
					);

					$image_process = str_replace($matching, $replace, $image_process);

				endif;

				if(strlen($file_process) > 0):
					$matching = array(
						'{$field_file_name}',
						'{$field_file_path}',
						'{$field_file_url}',
						'{$target_folder_name}'
					);

					$replace  = array(
						$colprefix . '_file_name',
						$colprefix . '_file_path',
						$colprefix . '_file_url',
						$table['TABLE_NAME']
					);

					$file_process = str_replace($matching, $replace, $file_process);

				endif;

				if(strlen($category_process) > 0):
					$category_process = str_replace('{$colprefix}', $colprefix, $category_process);
				endif;

				$fcon_class .= '	protected $fields	= array(' ."\n";
				$fcon_class .= $fields;
				$fcon_class .= '	);' . "\n\n";

				/*$fcon_class .= '	public function __construct(){
	}' . "\n\n";*/

				$fcon_class .= '	/*inserta o modifica un registro*/' ."\n\n";
				$fcon_class .= '	public static function save($id){

		$obj = new self($id);' ."\n\n";

				$post_fields = '
		foreach($obj->fields as $field => $k){
		    $obj->fields[$field][\'value\']	=	$_POST[$field];
		}';
				$fcon_class .= $post_fields . "\n" . $image_process . $category_process . $file_process;

				$fcon_class .= '		if($obj->validate($obj,$id)):
			return $obj->update($obj, $id);
		else:
			'.$image_delete_on_error.'
			Message::set("Por favor complete correctamente el formulario para continuar", MESSAGE_ERROR);
			return $obj->error;
		endif;
	}

	/*oculta o elimina un registro*/
	public static function delete($id){
		$obj = new self();
		'.$delete_action.'
	}

	public static function select($id){
		$obj = new self();
		return $obj->find($obj->tableName, $obj->primaryKey, $id, "'.$colprefix.'_hidden = 0");
	}
	'.$tree_function.'
	'.$get_function.'

	public static function getFirst($where=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}'.$colprefix.'_status = 1 AND '.$colprefix.'_hidden = 0 ORDER BY " . $obj->primaryKey . " ASC LIMIT 0,1";
		return $obj->execute($sql);
	}

	public static function getLast($where=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}'.$colprefix.'_status = 1 AND '.$colprefix.'_hidden = 0 ORDER BY " . $obj->primaryKey . " DESC LIMIT 0,1";
		return $obj->execute($sql);
	}

	public static function getAll($where=null,$order=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$ord = $order == null ? "" : " ORDER BY {$order}";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}'.$colprefix.'_hidden = 0{$ord}";
		return $obj->execute($sql);
	}

	public static function listing($limit=10, $page=1, $fields=null, $where=null){
		$obj = new self();
		$listing = new Listing();
		$where = strlen($where) > 0 ? " AND {$where}" : "";
		return $listing->get($obj->tableName, $limit, $fields, $page, "WHERE '.$colprefix.'_status = 1 AND '.$colprefix.'_hidden = 0{$where}");
	}

	public static function pagination($limit=10, $page=1, $fields=null, $where=null){
		$obj = new self();
		$listing = new Pagination();
		$where = strlen($where) > 0 ? " AND {$where}" : "";
		return $listing->get($obj->tableName, $limit, $fields, $page, "WHERE '.$colprefix.'_status = 1 AND '.$colprefix.'_hidden = 0{$where}");
	}

	public static function set($field, $value, $where=null){
		$obj = new self();
		$obj->change($obj->tableName, $field, $value, $where);
	}

	public static function bulk($action, $ids){

		$obj = new self();
		$ids = json_decode($ids);

		switch($action):
			//activar
			case "1":
				foreach($ids as $id):
					self::set("'.$colprefix.'_status", 1, $obj->primaryKey . " = {$id}");
				endforeach;
				break;
			//desactivar
			case "2":
				foreach($ids as $id):
					self::set("'.$colprefix.'_status", 0, $obj->primaryKey . " = {$id}");
				endforeach;
				break;
			//eliminar
			case "3":
				foreach($ids as $id):
					self::delete($id);
				endforeach;
				break;
		endswitch;

	}';

	$colselected = '';

	$combobox_colname = $columns[1]['COLUMN_NAME'];

	foreach($columns as $column):

		if(strlen(trim($column['COLUMN_COMMENT'])) > 0):
			$column_config = @json_decode("{" . utf8_encode($column['COLUMN_COMMENT']) . "}");

			if($column_config instanceof stdClass):
				$colselected  = isset($column_config->select) ? (strtolower($column_config->select) == "false" ? false : true) : false;

				if($colselected):
					$combobox_colname = $column['COLUMN_NAME'];
				endif;

			endif;

		endif;
	endforeach;

	$fcon_class .='

	public static function combobox($selected=null,$onchange=null){
		$obj = new self();
		$fsel = ($selected == null || $selected == 0) ? \' selected="selected"\' : \'\';
		$list = "SELECT '.$primaryKey.', '.$combobox_colname.' FROM '.$table['TABLE_NAME'].' WHERE '.$colprefix.'_status = 1 AND '.$colprefix.'_hidden = 0 ORDER BY '.$combobox_colname.' ASC";
		$list = $obj->exec($list);
		print \'<select name="'.$primaryKey.'" id="'.$table['TABLE_NAME'].'_combo" style="color:#000;" onchange="'.$onchange.'">\';
			print \'<option value=""\'.$fsel.\'>Seleccionar</option>\';
			if(is_array($list) && count($list) > 0):
				foreach($list as $dat):
					$select = $dat[\''.$primaryKey.'\'] == $selected ? \' selected="selected"\' : "";
					print \'<option value="\'.$dat[\''.$primaryKey.'\'].\'"\'.$select.\'>\'.htmlspecialchars($dat[\''.$combobox_colname.'\']).\'</option>\';
				endforeach;
			endif;
		print \'</select>\';
	}

	public static function getfields(){
		$obj = new self();
		return $obj->fields;
	}
	';

	if($login):
		$coluser = '';
		$colpass = '';
		$userfield = $colprefix.'_email';
		$passfield = $colprefix.'_clave';

		foreach($columns as $column):

			if(strlen(trim($column['COLUMN_COMMENT'])) > 0):
				$column_config = @json_decode("{" . utf8_encode($column['COLUMN_COMMENT']) . "}");

				if($column_config instanceof stdClass):
					$coluser  = isset($column_config->user) ? (strtolower($column_config->user) == "false" ? false : true) : false;
					if($coluser):
						$userfield = $column['COLUMN_NAME'];
					endif;

					$colpass  = isset($column_config->password) ? (strtolower($column_config->password) == "false" ? false : true) : false;
					if($colpass):
						$passfield = $column['COLUMN_NAME'];
					endif;

				endif;

			endif;
		endforeach;

		$fcon_class .='

			public static function setLogin($user, $pass){

				$obj = new self();
				$res = $obj->find($obj->tableName, "'.$userfield.'", $user, "'.$colprefix.'_status = 1 AND '.$colprefix.'_hidden = 0");

				if(haveRows($res)):
					$res = $res[0];
					if(md5($pass) == $res[\''.$passfield.'\']):
						$data = array();
						foreach($res as $k => $v):
							$data[Encryption::Encrypt($k)] = Encryption::Encrypt($v);
						endforeach;

						$_SESSION[Encryption::Encrypt(userLogin)] = $data;

						return true;
					else:
						return false;
					endif;
				else:
					return false;
				endif;
			}

			public static function login($data=null){
				if($data == null):
					return isset($_SESSION[Encryption::Encrypt(userLogin)]) ? true : false;
				else:
					return isset($_SESSION[Encryption::Encrypt(userLogin)][Encryption::Encrypt($data)]) ? Encryption::Decrypt($_SESSION[Encryption::Encrypt(userLogin)][Encryption::Encrypt($data)]) : "";
				endif;
			}
		';

		$folder = 'ajax';
		$filepath_to_controller = $folder.'/login__ajax.php';
		$content_controller = '
<?php
require("../_app/init.php");
switch(param(\'token\')):
     case token(\'login\'):
          $user = param(\'usuario\');
          $pass = param(\'contrasena\');
          if(Usuarios::setLogin($user, $pass)):
               echo json_encode(array(\'status\' => \'success\'));
          else:
               session_regenerate_id(true);
               echo json_encode(array(\'status\' => \'error\',\'description\'=>\'El Usuario o Contrase&ntilde;a son incorrectos.\'));
          endif;
     break;
     default:
          echo json_encode(array(\'status\' => \'error\',\'description\'=>\'No hay paramÃ©tros\'));
          break;
endswitch;
?>
		';
		$filepath_to_form = 'login__form.php';
		$content_form = '
<?php require_once(\'_app/init.php\');?>
<form method="post" name="form_login" id="form_login" enctype="multipart/form-data" action="ajax/login__ajax.php">
	<input type="hidden" name="token" id="token" value="<?php echo token(\'login\');?>">
	<fieldset>
		<label>Usuario</label>
		<input type="text" name="usuario" id="usuario" placeholder="Usuario">
	</fieldset>
	<fieldset>
		<label>Contrase&ntilde;a</label>
		<input type="password" name="contrasena" id="contrasena" placeholder="Contrase&ntilde;a">
	</fieldset>
	<fieldset>
		<input type="submit" id="btn_login" value="Login">
	</fieldset>
</form>
		';
		@mkdir("ajax",0755);
		self::create_file($filepath_to_controller,$content_controller);
		self::create_file($filepath_to_form,$content_form);

	endif;

	/*selecciona las columnas de busqueda y las columnas que se listan*/
	$search_cols = "";
	$onlist_cols_row = "";
	$onlist_cols_title = "";
	$datepicker_script = "";
	$colorpicker_script = "";
	$html5wysiwyg_script = "";
	$goglemaps_script = "";
	$mapcore_script="";

	foreach($columns as $column):

		if(strlen(trim($column['COLUMN_COMMENT'])) > 0):
			$column_config = @json_decode("{" . utf8_encode($column['COLUMN_COMMENT']) . "}");

			if($column_config instanceof stdClass):
				$field_search  		 = isset($column_config->search) ? (strtolower($column_config->search) == "false" ? false : true) : false;
				$field_onlist  		 = isset($column_config->onlist) ? (strtolower($column_config->onlist) == "false" ? false : true) : false;
				$field_datepicker  	 = isset($column_config->datepicker) ? (strtolower($column_config->datepicker) == "false" ? false : true) : false;
				$field_colorpicker   = isset($column_config->colorpicker) ? (strtolower($column_config->colorpicker) == "false" ? false : true) : false;
				$field_editor  		 = isset($column_config->editor) ? (strtolower($column_config->editor) == "false" ? false : true) : false;
				$field_label   		 = isset($column_config->label)  ? ucwords($column_config->label) : ucwords($column['COLUMN_NAME']);
				$field_maps  		 = isset($column_config->type) ? (strtolower($column_config->type) == "maps" ? true : false) : false;

				if($field_search):
					$search_cols .= $column['COLUMN_NAME'].", ";
				endif;

				if($field_onlist):
					$onlist_cols_title .= "				<th>".$field_label."</th>\n";
					$foreign = "SELECT REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA IS NOT NULL AND TABLE_SCHEMA = '" . DBName . "' AND TABLE_NAME = '".$table['TABLE_NAME']."' AND REFERENCED_COLUMN_NAME = '{$column['COLUMN_NAME']}';";
					$fk = $obj->execute($foreign);
					if(haveRows($fk)):
						$fk_table = $fk[0]['REFERENCED_TABLE_NAME'];
						$fk_field_id = $fk[0]['REFERENCED_COLUMN_NAME'];
						$fk_columns = null;
						$fk_columns = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DBName . "' AND TABLE_NAME = '{$fk_table}'";
						$fk_columns = $obj->execute($fk_columns);
						if(is_array($fk_columns) && count($fk_columns)>0):
							foreach($fk_columns as $fk_column):
								if(strlen($fk_column['COLUMN_COMMENT']) > 0):
									$fk_column_config = @json_decode("{" . utf8_encode($fk_column['COLUMN_COMMENT']) . "}");
									if($fk_column_config instanceof stdClass):
										if(strpos($fk_column['COLUMN_NAME'], "_status") === false):
											if(isset($fk_column_config->select)):
												$fk_field_name	= $fk_column['COLUMN_NAME'];
											endif;
										endif;
									endif;
								endif;
							endforeach;
						endif;
						$onlist_cols_row   .= '				<?php $'.$fk_table.'='. ucfirst($fk_table).'::get("'.$column['COLUMN_NAME'].'=".$list[\''.$column['COLUMN_NAME'].'\']); ?>'."\n";
						$onlist_cols_row   .= "				<td>".'<?php echo $'.$fk_table.'[0][\''.$fk_field_name.'\'];?>'."</td>\n";
					else:
						$onlist_cols_row   .= "				<td>".'<?php echo htmlspecialchars($list[\''.$column['COLUMN_NAME'].'\']);?>'."</td>\n";
					endif;
				endif;

				if($field_datepicker):
					$datepicker_script .= '$("#'.$column['COLUMN_NAME'].'").datepicker({
				dateFormat: "dd/mm/yy"
			});';
				endif;

				if($field_colorpicker):
					$colorpicker_script .= '$("#'.$column['COLUMN_NAME'].'").colorpicker();';
				endif;

				if($field_editor):
					$html5wysiwyg_script .= '$("#'.$column['COLUMN_NAME'].'").wysihtml5();';
				endif;

				if($field_maps):
					$mapcore_script = '<script src="js/mapcore.js"></script>';
					$goglemaps_script = '
	$(function(){
	    initmap();
	});';
				endif;

			endif;

		endif;
	endforeach;

	$search_cols = trim($search_cols);
	$search_query = "";
	if(strlen($search_cols) > 0):
		$search_cols  = substr($search_cols,0,-1);
		$search_query = " OR CONCAT_WS(' ', {$search_cols}) LIKE '%".'{$search}'."%'";
	endif;


				$fcon_class .= "\n" . '}' . "\n" . '?>';

				$fcon_view = '<?php
$title = "' . $title . '";
$page	 = pageNumber();
$search	 = addslashes(param(\'query\'));
$search_num	 = number($search);
$search	 = strlen(trim($search)) > 0 ? " AND (' . $primaryKey. ' = \'{$search_num}\''.$search_query.')" : "";
$listing = new Listing();
$listing->pgclick("module(\'' . $table['TABLE_NAME'] . '&page=%s\');return!1;");
$listing = $listing->get("'. $table['TABLE_NAME'] .'", 10, NULL, $page, "WHERE '.$colprefix.'_hidden = 0 {$search} ORDER BY ' . $primaryKey . ' DESC");

$permissionInsert = permissionInsert(\''.$table['TABLE_NAME'].'\');
$permissionUpdate = permissionUpdate(\''.$table['TABLE_NAME'].'\');
$permissionDelete = permissionDelete(\''.$table['TABLE_NAME'].'\');
?>
<ul class="breadcrumb">
	<li><a href="" class="glyphicons home" onclick="module(\'dashboard\');return!1;"><i></i> <?php echo sysName;?></a></li>
	<li class="divider"></li>
	<li><?php echo $title;?></li>
</ul>
<div class="separator"></div>
<div class="heading-buttons" style="overflow:hidden;">
	<h3 class="glyphicons '.$icon.'" style="display:inline-block !important; float:left !important;"><i></i> <?php echo $title;?></h3>
	<?php if($permissionInsert):?>
	<div class="buttons pull-right">
		<a href="" class="btn btn-primary btn-icon glyphicons circle_plus" onclick="create(\'' . $table['TABLE_NAME'] . '\',\'0&page=<?php echo $page;?>\');return!1;"><i></i>Nuevo</a>
	</div>
	<?php endif;?>
	'.$export.'
</div>
<div class="separator"></div>
<div class="innerLR">
<form name="searchform" id="searchform" method="get" onsubmit="module(\'' . $table['TABLE_NAME'] . '&query=\'+$(\'#squery\').val());return!1;">
<div class="input-append">
	<input class="span6" id="squery" name="query" type="text" value="<?php echo htmlspecialchars(param(\'query\'));?>" placeholder="Buscar..." />
	<button class="btn" type="submit"><i class="icon-search"></i></button>
</div>
</form>
<?php
if(is_array($listing[\'list\']) && count($listing[\'list\']) > 0):
?>
	<table class="table table-bordered table-condensed table-striped table-vertical-center checkboxs js-table-sortable">
		<thead>
			<tr>
				<th style="width: 1%;" class="uniformjs"><input type="checkbox" name="checkall_' . $table['TABLE_NAME'] . '" id="checkall_' . $table['TABLE_NAME'] . '" value="1" /></th>
				<th style="width: 1%;" class="center">ID</th>
'.$onlist_cols_title.'
				<th class="right" colspan="3">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach($listing[\'list\'] as $list):
		?>
			<tr class="selectable">
				<td class="center uniformjs"><input type="checkbox" name="check_'.$table['TABLE_NAME'].'_<?php echo $list[\''.$primaryKey.'\'];?>" value="<?php echo $list[\'' . $primaryKey . '\'];?>" /></td>
				<td class="center"><?php echo $list[\'' . $primaryKey . '\'];?></td>
'.$onlist_cols_row.'
				<td class="center" style="width: 150px;"><?php echo date(\'d/m/Y H:i\', strtotime($list[\''.$colprefix.'_timestamp\']));?></td>
				<td class="center" style="width: 80px;"><span class="label label-block label-<?php echo $list[\''.$colprefix.'_status\'] == 1 ? "important" : "inverse";?>"><?php echo $list[\''.$colprefix.'_status\'] == 1 ? "Activo" : "Inactivo";?></span></td>
				<td class="center" style="width: 100px;">
					<a href="" class="btn-action glyphicons folder_open btn-primary" onclick="viewModal(\'' . $table['TABLE_NAME'] . '\',\'<?php echo $list[\'' . $primaryKey . '\'];?>\');return!1;"><i></i></a>
					<?php if($permissionUpdate): ?>
					<a href="" class="btn-action glyphicons pencil btn-success" onclick="create(\'' . $table['TABLE_NAME'] . '\',\'<?php echo $list[\'' . $primaryKey . '\'];?>&page=<?php echo $page;?>\');return!1;"><i></i></a>
					<?php endif;?>
					<?php if($permissionDelete): ?>
					<a href="" class="btn-action glyphicons remove_2 btn-danger" onclick="removeit({\'option\':\''.$table['TABLE_NAME'].'\',\'id\':\'<?php echo $list[\''.$primaryKey.'\'];?>\',\'callback\':\'view\'});return!1;"><i></i></a>
					<?php endif;?>
				</td>
			</tr>
		<?php
		endforeach;
		?>
		</tbody>
	</table>
	<div class="separator top form-inline small">
		<div class="pull-left checkboxs_actions hide">
			<div class="row-fluid">
				<select style="color:#000;" onchange="checkedAction(\'' . $table['TABLE_NAME'] . '\',this);">
					<option value="0">Seleccionados</option>
					<?php if($permissionUpdate): ?>
					<option value="1">Activar</option>
					<option value="2">Desactivar</option>
					<?php endif;?>
					<?php if($permissionDelete): ?>
					<option value="3">Eliminar</option>
					<?php endif;?>
				</select>
			</div>
		</div>
		<div class="pagination pull-right" style="margin: 0;">
			<?php echo $listing[\'navigation\'];?>
		</div>
		<div class="clearfix"></div>
	</div>
<?php
else:
?>
<div class="alert alert-info">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<strong>Sin datos</strong> No se encontraron registros</div>
<?php
endif;
?>
</div>
<br/>
<!-- End Content --> ';

				$fcon_form .= '
						</div>
				</div>
		<hr class="separator" />
		<div class="form-actions">
			<input type="hidden" name="id" value="<?php echo $'.$primaryKey.';?>" />
			<input type="hidden" name="page" value="<?php echo pageNumber();?>" />
			<input type="hidden" name="token" value="<?php echo token("'.ucfirst($table['TABLE_NAME']).'::save(".$'.$primaryKey.'.")");?>" />
			'.$upload_option.'
			<input type="hidden" name="callback" value="<?php echo token(json_encode($callback));?>" />
			<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i>Aceptar</button>
			<button type="button" class="btn btn-icon btn-default glyphicons circle_remove" onclick="module(\''.$table['TABLE_NAME'].'&page=<?php echo pageNumber();?>\');return!1;"><i></i>Cancelar</button>
		</div>
	</div>
</form>
'.$uploader.'
'.$mapcore_script.'
<script type="text/javascript">
	$(document).ready(function () {
	'.$datepicker_script.' '.$colorpicker_script.' '.$html5wysiwyg_script.'
	});
'.$goglemaps_script.'
</script>
';
$export = null;
$html5wysiwyg_script = null;

				if($onForm):
					/*
					*****************************************************
					SE GENERA EL FORMULARIO PARA EL FRONTEND
					*****************************************************
					*/
					$filepath = strtolower($table['TABLE_NAME']).".php";
					if(file_exists($filepath)):
						$labelField = "";
						$typeField 	= "text";
						$addClass 	= "";
						$requiredField 	= "";
						$fieldValue	= "";
						$loadjs 	.= '	<script src="<?php echo baseSiteURL;?>js/blockUI.js"></script>'."\n";
						$loadjs 	.= '	<script src="<?php echo baseSiteURL;?>js/jquery.validate.js"></script>'."\n";
						$loadjs 	.= '	<script src="<?php echo baseSiteURL;?>js/messages_es.js"></script>'."\n";
						$form 	= '<p id="result"></p>'."\n";
						$form 	.= '	<form action="" method="post" enctype="multipart/form-data" id="form_'.$table['TABLE_NAME'].'" class="form_'.$table['TABLE_NAME'].'">'."\n";
						/*trae columnas de las tablas*/
						$columns = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DBName . "' AND TABLE_NAME = '{$table['TABLE_NAME']}'";
						$columns = $obj->execute($columns);
						foreach ($columns as $column):
							if(strlen($column['COLUMN_COMMENT']) > 0):
								if(strpos($column['COLUMN_NAME'], "_status") === false && strpos($column['COLUMN_NAME'], "_hidden") === false && strpos($column['COLUMN_NAME'], "_timestamp") === false):
									$column_name = strtolower($column['COLUMN_NAME']);
									$column_config = @json_decode("{" . utf8_encode($column['COLUMN_COMMENT']) . "}");
									if($column_config instanceof stdClass):
										$labelField 	= isset($column_config->label)? $column_config->label : $labelField;
										$typeField 		= isset($column_config->type)? $column_config->type : $typeField;
										$addClass		= isset($column_config->addclass)? $column_config->addclass : $addclass;
										$requiredField		= isset($column_config->required)? "required" : $requiredField;
										$fieldValue 		= isset($column_config->value)? $column_config->value : $fieldValue;
											$form .= '		<div class="form-group">'."\n";

										switch ($typeField) {
											case 'text':
											case 'email':
											case 'number':
											$form .= '			<label for="'.$column_name.'">'.$labelField.'</label>'."\n";
											$form .= '			<input type="'.$typeField.'" class="form-control '.$addClass.'" id="'.$column_name.'" name="'.$column_name.'" placeholder="'.$labelField.'" '.$requiredField.'>'."\n";
											$form .= '			<label id="'.$column_name.'-error" class="error text-danger" for="'.$column_name.'"></label>'."\n";
											$typeField = "text";
												break;
											case 'textarea':
											$form .= '			<label for="'.$column_name.'">'.$labelField.'</label>'."\n";
											$form .= '			<textarea class="form-control '.$addClass.'" id="'.$column_name.'" name="'.$column_name.'" placeholder="'.$labelField.'" '.$requiredField.'></textarea>'."\n";
											$form .= '			<label id="'.$column_name.'-error" class="error text-danger" for="'.$column_name.'"></label>'."\n";
												break;
											case 'checkbox':
											$form .= '			<div class="checkbox"><label>';
											$form .= '	    		<input type="checkbox" id="'.$column_name.'" name="'.$column_name.'" value="'.$fieldValue.'"> '.$labelField;
											$form .= '			</label><label id="'.$column_name.'-error" class="error text-danger" for="'.$column_name.'"></label></div>';
												break;
											default:
												break;
										}
											$form .= '		</div>'."\n";
									endif;
								endif;
							endif;
						endforeach;
						$form .= '		<div class="form-group">'."\n";
						$form .= '			<label for="'.$colprefix.'_captcha">Ingrese el valor de la imagen</label>'."\n";
						$form .= '				<input class="form-control" type="text" name="'.$colprefix.'_captcha" id="'.$colprefix.'_captcha" placeholder="Ingrese el valor" required>'."\n";
						$form .= '				<img src="images/captcha.jpg?<?php echo rand();?>" id="captcha_image" width="180">'."\n";
						$form .= '				<button type="button" class="btn btn-default" onclick="reloadCaptcha(); return false;">Recargar</button>'."\n";
						$form .= '			'."\n".'			<label id="'.$colprefix.'_captcha-error" class="error text-danger" for="'.$colprefix.'_captcha"></label>'."\n";
						$form .= '		</div>'."\n";
						$form .= '		<input type="hidden" value="<?php echo token("'.$table['TABLE_NAME'].'");?>" name="token" id="token">'."\n";
						$form .= '		<button type="submit" id="btn_enviar" class="btn btn-default">ENVIAR</button>'."\n";
						$form .= '	</form>'."\n";
						$loadjs .= '  <script>'."\n";
						$loadjs .= '	$.blockUI.defaults.message = \'<img src="<?php echo baseSiteURL;?>images/loading-rectangle.gif">\';'."\n";
						$loadjs .= '	$.blockUI.defaults.overlayCSS.opacity = .5;'."\n";
						$loadjs .= '	$.blockUI.defaults.css.backgroundColor = "none";'."\n";
						$loadjs .= '	$.blockUI.defaults.css.border = "none";'."\n";
						$loadjs .= '	$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);'."\n";
						$loadjs .= '		function reloadCaptcha(){'."\n";
						$loadjs .= '			var img = document.getElementById(\'captcha_image\');'."\n";
						$loadjs .= '			img.src= \'images/captcha.jpg?\'+Math.random();'."\n";
						$loadjs .= '		}'."\n";
						$loadjs .= '		$().ready(function() {'."\n";
						$loadjs .= '			$("#form_'.$table['TABLE_NAME'].'").validate({
                submitHandler: function() {
                	$("#result").removeClass("bg-success").html("");
                    $.post(\'ajax/'.$table['TABLE_NAME'].'\',
                    $("#form_'.$table['TABLE_NAME'].'").serialize() ,
                    function(data){
                    	if(data.status=="success"){
							$("input").not(":submit").val("");
							$("textarea").val("");
                        	$("#result").addClass("bg-success").css({\'padding\':\'15px\'}).html(data.description);
                    	}else{
							if(data.type=="autenticacion"){
								$("#result").html(\'<span style="display:block; text-align:center; color:red;">\'+data.description+\'</span>\');
							}else{
                    			$("#"+data.type).focus();
                    			$("#"+data.type+"-error").show().html(data.description);
							}
                    	}
                    }, "json");
                }
			});'."\n";
						$loadjs .= '		});'."\n";
						$loadjs .= '  </script>'."\n";

						//Obtenemos el contenido del archivo
						$content = file_get_contents($filepath);
						//$content = str_replace("</body>", $loadjs."\n</body>", $content);
						$content = str_replace("<!--formulario-->", $form.$loadjs, $content);
						$form=null;
						$loadjs=null;
						//se abre el archivo para escritura
						$handle = fopen($filepath , 'w');
						$fwrite = fwrite($handle, $content);
						fclose($handle);
						if($fwrite === FALSE):
							print "OcurriÃ³ un error el formulario no se pudo crear. \n\n";
							exit();
						endif;
						print "Se creÃ³ el formulario en: " . $filepath . "\n\n";

						/*
						*****************************************************
						SE GENERA EL CONTROLADOR PARA EL FRONTEND
						*****************************************************
						*/
						@mkdir("ajax",0755);
						$htacces_content = 'RewriteEngine On
	RewriteCond $1 !\.[a-z0-9]+$ [NC]
	RewriteCond %{REQUEST_FILENAME}__ajax.php -f
	RewriteRule ^(.+)$ $1__ajax.php [L]';
						$handle = fopen('ajax/.htaccess' , 'w');
						$fwrite = fwrite($handle, $htacces_content);
						fclose($handle);
						$filepath = "ajax/".$table['TABLE_NAME']."__ajax.php";

						$content = '<?php
require_once("../_app/init.php");
if(!Captcha::validate(param("'.$colprefix.'_captcha"))):
		$result = array("status"=>"error","description"=>"El valor no coincide con la imagen.","type"=>"'.$colprefix.'_captcha");
else:
	if(param("token") == token("'.$table['TABLE_NAME'].'")):

		if(!isValidEmail(param(\''.$colprefix.'_email\'))):
			$error = array(\''.$colprefix.'_email\' => \'Por favor, escribe una direcciÃ³n de correo vÃ¡lida.\' );
		endif;

		if(!isset($error)):
			$_POST[\''.$colprefix.'_status\'] = 1;
			$error = '.ucfirst($table['TABLE_NAME']).'::save(0);

			$from = array(SENDER => "'.ucfirst($table['TABLE_NAME']).'");
			$to   = array(CONTACT_EMAIL => "'.ucfirst($table['TABLE_NAME']).'");
			$subject = "Nuevo mensaje recibido";
			$template = "'.$table['TABLE_NAME'].'_template.html";'."\n";

						$content .=	'			$data["subject"] 	= $subject;'."\n";
						foreach ($columns as $column):
							if(strlen($column['COLUMN_COMMENT']) > 0):
								if(strpos($column['COLUMN_NAME'], "_status") === false && strpos($column['COLUMN_NAME'], "_hidden") === false && strpos($column['COLUMN_NAME'], "_timestamp") === false):
									$column_name = strtolower($column['COLUMN_NAME']);
						$content .= '			$data["'.$column_name.'"]	= "{$_POST[\''.$column_name.'\']}";'."\n";
								endif;
							endif;
						endforeach;

						$content .=	'			$data["send_date"] 	= date(\'d/m/Y H:i:s\');
		endif;

		if(!is_array($error)){
			Captcha::reset();
			session_regenerate_id();
			Mail::send($from, $to, $subject, $template, $data);
			$result = array("status"=>"success","description"=>"Mensaje enviado con Ã©xito!");
		}else{
			$msg = $error[key($error)];
			$result = array("status"=>"error","description"=>$msg,"type"=>key($error));
		};

	else:
		$result = array("status"=>"error","description"=>"Error de AutenticaciÃ³n por favor recargue la pagina.","type"=>"autenticacion");
	endif;
endif;
echo json_encode($result);
?>';
						$handle = fopen($filepath , 'w');
						$fwrite = fwrite($handle, $content);
						fclose($handle);
						if($fwrite === FALSE):
							print "OcurriÃ³ un error el controlador no se pudo crear. \n\n";
							exit();
						endif;
						print "Se creÃ³ el controlador del formulario en: " . $filepath . "\n\n";

						/*
						*****************************************************
						SE GENERA EL TEMPLATE PARA EL ENVIO
						*****************************************************
						*/
						$filepath = "_app/templates/".$table['TABLE_NAME']."_template.html";

						$content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>'.ucfirst($table['TABLE_NAME']).'</title>
</head>
<body>
	<table width="800" border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td colspan="2"><h2>{$subject}</h2><hr /></td>
		</tr>'."\n";

						foreach ($columns as $column):
							if(strlen($column['COLUMN_COMMENT']) > 0):
								if(strpos($column['COLUMN_NAME'], "_status") === false && strpos($column['COLUMN_NAME'], "_hidden") === false && strpos($column['COLUMN_NAME'], "_timestamp") === false):
									$column_name = strtolower($column['COLUMN_NAME']);
									$column_config = @json_decode("{" . utf8_encode($column['COLUMN_COMMENT']) . "}");
									if($column_config instanceof stdClass):
										$labelField 	= isset($column_config->label)? $column_config->label : $labelField;
						$content .= '		<tr>
			<td width="155" align="right"><strong>'.ucfirst($labelField).':</strong></td>
			<td width="625">{$'.$column_name.'}</td>
		</tr>'."\n";
									endif;
								endif;
							endif;
						endforeach;

						$content .=	'	</table>
</body>
</html>';

						$handle = fopen($filepath , 'w');
						$fwrite = fwrite($handle, $content);
						fclose($handle);
						if($fwrite === FALSE):
							print "OcurriÃ³ un error el template no se pudo crear. \n\n";
							exit();
						endif;
						print "Se creÃ³ el template del formulario en: " . $filepath . "\n\n";
					endif;
				endif;
				//FIN GENERADOR

				/*escribe los contenidos de cada archivo*/
				$file_class = isset($file_class) ? $file_class : NULL;
				$file_view  = isset($file_view)  ? $file_view  : NULL;
				$file_form  = isset($file_form)  ? $file_form  : NULL;
				$file_menu  = isset($file_menu) ? $file_menu  : NULL;

				if(is_resource($file_class)):
					fwrite($file_class, utf8_encode($fcon_class));
					fclose($file_class);
				endif;

				if(is_resource($file_view)):
					fwrite($file_view, ($fcon_view));
					fclose($file_view);
				endif;

				if(is_resource($file_form)):
					fwrite($file_form, ($fcon_form));
					fclose($file_form);
				endif;

				unset($onForm, $fcon_class, $fcon_view, $fcon_form, $fields, $post_fields, $search_cols, $search_query, $onlist_cols_title, $onlist_cols_row, $datepicker_script, $html5wysiwyg_script);

			endforeach;

			$fcon_menu .= '</ul>' . "\n";

			if(is_resource($file_menu)):
				fwrite($file_menu, $fcon_menu);
				fclose($file_menu);
			endif;

			print('Mapping done'. "\n");

		endif;

	}

	static private function getInstance($id=null) {
        return new self($id);
    }

    static private function create_file($filepath,$content){
    	if(!file_exists($filepath)):
			$handle = fopen($filepath , 'a');
			fwrite($handle, $content);
			fclose($handle);
			return true;
		else:
			return false;
		endif;
    }

}