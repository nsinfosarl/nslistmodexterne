<?php
/* Copyright (C) 2026		Nicolas SILOBRE				<contact@ns-info.fr>
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
 * \file        nslistmodexterne/class/listmodexterne.class.php
 * \ingroup     nslistmodexterne
 * \brief       List of the external modules of the instance and its JSON file (reusable by other modules)
 */

require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
dol_include_once('/nslistmodexterne/lib/nslistmodexterne_compat.lib.php');	// Functions missing in Dolibarr 14 (dolChmod)

/**
 * List of the external modules (modules not provided by Dolibarr) found in the module directories of the instance.
 *
 * Use in another module:
 *     dol_include_once('/nslistmodexterne/class/listmodexterne.class.php');
 *     $list = new ListModExterne($db);
 *     $data = $list->readJsonFile();		// Last generated file (array, or null if none), fast
 *     $modules = $list->getModules();		// Or the current list, computed now
 */
class ListModExterne
{
	/**
	 * Format of the JSON file (to change if the structure changes)
	 */
	const FORMAT_VERSION = 2;

	/**
	 * Former name of the JSON file (before it was named after the URL of the instance), deleted at the next generation;
	 * also the name used when the URL of the instance is unknown
	 */
	const FILENAME = 'modules_externes.json';

	/**
	 * @var DoliDB	Database handler
	 */
	public $db;

	/**
	 * @var string	Error message
	 */
	public $error = '';

	/**
	 * @var string[]	Error messages
	 */
	public $errors = array();

	/**
	 * @var string	Output of the scheduled job
	 */
	public $output = '';

	/**
	 * Constructor
	 *
	 * @param	DoliDB	$db		Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Return the path of the JSON file
	 *
	 * @return	string	Full path of the file (documents directory of the module, current entity)
	 */
	public function getJsonFilePath()
	{
		global $conf;

		$dir = '';
		if (!empty($conf->nslistmodexterne->multidir_output[$conf->entity])) {
			$dir = $conf->nslistmodexterne->multidir_output[$conf->entity];
		} elseif (!empty($conf->nslistmodexterne->dir_output)) {
			$dir = $conf->nslistmodexterne->dir_output;
		} else {
			$dir = DOL_DATA_ROOT.($conf->entity > 1 ? '/'.((int) $conf->entity) : '').'/nslistmodexterne';
		}

		return $dir.'/'.self::getFileName();
	}

	/**
	 * Return the path of the copy of the JSON file in the documents directory of the module nssuiviprod (Suivi des sites)
	 *
	 * @return	string	Full path of the copy, '' if the module nssuiviprod is not enabled
	 */
	public function getSuiviProdFilePath()
	{
		global $conf;

		if (!isModEnabled('nssuiviprod')) {
			return '';
		}
		if (!empty($conf->nssuiviprod->multidir_output[$conf->entity])) {
			$dir = $conf->nssuiviprod->multidir_output[$conf->entity];
		} elseif (!empty($conf->nssuiviprod->dir_output)) {
			$dir = $conf->nssuiviprod->dir_output;
		} else {
			$dir = DOL_DATA_ROOT.($conf->entity > 1 ? '/'.((int) $conf->entity) : '').'/nssuiviprod';
		}

		return $dir.'/'.self::getFileName();
	}

	/**
	 * Return the external modules of the instance, sorted by name
	 *
	 * @return	array<int,array<string,mixed>>	One array per module (see the keys in the code)
	 */
	public function getModules()
	{
		global $langs;

		$langs->loadLangs(array("admin", "main"));
		$modules = array();
		$loaded = array();

		foreach (dolGetModulesDirs() as $dir) {
			$handle = @opendir($dir);
			if (!is_resource($handle)) {
				continue;
			}
			while (($file = readdir($handle)) !== false) {
				if (!is_readable($dir.$file) || substr($file, 0, 3) != 'mod' || substr($file, dol_strlen($file) - 10) != '.class.php') {
					continue;
				}
				$modName = substr($file, 0, dol_strlen($file) - 10);
				if (empty($modName) || !empty($loaded[$modName]) || strtolower($modName) == 'modnslistmodexterne') {	// This module is not listed in its own file
					continue;
				}
				try {
					include_once $dir.$file;
					if (!class_exists($modName)) {
						continue;
					}
					$objMod = @new $modName($this->db);	// Warnings of the descriptors of other modules hidden
				} catch (Throwable $e) {
					dol_syslog(__METHOD__.' '.$modName.' '.$e->getMessage(), LOG_WARNING);
					continue;
				}
				$loaded[$modName] = 1;
				if (!is_object($objMod) || !method_exists($objMod, 'isCoreOrExternalModule') || $objMod->isCoreOrExternalModule() != 'external') {
					continue;
				}
				$modules[] = $this->describe($objMod, $dir);
			}
			closedir($handle);
		}

		usort($modules, function ($a, $b) {
			return strcmp($a['name'], $b['name']);
		});

		return $modules;
	}

