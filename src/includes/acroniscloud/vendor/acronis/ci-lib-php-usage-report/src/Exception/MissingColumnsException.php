<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Exception;

class MissingColumnsException extends FormatParserException
{

    /**
     * @param array $columns
     */
    public function __construct($columns)
    {
        parent::__construct(\sprintf(
            'Column names (%s) must be present in CSV file.',
            implode(',', $columns)
        ));
    }
}