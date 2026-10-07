<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit" size="md">
            Save
        </x-filament::button>
    </form>
</x-filament-panels::page>
