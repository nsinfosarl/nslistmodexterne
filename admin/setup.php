<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2026      Nicolas Silobre      <contact@ns-info.fr>
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
 * \file    nslistmodexterne/admin/setup.php
 * \ingroup nslistmodexterne
 * \brief   Nslistmodexterne setup page.
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) { $i--; $j--; }
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) $res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) $res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (!$res) die("Include of main fails");

global $langs, $user, $conf, $db;

// Libraries
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
dol_include_once('/nslistmodexterne/lib/nslistmodexterne.lib.php');
dol_include_once('/nslistmodexterne/class/listmodexterne.class.php');

// Translations
$langs->loadLangs(array("admin", "nslistmodexterne@nslistmodexterne"));

// Access control
if (!$user->admin) accessforbidden();

// Parameters
$action = GETPOST('action', 'alpha');
$backtopage = GETPOST('backtopage', 'alpha');


/*
 * Actions
 */

// Generate the JSON file of the external modules
$listmod = new ListModExterne($db);
$jsonfile = $listmod->getJsonFilePath();
$permissiontogenerate = nslistmodexterneHasRight($user, 'nslistmodexterne', 'liste', 'write');
if ($action == 'generate' && $permissiontogenerate) {
	if (GETPOST('token', 'alpha') != currentToken()) {
		accessforbidden('Bad value for CSRF token');
	}
	if ($listmod->generateJsonFile() == 0) {
		$jsondata = $listmod->readJsonFile();
		setEventMessages($langs->trans('NslistmodexterneFileGenerated', is_array($jsondata) && isset($jsondata['count']) ? $jsondata['count'] : 0), null, 'mesgs');
	} else {
		setEventMessages($listmod->error, $listmod->errors, 'errors');
	}
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

// Option: hide the module in the list of the modules of Dolibarr
if ($action == 'sethidemodule') {
	if (GETPOST('token', 'alpha') != currentToken()) {
		accessforbidden('Bad value for CSRF token');
	}
	if (dolibarr_set_const($db, 'NSLISTMODEXTERNE_HIDE_MODULE', (GETPOST('value', 'int') ? '1' : '0'), 'chaine', 0, '', $conf->entity) > 0) {
		setEventMessages($langs->trans('SetupSaved'), null, 'mesgs');
	} else {
		setEventMessages($langs->trans('Error'), null, 'errors');
	}
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

// Download the JSON file
if ($action == 'download' && is_readable($jsonfile)) {
	top_httphead('application/json');
	header('Content-Disposition: attachment; filename="'.ListModExterne::FILENAME.'"');
	header('Content-Length: '.dol_filesize($jsonfile));
	readfile($jsonfile);
	exit;
}


/*
 * View
 */

$page_name = "NslistmodexterneSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="'.($backtopage ? $backtopage : DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'object_nslistmodexterne@nslistmodexterne');

// Configuration header
$head = nslistmodexterneAdminPrepareHead();
print dol_get_fiche_head($head, 'settings', $langs->trans("Settings"), -1, "nslistmodexterne@nslistmodexterne");

// JSON file of the external modules: path, date, buttons
$filedate = (is_readable($jsonfile) ? dol_filemtime($jsonfile) : 0);
$buttons = dolGetButtonTitle($langs->trans('NslistmodexterneGenerate'), '', 'fa fa-sync', $_SERVER['PHP_SELF'].'?action=generate&token='.newToken(), '', $permissiontogenerate);
if ($filedate) {
	$buttons .= dolGetButtonTitle($langs->trans('Download'), '', 'fa fa-download', $_SERVER['PHP_SELF'].'?action=download&token='.newToken());
}
print load_fiche_titre(nslistmodexterneTitlePicto('file-code', '#22A7D8').$langs->trans('NslistmodexterneJsonFile'), $buttons, '');
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="oddeven"><td class="titlefield">'.$langs->trans('File').'</td><td class="wordbreak">';
if ($filedate) {
	print '<a href="'.$_SERVER['PHP_SELF'].'?action=download&token='.newToken().'">'.'<span class="fas fa-download pictofixedwidth" style="color: #22A7D8;"></span>'.dol_escape_htmltag(ListModExterne::FILENAME).'</a> ';
}
print '<span class="opacitymedium">'.dol_escape_htmltag($jsonfile).'</span></td></tr>';
$suiviprodfile = $listmod->getSuiviProdFilePath();
if ($suiviprodfile) {
	print '<tr class="oddeven"><td>'.$langs->trans('NslistmodexterneSuiviProdCopy').'</td><td class="wordbreak"><span class="opacitymedium">'.dol_escape_htmltag($suiviprodfile).'</span></td></tr>';
}
print '<tr class="oddeven"><td>'.$langs->trans('DateLastGeneration').'</td><td>'.($filedate ? dol_print_date($filedate, 'dayhour', 'tzuser') : '<span class="opacitymedium">'.$langs->trans('NslistmodexterneNoFile').'</span>').'</td></tr>';
print '</table>';
print '</div>';

// Options
$hidemodule = getDolGlobalString('NSLISTMODEXTERNE_HIDE_MODULE') ? 1 : 0;
print '<br>';
print load_fiche_titre(nslistmodexterneTitlePicto('sliders-h', '#5A2D82').$langs->trans('Parameters'), '', '');
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre"><td>'.$langs->trans('Parameter').'</td><td class="right">'.$langs->trans('Value').'</td></tr>';
print '<tr class="oddeven"><td>'.$langs->trans('NslistmodexterneHideModule');
print '<br><span class="opacitymedium small">'.$langs->trans('NslistmodexterneHideModuleHelp').' <a href="'.dol_escape_htmltag(dol_buildpath('/nslistmodexterne/admin/setup.php', 2)).'">'.dol_escape_htmltag(dol_buildpath('/nslistmodexterne/admin/setup.php', 2)).'</a></span></td>';
print '<td class="right"><a class="reposition" href="'.$_SERVER['PHP_SELF'].'?action=sethidemodule&value='.($hidemodule ? 0 : 1).'&token='.newToken().'">'.img_picto($langs->trans($hidemodule ? 'Activated' : 'Disabled'), $hidemodule ? 'switch_on' : 'switch_off').'</a></td></tr>';
print '</table>';
print '</div>';

// Page end
print dol_get_fiche_end();

llxFooter();
$db->close();
