<?php
# * ********************************************************************* *
# *                                                                       *
# *   JSON Simple Config module                                           *
# *   This file is part of PHP JSC. This project may be found at:         *
# *   https://github.com/IdentityBank/Php_jsonsimpleconfig.               *
# *                                                                       *
# *   Copyright (C) 2020 by Identity Bank. All Rights Reserved.           *
# *   https://www.identitybank.eu - You belong to you                     *
# *                                                                       *
# *   This program is free software: you can redistribute it and/or       *
# *   modify it under the terms of the GNU Affero General Public          *
# *   License as published by the Free Software Foundation, either        *
# *   version 3 of the License, or (at your option) any later version.    *
# *                                                                       *
# *   This program is distributed in the hope that it will be useful,     *
# *   but WITHOUT ANY WARRANTY; without even the implied warranty of      *
# *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the        *
# *   GNU Affero General Public License for more details.                 *
# *                                                                       *
# *   You should have received a copy of the GNU Affero General Public    *
# *   License along with this program. If not, see                        *
# *   https://www.gnu.org/licenses/.                                      *
# *                                                                       *
# * ********************************************************************* *

################################################################################
# Namespace                                                                    #
################################################################################

namespace xmz\jsonsimpleconfig;

################################################################################
# Include(s)                                                                   #
################################################################################

include('JscSection.php');

################################################################################
# Class(es)                                                                    #
################################################################################

class JscData
{

    private $jsc = [];

    public function addSectionData($sectionName, $sectionData)
    {
        if (substr($sectionName, 0, 1) === '[') {
            $sectionName = ltrim($sectionName, '[');
        }
        if (substr($sectionName, -1, 1) === ']') {
            $sectionName = rtrim($sectionName, ']');
        }

        foreach ($sectionData as $key => $value) {
            if (empty($this->jsc[$sectionName])) {
                $this->jsc[$sectionName] = [];
            }
            $this->jsc[$sectionName][$key] = $value;
        }
    }

    public function addSectionJsonString($sectionName, $sectionJsonString)
    {
        if (!empty($sectionJsonString)) {
            $sectionData = json_decode($sectionJsonString, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->addSectionData($sectionName, $sectionData);
            }
        }
    }

    public function getValue($sectionName, $key, $default = null)
    {
        $value = $default;
        $sectionData = $this->getSection($sectionName);
        if (isset($sectionData[$key])) {
            $value = $sectionData[$key];
        }

        return $value;
    }

    public function getSection($sectionName = null)
    {
        if (
            (empty($sectionName))
            || ($sectionName === JscSection::GLOBAL_SECTION_NAME)
        ) {
            $sectionName = JscSection::GLOBAL_SECTION_NAME;
        } elseif ($sectionName !== JscSection::GLOBAL_SECTION_NAME) {
            $sectionName = trim($sectionName);
            if (substr($sectionName, 0, 1) !== '"') {
                $sectionName = '"' . $sectionName;
            }
            if (substr($sectionName, -1, 1) !== '"') {
                $sectionName = $sectionName . '"';
            }
        }

        if (!empty($this->jsc[$sectionName])) {
            return $this->jsc[$sectionName];
        }

        return null;
    }

    public function getSectionNames()
    {
        if (!empty($this->jsc)) {
            return array_keys($this->jsc);
        }

        return null;
    }

    public function merge($jscData)
    {
        if (!is_null($jscData) && ($jscData instanceof self)) {
            $sectionNames = $jscData->getSectionNames();
            if (!empty($sectionNames)) {
                foreach ($sectionNames as $sectionName) {
                    $sectionData = $jscData->getSection($sectionName);
                    foreach ($sectionData as $key => $value) {
                        if (empty($this->jsc[$sectionName])) {
                            $this->jsc[$sectionName] = [];
                        }
                        $this->jsc[$sectionName][$key] = $value;
                    }
                }
            }
        }
    }

    public function toString()
    {
        $jscDataString = PHP_EOL;
        $sectionNames = $this->getSectionNames();
        if (!empty($sectionNames)) {
            foreach ($sectionNames as $sectionName) {
                $sectionPrint = "* Section";
                if ($sectionName === JscSection::GLOBAL_SECTION_NAME) {
                    $sectionPrint .= " (Global):";
                } else {
                    $sectionPrint .= sprintf(" - %s:", ($sectionName));
                }
                $jscDataString .= $sectionPrint . PHP_EOL;
                $sectionData = $this->getSection($sectionName);
                if (is_array($sectionData)) {
                    foreach ($sectionData as $key => $value) {
                        $jscDataString .= '*** [' . strval($key) . '] : [' . ((is_array($value)) ? json_encode($value)
                                : $value) . ']' . PHP_EOL;
                    }
                }
            }
        }

        return $jscDataString;
    }

    public function toStringHtml()
    {
        return str_replace(PHP_EOL, "<br>" . PHP_EOL, $this->toString());
    }

    public function print()
    {
        echo($this->toString());
    }

    public function printHtml()
    {
        echo($this->toStringHtml());
    }
}

################################################################################
#                                End of file                                   #
################################################################################
