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

/**
 *      \file       htdocs/tos/class/tos.class.php
 *      \ingroup    tos
 *      \brief      File of class to add terms of sale 
 */

require_once(DOL_DOCUMENT_ROOT ."/core/class/commonobject.class.php");
require_once(DOL_DOCUMENT_ROOT ."/core/lib/functions.lib.php");
require_once(DOL_DOCUMENT_ROOT ."/core/lib/functions2.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';

/**
 *      \class      Tos
 *      \brief      
 */
class ActionsToS
{ 

	function formBuilddocOptions($parameters, &$object, &$action='')
	{
		global $langs, $db, $form, $mysoc, $conf;
		
		$langs->load('tos@tos');
		
		$modulepart = $parameters['modulepart'];
		
		$out = '';
		$addInput = false;
		
		if ($modulepart == 'facture' || $modulepart == 'commande' || $modulepart == 'propal')
		{
		 	$addInput = true;
		 			 	
		}
		$html = new Form($db);
		$out = '';

		if ($addInput)
		{
			$upload_dir = $conf->tos->dir_output;
			$filearray = dol_dir_list($upload_dir, 'files', 0, '', '\.meta$', '', SORT_ASC,1);
		
			$filenames = array();
			$filenames[] = '';

			if (count($filearray))
			{
				foreach ($filearray as $file)
				{		
					$filenames[] = $file['name'];
				}
			}

			$tos_file = GETPOST('tos_file') ? GETPOST('tos_file') : $conf->global->TOS_DEFAULT_FILE;
			
			$out = $html->selectarray("tos_file", $filenames, $tos_file, 0, 0, 1);
		}
		
		$this->resprints = $out;
		return 0;
	
	}
	  
	/**
	 * 	afterPDFCreation
	 * 	@param		object			Linked object
	 */
	function afterPDFCreation($parameters=false, &$pdfclass, &$action='')
	{
		global $conf, $user, $langs, $db;
		
		$attachTOS = GETPOST('tos_file') ? true : $conf->global->AUTO_ADD_TOS;
		$tosFilename = GETPOST('tos_file') ? GETPOST('tos_file') : $conf->global->TOS_DEFAULT_FILE;
		
		if (empty($tosFilename))
		{
			$attachTOS = false;
		}
		/*if (!$attachTOS)
		{
			$attachTOS = $conf->global->AUTO_ADD_TOS ? true : false;
		}*/
		
		$file = $parameters['file'];
		$object = $parameters['object'];

		// Load Terms of Sale
		$upload_dir = $conf->tos->dir_output;
		$tosFilePath = $upload_dir ."/". $tosFilename;


		if (($object->element == 'facture' || $object->element == 'commande' || $object->element == 'propal') && $attachTOS)
		{

			$pdf = pdf_getInstance($pdfclass->format);
			$pdf->Open();
			$pdf->setPrintHeader(false);
    		$pdf->setPrintFooter(false);
    
			// Load PDF file
			$pagesNbr = $pdf->setSourceFile($file);
			dol_syslog("ActionsToS::afterPDFCreation load file=".$file, LOG_DEBUG);
			for ($p = 1; $p <= $pagesNbr; $p++)
			{
				$templateIdx = $pdf->ImportPage($p);
				$size = $pdf->getTemplatesize($templateIdx);
	
				$portrait = $size['h'] > $size['w'] ? true : false;
	
				$pdf->AddPage($portrait ? 'P' : 'L');
	
				$pdf->useTemplate($templateIdx);

				if ($conf->global->ADD_TOS_ON_EACH_PAGE && !empty($tosFilePath))
				{
					$pagesNbrTos = $pdf->setSourceFile($tosFilePath);
					dol_syslog("ActionsToS::afterPDFCreation load file=".$tosFilePath, LOG_DEBUG);
					for ($pTos = 1; $pTos <= $pagesNbrTos; $pTos++)
					{
						$templateIdx = $pdf->ImportPage($pTos);
						$size = $pdf->getTemplatesize($templateIdx);

						$portrait = $size['h'] > $size['w'] ? true : false;

						$pdf->AddPage($portrait ? 'P' : 'L');

						$pdf->useTemplate($templateIdx);	
					}
					$pagesNbr = $pdf->setSourceFile($file);
				}
			}
			
			if (empty($conf->global->ADD_TOS_ON_EACH_PAGE) && !empty($tosFilePath))
			{
				$pagesNbr = $pdf->setSourceFile($tosFilePath);
				dol_syslog("ActionsToS::afterPDFCreation load file=".$tosFilePath, LOG_DEBUG);
				for ($p = 1; $p <= $pagesNbr; $p++)
				{
					$templateIdx = $pdf->ImportPage($p);
					$size = $pdf->getTemplatesize($templateIdx);

					$portrait = $size['h'] > $size['w'] ? true : false;

					$pdf->AddPage($portrait ? 'P' : 'L');

					$pdf->useTemplate($templateIdx);	
				}				
			}
	
				
			// Save file
			if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

			$pdf->Close();

			dol_syslog("ActionsToS::afterPDFCreation save file=".$file, LOG_DEBUG);
			$pdf->Output($file,'F');												
		}		
	
		return 0;
	}
	
}


