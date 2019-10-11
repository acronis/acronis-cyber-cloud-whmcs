<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace WHMCS\Module\Addon\AcronisCloud\Controller;

use AcronisCloud\CloudApi\CloudApiTrait;
use AcronisCloud\CloudApi\CloudServerInterface;
use AcronisCloud\Model\Template;
use AcronisCloud\Repository\Validation\Template\TemplateValidator;
use AcronisCloud\Service\Database\Repository\RepositoryAwareTrait;
use AcronisCloud\Service\Dispatcher\AbstractController;
use AcronisCloud\Service\Dispatcher\RequestException;
use AcronisCloud\Service\Dispatcher\Response\HttpResponse;
use AcronisCloud\Service\MetaInfo\MetaInfoAwareTrait;
use AcronisCloud\Util\MemoizeTrait;

class TemplateHandler extends AbstractController
{
    use CloudApiTrait,
        RepositoryAwareTrait,
        MemoizeTrait,
        MetaInfoAwareTrait;

    const PARAM_SERVER_ID = 'server_id';
    const PARAM_TENANT_KIND = 'tenant_kind';
    const PARAM_EDITION = 'edition';

    /** @var \AcronisCloud\Repository\TemplateRepository */
    protected $repository;

    /** @var int */
    protected $serverId;

    public function __construct()
    {
        $this->repository = $this->getRepository()->getTemplateRepository();
    }

    /**
     * @param $data
     * @return array
     * @throws RequestException
     * @throws \AcronisCloud\CloudApi\CloudServerException
     */
    protected function validateData($data)
    {
        $this->setServerId($data[Template::COLUMN_SERVER_ID]);
        $applications = $this->getCloudApi()->getRootTenantApplications();
        $offeringItems = $this->getCloudApi()->getRootTenantOfferingItems();
        $infras = $this->getCloudApi()->getRootTenantInfras();
        $validator = new TemplateValidator($data, $applications, $offeringItems, $infras);
        if (!$validator->passes()) {
            throw new RequestException(
                'Invalid template data.',
                ['errors' => $validator->errors()->messages()],
                HttpResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $validator->getData();
    }

    protected function setServerId($serverId)
    {
        $this->serverId = (int)$serverId;
    }

    /**
     * @return CloudServerInterface
     */
    protected function getCloudServer()
    {
        return $this->getRepository()
            ->getAcronisServerRepository()
            ->find($this->serverId);
    }
}