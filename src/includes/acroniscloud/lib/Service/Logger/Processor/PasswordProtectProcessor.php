<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Logger\Processor;

class PasswordProtectProcessor extends TokenProtectProcessor
{
    const PASSWORD_REPLACEMENT = '*****';

    /** @var string[] */
    protected static $_sensitiveDataEnclosedIn = [
        '/password"\s*:\s*"/ui' => '/(?<!\\\\)"/u', // password in json
        '/password=/ui' => '/&|\s/u',               // password in URL param
        '/password%3D/ui' => '/%26|\s/u',           // password in URL param with encoded URL
        '/password\]\s*=\>\s*/ui' => '/\[|\s/u',    // password in print_r
        '/password\\\\"\s*:\s*\\\\"/ui' => '/\\\\"\s*[},]/u', // password in logs
    ];

    protected function protectSensitiveData($token)
    {
        return static::PASSWORD_REPLACEMENT;
    }
}