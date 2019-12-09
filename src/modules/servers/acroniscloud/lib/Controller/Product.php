<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Controller;

use AcronisCloud\Localization\GetTextTrait;
use AcronisCloud\Service\Config\ConfigAwareTrait;
use AcronisCloud\Service\Database\Repository\RepositoryAwareTrait;
use AcronisCloud\Service\Dispatcher\AbstractController;
use AcronisCloud\Service\Dispatcher\Request;
use AcronisCloud\Service\Dispatcher\RequestException;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Dispatcher\Response\StatusCodeInterface;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\MemoizeTrait;
use AcronisCloud\Util\Str;
use Exception;
use WHMCS\Module\Server\AcronisCloud\Controller\RequestParameters\ProductEditAccessor;
use WHMCS\Module\Server\AcronisCloud\Product\CustomFields;
use WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters\ProductOptions;

class Product extends AbstractController
{
    use ConfigAwareTrait,
        GetTextTrait,
        LoggerAwareTrait,
        MemoizeTrait,
        RepositoryAwareTrait;

    const REQUEST_PARAMETER_NEW_PRODUCT_ID  = 'newproductid';

    private $cloudServer;

    /**
     * @param $request
     * @throws Exception
     */
    public function beforeUpgrade($request)
    {
        $newProductId = Arr::get($request->getParameters(), static::REQUEST_PARAMETER_NEW_PRODUCT_ID);
        $customFields = new CustomFields($newProductId, null);
        $customFields->createField(CustomFields::FIELD_NAME_TENANT_ID);
        $customFields->createField(CustomFields::FIELD_NAME_USER_ID);
        $customFields->createField(CustomFields::FIELD_NAME_CLOUD_LOGIN);
    }

    /**
     * @param RequestInterface $request
     * @return array
     */
    public function getMetaData($request)
    {
        return [
            'DisplayName' => ACRONIS_CLOUD_FRIENDLY_NAME,
            'APIVersion' => '1.1', // Use API Version 1.1
            'RequiresServer' => true, // Set true if module requires a server to work
        ];
    }

    /**
     * @param RequestInterface $request
     * @return array
     */
    public function getConfigOptions($request)
    {
        $options = [
            'templateId' => [
                'FriendlyName' => $this->gettext('Template name'),
                'Description' => $this->gettext('You may view, create or modify templates at {0} in Addons section.', [
                    Str::format(
                        '<a href="%s">%s</a>',
                        $this->getAddonModuleLink(),
                        ACRONIS_CLOUD_FRIENDLY_NAME
                    ),
                ]),
                'Type' => 'dropdown',
                'Options' => $this->getTemplates(),
            ],
        ];
        $askUserCredentials = $this->getConfig()
            ->getProductSettings()
            ->getAskUserCredentials();

        if ($askUserCredentials) {
            $options['activationMethod'] = [
                'FriendlyName' => $this->gettext('Activation method'),
                'Type' => 'dropdown',
                'Options' => [
                    ProductOptions::ACTIVATION_METHOD_EMAIL => $this->gettext('Send an email message'),
                    ProductOptions::ACTIVATION_METHOD_PASSWORD => $this->gettext('Require credentials when placing order'),
                ],
            ];
        }

        return $options;
    }

    public function setupCustomFields(Request $request)
    {
        $productParams = new ProductEditAccessor($request->getParameters());
        if ($productParams->getServerType() !== ACRONIS_CLOUD_SERVICE_NAME) {
            return;
        }

        $productId = $productParams->getProductId();
        if (!$productId) {
            throw new RequestException(
                'Cannot set custom fields, missing product id in request data.',
                $request->getParameters(),
                StatusCodeInterface::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $activationMethod = $productParams->getProductOptions()->getActivationMethod();
        $customFieldsManager = new CustomFields($productId, null);
        $customFieldsManager->createField(CustomFields::FIELD_NAME_USER_ID);
        $customFieldsManager->createField(CustomFields::FIELD_NAME_TENANT_ID);
        $customFieldsManager->createField(CustomFields::FIELD_NAME_CLOUD_LOGIN);

        if ($activationMethod === ProductOptions::ACTIVATION_METHOD_PASSWORD) {
            $customFieldsManager->createField(CustomFields::FIELD_NAME_CLOUD_PASSWORD);
        } else {
            // this field must not exist for email activation products
            $customFieldsManager->removeField(CustomFields::FIELD_NAME_CLOUD_PASSWORD);
        }
    }

    /**
     * @return array
     */
    private function getTemplates()
    {
        $templates = $this->getRepository()
            ->getTemplateRepository()
            ->all();

        return Arr::map($templates->toArray(), 'id', 'name');
    }

    private function getAddonModuleLink()
    {
        return '/' . $this->getAdminAreaPath() . '/addonmodules.php?' . http_build_query([
                'module' => ACRONIS_CLOUD_SERVICE_NAME,
            ]);
    }

    private function getAdminAreaPath()
    {
        global $customadminpath;

        return $customadminpath ?: 'admin';
    }
}