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
# Class(es)                                                                    #
################################################################################

class JscComments
{

    public static function replacer($matches)
    {
        if (is_array($matches) && (count($matches) > 1)) {
            return $matches[1];
        } else {
            return '';
        }
    }

    public static function stripComments($text)
    {
        return preg_replace_callback(
            '/#.*?$|;.*?$|\/\/.*?$|\/\*[\s\S]*?\*\/|("(\\.|[^"])*")/m',
            function ($matches) {
                return JscComments::replacer($matches);
            },
            $text
        );
    }

    public static function stripCommentsFile($inFilePath, $outFilePath)
    {
        if (is_readable($inFilePath)) {
            file_put_contents($outFilePath, self::stripComments(file_get_contents($inFilePath)));
        }
    }
}

################################################################################
#                                End of file                                   #
################################################################################
