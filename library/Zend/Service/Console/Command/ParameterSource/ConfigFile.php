<?php
/**
 * Copyright (c) 2009 - 2011, RealDolmen
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of RealDolmen nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY RealDolmen ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL RealDolmen BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Zend
 * @package    Zend_Service_Console
 * @subpackage Exception
 * @version    $Id$
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */

/**
* @see Zend_Service_Console_Command_ParameterSource_ParameterSourceInterface
*/
require_once 'Zend/Service/Console/Command/ParameterSource/ParameterSourceInterface.php';

/**
 * @category   Zend
 * @package    Zend_Service_Console
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class Zend_Service_Console_Command_ParameterSource_ConfigFile
	implements Zend_Service_Console_Command_ParameterSource_ParameterSourceInterface
{
	/**
	 * Get value for a named parameter.
	 *
	 * @param mixed $parameter Parameter to get a value for
	 * @param array $argv Argument values passed to the script when run in console.
	 * @return mixed
	 */
	public function getValueForParameter($parameter, $argv = array())
	{
		// Configuration file path
		$configurationFilePath = null;

		// Check if a path to a configuration file is specified
		foreach ($argv as $parameterInput) {
			$parameterInput = explode('=', $parameterInput, 2);

			if (strtolower($parameterInput[0]) == '--configfile' || strtolower($parameterInput[0]) == '-f') {
				if (!isset($parameterInput[1])) {
					require_once 'Zend/Service/Console/Exception.php';
					throw new Zend_Service_Console_Exception("No path to a configuration file is given. Specify the path using the --ConfigFile or -F switch.");
				}
				$configurationFilePath = $parameterInput[1];
				break;
			}
		}

		// Value given?
		if (is_null($configurationFilePath)) {
			return null;
		}
		if (!file_exists($configurationFilePath)) {
			require_once 'Zend/Service/Console/Exception.php';
			throw new Zend_Service_Console_Exception("Invalid configuration file given. Specify the correct path using the --ConfigFile or -F switch.");
		}

		// Parse values
		$iniValues = parse_ini_file($configurationFilePath);

		// Default value
		$parameterValue = null;

		// Loop aliases
		foreach ($parameter->aliases as $alias) {
			if (array_key_exists($alias, $iniValues)) {
				$parameterValue = $iniValues[$alias]; break;
			} else if (array_key_exists(strtolower($alias), $iniValues)) {
				$parameterValue = $iniValues[strtolower($alias)]; break;
			} else if (array_key_exists(str_replace('-', '', $alias), $iniValues)) {
				$parameterValue = $iniValues[str_replace('-', '', $alias)]; break;
			} else if (array_key_exists(strtolower(str_replace('-', '', $alias)), $iniValues)) {
				$parameterValue = $iniValues[strtolower(str_replace('-', '', $alias))]; break;
			}
		}

		if (strtolower($parameterValue) == 'true') {
			$parameterValue = true;
		} else if (strtolower($parameterValue) == 'false') {
			$parameterValue = false;
		}

		// Done!
		return $parameterValue;
	}
}
