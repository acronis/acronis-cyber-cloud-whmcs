<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model;

use AcronisCloud\Model\WHMCS\Product;
use AcronisCloud\Model\WHMCS\Server;
use AcronisCloud\Util\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters\ProductOptions;

class Template extends AbstractModel
{
    use SoftDeletes;

    const TABLE = 'acroniscloud_service_templates';

    const COLUMN_NAME = 'name';
    const COLUMN_DESCRIPTION = 'description';
    const COLUMN_SERVER_ID = 'server_id';
    const COLUMN_TENANT_KIND = 'tenant_kind';
    const COLUMN_USER_ROLE = 'user_role';

    const USER_ROLE_USER = 'user';
    const USER_ROLE_ADMIN = 'admin';
    const ALLOWED_USER_ROLES = [
        self::USER_ROLE_USER,
        self::USER_ROLE_ADMIN,
    ];

    /** @var string Used to access POST request's payload data and relationship method */
    const RELATION_APPLICATIONS = 'applications';

    /** @var string Used for Eager Loading of Multiple Relationships (https://laravel.com/docs/5.8/eloquent-relationships#eager-loading) */
    const RELATION_APP_OFFERING_ITEMS = self::RELATION_APPLICATIONS . '.' . TemplateApplication::RELATION_OFFERING_ITEMS;

    /**
     * Needed for compile-time declaration, missing in Eloquent
     */
    const DELETED_AT = 'deleted_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        self::COLUMN_NAME,
        self::COLUMN_DESCRIPTION,
        self::COLUMN_SERVER_ID,
        self::COLUMN_TENANT_KIND,
        self::COLUMN_USER_ROLE,
    ];

    /**
     *  Hide unneeded for UI columns
     *
     * @var array
     */
    protected $hidden = [
        self::CREATED_AT,
        self::UPDATED_AT,
        self::DELETED_AT
    ];

    /**
     * Enforce server is an integer to prevent ui issues
     *
     * @var array
     */
    protected $casts = [
        self::COLUMN_SERVER_ID => self::TYPE_INTEGER,
    ];

    /**
     * List of applications that this template has access to
     *
     * @return HasMany
     */
    public function applications()
    {
        return $this->hasMany(TemplateApplication::class);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getAttributeValue(static::COLUMN_NAME);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getAttributeValue(static::COLUMN_DESCRIPTION);
    }

    /**
     * @return int
     */
    public function getServerId()
    {
        return $this->getAttributeValue(static::COLUMN_SERVER_ID);
    }

    /**
     * @return string
     */
    public function getTenantKind()
    {
        return $this->getAttributeValue(static::COLUMN_TENANT_KIND);
    }

    /**
     * @return boolean
     */
    public function isAdministrator()
    {
        return $this->getAttributeValue(static::COLUMN_USER_ROLE) === static::USER_ROLE_ADMIN;
    }

    public function setUserRole($userRole)
    {
        if (!in_array($userRole, static::ALLOWED_USER_ROLES)) {
            throw new \InvalidArgumentException(Str::format(
                'Invalid template user role. Valid values: %s',
                implode(', ', static::ALLOWED_USER_ROLES)
            ));
        }

        $this->setAttribute(static::COLUMN_USER_ROLE, $userRole);
    }

    /**
     * @return HasMany
     */
    public function products()
    {
        $templateIdCol = ProductOptions::getConfigOptionName(ProductOptions::INDEX_TEMPLATE_ID);

        return $this->hasMany(Product::class, $templateIdCol, static::COLUMN_ID)
            ->where(Product::COLUMN_SERVER_TYPE, ACRONIS_CLOUD_SERVICE_NAME);
    }

    /**
     * @return Server
     */
    public function server()
    {
        return $this->hasOne(Server::class, Server::COLUMN_ID, static::COLUMN_SERVER_ID)->getResults();
    }
}
