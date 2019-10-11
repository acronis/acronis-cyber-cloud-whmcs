<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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
