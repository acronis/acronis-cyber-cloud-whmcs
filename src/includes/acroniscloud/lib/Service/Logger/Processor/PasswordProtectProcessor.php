<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Logger\Processor;

class PasswordProtectProcessor extends TokenProtectProcessor
{
    const PASSWORD_REPLACEMENT = '*****';

    /** @var string[] */
    protected static $_sensitiveDataEnclosedIn = [
        '/password2?"\s*:\s*"/ui' => '/(?<!\\\\)"/u', // password in json
        '/password=/ui' => '/&|\s/u',               // password in URL param
        '/password%3D/ui' => '/%26|\s/u',           // password in URL param with encoded URL
        '/password\]\s*=\>\s*/ui' => '/\[|\s/u',    // password in print_r
        '/password2?\\\\"\s*:\s*\\\\"/ui' => '/\\\\"\s*[},]/u', // password in logs
        '/client_secret\\\\"\s*:\s*\\\\"/ui' => '/\\\\"\s*[},]/u', // client secret auth in logs
    ];

    protected function protectSensitiveData($token)
    {
        return static::PASSWORD_REPLACEMENT;
    }
}