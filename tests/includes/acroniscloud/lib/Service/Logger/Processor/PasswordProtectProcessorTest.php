<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Logger\Processor;

require_once('TokenProtectProcessorTest.php');

class PasswordProtectProcessorTest extends TokenProtectProcessorTest
{

    const PLACEHOLDER = '#PASSWORD#';

    protected static $passwordPatternsGroups = [
        'json' => [
            'encoded_passwords' => [
                'asd\"fg=&\}' => '*****',
                '&\}veryvery12eWDW$%Cm9we9rftjlongPAAAASSSSssswWwwwoOOorRrRdDDD' => '*****',
                'Z#6:U7uVf29gIb' => '*****',
            ],
            'patterns' => [
                'no_password' => 'Sample text, https://some.url?foo=bar&baz=fizz {"abc":""}',
                'password_json' => '"password":"#PASSWORD#"',
                'password_json_end' => '"password":"#PASSWORD#"}',
                'Password_json' => '"Password":"#PASSWORD#"',
                'Password_json_end' => '"Password":"#PASSWORD#"}',
            ],
        ],
        'url' => [
            'encoded_passwords' => [
                'pass%26%3D' => '*****',
            ],
            'patterns' => [
                'password_url_amp' => 'password=#PASSWORD#&param=abc some text',
                'password_url_whitespace_text' => 'password=#PASSWORD# some text',
                'password_url_whitespace_only' => 'password=#PASSWORD# ',
                'password_url_end_of_string' => 'password=#PASSWORD#',
                'Password_url_amp' => 'Password=#PASSWORD#&param=abc some text',
                'Password_url_whitespace_text' => 'Password=#PASSWORD# some text',
                'Password_url_whitespace_only' => 'Password=#PASSWORD# ',
                'Password_url_end_of_string' => 'Password=#PASSWORD#',
            ],
        ],
        'url_in_url' => [
            'encoded_passwords' => [
                'pass%2526%253D' => '*****',
            ],
            'patterns' => [
                'password_url_encoded_amp' => 'password%3D#PASSWORD#%26param=abc some text',
                'password_url_encoded_whitespace_text' => 'password%3D#PASSWORD#%26param=abc some text',
                'password_url_encoded_whitespace_only' => 'password%3D#PASSWORD#%26param=abc ',
                'password_url_encoded_end_of_string' => 'password%3D#PASSWORD#',
                'Password_url_encoded_amp' => 'Password%3D#PASSWORD#%26param=abc some text',
                'Password_url_encoded_whitespace_text' => 'Password%3D#PASSWORD#%26param=abc some text',
                'Password_url_encoded_whitespace_only' => 'Password%3D#PASSWORD#%26param=abc ',
                'Password_url_encoded_end_of_string' => 'Password%3D#PASSWORD#',
            ],
        ],
        'logs' => [
            'encoded_passwords' => [
                'asd\"fg=&\}' => '*****',
                '&\}veryvery12eWDW$%Cm9we9rftjlongPAAAASSSSssswWwwwoOOorRrRdDDD' => '*****',
                'Z#6:U7uVf29gIb' => '*****',
            ],
            'patterns' => [
                'Password_in_logs' => '{\"password\":\"#PASSWORD#\",\"some record\"}',
                'Password_in_logs_with_spaces' => '{\"password\":\"#PASSWORD#\"   ,\"some record\"}',
                'Password_in_logs_end' => '{\"password\":\"#PASSWORD#\"}',
                'Password_in_logs_middle' => '{\"some record\",\"password\":\"#PASSWORD#\",\"some record\"}',
                'Password_in_logs_middle_with_spaces' => '{\"some record\",   \"password\":\"#PASSWORD#\",  \"some record\"}',
            ],
        ],
    ];

    /**
     * We implement some separated groups of data building here
     * because in different cases password data is specially encoded
     *
     * @return array
     */
    public function dataProvider()
    {
        $dataProvider = [];
        foreach (static::$passwordPatternsGroups as $group) {
            $dataProvider = array_merge($dataProvider, $this->buildDataProvider(
                $group['patterns'],
                $group['encoded_passwords']
            ));
        }

        return $dataProvider;
    }

    protected function createProcessor()
    {
        return new PasswordProtectProcessor();
    }

}
