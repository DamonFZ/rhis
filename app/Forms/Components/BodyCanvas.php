<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class BodyCanvas extends Field
{
    protected string $view = 'filament.forms.components.body-canvas';

    public static function make(string $name): static
    {
        $static = app(static::class);
        $static->name($name);
        $static->configure();
        return $static;
    }
}
