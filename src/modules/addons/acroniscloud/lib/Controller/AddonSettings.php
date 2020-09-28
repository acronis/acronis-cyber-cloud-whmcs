<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Addon\AcronisCloud\Controller;

use AcronisCloud\Localization\GetTextTrait;
use AcronisCloud\Model\Report;
use AcronisCloud\Model\ReportStorage;
use AcronisCloud\Model\Template;
use AcronisCloud\Model\TemplateApplication;
use AcronisCloud\Model\TemplateOfferingItem;
use AcronisCloud\ModuleMigration\Manager;
use AcronisCloud\Service\BuildInfo\BuildInfoAwareTrait;
use AcronisCloud\Service\Config\AddonConfigAccessor;
use AcronisCloud\Service\Config\AddonConfigAwareTrait;
use AcronisCloud\Service\Dispatcher\AbstractController;
use AcronisCloud\Service\Dispatcher\ActionInterface;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Dispatcher\Response\DataResponse;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Service\Logger\LogScope;
use Exception;
use WHMCS\Database\Capsule;

class AddonSettings extends AbstractController
{
    use AddonConfigAwareTrait,
        GetTextTrait,
        BuildInfoAwareTrait,
        LoggerAwareTrait;

    const REQUIRED_DB_RIGHTS = ['SELECT', 'INSERT', 'UPDATE', 'DELETE', 'CREATE'];

    /**
     * @inheritdoc
     */
    public function handleException(
        Exception $e,
        ActionInterface $action,
        RequestInterface $request
    ) {
        return [
            'status' => DataResponse::ERROR,
            'description' => $e->getMessage(),
        ];
    }

