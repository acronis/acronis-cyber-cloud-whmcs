<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Logger\Whmcs;

use AcronisCloud\Util\Arr;
use AcronisCloud\Util\Str;

class ProtectSensitiveDataProcessor
{
    const REPLACEMENT = '*****';
    const PROTECTED_TEXTS = [
        '/((?:password|jwt|token)=)(.*?)(&|$)/mi' => '${1}' . self::REPLACEMENT . '${3}', // json
        '/((?:password|jwt|token|client_secret)"\s*:\s*")(.*?)((?<!\\\\)")/mi' => '${1}' . self::REPLACEMENT . '${3}', // url
        '/(authorization:\s*(?:bearer|basic)\s+)(.*)/mi' => '${1}' . self::REPLACEMENT, // http header
        '/((?:set-)?cookie:\s*)(.*)/mi' => '${1}' . self::REPLACEMENT, // http header
    ];
    const PROTECTED_PROPERTIES = [
        'password' => self::REPLACEMENT,
        'password2' => self::REPLACEMENT,
    ];

    public function __invoke(array $record)
    {
        if (!isset($record['context']['module_log'])) {
            return $record;
        }

        $scopeType = $record['context']['module_log'];
        switch ($scopeType) {
            case ScopeWhmcsApiFormatter::NAME:
                return $this->protectWhmcsApi($record);
            case ScopeCloudApiFormatter::NAME:
                return $this->protectCloudApi($record);
            case ScopeDbQueryFormatter::NAME:
                return $this->protectDBQuery($record);
        }

        return $record;
    }

    private function protectWhmcsApi(array $record)
    {
        $this->replaceProperties($record['context']);

        return $record;
    }

    private function protectCloudApi(array $record)
    {
        if (isset($record['context']['request'])) {
            $this->protectEntry($record['context']['request']);
        }

        if (isset($record['context']['response'])) {
            $this->protectEntry($record['context']['response']);
        }

        return $record;
    }

    private function protectDBQuery(array $record)
    {
        $this->protectBindings($record['context']);

        return $record;
    }

    private function protectEntry(&$entry)
    {
        if (is_string($entry)) {
            $entry = $this->replaceTexts($entry);
        } elseif (is_array($entry) || is_object($entry)) {
            foreach ($entry as &$item) {
                $this->protectEntry($item);
            }
        }
    }

    private function replaceTexts($text)
    {
        return preg_replace(
            array_keys(static::PROTECTED_TEXTS),
            array_values(static::PROTECTED_TEXTS),
            $text
        );
    }

    private function replaceProperties(&$properties)
    {
        if (!is_array($properties) && !is_object($properties)) {
            return;
        }

        foreach ($properties as $key => &$value) {
            if (Arr::has(static::PROTECTED_PROPERTIES, $key)) {
                $value = static::PROTECTED_PROPERTIES[$key];
                continue;
            }

            $this->replaceProperties($value);
        }
    }

    private function protectBindings(&$statement)
    {
        if (!isset($statement['query']) || !isset($statement['bindings'])) {
            return;
        }

        preg_match_all('/`?(?P<parameters>[a-zA-Z]+)`?\s=\s\\?/u', $statement['query'], $bindings);

        foreach ($bindings['parameters'] as $position => $paramName) {
            if (Arr::has(static::PROTECTED_PROPERTIES, $paramName)) {
                $statement['bindings'][$position] = self::REPLACEMENT;
            }
        }
    }
}