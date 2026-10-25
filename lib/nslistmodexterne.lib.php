<?php
/* Copyright (C) 2026		Nicolas Silobre					<contact@ns-info.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    nslistmodexterne/lib/nslistmodexterne.lib.php
 * \ingroup nslistmodexterne
 * \brief   Library files with common functions for Nslistmodexterne
 */

dol_include_once('/nslistmodexterne/lib/nslistmodexterne_compat.lib.php');	// Functions missing in Dolibarr 14

/**
 * Return a colored Font Awesome icon for a title of load_fiche_titre() (the syntax 'fa-icon_fas_#color' of img_picto()
 * does not exist in Dolibarr 14)
 *
 * @param	string	$icon		Font Awesome icon, without 'fa-' (example: 'file-code')
 * @param	string	$color		Color (example: '#22A7D8')
 * @return	string				HTML of the icon
 */
function nslistmodexterneTitlePicto($icon, $color)
{
	return '<span class="fas fa-'.dol_escape_htmltag($icon).' valignmiddle pictotitle widthpictotitle" style="color: '.dol_escape_htmltag($color).';"></span>';
}

/**
 * Prepare admin pages header
 *
 * @return array
 */
function nslistmodexterneAdminPrepareHead()
{
	global $db, $langs, $conf;

	$langs->loadLangs(array("admin", "nslistmodexterne@nslistmodexterne"));

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/nslistmodexterne/admin/setup.php", 1);
	$head[$h][1] = '<i class="fas fa-cogs paddingright pictofixedwidth valignmiddle" style="color: #5A2D82"></i>' . $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	// Main tab of the extrafields of the objects (one sub-tab per object: nslistmodexterneExtrafieldsPrepareHead())
	$nbextrafields = 0;
	$extrafieldshead = nslistmodexterneExtrafieldsPrepareHead($nbextrafields);
	if (count($extrafieldshead)) {
		$head[$h][0] = $extrafieldshead[0][0];
		$head[$h][1] = '<i class="fas fa-th-list paddingright pictofixedwidth valignmiddle" style="color: #22A7D8"></i>'.$langs->trans('ExtraFields');
		if ($nbextrafields > 0) {
			$head[$h][1] .= '<span class="badge marginleftonlyshort">'.$nbextrafields.'</span>';
		}
		$head[$h][2] = 'extrafields';
		$h++;
	}

	$head[$h][0] = dol_buildpath("/nslistmodexterne/admin/changelog.php", 1);
	$head[$h][1] = '<i class="fas fa-code-branch paddingright pictofixedwidth valignmiddle" style="color: #3879B2"></i>' . $langs->trans("NslistmodexterneChangeLog");
	$head[$h][2] = 'changelog';
	$h++;


	$head[$h][0] = dol_buildpath("/nslistmodexterne/admin/about.php", 1);
	$head[$h][1] = '<i class="fab fa-readme paddingright pictofixedwidth valignmiddle" style="color: #14182B"></i>' . $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	complete_head_from_modules($conf, $langs, null, $head, $h, 'nslistmodexterne');

	return $head;
}

/**
 * Prepare the sub-tabs of the extrafields of the objects of the module (setup, tab "Extrafields")
 *
 * @param	int		$total		Total number of extrafields of the objects (output)
 * @return	array<int,array<int,string>>
 */
function nslistmodexterneExtrafieldsPrepareHead(&$total = 0)
{
	global $db, $langs;

	require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
	$langs->loadLangs(array("admin", "nslistmodexterne@nslistmodexterne"));

	// Objects of the module with extrafields: key of the object => array(translation key of the label, picto of the object)
	$objects = array(
		// BEGIN OBJECTS WITH EXTRAFIELDS (completed by scripts/objet_aligner.py of the skill)
		// END OBJECTS WITH EXTRAFIELDS
	);

	$extrafields = new ExtraFields($db);
	$total = 0;
	$h = 0;
	$head = array();
	foreach ($objects as $objectkey => $objectdesc) {
		list($objectlabel, $objectpicto) = (is_array($objectdesc) ? $objectdesc : array($objectdesc, ''));
		$extrafields->fetch_name_optionals_label('nslistmodexterne_'.$objectkey);
		$nb = (isset($extrafields->attributes['nslistmodexterne_'.$objectkey]['label']) && is_countable($extrafields->attributes['nslistmodexterne_'.$objectkey]['label'])) ? count($extrafields->attributes['nslistmodexterne_'.$objectkey]['label']) : 0;
		$total += $nb;
		$head[$h][0] = dol_buildpath('/nslistmodexterne/admin/'.$objectkey.'_extrafields.php', 1);
		$head[$h][1] = ($objectpicto ? img_picto('', $objectpicto, 'class="pictofixedwidth"') : '').$langs->trans($objectlabel).($nb > 0 ? '<span class="badge marginleftonlyshort">'.$nb.'</span>' : '');
		$head[$h][2] = $objectkey.'_extrafields';
		$h++;
	}

	return $head;
}
