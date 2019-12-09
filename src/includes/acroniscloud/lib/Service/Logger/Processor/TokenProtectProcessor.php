<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Logger\Processor;

use AcronisCloud\Util\Str;

class TokenProtectProcessor
{
    /** @var string[] */
    protected static $_sensitiveDataEnclosedIn = [
        '/jwt%3D/ui' => '/%26|\s/u',           // jwt in URL param with encoded URL
        '/jwt=/ui' => '/&|\s/u',               // jwt in URL param
        '/jwt"\s*:\s*"/ui' => '/"/u',          // jwt in json
        '/jwt\]\s*=\>\s*/ui' => '/\[|\s/u',    // jwt in print_r
        '/token%3D/ui' => '/%26|\s/u',         // any ***token in URL param with encoded URL
        '/token=/ui' => '/&|\s/u',             // any ***token in URL param
        '/token"\s*:\s*"/ui' => '/"/u',        // any ***token in json
        '/token\]\s*=\>\s*/ui' => '/\[|\s/u',  // any ***token in print_r
        '/authorization:\s*(?:bearer|basic)\s+/ui' => '/\s/u', // http header Authorization
        '/set-cookie:\s*/ui' => '/\s/u',                       // http header Set-Cookie
        '/cookie:\s*/ui' => '/\s/u',                           // http header Cookie
    ];

    public function __invoke(array $record)
    {
        $record['message'] = $this->protectTokens($record['message']);

        return $record;
    }

    protected function protectSensitiveData($token)
    {
        $parts = explode('.', $token);

        // For JWT tokens only - remove last part with signature
        if (count($parts) === 3) {
            array_pop($parts);

            return implode('.', $parts) . '.*****';
        }

        // For another tokens - keep 5 first chars, hide another with *****
        return mb_substr($token, 0, 5) . '*****';
    }

    private function protectTokens($message)
    {
        foreach (static::$_sensitiveDataEnclosedIn as $tokenBeginsWith => $tokenEndsWith) {
            $message = $this->protectToken($message, $tokenBeginsWith, $tokenEndsWith);
        }

        return $message;
    }

    private function protectToken($message, $tokenBeginsWithRegexp, $tokenEndsWithRegexp)
    {
        $tokenOpeningTagData = Str::findTag($message, $tokenBeginsWithRegexp);
        if (!is_null($tokenOpeningTagData)) {

            $tokenClosingTagData = Str::findTag($message, $tokenEndsWithRegexp, $tokenOpeningTagData['end_position']);

            $tokenStartOffset = $tokenOpeningTagData['end_position'];
            $tokenEndOffset = is_null($tokenClosingTagData) ? mb_strlen($message) : $tokenClosingTagData['start_position'];
            $afterTokenOffset = is_null($tokenClosingTagData) ? mb_strlen($message) : $tokenClosingTagData['end_position'];
            $closingTag = is_null($tokenClosingTagData) ? '' : $tokenClosingTagData['tag'];

            $messageStart = mb_substr($message, 0, $tokenStartOffset);
            $token = mb_substr($message, $tokenStartOffset, $tokenEndOffset - $tokenStartOffset);
            $messageEnd = mb_substr($message, $afterTokenOffset);

            return $messageStart
                . $this->protectSensitiveData($token)
                . $closingTag
                . $this->protectToken($messageEnd, $tokenBeginsWithRegexp, $tokenEndsWithRegexp);
        }

        return $message;
    }
}