<?php
namespace haimaz\BusinessSteper\Supports;

class Helper
{
    public static function normalizeName(string $input): string
    {
        // Replace hyphens with underscores
        $input = str_replace('-', '_', $input);

        // Add underscores before capital letters, except the first one
        $input = preg_replace('/(?<!^)([A-Z])/', '_$1', $input);

        // Convert to lowercase
        return strtolower($input);
    }

    public static function getBusinessTypes(): array
    {
        return config('business_steper.types', ['b2c']);
    }

    public static function getDefaultBusinessType(): string
    {
        // Convert to lowercase
        return config('business_steper.default_type', 'b2c');
    }

    public static function getDefaultStep(): ?string
    {
        // Convert to lowercase
        return config('business_steper.default_step', null);
    }

    public static function build(string $input): string
    {
        // Replace hyphens with underscores
        $input = str_replace('-', '_', $input);

        // Add underscores before capital letters, except the first one
        $input = preg_replace('/(?<!^)([A-Z])/', '_$1', $input);

        // Convert to lowercase
        return strtolower($input);
    }
}