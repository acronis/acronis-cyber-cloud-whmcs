<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Controller;

use AcronisCloud\Localization\GetTextTrait;
use AcronisCloud\Service\Dispatcher\AbstractController;
use AcronisCloud\Service\Dispatcher\ActionInterface;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Dispatcher\Response\DataResponse;
use AcronisCloud\Service\Dispatcher\Response\HtmlResponse;
use AcronisCloud\Service\Dispatcher\Response\HttpResponse;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Util\Str;
use AcronisCloud\View\ViewLoader;
use Exception;

class ClientAreaPage extends AbstractController
{
    use LoggerAwareTrait;
    use GetTextTrait;

    const MANAGEMENT_PAGE_HANDLER = 'ClientAreaManagement';

    /**
     * @return DataResponse
     */
    public function getResponseStrategy()
    {
        return new DataResponse();
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
                ':headerText' => htmlspecialchars($headerText),
                ':errorMessage' => htmlspecialchars($e->getMessage()),
                ':infoText' => htmlspecialchars($infoText),
            ]
        );

        return new HtmlResponse($message, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws \SmartyException
     */
    public function index(RequestInterface $request)
    {
        $viewLoader = new ViewLoader(
            ACRONIS_CLOUD_SERVER_MODULE_DIR . '/views',
            ACRONIS_CLOUD_SERVER_MODULE_DIR . '/assets'
        );

        $noScriptInfo = $this->gettext('{0} requires JavaScript enabled for proper work. Please enable it to continue.', [ACRONIS_CLOUD_FRIENDLY_NAME]);
        $content = $viewLoader->addAsset('client.js')
            ->assign('noScriptInfo', $noScriptInfo)
            ->assign('whmcsLocale', $this->getLocale())
            ->fetch('client_area.tpl');

        if ($request->getQueryParameter('a') !== static::MANAGEMENT_PAGE_HANDLER) {
            $this->redirect(
                'clientarea.php',
                [
                    'action' => $request->getQueryParameter('action'),
                    'id' => $request->getQueryParameter('id'),
                    'modop' => 'custom',
                    'a' => static::MANAGEMENT_PAGE_HANDLER
                ]
            );
        }

        return [
            'pagetitle' => ACRONIS_CLOUD_FRIENDLY_NAME,
            'templatefile' => 'views/content',
            'requirelogin' => true,
            'vars' => [
                'content' => $content,
            ],
        ];
    }

    public function getActions()
    {
        $manageButtonName = $this->gettext('Management');
        $manageButtonHandler = static::MANAGEMENT_PAGE_HANDLER;

        return [
            $manageButtonName => $manageButtonHandler,
        ];
    }
}