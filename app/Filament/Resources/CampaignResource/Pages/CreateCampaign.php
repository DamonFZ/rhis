<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCampaign extends CreateRecord
{
    protected static string $resource = CampaignResource::class;

    /**
     * 保存活动后同步套餐赠送规则到中间表
     */
    protected function afterCreate(): void
    {
        $this->syncPackageRules();
    }

    protected function syncPackageRules(): void
    {
        $rules = $this->data['package_rules'] ?? [];

        $syncData = [];
        foreach ($rules as $rule) {
            if (empty($rule['rehab_package_id'])) {
                continue;
            }
            $syncData[$rule['rehab_package_id']] = [
                'extra_count' => (int) ($rule['extra_count'] ?? 0),
            ];
        }

        $this->record->rehabPackages()->sync($syncData);
    }
}
