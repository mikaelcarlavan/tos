<?php
/* Copyright (C) 2014      Mikael Carlavan        <contact@mika-carl.fr>
 *                                                http://www.mikael-carlavan.fr
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */


/**
 *      \file       htdocs/tos/admin/config.php
 *		\ingroup    tos
 *		\brief      Page to setup tos module
 */

$res=@include("../../main.inc.php");				// For root directory
if (! $res) $res=@include("../../../main.inc.php");	// For "custom" directory


require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.form.class.php");


$langs->load("admin");
$langs->load("companies");
$langs->load("tos@tos");
$langs->load("other");
$langs->load("errors");

if (!$user->admin)
{
   accessforbidden();
}

//Init error
$error = false;
$message = false;

$action = GETPOST('action');
$value = GETPOST('value');
$file = GETPOST('file');

$html = new Form($db);

if ($action == 'update')
{
	$varfiles = 'file';
	
	// Upload dir
	$upload_dir = $conf->tos->dir_output;
	$result = 0;
	
	if (! empty($_FILES[$varfiles])) // For view $_FILES[$varfiles]['error']
	{
	
		$finame = $_FILES[$varfiles]['name'];
		$ext = substr($finame, (strrpos($finame, '.') + 1));
		$ext = strtolower($ext);
		
		if ($ext != 'pdf')
		{
			dol_syslog("ToS::config wrong extension", LOG_DEBUG);
			$error = true;
		}
		
		if (!$error)
		{					
			//$res = dol_delete_dir_recursive($upload_dir); // Empty dir
			$res = dol_mkdir($upload_dir);
			if ($res >= 0)
			{									
				$resupload = dol_move_uploaded_file($_FILES[$varfiles]['tmp_name'], $upload_dir . "/" . $finame, 1, 0, $_FILES[$varfiles]['error'], 0, $varfiles);
			}
			else
			{
				dol_syslog("ToS::config create directory=".$res, LOG_DEBUG);
			}
		}
	}

}
else
{
	if ($action == 'delete')
	{
		$upload_dir = $conf->tos->dir_output;
		$path = $upload_dir.'/'.urldecode($file);

		dol_delete_file($path);

		$message = $langs->trans('CGVUpdated');
	}	

	if ($action == 'updated')
	{
		$message = $langs->trans('CGVUpdated');
	}

	if ($action == 'setautotos')
	{
		dolibarr_set_const($db, 'AUTO_ADD_TOS', 1, 'chaine', 0, '', $conf->entity);
	}

	if ($action == 'unsetautotos')
	{
		dolibarr_set_const($db, 'AUTO_ADD_TOS', '', 'chaine', 0, '', $conf->entity);
	}

	if ($action == 'settosoneachpage')
	{
		dolibarr_set_const($db, 'ADD_TOS_ON_EACH_PAGE', 1, 'chaine', 0, '', $conf->entity);
	}

	if ($action == 'unsettosoneachpage')
	{
		dolibarr_set_const($db, 'ADD_TOS_ON_EACH_PAGE', '', 'chaine', 0, '', $conf->entity);
	}

	if ($action == 'save')
	{
		dolibarr_set_const($db, 'TOS_DEFAULT_FILE', $file, 'chaine', 0, '', $conf->entity);	

		$message = $langs->trans('CGVUpdated');
	}

	$upload_dir = $conf->tos->dir_output;
	$filearray = dol_dir_list($upload_dir, 'files', 0, '', '\.meta$', '', SORT_ASC,1);

	$files = array();
	$filenames = array();

	if (count($filearray))
	{
		$filenames[] = '';
		foreach ($filearray as $file)
		{
			$files[] = array(
				'filename' => $file['name'],
				'link' => DOL_URL_ROOT."/document.php?modulepart=tos&file=".$file['name'],
				'size' => (intval($file['size'])/(1024*1024))
			);

			$filenames[] = $file['name'];
		}
	}
				
	/*
	 * View
	 */

	$actionAddToS = ($conf->global->AUTO_ADD_TOS ?  'unsetautotos' : 'setautotos');
	$imgAddToS = ($conf->global->AUTO_ADD_TOS ?  img_picto($langs->trans("Activated"),'switch_on') : img_picto($langs->trans("Disabled"),'switch_off'));
	$linkAddToS	= '<a href="'.$_SERVER['PHP_SELF'].'?action='.$actionAddToS.'" >'.$imgAddToS.'</a>';

	$actionAddToSPage = ($conf->global->ADD_TOS_ON_EACH_PAGE ?  'unsettosoneachpage' : 'settosoneachpage');
	$imgAddToSPage = ($conf->global->ADD_TOS_ON_EACH_PAGE ?  img_picto($langs->trans("Activated"),'switch_on') : img_picto($langs->trans("Disabled"),'switch_off'));
	$linkAddToSPage	= '<a href="'.$_SERVER['PHP_SELF'].'?action='.$actionAddToSPage.'" >'.$imgAddToSPage.'</a>';

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath('/tos/admin/config.php', 1);
	$head[$h][1] = $langs->trans("Setup");
	$head[$h][2] = 'config';
	$h++;
	
	$current_head = 'config';

	$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';

	require_once("../tpl/admin.config.tpl.php");
}

$db->close();

?>