	/**
	 * Return the information of a module
	 *
	 * @param	DolibarrModules	$objMod		Descriptor of the module
	 * @param	string			$dir		Directory of the descriptor (.../core/modules/)
	 * @return	array<string,mixed>
	 */
	private function describe($objMod, $dir)
	{
		$name = strtolower(preg_replace('/^mod/i', '', get_class($objMod)));
		$moduledir = dirname(dirname(rtrim($dir, '/')));
		$constname = 'MAIN_MODULE_'.strtoupper($name);

		// Activation date (last activation recorded by Dolibarr)
		$activationdate = null;
		if (method_exists($objMod, 'getLastActivationDate')) {
			$tmp = $objMod->getLastActivationDate();
			if (!empty($tmp)) {
				$activationdate = dol_print_date($tmp, 'dayhourrfc', 'gmt');
			}
		}

		// Features provided by the module
		$parts = (isset($objMod->module_parts) && is_array($objMod->module_parts)) ? $objMod->module_parts : array();
		$hooks = array();
		if (!empty($parts['hooks']) && is_array($parts['hooks'])) {
			$hooks = isset($parts['hooks']['data']) && is_array($parts['hooks']['data']) ? $parts['hooks']['data'] : array_values(array_filter($parts['hooks'], 'is_string'));
		}

		$file = $moduledir.'/VERSION';
		$changelog = $moduledir.'/ChangeLog.md';

		return array(
			'name' => $name,
			'class' => get_class($objMod),
			'number' => (int) $objMod->numero,
			'label' => $this->text($objMod->getName()),
			'description' => $this->text($objMod->getDesc()),
			'version' => (string) $objMod->getVersion(0),
			'publisher' => $this->text($objMod->getPublisher()),
			'publisher_url' => (string) $objMod->getPublisherUrl(),
			'family' => (string) $objMod->family,
			'enabled' => (bool) getDolGlobalString($constname),
			'status' => getDolGlobalString($constname) ? 'enabled' : 'disabled',
			'activation_date' => $activationdate,
			'path' => $this->relativePath($moduledir),
			'version_file' => is_readable($file) ? trim((string) file_get_contents($file)) : null,
			'has_changelog' => is_readable($changelog),
			'need_dolibarr_version' => $this->versionText(isset($objMod->need_dolibarr_version) ? $objMod->need_dolibarr_version : null),
			'php_min' => $this->versionText(isset($objMod->phpmin) ? $objMod->phpmin : null),
			'depends' => $this->listText(isset($objMod->depends) ? $objMod->depends : array()),
			'required_by' => $this->listText(isset($objMod->requiredby) ? $objMod->requiredby : array()),
			'conflict_with' => $this->listText(isset($objMod->conflictwith) ? $objMod->conflictwith : array()),
			'setup_page' => !empty($objMod->config_page_url) ? $this->listText($objMod->config_page_url) : array(),
			'lang_files' => $this->listText(isset($objMod->langfiles) ? $objMod->langfiles : array()),
			'url_last_version' => !empty($objMod->url_last_version) ? (string) $objMod->url_last_version : null,
			'features' => array(
				'rights' => is_array($objMod->rights) ? count($objMod->rights) : 0,
				'menus' => is_array($objMod->menu) ? count($objMod->menu) : 0,
				'tabs' => is_array($objMod->tabs) ? count($objMod->tabs) : 0,
				'dictionaries' => (!empty($objMod->dictionaries['tabname']) && is_array($objMod->dictionaries['tabname'])) ? count($objMod->dictionaries['tabname']) : 0,
				'widgets' => is_array($objMod->boxes) ? count($objMod->boxes) : 0,
				'cron_jobs' => is_array($objMod->cronjobs) ? count($objMod->cronjobs) : 0,
				'triggers' => !empty($parts['triggers']),
				'models' => !empty($parts['models']),
				'hooks' => $this->listText($hooks),
				'css' => $this->listText(isset($parts['css']) ? (array) $parts['css'] : array()),
				'js' => $this->listText(isset($parts['js']) ? (array) $parts['js'] : array()),
			),
		);
	}

	/**
	 * Return the content of the JSON file
	 *
	 * @return	array<string,mixed>
	 */
	public function getData()
	{
		global $conf, $dolibarr_main_url_root;

		$modules = $this->getModules();

		return array(
			'format_version' => self::FORMAT_VERSION,
			'generated_at' => dol_print_date(dol_now(), 'dayhourrfc', 'gmt'),
			'generator' => 'nslistmodexterne',
			'instance' => array(
				'url' => (string) $dolibarr_main_url_root,
				'entity' => (int) $conf->entity,
				'dolibarr_version' => DOL_VERSION,
				'php_version' => PHP_VERSION,
			),
			'count' => count($modules),
			'count_enabled' => count(array_filter($modules, function ($m) {
				return $m['enabled'];
			})),
			'modules' => $modules,
		);
	}

