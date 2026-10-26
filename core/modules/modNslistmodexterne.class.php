<?php
/* Copyright (C) 2004-2018  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019  Nicolas ZABOURI         <info@inovea-conseil.com>
 * Copyright (C) 2019-2024  Frédéric France         <frederic.france@free.fr>
 * Copyright (C) 2026       Nicolas Silobre         <contact@ns-info.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *  \defgroup   nslistmodexterne     Module Nslistmodexterne
 *  \brief      Nslistmodexterne module descriptor.
 *
 *  \file       htdocs/nslistmodexterne/core/modules/modNslistmodexterne.class.php
 *  \ingroup    nslistmodexterne
 *  \brief      Description and activation file for module Nslistmodexterne
 */
include_once DOL_DOCUMENT_ROOT . '/core/modules/DolibarrModules.class.php';

/**
 *  Description and activation class for module Nslistmodexterne
 */
class modNslistmodexterne extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;
		$this->db = $db;
		$this->numero = 172792;
		$this->rights_class = 'nslistmodexterne';
		$this->family = "NS INFO";
		$this->module_position = '90';
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		$this->description = "NslistmodexterneDescription";
		$this->descriptionlong = "NslistmodexterneDescription";

		// Author
		$this->editor_name = 'NS INFO';
		$this->editor_url = 'https://www.ns-info.fr';

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated', 'experimental_deprecated' or a version string like 'x.y.z'
		$this->version = trim(file_get_contents(__DIR__.'/../../VERSION'));
		$this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
		$this->picto = 'nslistmodexterne@nslistmodexterne';

		// Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array(
			'triggers' => 0,
			/* BEGIN MODULEBUILDER HOOKSCONTEXTS */
			'hooks' => array(),
			/* END MODULEBUILDER HOOKSCONTEXTS */
		);
		$this->dirs = array("/nslistmodexterne/temp");	// Documents directory of the module: JSON file of the external modules
		$this->config_page_url = array("setup.php@nslistmodexterne");
		$this->hidden = getDolGlobalString('NSLISTMODEXTERNE_HIDE_MODULE') ? true : false;	// Option of the setup: module hidden in the list of the modules
		$this->depends = array();
		$this->requiredby = array();
		$this->conflictwith = array();
		$this->langfiles = array("nslistmodexterne@nslistmodexterne");

		// Prerequisites
		$this->phpmin = array(7, 4); // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(14, 0); // Minimum version of Dolibarr required by module (functions of more recent versions in lib/nslistmodexterne_compat.lib.php)
		$this->need_javascript_ajax = 0;

		// Messages at activation
		$this->warnings_activation = array(); // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		$this->warnings_activation_ext = array(); // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)

		$this->const = array();

		if (!(function_exists('isModEnabled') ? isModEnabled("nslistmodexterne") : !empty($conf->nslistmodexterne->enabled))) {
			$conf->nslistmodexterne = new stdClass();
			$conf->nslistmodexterne->enabled = 0;
		}

		// Keep the MODULEBUILDER tags : ModuleBuilder uses them to add objects, permissions, menus...
		/* BEGIN MODULEBUILDER TABS */
		$this->tabs = array();
		/* END MODULEBUILDER TABS */

		/* BEGIN MODULEBUILDER DICTIONARIES */
		$this->dictionaries = array();
		/* END MODULEBUILDER DICTIONARIES */

		/* BEGIN MODULEBUILDER WIDGETS */
		$this->boxes = array();
		/* END MODULEBUILDER WIDGETS */

		/* BEGIN MODULEBUILDER CRON */
		$this->cronjobs = array(
			0 => array(
				'label' => 'NslistmodexterneCronGenerate',
				'jobtype' => 'method',
				'class' => '/nslistmodexterne/class/listmodexterne.class.php',
				'objectname' => 'ListModExterne',
				'method' => 'generateJsonFile',
				'parameters' => '',
				'comment' => 'Generation of the JSON file of the external modules',
				'frequency' => 1,
				'unitfrequency' => 86400,
				'status' => 1,
				'test' => function_exists('isModEnabled') ? 'isModEnabled("nslistmodexterne")' : '!empty($conf->nslistmodexterne->enabled)',
				'priority' => 50,
			),
		);
		/* END MODULEBUILDER CRON */

		// Permissions provided by this module
		$this->rights = array();
		$r = 0;
		/* BEGIN MODULEBUILDER PERMISSIONS */
		$this->rights[$r][0] = $this->numero . sprintf('%02d', (0 * 10) + 0 + 1);
		$this->rights[$r][1] = 'Read the list of the external modules';
		$this->rights[$r][4] = 'liste';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf('%02d', (0 * 10) + 1 + 1);
		$this->rights[$r][1] = 'Generate the JSON file of the external modules';
		$this->rights[$r][4] = 'liste';
		$this->rights[$r][5] = 'write';
		$r++;
		/* END MODULEBUILDER PERMISSIONS */

		// Menus provided by this module
		$this->menu = array();
		$r = 0;
		/* BEGIN MODULEBUILDER TOPMENU */
		/* END MODULEBUILDER TOPMENU */

		/* BEGIN MODULEBUILDER LEFTMENU MYOBJECT */
		/* END MODULEBUILDER LEFTMENU MYOBJECT */

		// Exports profiles provided by this module
		$r = 0;
		/* BEGIN MODULEBUILDER EXPORT MYOBJECT */
		/* END MODULEBUILDER EXPORT MYOBJECT */

		// Imports profiles provided by this module
		$r = 0;
		/* BEGIN MODULEBUILDER IMPORT MYOBJECT */
		/* END MODULEBUILDER IMPORT MYOBJECT */
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 * @param string $options Options when enabling module ('', 'noboxes')
	 * @return     int                1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		$result = $this->_load_tables('/nslistmodexterne/sql/');
		if ($result < 0) {
			return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')
		}

		// Permissions
		$this->remove($options);

		$sql = array();

		return $this->_init($sql, $options);
	}

	/**
	 *  Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from Dolibarr database.
	 *  Data directories are not deleted
	 *
	 * @param string $options Options when enabling module ('', 'noboxes')
	 * @return     int                 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();
		return $this->_remove($sql, $options);
	}
}
