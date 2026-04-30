<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
        <x-filament-panels::form.actions>
            <x-filament::button type="submit">保存设置</x-filament::button>
        </x-filament-panels::form.actions>
    </x-filament-panels::form>
</x-filament-panels::page>
