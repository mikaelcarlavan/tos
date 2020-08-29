<?php

/* Copyright (C) 2012      Mikael Carlavan        <contact@mika-carl.fr>
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

/**	    \file       htdocs/tos/tpl/admin.config.tpl.php
 *		\ingroup    tos
 *		\brief      Admin setup view
 */
 
llxHeader('', $langs->trans("CGVSetup"), '', '', 0, 0, array('/tos/js/functions.js.php', '/tos/js/jquery.form.js'));

echo ($message ? dol_htmloutput_mesg($message, '', ($error ? 'error' : 'ok'), 0) : '');


print_fiche_titre($langs->trans("CGVSetup"), $linkback, 'setup');

dol_fiche_head($head, 'config', $langs->trans("CGV"));

?>
<br />
<?php if (count($files)) { ?>
	<?php print_titre($langs->trans("ReadCGVFile")); ?>
	<table border="0">
    <?php foreach ($files as $file) { ?>
        <tr>
            <td><a href="<?php echo $file['link']; ?>"><?php echo img_object($file['filename'], 'pdf@tos'); ?></a></td>
            <td><a href="<?php echo $file['link']; ?>"><?php echo $file['filename']; ?></a></td>
            <td> <?php echo img_delete(); ?> <a href="<?php echo $_SERVER['PHP_SELF'].'?action=delete&file='.urlencode($file['filename']); ?>"><?php echo $langs->trans('DeleteFile', $file['filename']); ?></a></td>
        </tr>
    <?php } ?>
	</table>
	<br />
<?php } ?>

<?php print_titre($langs->trans("SelectCGVFile")); ?>
<form  id="upform" name="upform" action="<?php echo $_SERVER['PHP_SELF']; ?>?action=update"  enctype="multipart/form-data" method="post">
<input type="hidden" name="action" value="update" />

<div id="progressbar"></div>
<input type="file" id="file" name="file" />&nbsp;<input type="submit" class="butAction" name="update" id="update" value="<?php echo $langs->trans('Ok'); ?>" />&nbsp;<input type="submit" class="butActionDelete" name="cancel" id="cancel" value="<?php echo $langs->trans('Cancel'); ?>" />	
<br /><br />
</form>


<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>" />
<input type="hidden" name="action" value="save" />

<?php print_titre($langs->trans("ToSOptions")); ?>
<table class="noborder" width="100%">
	<tr class="liste_titre">
        <td width="50%"><?php echo $langs->trans("Name"); ?></td>
        <td><?php echo $langs->trans("Value"); ?></td>
    </tr>
    <tr class="impair">
        <td><?php echo $langs->trans("AutoAddToS"); ?></td>
        <td><?php echo $linkAddToS; ?></td>
    </tr>
    <tr class="pair">
        <td><?php echo $langs->trans("DefaultCGV"); ?></td>
        <td><?php echo $html->selectarray("file", $filenames, $conf->global->TOS_DEFAULT_FILE, 0, 0, 1); ?></td>
    </tr>  
    <tr class="impair">
        <td><?php echo $langs->trans("AddTosOnEachPage"); ?></td>
        <td><?php echo $linkAddToSPage; ?></td>
    </tr>    
</table>
<br />
<center>
<input type="submit" name="save" class="button" value="<?php echo $langs->trans("Save"); ?>" />
</center>

</form>

<br />
<?php llxFooter(''); ?>
