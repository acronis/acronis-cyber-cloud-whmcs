<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\BuildInfo;

use AcronisCloud\Util\Arr;
use AcronisCloud\Util\MemoizeTrait;
use AcronisCloud\Util\Str;

class BuildInfoParser
{
    use MemoizeTrait;

    const DEFAULT_VERSION = '0';

    const MAJOR = 'major';
    const MINOR = 'minor';
    const PATCH = 'patch';
    const BUILD = 'build';

    /** @var string */
    private $versionFile;

    public function __construct($versionFile)
    {
        $this->versionFile = $versionFile;
    }

    /**
     * Method returns version of the package
     * Example:
     * [
     *     'major' => '1',
     *     'minor' => '2',
     *     'patch' => '3',
     *     'build' => '456'
     * ]
     *
     * @return array
     */
    public function getPackageInfo()
    {
        return $this->memoize(function () {
            return $this->parseVersionFile();
        });
    }

    /**
     * Method returns version of the package in format: major.minor.patch-build
     * Example: 1.2.3-456
     *
     * @return string
     */
    public function getPackageVersion()
    {
        $packageInfo = $this->getPackageInfo();

        return Str::format(
            '%s.%s.%s-%s',
            Arr::get($packageInfo, static::MAJOR),
            Arr::get($packageInfo, static::MINOR),
            Arr::get($packageInfo, static::PATCH),
            Arr::get($packageInfo, static::BUILD)
        );
    }

    private function parseVersionFile()
    {
        $pairs = parse_ini_file($this->versionFile);
        $data = [];
        foreach ($pairs as $key => $value) {
            $data[strtolower(trim($key))] = trim($value);
        }

        return [
            static::MAJOR => Arr::get($data, static::MAJOR, static::DEFAULT_VERSION),
            static::MINOR => Arr::get($data, static::MINOR, static::DEFAULT_VERSION),
            static::PATCH => Arr::get($data, static::PATCH, static::DEFAULT_VERSION),
            static::BUILD => Arr::get($data, static::BUILD, static::DEFAULT_VERSION),
        ];
    }
}
