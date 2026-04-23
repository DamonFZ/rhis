<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ImagingRecord;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class CompareImaging extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    
    protected static string $view = 'filament.pages.compare-imaging';
    
    protected static ?string $title = '康复影像对比';
    
    protected static bool $shouldRegisterNavigation = false;
    
    public $records = [];
    
    public bool $hasError = false;
    
    public string $errorMessage = '';
    
    public $photoLabels = [
        'front' => '站姿正面',
        'back' => '站姿背面',
        'left_side' => '站姿侧面',
        'right_side' => '侧面弯腰',
        'forward_bending' => '站姿弯腰正面',
        'back_sitting' => '坐姿背面',
    ];
    
    public function mount(): void
    {
        $idsParam = Request::query('ids');
        
        if (!$idsParam) {
            $this->hasError = true;
            $this->errorMessage = '缺少必要的记录ID参数';
            return;
        }
        
        $idsArray = explode(',', $idsParam);
        $idsArray = array_filter(array_map('intval', $idsArray));
        
        if (empty($idsArray) || count($idsArray) < 2 || count($idsArray) > 5) {
            $this->hasError = true;
            $this->errorMessage = '请选择 2 到 5 条记录进行对比';
            return;
        }
        
        $this->records = ImagingRecord::whereIn('id', $idsArray)
            ->orderBy('treatment_date', 'asc')
            ->get();
        
        if ($this->records->isEmpty()) {
            $this->hasError = true;
            $this->errorMessage = '找不到指定的记录';
            return;
        }
        
        $patientIds = $this->records->pluck('patient_profile_id')->unique();
        if ($patientIds->count() > 1) {
            $this->hasError = true;
            $this->errorMessage = '只能对比同一客户的记录';
            return;
        }
    }
    
    public function getPhotoUrl($record, $photoKey): ?string
    {
        $photoUrls = $record->photo_urls ?? [];
        if (!is_array($photoUrls)) {
            $photoUrls = json_decode($photoUrls, true) ?? [];
        }
        
        if (!isset($photoUrls[$photoKey])) {
            return null;
        }
        
        $path = $photoUrls[$photoKey];
        
        // 如果是完整 URL，直接返回
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        
        return Storage::disk('public')->url($path);
    }
    
    public function getVideoUrl($record): ?string
    {
        $videoUrl = $record->video_url;
        if (!$videoUrl) {
            return null;
        }
        
        // 如果是完整 URL，直接返回
        if (filter_var($videoUrl, FILTER_VALIDATE_URL)) {
            return $videoUrl;
        }
        
        return Storage::disk('public')->url($videoUrl);
    }
    
    public function getRecordTypeLabel($record): string
    {
        return match ($record->record_type) {
            1 => '康复前',
            2 => '康复后',
            3 => '康复中',
            default => $record->record_type,
        };
    }
}
