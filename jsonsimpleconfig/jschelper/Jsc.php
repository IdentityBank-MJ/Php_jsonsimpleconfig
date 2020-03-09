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

include(JSC_ROOT_PATH . '/jscparser/JscParser.php');

################################################################################
# Class(es)                                                                    #
################################################################################

class __Jsc
{

    private $jscFile = null;
    private $jscData = null;

    public function __construct($jscFile)
    {
        $this->jscFile = $jscFile;
        $jscParser = new JscParser();
        $this->jscData = $jscParser->parseFile($jscFile);
    }

    public function get()
    {
        return $this->jscData;
    }

    public function getFile()
    {
        return $this->jscFile;
    }
}

class Jsc
{

    private static $instance;

    private function __construct($jscFile)
    {
        if (self::$instance == null) {
            self::$instance = new __Jsc($jscFile);
        }
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public static function get($jscFile, $refresh = false)
    {
        if (
            $refresh
            || (is_null(self::$instance))
            || (self::$instance->getFile() != $jscFile)
        ) {
            self::$instance = null;
            new Jsc($jscFile);
        }

        return self::$instance->get();
    }

    public static function gets($jscData)
    {
        $jscParser = new JscParser();

        return $jscParser->parseString($jscData);
    }
}

################################################################################
#                                End of file                                   #
################################################################################