	/**
	 * Return the name of the JSON file: the address of the instance (URL of Dolibarr)
	 *
	 * @return	string	Name of the file, with the extension .json
	 */
	public static function getFileName()
	{
		global $dolibarr_main_url_root;

		// Address of the instance without the protocol, "/" and ":" replaced (example: erp.example.com_dolibarr.json)
		$name = dol_strtolower(trim((string) $dolibarr_main_url_root));
		$name = preg_replace('/^[a-z]+:\/\//', '', $name);
		$name = trim(str_replace(array('/', ':', '\\'), '_', rtrim($name, '/')), '_');
		$name = dol_sanitizeFileName($name);

		return ($name !== '' ? $name.'.json' : self::FILENAME);
	}

	/**
	 * Generate the JSON file. Also used by the scheduled job (returns 0 if OK).
	 *
	 * @return	int		0 if OK, <0 if KO (error in $this->error)
	 */
	public function generateJsonFile()
	{
		$path = $this->getJsonFilePath();
		if (dol_mkdir(dirname($path)) < 0) {
			$this->error = 'Failed to create the directory '.dirname($path);
			$this->errors[] = $this->error;
			return -1;
		}
		$data = $this->getData();
		$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		if ($json === false) {
			$this->error = 'JSON error: '.json_last_error_msg();
			$this->errors[] = $this->error;
			return -2;
		}
		if (file_put_contents($path, $json."\n") === false) {
			$this->error = 'Failed to write the file '.$path;
			$this->errors[] = $this->error;
			return -3;
		}
		dolChmod($path);
		$this->output = $data['count'].' external modules written into '.$path;
		$this->deleteFormerFile($path);

		// Copy in the documents directory of the module nssuiviprod (Suivi des sites), if enabled
		$copy = $this->getSuiviProdFilePath();
		if ($copy) {
			if (dol_mkdir(dirname($copy)) < 0 || file_put_contents($copy, $json."\n") === false) {
				$this->error = 'Failed to write the file '.$copy;
				$this->errors[] = $this->error;
				return -4;
			}
			dolChmod($copy);
			$this->output .= ' and '.$copy;
			$this->deleteFormerFile($copy);
		}

		return 0;
	}

	/**
	 * Delete the file with the former name (modules_externes.json) in the directory of a generated file
	 *
	 * @param	string	$path	Full path of the generated file
	 * @return	void
	 */
	private function deleteFormerFile($path)
	{
		$former = dirname($path).'/'.self::FILENAME;
		if ($former != $path && is_file($former)) {
			dol_delete_file($former, 0, 1);
		}
	}

	/**
	 * Read the last generated JSON file
	 *
	 * @return	array<string,mixed>|null	Content of the file, null if no file or invalid file
	 */
	public function readJsonFile()
	{
		$path = $this->getJsonFilePath();
		if (!is_readable($path)) {
			return null;
		}
		$data = json_decode((string) file_get_contents($path), true);

		return is_array($data) ? $data : null;
	}

	/**
	 * Plain text (no HTML, entities decoded)
	 *
	 * @param	mixed	$value	Value
	 * @return	string
	 */
	private function text($value)
	{
		return trim(html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
	}

	/**
	 * Version given as array (19, -3) or string
	 *
	 * @param	mixed	$value	Value
	 * @return	string|null
	 */
	private function versionText($value)
	{
		if (is_array($value)) {
			$value = array_filter($value, function ($v) {
				return (int) $v >= 0;
			});
			return count($value) ? implode('.', $value) : null;
		}

		return ($value === null || $value === '') ? null : (string) $value;
	}

	/**
	 * List of strings (values that are strings or numbers only)
	 *
	 * @param	mixed	$value	Value
	 * @return	string[]
	 */
	private function listText($value)
	{
		$list = array();
		foreach ((array) $value as $val) {
			if (is_string($val) || is_numeric($val)) {
				if ((string) $val !== '') {
					$list[] = (string) $val;
				}
			}
		}

		return array_values(array_unique($list));
	}

	/**
	 * Path relative to the root of the instance (htdocs)
	 *
	 * @param	string	$path	Full path
	 * @return	string
	 */
	private function relativePath($path)
	{
		$root = dirname(DOL_DOCUMENT_ROOT);
		return (strpos($path, $root) === 0) ? substr($path, dol_strlen($root)) : $path;
	}
}
