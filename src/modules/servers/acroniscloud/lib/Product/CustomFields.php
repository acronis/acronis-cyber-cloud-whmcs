<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Product;

use AcronisCloud\Localization\GetTextTrait;
use AcronisCloud\Service\Database\Repository\RepositoryAwareTrait;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Service\MetaInfo\MetaInfoAwareTrait;
use AcronisCloud\Util\Str;
use Exception;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;
use WHMCS\Module\Server\AcronisCloud\Exception\ProvisioningException;

class CustomFields
{
    use GetTextTrait,
        LoggerAwareTrait,
        MetaInfoAwareTrait,
        RepositoryAwareTrait;

    const FIELD_NAME_TENANT_ID = 'tenant_id';
    const FIELD_NAME_USER_ID = 'user_id';

    // Client area
    const FIELD_NAME_CLOUD_LOGIN = 'cloud_login';
    const FIELD_NAME_CLOUD_PASSWORD = 'cloud_password';

    /** @var int */
    private $productId;

    /** @var int */
    private $serviceId;

    /**
     * CustomFields constructor.
     *
     * @param int $productId
     * @param int $serviceId
     */
    public function __construct($productId, $serviceId)
    {
        $this->productId = $productId;
        $this->serviceId = $serviceId;
    }

    /**
     * @param string $fieldName
     * @return Model
     */
    public function createField($fieldName)
    {
        return $this->getOrCreateProductField($fieldName);
    }

    /**
     * @param $fieldName
     */
    public function removeField($fieldName)
    {
        try {
            $field = $this->getProductField($fieldName);
            if ($field) {
                $field->delete();
            }
        } catch (Exception $e) {
            // if deletion fails, admin can delete them manually. No reason to stop the saving
            $this->getLogger()->error(Str::format(
                'Error while deleting custom field "%s": %s',
                $fieldName, $e->getMessage()
            ));
            $this->getLogger()->debug($e->getTraceAsString());
        }
    }

    /**
     * @param string $tenantId
     * @return $this
     * @throws Exception
     */
    public function setTenantId($tenantId)
    {
        $this->setFieldValue(static::FIELD_NAME_TENANT_ID, $tenantId);

        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getTenantId()
    {
        return $this->getFieldValue(static::FIELD_NAME_TENANT_ID);
    }

    /**
     * @return $this
     */
    public function resetTenantId()
    {
        $this->resetFieldValue(static::FIELD_NAME_TENANT_ID);

        return $this;
    }

    /**
     * @param string $userId
     * @return $this
     * @throws Exception
     */
    public function setUserId($userId)
    {
        $this->setFieldValue(static::FIELD_NAME_USER_ID, $userId);

        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getUserId()
    {
        return $this->getFieldValue(static::FIELD_NAME_USER_ID);
    }

    /**
     * @return $this
     */
    public function resetUserId()
    {
        $this->resetFieldValue(static::FIELD_NAME_USER_ID);

        return $this;
    }

    /**
     * @param string $login
     * @return $this
     * @throws Exception
     */
    public function setCloudLogin($login)
    {
        $this->setFieldValue(static::FIELD_NAME_CLOUD_LOGIN, $login);

        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getCloudLogin()
    {
        return $this->getFieldValue(static::FIELD_NAME_CLOUD_LOGIN);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getCloudPassword()
    {
        return $this->getFieldValue(static::FIELD_NAME_CLOUD_PASSWORD);
    }

    /**
     * @return $this
     */
    public function resetCloudPassword()
    {
        $this->resetFieldValue(static::FIELD_NAME_CLOUD_PASSWORD);

        return $this;
    }

    /**
     * @param string $fieldName
     * @param mixed $value
     * @throws Exception
     */
    private function setFieldValue($fieldName, $value)
    {
        $field = $this->getOrCreateProductField($fieldName);
        $this->validateValue($field, $value);
        $this->setServiceFieldValue($field, $value);
    }

    /**
     * @param string $fieldName
     * @return mixed
     * @throws Exception
     */
    private function getFieldValue($fieldName)
    {
        $field = $this->getProductField($fieldName);
        if (!$field) {
            return '';
        }

        $value = $this->getServiceFieldValue($field);
        $this->validateValue($field, $value);

        return $value;
    }

    /**
     * @param string $fieldName
     */
    private function resetFieldValue($fieldName)
    {
        $field = $this->getOrCreateProductField($fieldName);
        $this->resetServiceFieldValue($field);
    }

    /**
     * @param string $fieldName
     * @return Model
     */
    private function getOrCreateProductField($fieldName)
    {
        $field = $this->getProductField($fieldName);
        if ($field) {
            return $field;
        }

        $fieldRepository = $this->getRepository()->getCustomFieldsRepository();
        $fieldMeta = $this->getMetaInfo()->getCustomFieldMeta($fieldName);

        return $fieldRepository->createProductField($this->productId, $fieldMeta);
    }

    /**
     * @param string $fieldName
     * @return Model
     */
    private function getProductField($fieldName)
    {
        $fieldRepository = $this->getRepository()->getCustomFieldsRepository();

        return $fieldRepository->getProductField($this->productId, $fieldName);
    }

    /**
     * @param Model $field
     * @param string $value
     * @throws Exception
     */
    private function validateValue($field, $value)
    {
        $pattern = $field->regexpr;
        if (!$pattern) {
            return;
        }

        if (!preg_match($pattern, $value)) {
            throw new ProvisioningException(
                $this->gettext('Custom field "{0}" has incorrect value "{1}".',
                    [$field->fieldname, $value,]
                )
            );
        }
    }

    private function setServiceFieldValue(Model $field, $value)
    {
        $result = $this->getRepository()
            ->getCustomFieldsValuesRepository()
            ->saveCustomFieldServiceValue($field->id, $this->serviceId, $value);

        if (!$result) {
            throw new RuntimeException(Str::format(
                'Unable to save the value "%s" for field %s for service %s.',
                $value, $field->id, $this->serviceId
            ));
        }
    }

    private function getServiceFieldValue(Model $field)
    {
        $valueField = $this->getRepository()
            ->getCustomFieldsValuesRepository()
            ->getCustomFieldServiceValue($field->id, $this->serviceId);

        return $valueField ? $valueField->value : '';
    }

    private function resetServiceFieldValue(Model $field)
    {
        $result = $this->getRepository()
            ->getCustomFieldsValuesRepository()
            ->deleteCustomFieldServiceValue($field->id, $this->serviceId);

        if (!$result) {
            throw new RuntimeException(Str::format(
                'Unable to delete the value for field %s for service %s.',
                $field->id, $this->serviceId
            ));
        }
    }
}