    /**
     * @param RequestInterface $request
     * @return array
     */
    public function getConfig($request)
    {
        $config = [
            'name' => ACRONIS_CLOUD_FRIENDLY_NAME,
            'description' => ACRONIS_CLOUD_FRIENDLY_NAME,
            'version' => $this->getBuildInfo()->getPackageVersion(),
            'author' => '<a href="https://www.acronis.com/" target="_blank">Acronis</a>',
            'language' => 'english',
            'fields' => [
                AddonConfigAccessor::SETTING_LOGGING_CLOUD_API=> [
                    'FriendlyName' => $this->gettext('Module Log Settings'),
                    'Type' => 'yesno',
                    'Description' => $this->gettext('API calls to {0}', [ACRONIS_CLOUD_FRIENDLY_NAME]),
                ],
                AddonConfigAccessor::SETTING_LOGGING_WHMCS_API => [
                    'Type' => 'yesno',
                    'Description' => $this->gettext('API calls to WHMCS platform'),
                ],
                AddonConfigAccessor::SETTING_LOGGING_DB_QUERY=> [
                    'Type' => 'yesno',
                    'Description' => $this->gettext('Queries to WHMCS database'),
                ],
            ],
        ];

        return $config;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function activate()
    {
        $this->checkRights();

        $this->createUsageReportsDBTables();

        if (!Capsule::schema()->hasTable(Template::TABLE)) {
            Capsule::schema()->create(Template::TABLE, function ($table) {
                $table->increments(Template::COLUMN_ID);
                $table->string(Template::COLUMN_NAME);
                $table->string(Template::COLUMN_DESCRIPTION, 1024);
                $table->unsignedInteger(Template::COLUMN_SERVER_ID);
                $table->enum(Template::COLUMN_TENANT_KIND, ['partner', 'customer']);
                $table->enum(Template::COLUMN_USER_ROLE, Template::ALLOWED_USER_ROLES);
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Capsule::schema()->hasTable(TemplateApplication::TABLE)) {
            Capsule::schema()->create(TemplateApplication::TABLE, function ($table) {
                $table->increments(TemplateApplication::COLUMN_ID);
                $table->string(TemplateApplication::COLUMN_TYPE);
                $table->string(TemplateApplication::COLUMN_EDITIONS, TemplateApplication::COLUMN_EDITIONS_SIZE);
                $table->enum(TemplateApplication::COLUMN_STATUS, ['active', 'inactive']);
                $table->unsignedInteger(TemplateApplication::COLUMN_TEMPLATE_ID);
                $table->unique([TemplateApplication::COLUMN_TEMPLATE_ID, TemplateApplication::COLUMN_TYPE],
                    TemplateApplication::UNIQUE_TEMPLATE_TYPE);

                $table->foreign(TemplateApplication::COLUMN_TEMPLATE_ID)
                    ->references(Template::COLUMN_ID)
                    ->on(Template::TABLE)
                    ->onDelete('cascade');
            });
        }

        if (!Capsule::schema()->hasTable(TemplateOfferingItem::TABLE)) {
            Capsule::schema()->create(TemplateOfferingItem::TABLE, function ($table) {
                $table->increments(TemplateOfferingItem::COLUMN_ID);
                $table->string(TemplateOfferingItem::COLUMN_NAME);
                $table->string(TemplateOfferingItem::COLUMN_INFRA_ID, 36)->nullable();
                $table->enum(TemplateOfferingItem::COLUMN_STATUS, ['active', 'inactive']);
                $table->string(TemplateOfferingItem::COLUMN_MEASUREMENT_UNIT);
                $table->unsignedBigInteger(TemplateOfferingItem::COLUMN_QUOTA_VALUE)->nullable();
                $table->unsignedInteger(TemplateOfferingItem::COLUMN_APPLICATION_ID);

                $table->foreign(
                    TemplateOfferingItem::COLUMN_APPLICATION_ID,
                    TemplateOfferingItem::FOREIGN_KEY_CONSTRAIN
                )
                    ->references(TemplateApplication::COLUMN_ID)
                    ->on(TemplateApplication::TABLE)
                    ->onDelete('cascade');
            });
        }

        $moduleMigrationManager = new Manager();

        try {
            $migrationWarnings = $moduleMigrationManager->migrate();
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->error($e->getMessage());
            $logger->debug($e->getTraceAsString());

            return [
                'status' => DataResponse::ERROR,
                'description' =>
                    $this->gettext('Cannot upgrade products and services.') . ' ' .
                    $this->gettext('Error: {0}', [$e->getMessage()])
                ,
            ];
        }

        if (is_array($migrationWarnings)) {
            if (empty($migrationWarnings)) {
                return [
                    'status' => DataResponse::INFO,
                    'description' => $this->gettext('Module was successfully upgraded.'),
                ];
            }

            $number = 1;
            $messages = [];
            foreach ($migrationWarnings as $message) {
                $messages[] = '[' . $number . '] ' . $message;
                $number++;
            }

            return [
                'status' => DataResponse::INFO,
                'description' =>
                    $this->gettext('Module was successfully upgraded with warnings.') . ' ' .
                    $this->gettext('Warnings: {0}', [implode('  ', $messages)]),
            ];
        }

        return [
            'status' => DataResponse::SUCCESS,
            'description' => $this->gettext('{0} addon activated successfully.', [ACRONIS_CLOUD_FRIENDLY_NAME]),
        ];
    }


    /**
     * @throws Exception
     */
    public function upgrade($request)
    {
        $version = $request->getParameters()['version'];

        if (version_compare($version, '2.0.0-130', '<')) {
            $logger = $this->getLogger();
            $pdo = Capsule::connection()->getPdo();
            $pdo->beginTransaction();
            try {
                $query = strtr('ALTER TABLE {table} MODIFY {column} varchar({size}) NOT NULL;', [
                    '{table}' => TemplateApplication::TABLE,
                    '{column}' => TemplateApplication::COLUMN_EDITIONS,
                    '{size}' => TemplateApplication::COLUMN_EDITIONS_SIZE,
                ]);
                $pdo->exec($query);
                $pdo->commit();
                $logger->info('Table "{0}" migrated.', [TemplateApplication::TABLE]);
            } catch (\Exception $e) {
                $pdo->rollBack();
                $logger = $this->getLogger();
                $logger->error($e->getMessage());
                $logger->debug($e->getTraceAsString());
            }
        }

        if (version_compare($version, '2.2.0-145', '<')) {
            $this->createUsageReportsDBTables();
        }
    }

    /**
     * @return array
     */
    public function deactivate()
    {
        return [
            'status' => DataResponse::SUCCESS,
            'description' => $this->gettext('{0} addon deactivated successfully.', [ACRONIS_CLOUD_FRIENDLY_NAME]),
        ];
    }

    private function checkRights()
    {
        $dbUser = $this->getCurrentDbUser();
        $userDBRights = $this->getUserDBRights($dbUser);
        $userGlobalRights = $this->getUserGlobalRights($dbUser);
        $allUserRights = array_merge($userDBRights, $userGlobalRights);
        $missingRights = array_diff(static::REQUIRED_DB_RIGHTS, $allUserRights);
        if (!in_array('ALL PRIVILEGES', $allUserRights) && $missingRights) {
            throw new \Exception($this->gettext(
                'Current database user({0}) has missing rights: {1}',
                [$dbUser, implode(', ', $missingRights)]
            ));
        }
    }

    private function getCurrentDbUser()
    {
        return Capsule::connection()->select('SELECT CURRENT_USER;')[0]->CURRENT_USER;
    }

    private function getUserGlobalRights($dbUser)
    {
        $quotedDbUser = addslashes($this->quoteDBUser($dbUser));
        $userGrants = Capsule::connection()
            ->select("SELECT PRIVILEGE_TYPE FROM information_schema.user_privileges WHERE GRANTEE='{$quotedDbUser}';");

        return array_map(function ($grant) {
            return $grant->PRIVILEGE_TYPE;
        }, $userGrants);
    }

    private function getUserDBRights($dbUser)
    {
        $database = Capsule::connection()->getConfig('database');
        $user = $this->quoteDBUser($dbUser);
        $grantsExctract = "/GRANT (.*) ON ([`']{$database}[`']|\*)\.\* TO .*/";
        $grants = Capsule::connection()->select("SHOW GRANTS FOR {$user}");
        $grantedRights = [];
        foreach ($grants as $idx => $grant) {
            // MariaDB escapes some characters with addslashes
            $grant = stripslashes(current($grant));
            if (preg_match($grantsExctract, $grant, $foundGrants)) {
                $rights = explode(', ', $foundGrants[1]);
                $grantedRights = array_merge($grantedRights, $rights);
            }
        }

        return $grantedRights;
    }

    private function quoteDBUser($user)
    {
        $userParts = explode('@', $user);
        array_walk($userParts, function (&$part) {
            $part = "'{$part}'";
        });

        return implode('@', $userParts);
    }

    private function createUsageReportsDBTables()
    {
        if (!Capsule::schema()->hasTable(Report::TABLE)) {
            Capsule::schema()->create(Report::TABLE, function ($table) {
                $table->increments(Report::COLUMN_ID);
                $table->date(Report::COLUMN_DATE);
                $table->unsignedInteger(Report::COLUMN_DATACENTER_ID);
                $table->unsignedInteger(Report::COLUMN_STATUS);
                $table->string(Report::COLUMN_REPORT_ID, 36)->nullable();
                $table->string(Report::COLUMN_STORED_REPORT_ID, 36)->nullable();
                $table->string(Report::COLUMN_FILE_PATH, 255)->nullable();
                $table->unsignedInteger(Report::COLUMN_INSTANCE_ID)->nullable();
                $table->timestamps();
            });
        }

        if (!Capsule::schema()->hasTable(ReportStorage::TABLE)) {
            Capsule::schema()->create(ReportStorage::TABLE, function ($table) {
                $table->increments(ReportStorage::COLUMN_ID);
                $table->string(ReportStorage::COLUMN_KEY, 100)->unique();
                $table->longText(ReportStorage::COLUMN_VALUE);
                $table->timestamps();
            });
        }
    }
}
