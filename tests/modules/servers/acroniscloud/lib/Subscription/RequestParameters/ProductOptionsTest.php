<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters;

class ProductOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTemplateId()
    {
        $data = [
            'configoption1' => '322',
            'configoption2' => 'email',
        ];
        $options = new ProductOptions($data);
        $this->assertEquals(
            $data['configoption1'],
            $options->getConfigOption($index = 1),
            'Testing getConfigOption failed'
        );
        $this->assertEquals($data['configoption1'], $options->getTemplateId(), 'Testing getTemplateId failed');
        $this->assertEquals(
            $data['configoption2'],
            $options->getActivationMethod(),
            'Testing getActivationMethod failed'
        );
    }

}
