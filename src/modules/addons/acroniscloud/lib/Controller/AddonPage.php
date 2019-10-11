<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace WHMCS\Module\Addon\AcronisCloud\Controller;

use AcronisCloud\Localization\GetTextTrait;
use AcronisCloud\Service\Dispatcher\ActionInterface;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Dispatcher\Response\HtmlResponse;
use AcronisCloud\Service\Dispatcher\Response\HttpResponse;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Util\Str;
use AcronisCloud\View\ViewLoader;
use Exception;

class AddonPage extends TemplateHandler
{
    use LoggerAwareTrait;
    use GetTextTrait;

    /**
     * @return HtmlResponse
     */
    public function getResponseStrategy()
    {
        return new HtmlResponse();
    }

    /**
     * @inheritdoc
     */
    public function handleException(
        Exception $e,
        ActionInterface $action,
        RequestInterface $request
    ) {
        $headerText = $this->gettext('Error');
        $infoText = $this->gettext('Please call the server administrator for further assistance.');
        $message = Str::format(
            '<div class="errorbox"><strong><span class="title">:headerText </span></strong><br/>:infoText<br/></div>:errorMessage',
            [
                ':headerText' => $headerText,
                ':errorMessage' => $e->getMessage(),
                ':infoText' => $infoText
            ]
        );

        return new HtmlResponse($message, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @return string
     * @throws \SmartyException
     */
    public function index()
    {
        $noScriptInfo = $this->gettext('{0} requires JavaScript enabled for proper work. Please enable it to continue.', [ACRONIS_CLOUD_FRIENDLY_NAME]);
        $viewLoader = new ViewLoader(
            ACRONIS_CLOUD_ADDON_MODULE_DIR . '/views',
            ACRONIS_CLOUD_ADDON_MODULE_DIR . '/assets'
        );

        return $viewLoader->addAsset('admin.js')
            ->assign('noScriptInfo', $noScriptInfo)
            ->assign('whmcsLocale', $this->getLocale())
            ->fetch('admin_area.tpl');
    }
}