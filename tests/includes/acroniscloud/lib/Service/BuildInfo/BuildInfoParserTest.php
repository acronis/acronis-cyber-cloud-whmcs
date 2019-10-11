<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\BuildInfo;

class BuildInfoParserTest extends \PHPUnit_Framework_TestCase
{
    private $versionFileNames = [];

    /**
     * @dataProvider VersionProvider
     */
    public function testGetPackageVersion($fileData, $expected)
    {
        $filename = $this->createVersionFile($fileData);
        $info = new BuildInfoParser($filename);
        $this->assertEquals($expected, $info->getPackageVersion());
    }

    /**
     * @dataProvider InfoProvider
     */
    public function testGetPackageInfo($fileData, $expected)
    {
        $filename = $this->createVersionFile($fileData);
        $info = new BuildInfoParser($filename);
        $this->assertEquals($expected, $info->getPackageInfo());
    }

    public function InfoProvider()
    {
        return $dataProvider = [
            //'test name' => [dataString,expected],
            'correct data' => [
                "MAJOR=1\nMINOR=2\nPATCH=3\nBUILD=456",
                [
                    'major' => '1',
                    'minor' => '2',
                    'patch' => '3',
                    'build' => '456',
                ],
            ],
            'wrong data' => [
                "z=a\nFAKE=BUILD",
                [
                    'major' => '0',
                    'minor' => '0',
                    'patch' => '0',
                    'build' => '0',
                ],
            ],
            'empty file' => [
                "",
                [
                    'major' => '0',
                    'minor' => '0',
                    'patch' => '0',
                    'build' => '0',
                ],
            ],
            'miss major value' => [
                "MINOR=2\nPATCH=3\nBUILD=456",
                [
                    'major' => '0',
                    'minor' => '2',
                    'patch' => '3',
                    'build' => '456',
                ],
            ],
            'miss minor value' => [
                "MAJOR=1\nPATCH=3\nBUILD=456",
                [
                    'major' => '1',
                    'minor' => '0',
                    'patch' => '3',
                    'build' => '456',
                ],
            ],
            'miss patch value' => [
                "MAJOR=1\nMINOR=2\nBUILD=456",
                [
                    'major' => '1',
                    'minor' => '2',
                    'patch' => '0',
                    'build' => '456',
                ],
            ],
            'miss build value' => [
                "MAJOR=1\nMINOR=2\nPATCH=3\n",
                [
                    'major' => '1',
                    'minor' => '2',
                    'patch' => '3',
                    'build' => '0',
                ],
            ],
            'random order of value' => [
                "PATCH=3\nBUILD=456\nMAJOR=1\nMINOR=2",
                [
                    'major' => '1',
                    'minor' => '2',
                    'patch' => '3',
                    'build' => '456',
                ],
            ],
            'negative and float value' => [
                "MAJOR=-1\nMINOR=2\nPATCH=3,6\nBUILD=4.56",
                [
                    'major' => '-1',
                    'minor' => '2',
                    'patch' => '3,6',
                    'build' => '4.56',
                ],
            ],
            'double prefix' => [
                "MAJOR=1\nMINOR=2\nPATCH=2\nPATCH=3",
                [
                    'major' => '1',
                    'minor' => '2',
                    'patch' => '3',
                    'build' => '0',
                ],
            ],
        ];
    }

    public function VersionProvider()
    {
        return $dataProvider = [
            //'test name' => [dataString,expected],
            'correct data' => [
                "MAJOR=1\nMINOR=2\nPATCH=3\nBUILD=456",
                '1.2.3-456',
            ],
            'wrong data' => [
                "z=a\nFAKE=BUILD",
                '0.0.0-0',
            ],
            'empty file' => [
                "",
                '0.0.0-0',
            ],
            'miss major value' => [
                "MINOR=2\nPATCH=3\nBUILD=456",
                '0.2.3-456',
            ],
            'miss minor value' => [
                "MAJOR=1\nPATCH=3\nBUILD=456",
                '1.0.3-456',
            ],
            'miss patch value' => [
                "MAJOR=1\nMINOR=2\nBUILD=456",
                '1.2.0-456',
            ],
            'miss build value' => [
                "MAJOR=1\nMINOR=2\nPATCH=3\n",
                '1.2.3-0',
            ],
            'random order of value' => [
                "PATCH=3\nBUILD=456\nMAJOR=1\nMINOR=2",
                '1.2.3-456',
            ],
            'negative and float value' => [
                "MAJOR=-1\nMINOR=2\nPATCH=3,6\nBUILD=4.56",
                '-1.2.3,6-4.56',
            ],
            'double prefix' => [
                "MAJOR=1\nMINOR=2\nPATCH=2\nPATCH=3",
                '1.2.3-0',
            ],
        ];
    }

    protected function tearDown()
    {
        if (isset($this->versionFileNames[$this->getName()])) {
            unlink($this->versionFileNames[$this->getName()]);
            unset($this->versionFileNames[$this->getName()]);
        }
    }

    private function createVersionFile($content)
    {
        $filename = ACRONIS_CLOUD_TESTS_DIR . '/tmp/' . uniqid('version_') . '.ini';

        $handler = fopen($filename, 'w');
        fwrite($handler, $content);
        fclose($handler);

        $this->versionFileNames[$this->getName()] = $filename;

        return $filename;
    }

}
