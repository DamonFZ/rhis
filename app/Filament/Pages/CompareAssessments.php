<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\PhysicalAssessment;
use Illuminate\Support\Facades\Redirect;

class CompareAssessments extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static string $view = 'filament.pages.compare-assessments';
    
    protected static ?string $title = '康复成效对比';
    
    protected static bool $shouldRegisterNavigation = false;
    
    public ?PhysicalAssessment $baseAssessment = null;
    public ?PhysicalAssessment $targetAssessment = null;
    public ?array $differences = null;
    
    public function mount(?int $base_id = null, ?int $target_id = null): void
    {
        if (!$base_id || !$target_id) {
            Redirect::route('filament.admin.pages.patient-profiles.list')->send();
            return;
        }
        
        $this->baseAssessment = PhysicalAssessment::find($base_id);
        $this->targetAssessment = PhysicalAssessment::find($target_id);
        
        if (!$this->baseAssessment || !$this->targetAssessment) {
            Redirect::route('filament.admin.pages.patient-profiles.list')->send();
            return;
        }
        
        if ($this->baseAssessment->patient_profile_id !== $this->targetAssessment->patient_profile_id) {
            Redirect::route('filament.admin.pages.patient-profiles.list')->send();
            return;
        }
        
        $this->differences = $this->calculateDifferences();
    }
    
    protected function calculateDifferences(): array
    {
        $base = $this->baseAssessment;
        $target = $this->targetAssessment;
        
        $differences = [
            'basic' => [],
            'circumference' => [],
            'flexibility' => [],
            'posture' => []
        ];
        
        $basicFields = ['height', 'weight', 'bmi', 'body_fat_rate'];
        foreach ($basicFields as $field) {
            $baseVal = $base->$field ?? 0;
            $targetVal = $target->$field ?? 0;
            $differences['basic'][$field] = [
                'base' => $baseVal,
                'target' => $targetVal,
                'delta' => $targetVal - $baseVal
            ];
        }
        
        $circumferenceFields = ['chest', 'waist', 'hip', 'left_arm', 'right_arm', 'left_thigh', 'right_thigh'];
        foreach ($circumferenceFields as $field) {
            $baseVal = $base->circumference[$field] ?? 0;
            $targetVal = $target->circumference[$field] ?? 0;
            $differences['circumference'][$field] = [
                'base' => $baseVal,
                'target' => $targetVal,
                'delta' => $targetVal - $baseVal
            ];
        }
        
        $flexibilityFields = ['trunk', 'hamstrings', 'iliopsoas', 'quadriceps', 'calf', 'shoulder_1', 'shoulder_2'];
        foreach ($flexibilityFields as $field) {
            $baseVal = $base->flexibility[$field] ?? null;
            $targetVal = $target->flexibility[$field] ?? null;
            $differences['flexibility'][$field] = [
                'base' => $baseVal,
                'target' => $targetVal,
                'changed' => $baseVal !== $targetVal
            ];
        }
        
        return $differences;
    }
}
