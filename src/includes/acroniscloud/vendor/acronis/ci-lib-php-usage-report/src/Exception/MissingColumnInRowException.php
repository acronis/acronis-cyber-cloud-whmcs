<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Exception;

class MissingColumnInRowException extends FormatParserException
{

    /**
     * @param string $columnName
     * @param string[] $row
     */
    public function __construct($columnName, $row)
    {
        parent::__construct(\sprintf(
            'Column "%s" is not set in row "%s".',
            $columnName, json_encode($row)
        ));
    }
}