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

include(JSC_ROOT_PATH . '/jsccommon/JscComments.php');
include(JSC_ROOT_PATH . '/jscdata/JscData.php');

################################################################################
# Class(es)                                                                    #
################################################################################

class JscParser
{

    private $jscData = null;
    private $currentSection = null;
    private $currentSectionData = null;

    public function newSection($sectionName)
    {
        if (substr($sectionName, 0, 1) === '[') {
            $sectionName = ltrim($sectionName, '[');
        }
        if (substr($sectionName, -1, 1) === ']') {
            $sectionName = rtrim($sectionName, ']');
        }

        if ($sectionName !== JscSection::GLOBAL_SECTION_NAME) {
            $sectionName = trim($sectionName);
            if (substr($sectionName, 0, 1) !== '"') {
                $sectionName = '"' . $sectionName;
            }
            if (substr($sectionName, -1, 1) !== '"') {
                $sectionName = $sectionName . '"';
            }
        }

        $this->currentSection = $sectionName;
        $this->currentSectionData = '{';
    }

    public function endSection()
    {
        $this->currentSectionData .= '}';
        if (is_null($this->jscData)) {
            $this->jscData = new JscData();
        }

        $sectionName = $this->currentSection;
        $jsonString = $this->currentSectionData;

        $this->currentSectionData = $this->currentSection = null;
        $this->jscData->addSectionJsonString($sectionName, $jsonString);
    }

    public function parseLine($line)
    {
        $line = JscComments::stripComments($line);
        $line = trim($line);
        if (!empty($line)) {
            if (
                substr($line, 0, 1) === '['
                && substr($line, -1, 1) === ']'
            ) {
                if (
                    (!is_null($this->currentSection))
                    && (!is_null($this->currentSectionData))
                ) {
                    $this->endSection();
                }
                $this->newSection($line);
            } else {
                if (
                    (is_null($this->currentSection))
                    && (is_null($this->currentSectionData))
                ) {
                    $this->newSection(JscSection::GLOBAL_SECTION_NAME);
                }
                if ($this->currentSectionData !== '{') {
                    $this->currentSectionData .= ',';
                }
                $this->currentSectionData .= $line;
            }
        }
    }

    public function parseFile($jscFile, $parseLineByLine = false)
    {
        $this->jscData = null;
        try {
            if (is_readable($jscFile)) {
                if ($parseLineByLine) {
                    $fileHandle = fopen($jscFile, "r");
                    if ($fileHandle) {
                        while (($line = fgets($fileHandle)) !== false) {
                            $this->parseLine($line);
                        }
                        fclose($fileHandle);
                    }
                } else {
                    $jscText = JscComments::stripComments(file_get_contents($jscFile));
                    $lines = explode(PHP_EOL, $jscText);
                    foreach ($lines as $line) {
                        $this->parseLine($line);
                    }
                }
                $this->endSection();
            }
        } catch (Exception $e) {
            $this->jscData = null;
        } finally {
            return $this->jscData;
        }
    }

    public function parseString($jscString)
    {
        $this->jscData = null;
        try {
            $jscText = JscComments::stripComments($jscString);
            $lines = explode(PHP_EOL, $jscText);
            foreach ($lines as $line) {
                $this->parseLine($line);
            }
            $this->endSection();
        } catch (Exception $e) {
            $this->jscData = null;
        } finally {
            return $this->jscData;
        }
    }
}

################################################################################
#                                End of file                                   #
################################################################################
