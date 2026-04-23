<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\PhysicalAssessment;
use Illuminate\Support\Facades\Request;

class CompareAssessments extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static string $view = 'filament.pages.compare-assessments';
    
    protected static ?string $title = '康复成效对比';
    
    protected static bool $shouldRegisterNavigation = false;
    
    public $records = [];
    public $firstRecord = null;
    public $lastRecord = null;
    public $differences = [];
    public bool $hasError = false;
    public string $errorMessage = '';
    
    public function mount(): void
    {
        $idsParam = Request::query('ids');
        
        if (!$idsParam) {
            $this->hasError = true;
            $this->errorMessage = '缺少必要的评估记录ID参数';
            return;
        }
        
        $idsArray = explode(',', $idsParam);
        $idsArray = array_filter(array_map('intval', $idsArray));
        
        if (empty($idsArray) || count($idsArray) < 2) {
            $this->hasError = true;
            $this->errorMessage = '至少需要2条记录进行对比';
            return;
        }
        
        if (count($idsArray) > 5) {
            $this->hasError = true;
            $this->errorMessage = '最多仅支持5条记录对比';
            return;
        }
        
        $this->records = PhysicalAssessment::whereIn('id', $idsArray)
            ->orderBy('assessment_date', 'asc')
            ->get();
        
        if ($this->records->isEmpty()) {
            $this->hasError = true;
            $this->errorMessage = '找不到指定的评估记录';
            return;
        }
        
        $patientIds = $this->records->pluck('patient_profile_id')->unique();
        if ($patientIds->count() > 1) {
            $this->hasError = true;
            $this->errorMessage = '只能对比同一客户的评估记录';
            return;
        }
        
        $this->firstRecord = $this->records->first();
        $this->lastRecord = $this->records->last();
        
        $this->differences = $this->calculateDifferences();
    }
    
    protected function calculateDifferences(): array
    {
        $first = $this->firstRecord;
        $last = $this->lastRecord;
        
        $differences = [
            'basic' => [],
            'circumference' => [],
            'flexibility' => [],
            'posture_side' => [],
            'posture_back' => [],
        ];
        
        $basicFields = ['height', 'weight', 'bmi', 'body_fat_rate'];
        foreach ($basicFields as $field) {
            $firstVal = $first->$field ?? 0;
            $lastVal = $last->$field ?? 0;
            $differences['basic'][$field] = [
                'first' => $firstVal,
                'last' => $lastVal,
                'delta' => $lastVal - $firstVal,
                'type' => 'numeric',
            ];
        }
        
        $circumferenceFields = ['chest', 'waist', 'hip', 'left_arm', 'right_arm', 'left_thigh', 'right_thigh'];
        foreach ($circumferenceFields as $field) {
            $firstVal = $first->circumference[$field] ?? 0;
            $lastVal = $last->circumference[$field] ?? 0;
            $differences['circumference'][$field] = [
                'first' => $firstVal,
                'last' => $lastVal,
                'delta' => $lastVal - $firstVal,
                'type' => 'numeric',
            ];
        }
        
        $flexibilityFields = ['trunk', 'hamstrings', 'iliopsoas', 'quadriceps', 'calf', 'shoulder_1', 'shoulder_2'];
        foreach ($flexibilityFields as $field) {
            $firstVal = $first->flexibility[$field] ?? null;
            $lastVal = $last->flexibility[$field] ?? null;
            $differences['flexibility'][$field] = [
                'first' => $firstVal,
                'last' => $lastVal,
                'changed' => $firstVal !== $lastVal,
                'type' => 'qualitative',
            ];
        }
        
        $postureSideFields = ['side_head', 'side_cervical', 'side_scapula', 'side_thoracic', 'side_lumbar', 'side_pelvis', 'side_knee'];
        foreach ($postureSideFields as $field) {
            $firstVal = $first->posture_tags[$field] ?? [];
            $lastVal = $last->posture_tags[$field] ?? [];
            sort($firstVal);
            sort($lastVal);
            $differences['posture_side'][$field] = [
                'first' => $firstVal,
                'last' => $lastVal,
                'changed' => $firstVal !== $lastVal,
                'type' => 'qualitative',
            ];
        }
        
        $postureBackFields = ['back_cervical', 'back_shoulder', 'back_scapula', 'back_thoracolumbar', 'back_pelvis', 'back_knee', 'back_foot'];
        foreach ($postureBackFields as $field) {
            $firstVal = $first->posture_tags[$field] ?? [];
            $lastVal = $last->posture_tags[$field] ?? [];
            sort($firstVal);
            sort($lastVal);
            $differences['posture_back'][$field] = [
                'first' => $firstVal,
                'last' => $lastVal,
                'changed' => $firstVal !== $lastVal,
                'type' => 'qualitative',
            ];
        }
        
        return $differences;
    }
}
