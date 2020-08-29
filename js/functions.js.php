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

$res=@include("../../main.inc.php");				// For root directory
if (! $res) $res=@include("../../../main.inc.php");	// For "custom" directory


$langs->load('tos@tos');
$langs->load('main');



$db->close();
?>

$(document).ready(function() {

	 var options = { 
		beforeSend: function() 
		{
			$( "#progressbar" ).progressbar({ disabled: true });
			$('#cancel').attr('disabled', true);
		},
		uploadProgress: function(event, position, total, percentComplete) 
		{
		 	$( "#progressbar" ).progressbar( "option", {
				value : position,
				max : total,
				disabled : false,
			});	
		},
		success: function() 
		{
			$(location).attr('href', "<?php echo dol_buildpath('/tos/admin/config.php?action=updated', 1);?>") ;
 			
		},
		complete: function(response) 
		{

		},
		error: function()
		{
 
		}
 
	}; 
	
	$("#upform").ajaxForm(options);
				
		
});


