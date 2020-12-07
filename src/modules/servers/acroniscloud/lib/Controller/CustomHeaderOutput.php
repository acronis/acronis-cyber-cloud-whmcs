<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Controller;

use AcronisCloud\Localization\GetTextTrait;
use AcronisCloud\Service\Dispatcher\AbstractController;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Errors\ErrorNotification;
use AcronisCloud\Service\Errors\ProvisioningErrorsAwareTrait;
use AcronisCloud\Service\Locator;
use AcronisCloud\View\ViewLoader;
use SmartyException;

class CustomHeaderOutput extends AbstractController
{
    use GetTextTrait,
        ProvisioningErrorsAwareTrait;

    /**
     * @param RequestInterface $request
     * @return string
     * @throws SmartyException
     */
    public function adminOutput(RequestInterface $request)
    {
        return $this->getOutput(true);
    }

    /**
     * @param RequestInterface $request
     * @return string
     * @throws SmartyException
     */
    public function clientOutput(RequestInterface $request)
    {
        return $this->getOutput(false);
    }

    /**
     * @param $isAdmin
     * @return string
     * @throws SmartyException
     */
    protected function getOutput($isAdmin)
    {
        $errorsManager = $this->getProvisioningErrorsManager();
        if ($errorsManager->hasErrors()) {
            $errors = $errorsManager->getErrors();
            $firstError = reset($errors);
            $errorsManager->resetErrors()->flush();
            return $this->outputError($firstError, $isAdmin);
        }

        return '';
    }

    /**
     * @param ErrorNotification $error
     * @param $isAdmin
     * @return string
     * @throws SmartyException
     */
    protected function outputError(ErrorNotification $error, $isAdmin)
    {
        $viewLoader = new ViewLoader(
            ACRONIS_CLOUD_SERVER_MODULE_DIR . '/views',
            ACRONIS_CLOUD_SERVER_MODULE_DIR . '/assets'
        );

        $errorMessage = $isAdmin
            ? $error->getMessage()
            : $this->gettext('Please contact the support team.');

        return $viewLoader->assign('isAdmin', $isAdmin)
            ->assign('errorTitle', $error->getTitle())
            ->assign('errorMessage', $errorMessage)
            ->fetch('partial_error.tpl');
    }
}