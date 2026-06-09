<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteConfig;
use App\Support\PageContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfigController extends Controller
{
    /**
     * Get site configuration (public).
     */
    public function index(): JsonResponse
    {
        $config = SiteConfig::default();

        $payload = [
            'company_name' => $config->company_name,
            'logo_text' => $config->logo_text,
            'hero_title' => $config->hero_title,
            'hero_subtitle' => $config->hero_subtitle,
            'primary_color' => $config->primary_color,
            'logo_url' => $config->logo_url,
            'banner_url' => $config->banner_url,
            'banner_bg_url' => $config->banner_bg_url,
        ];

        $siteKey = config('services.recaptcha.site_key');
        if (! empty($siteKey)) {
            $payload['recaptcha_site_key'] = $siteKey;
        }

        return response()->json($payload);
    }

    /**
     * Update site configuration (auth required).
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_name' => 'sometimes|required|string|max:255',
            'logo_text' => 'sometimes|required|string|max:255',
            'hero_title' => 'sometimes|required|string|max:255',
            'hero_subtitle' => 'sometimes|required|string|max:500',
            'primary_color' => 'sometimes|required|string|max:20',
            'logo_url' => 'nullable|string',
            'favicon_url' => 'nullable|string',
            'banner_url' => 'nullable|string',
            'banner_bg_url' => 'nullable|string',
            'tracking_api_url' => 'nullable|string|max:500',
            'calculator_default_mode' => 'nullable|string|in:weight,dimensions',
            'calculator_default_weight' => 'nullable|integer|min:1|max:5000',
            'calculator_default_length' => 'nullable|integer|min:1|max:500',
            'calculator_default_width' => 'nullable|integer|min:1|max:500',
            'calculator_default_height' => 'nullable|integer|min:1|max:500',
            'calculator_base_fee' => 'nullable|numeric|min:0',
            'calculator_included_kg' => 'nullable|integer|min:0|max:5000',
            'calculator_excess_price_per_kg' => 'nullable|numeric|min:0',
            'calculator_express_multiplier' => 'nullable|numeric|min:1|max:10',
            'calculator_default_origin' => 'nullable|string|max:100',
            'calculator_default_destination' => 'nullable|string|max:100',
            'gemini_api_key' => 'nullable|string|max:500',
            'gemini_model' => 'nullable|string|max:64',
            'gemini_system_instruction' => 'nullable|string|max:8000',
            'gemini_enabled' => 'nullable|boolean',
            'assistant_provider' => 'nullable|string|in:gemini,chatgpt',
            'openai_api_key' => 'nullable|string|max:500',
            'openai_model' => 'nullable|string|max:64',
            'openai_system_instruction' => 'nullable|string|max:8000',
            'openai_enabled' => 'nullable|boolean',
            'page_content' => 'nullable|array',
        ]);

        $config = SiteConfig::default();

        if (array_key_exists('gemini_api_key', $validated)) {
            $validated['gemini_api_key'] = $validated['gemini_api_key'] ?: null;
        }
        if (array_key_exists('gemini_enabled', $validated)) {
            $validated['gemini_enabled'] = (bool) $validated['gemini_enabled'];
        }
        if (array_key_exists('assistant_provider', $validated)) {
            $validated['assistant_provider'] = $validated['assistant_provider'] === 'chatgpt' ? 'chatgpt' : 'gemini';
        }
        if (array_key_exists('openai_api_key', $validated)) {
            $validated['openai_api_key'] = $validated['openai_api_key'] ?: null;
        }
        if (array_key_exists('openai_enabled', $validated)) {
            $validated['openai_enabled'] = (bool) $validated['openai_enabled'];
        }

        if (isset($validated['page_content'])) {
            $validated['page_content'] = PageContent::sanitizeForSave($validated['page_content']);
        }

        $config->update($validated);

        return response()->json(['message' => 'Configuración actualizada']);
    }

    /**
     * Upload logo and return public URL (auth required).
     */
    public function uploadLogo(Request $request): JsonResponse
    {
        $url = $this->uploadFile($request, 'logo');

        SiteConfig::default()->update(['logo_url' => $url]);

        return response()->json(['url' => $url]);
    }

    /**
     * Upload favicon and return public URL (auth required).
     */
    public function uploadFavicon(Request $request): JsonResponse
    {
        $url = $this->uploadFile($request, 'favicon', ['ico', 'png', 'jpg', 'jpeg', 'webp', 'svg'], 2048);

        SiteConfig::default()->update(['favicon_url' => $url]);

        return response()->json(['url' => $url]);
    }

    /**
     * Upload banner and return public URL (auth required).
     */
    public function uploadBanner(Request $request): JsonResponse
    {
        $url = $this->uploadFile($request, 'banner');

        SiteConfig::default()->update(['banner_url' => $url]);

        return response()->json(['url' => $url]);
    }

    /**
     * Upload banner background and return public URL (auth required).
     */
    public function uploadBannerBg(Request $request): JsonResponse
    {
        $url = $this->uploadFile($request, 'banner_bg');

        SiteConfig::default()->update(['banner_bg_url' => $url]);

        return response()->json(['url' => $url]);
    }

    /**
     * Upload "nosotros" section image and return public URL (auth required).
     */
    public function uploadAboutImage(Request $request): JsonResponse
    {
        $url = $this->uploadFile($request, 'about');

        $config = SiteConfig::default();
        $content = $config->resolvedPageContent();
        $content['about']['image_url'] = $url;
        $config->update(['page_content' => PageContent::sanitizeForSave($content)]);

        return response()->json(['url' => $url]);
    }

    /**
     * @param  list<string>  $mimes
     */
    private function uploadFile(Request $request, string $prefix, array $mimes = ['png', 'jpg', 'jpeg', 'webp'], int $maxKb = 10240): string
    {
        $request->validate([
            'file' => 'required|file|mimes:'.implode(',', $mimes).'|max:'.$maxKb,
        ]);

        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        $name = $prefix.'_'.time().'.'.strtolower($ext);
        $path = $file->storeAs('uploads', $name, 'public');

        return asset('storage/'.$path);
    }
}
