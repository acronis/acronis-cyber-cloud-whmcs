<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Repository;

use AcronisCloud\CloudApi\ApiInterface;
use AcronisCloud\Model\Template;
use AcronisCloud\Model\TemplateApplication;
use AcronisCloud\Model\TemplateOfferingItem;
use AcronisCloud\Service\MetaInfo\MetaInfoAwareTrait;
use AcronisCloud\Util\Str;

class TemplateRepository extends AbstractRepository
{
    use MetaInfoAwareTrait;

    // request payload and eloquent's db structure do not match
    const REQUEST_MAPPINGS = [
        TemplateApplication::RELATION_OFFERING_ITEMS => 'offering_items'
    ];

    /**
     * @inheritdoc
     */
    public function all()
    {
        return Template::all();
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        // todo: refactor it, we don't need applications and offering items every time
        return Template::with(Template::RELATION_APP_OFFERING_ITEMS)->find($id);
    }

    /**
     * @inheritdoc
     */
    public function update(array $data, $id)
    {
        /** @var Template $model*/
        $model = static::find($id);
        if (!$model) {
            throw new \Exception(Str::format(
                'Cannot update template: No model was found with Id "%s"',
                $id
            ));
        }
        $applications = $this->extractApplications($data);

        $appIdColumn = TemplateApplication::COLUMN_ID;
        $model->update([
            Template::COLUMN_NAME => $data[Template::COLUMN_NAME],
            Template::COLUMN_DESCRIPTION => $data[Template::COLUMN_DESCRIPTION],
        ]);
        $model->applications()->whereNotIn($appIdColumn, array_column($applications, $appIdColumn))->delete();
        foreach ($applications as $application) {
            /** @var TemplateApplication $appModel */
            $appModel = $model->applications()->updateOrCreate([$appIdColumn => $application[$appIdColumn]], $application);
            $offeringItemsProp = static::REQUEST_MAPPINGS[TemplateApplication::RELATION_OFFERING_ITEMS];
            $offeringItems = $application[$offeringItemsProp];
            $oiIdColumn = TemplateOfferingItem::COLUMN_ID;
            $appModel->offeringItems()->delete();
            foreach ($offeringItems  as $offeringItem) {
                $appModel->offeringItems()->updateOrCreate([$oiIdColumn => $offeringItem[$oiIdColumn]], $offeringItem);
            }
        }

        return $model->toArray();
    }

    /**
     * @inheritdoc
     */
    public function create(array $data)
    {
        $template = new Template();
        if (isset($data[$template->getKeyName()])) {
            throw new \Exception(Str::format(
                'Cannot create template with set "%s". Maybe you wanted to update instead?',
                $template->getKeyName()
            ));
        }

        $template->fill($data);
        if ($template->getTenantKind() === ApiInterface::TENANT_KIND_PARTNER) {
            // A partner is always an admin
            $template->setUserRole(Template::USER_ROLE_ADMIN);
        }
        $template->save();

        $applications = $this->extractApplications($data);
        $applicationModels = $template->applications()->createMany($applications);

        /** @var $appModel TemplateApplication */
        foreach ($applicationModels as $appModel) {
            $application = $applications[$appModel->type];
            $offeringItemsProp = static::REQUEST_MAPPINGS[TemplateApplication::RELATION_OFFERING_ITEMS];
            $oiData = $application[$offeringItemsProp];
            $appModel->offeringItems()->createMany($oiData);
        }

        return $template->id;
    }

    /**
     * @inheritdoc
     */
    public function delete($id)
    {
        /** @var Template $model*/
        $model = Template::findOrFail($id);

        return $model->delete();
    }

    /**
     * @param array $data
     * @return array
     */
    public function extractApplications($data)
    {
        $apps = $data[Template::RELATION_APPLICATIONS];
        $oiProp =  TemplateApplication::RELATION_OFFERING_ITEMS;
        $valueProp = TemplateOfferingItem::COLUMN_QUOTA_VALUE;
        $groupedApps = [];
        // clean the missing values to be stored as null, or UI marks input as filled
        foreach ($apps as $appData) {
            $appName = $appData[TemplateApplication::COLUMN_TYPE];
            $groupedApps[$appName] = $appData;
            $offeringItems = $appData[$oiProp];
            foreach ($offeringItems as $offeringItemData) {
                if ($offeringItemData[$valueProp] === '') {
                    $oiName = $offeringItemData[TemplateOfferingItem::COLUMN_NAME];
                    $apps[$appName][$oiProp][$oiName][$valueProp] = null;
                }
            }
        }

        return $groupedApps;
    }

    public static function getOfferingItemHash($offeringItem)
    {
        return Str::format(
            '%s:%s',
            $offeringItem[TemplateOfferingItem::COLUMN_NAME], $offeringItem[TemplateOfferingItem::COLUMN_INFRA_ID]
        );
    }
}