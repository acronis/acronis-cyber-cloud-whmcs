<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Language;

use AcronisCloud\Util\Arr;

class IsoCode
{
    const CODE_EN = 'en';
    const NAME_EN = 'english';

    /** @var array */
    private $languages;

    /**
     * @param array $languages
     */
    public function __construct(array $languages)
    {
        $this->languages = $languages;
    }

    /**
     * @param string $name
     * @param string|null $defaultCode
     * @return string|null
     */
    public function getCode($name, $defaultCode = null)
    {
        return Arr::get($this->languages, strtolower(trim($name)), $defaultCode);
    }
}