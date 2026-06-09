<?php

namespace App\Models;

use App\Support\PageContent;
use Illuminate\Database\Eloquent\Model;

class SiteConfig extends Model
{
    protected $table = 'site_config';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'company_name',
        'logo_text',
        'hero_title',
        'hero_subtitle',
        'primary_color',
        'logo_url',
        'favicon_url',
        'banner_url',
        'banner_bg_url',
        'page_content',
        'tracking_api_url',
        'calculator_default_mode',
        'calculator_default_weight',
        'calculator_default_length',
        'calculator_default_width',
        'calculator_default_height',
        'calculator_base_fee',
        'calculator_included_kg',
        'calculator_excess_price_per_kg',
        'calculator_express_multiplier',
        'calculator_default_origin',
        'calculator_default_destination',
        'gemini_api_key',
        'gemini_model',
        'gemini_system_instruction',
        'gemini_enabled',
        'assistant_provider',
        'openai_api_key',
        'openai_model',
        'openai_system_instruction',
        'openai_enabled',
    ];

    protected $casts = [
        'page_content' => 'array',
        'gemini_enabled' => 'boolean',
        'openai_enabled' => 'boolean',
    ];

    /**
     * @return array<string, mixed>
     */
    public function resolvedPageContent(): array
    {
        return PageContent::merge($this->page_content);
    }

    /**
     * Get the default site config, creating it if it does not exist.
     */
    public static function default(): self
    {
        return self::firstOrCreate(
            ['id' => 'default'],
            [
                'company_name' => 'Brayan Brush',
                'logo_text' => 'Corporación Logística',
                'hero_title' => 'Brayan Brush.',
                'hero_subtitle' => 'Líder en transporte terrestre nacional en Perú.',
                'primary_color' => '#059669',
                'logo_url' => null,
                'favicon_url' => null,
                'banner_url' => null,
                'banner_bg_url' => null,
                'tracking_api_url' => config('services.system_brayan.tracking_api_url'),
                'calculator_default_mode' => 'weight',
                'calculator_default_weight' => 5,
                'calculator_default_length' => 30,
                'calculator_default_width' => 30,
                'calculator_default_height' => 30,
                'calculator_base_fee' => 25,
                'calculator_included_kg' => 5,
                'calculator_excess_price_per_kg' => 1.4,
                'calculator_express_multiplier' => 1.5,
                'calculator_default_origin' => 'Lima',
                'calculator_default_destination' => 'Arequipa',
                'gemini_api_key' => null,
                'gemini_model' => 'gemini-2.0-flash',
                'gemini_system_instruction' => null,
                'gemini_enabled' => true,
                'assistant_provider' => 'gemini',
                'openai_api_key' => null,
                'openai_model' => 'gpt-4o-mini',
                'openai_system_instruction' => null,
                'openai_enabled' => true,
            ]
        );
    }
}
