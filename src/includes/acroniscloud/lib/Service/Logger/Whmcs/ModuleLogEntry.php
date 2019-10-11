<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Logger\Whmcs;

class ModuleLogEntry
{
    /**
     * @var string
     */
    private $action;
    /**
     * @var array|string
     */
    private $request;
    /**
     * @var array|string
     */
    private $response;
    /**
     * @var array|string
     */
    private $processedData;
    /**
     * @var array
     */
    private $replaceVars;

    /**
     * Data used by WHMCS's logModuleCall()
     *
     * @param string $action The name of the action being performed
     * @param string|array $request The input parameters for the API call
     * @param string|array $response The response data from the API call
     * @param string|array $processedData The resulting data after any post processing (eg. json decode, xml decode, etc...)
     * @param array $replaceVars An array of strings for replacement
     */
    public function __construct($action, $request, $response, $processedData = null, $replaceVars = null)
    {
        $this->action = $action;
        $this->request = $request;
        $this->response = $response;
        $this->processedData = $processedData;
        $this->replaceVars = $replaceVars;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return array|string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return array|string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return array|string
     */
    public function getProcessedData()
    {
        return $this->processedData;
    }

    /**
     * @return array
     */
    public function getReplaceVars()
    {
        return $this->replaceVars;
    }
}