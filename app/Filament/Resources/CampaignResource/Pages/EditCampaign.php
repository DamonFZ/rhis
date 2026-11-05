<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
use Filament\Resources\Pages\EditRecord;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;

    /**
     * 编辑时填充 Repeater 数据
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $rules = $this->record->rehabPackages()->get()->map(function ($pkg) {
            return [
                'rehab_package_id' => $pkg->id,
                'extra_count' => $pkg->pivot->extra_count ?? 0,
            ];
        })->toArray();

        $data['package_rules'] = $rules;

        return $data;
    }

    /**
     * 保存后同步套餐赠送规则
     */
    protected function afterSave(): void
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
