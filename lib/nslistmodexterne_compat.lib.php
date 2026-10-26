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
 * \file    nslistmodexterne/lib/nslistmodexterne_compat.lib.php
 * \ingroup nslistmodexterne
 * \brief   Functions of recent Dolibarr versions missing in Dolibarr 14 (module independent of any framework)
 */

if (!function_exists('dolChmod')) {
	/**
	 * Change mod of a file (Dolibarr 17)
	 *
	 * @param	string	$filepath	Full file path
	 * @param	string	$newmask	Force new mask. For example '0644'
	 * @return	void
	 */
	function dolChmod($filepath, $newmask = '')
	{
		global $conf;

		if (!empty($newmask)) {
			@chmod($filepath, octdec($newmask));
		} elseif (!empty($conf->global->MAIN_UMASK)) {
			@chmod($filepath, octdec($conf->global->MAIN_UMASK));
		}
	}
}

if (!function_exists('isModEnabled')) {
	/**
	 * Test if a module is enabled (Dolibarr 16)
	 *
	 * @param	string	$module		Module name (lowercase)
	 * @return	bool
	 */
	function isModEnabled($module)
	{
		global $conf;

		return !empty($conf->$module->enabled);
	}
}

/**
 * Check a permission of the user (User::hasRight() exists only since Dolibarr 16)
 *
 * @param	User	$user			User
 * @param	string	$module			Module (rights_class)
 * @param	string	$permlevel1		Permission level 1
 * @param	string	$permlevel2		Permission level 2
 * @return	bool
 */
function nslistmodexterneHasRight($user, $module, $permlevel1, $permlevel2 = '')
{
	if (method_exists($user, 'hasRight')) {
		return (bool) $user->hasRight($module, $permlevel1, $permlevel2);
	}
	if ($permlevel2 !== '') {
		return !empty($user->rights->$module->$permlevel1->$permlevel2);
	}
	return !empty($user->rights->$module->$permlevel1);
}
