<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Logger\Processor;

class TokenProtectProcessorTest extends \PHPUnit_Framework_TestCase
{
    const PLACEHOLDER = '#TOKEN#';

    protected static $sensitiveDataMap = [
        'eyJ0eiJ9.eyJuYW1lIcyNTh9.F5OFmw' => 'eyJ0eiJ9.eyJuYW1lIcyNTh9.*****',
        'eyJ0eiJ9eyJuYW1lIceyJuYWrgjjk==' => 'eyJ0e*****',
    ];

    protected static $messagePatterns = [
        'no_token'                              => 'Sample text, https://some.url?foo=bar&baz=fizz {"abc":""}',
        'jwt_token_url_amp'                     => 'jwt=#TOKEN#&param=abc some text',
        'jwt_token_url_whitespace_text'         => 'jwt=#TOKEN# some text',
        'jwt_token_url_whitespace_only'         => 'jwt=#TOKEN# ',
        'jwt_token_url_end_of_string'           => 'jwt=#TOKEN#',
        'jwt_token_url_encoded_amp'             => 'jwt%3D#TOKEN#%26param=abc some text',
        'jwt_token_url_encoded_whitespace_text' => 'jwt%3D#TOKEN#%26param=abc some text',
        'jwt_token_url_encoded_whitespace_only' => 'jwt%3D#TOKEN#%26param=abc ',
        'jwt_token_url_encoded_end_of_string'   => 'jwt%3D#TOKEN#',
        'jwt_token_json'                        => '"jwt":"#TOKEN#"',
        'id_token_json'                         => '"id_token":"#TOKEN#"',
        'token_json'                            => '"token":"#TOKEN#"',
        'token_url'                             => 'token=#TOKEN#',
        'aps_token'                             => '[aps_token] => #TOKEN#',
        'refresh_token_json'                    => '"refresh_token":"#TOKEN#"',
        'access_token_json'                     => '"access_token":"#TOKEN#"',
        'another_token_json'                    => '"another_token":"#TOKEN#"',
        'unknown_tken_json'                     => '"another_tken":"ANOTHER.TOKEN.SIGNATURE"',
        'many_tokens'                           => 'jwt=#TOKEN#&param=abc some text jwt=#TOKEN#' .
            '&param=abc some text {"access_token":"#TOKEN#"}' .
            ' http://some-url?encoded=jwt%3D#TOKEN#%26param=abc some text',
        'JWT_TOKEN_URL_AMP'                     => 'JWT=#TOKEN#&param=abc some text',
        'JWT_TOKEN_URL_WHITESPACE_TEXT'         => 'JWT=#TOKEN# some text',
        'JWT_TOKEN_URL_WHITESPACE_ONLY'         => 'JWT=#TOKEN# ',
        'JWT_TOKEN_URL_END_OF_STRING'           => 'JWT=#TOKEN#',
        'JWT_TOKEN_URL_ENCODED_AMP'             => 'JWT%3D#TOKEN#%26param=abc some text',
        'JWT_TOKEN_URL_ENCODED_WHITESPACE_TEXT' => 'JWT%3D#TOKEN#%26param=abc some text',
        'JWT_TOKEN_URL_ENCODED_WHITESPACE_ONLY' => 'JWT%3D#TOKEN#%26param=abc ',
        'JWT_TOKEN_URL_ENCODED_END_OF_STRING'   => 'JWT%3D#TOKEN#',
        'JWT_TOKEN_JSON'                        => '"JWT":"#TOKEN#"',
        'ID_TOKEN_JSON'                         => '"ID_TOKEN":"#TOKEN#"',
        'TOKEN_JSON'                            => '"TOKEN":"#TOKEN#"',
        'TOKEN_URL'                             => 'TOKEN=#TOKEN#',
        'APS_TOKEN'                             => '[APS_TOKEN] => #TOKEN#',
        'REFRESH_TOKEN_JSON'                    => '"REFRESH_TOKEN":"#TOKEN#"',
        'ACCESS_TOKEN_JSON'                     => '"ACCESS_TOKEN":"#TOKEN#"',
        'ANOTHER_TOKEN_JSON'                    => '"ANOTHER_TOKEN":"#TOKEN#"',
        'UNKNOWN_TKEN_JSON'                     => '"ANOTHER_TKEN":"ANOTHER.TOKEN.SIGNATURE"',
        'MANY_tokens'                           => 'JWT=#TOKEN#&param=abc some text jwt=#TOKEN#' .
            '&param=abc some text {"access_token":"#TOKEN#"}' .
            ' http://some-url?encoded=jwt%3D#TOKEN#%26param=abc some text',
        'HTTP HEADER AUTHORIZATION BEARER'      => 'Authorization: Bearer #TOKEN#',
        'HTTP HEADER AUTHORIZATION BASIC'       => 'Authorization: Bearer #TOKEN#',
        'HTTP HEADER COOKIE'                    => 'Cookie: #TOKEN#',
        'HTTP HEADER SET-COOKIE'                => 'Set-Cookie: #TOKEN#',
    ];

    /**
     * @dataProvider dataProvider
     */
    public function testProtectTokens($message, $expectedMessage)
    {
        $processor = $this->createProcessor();
        $record = ['message' => $message];
        $expectedRecord = ['message' => $expectedMessage];

        $record = $processor->__invoke($record);

        $this->assertEquals($expectedRecord, $record);
    }

    public function dataProvider()
    {
        return $this->buildDataProvider(static::$messagePatterns, static::$sensitiveDataMap);
    }

    protected function buildDataProvider($messagePatterns, $sensitiveDataMap)
    {
        $dataProvider = [];
        foreach ($messagePatterns as $key => $messagePattern) {
            foreach ($sensitiveDataMap as $original => $protected) {
                $dataProvider[$key . '_' . $original] = [
                    str_replace(static::PLACEHOLDER, $original, $messagePattern),
                    str_replace(static::PLACEHOLDER, $protected, $messagePattern),
                ];
            }
        }

        return $dataProvider;
    }

    protected function createProcessor()
    {
        return new TokenProtectProcessor();
    }
}
