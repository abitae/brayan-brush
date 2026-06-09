<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    private function serviceToArray(Service $s): array
    {
        return [
            'id' => (string) $s->id,
            'title' => $s->title,
            'description' => $s->description,
            'icon_type' => $s->icon_type,
            'icon_url' => $s->icon_url,
            'image_url' => $s->image_url,
        ];
    }

    public function index(): JsonResponse
    {
        $services = Service::orderBy('sort_order')->get()->map(fn (Service $s) => $this->serviceToArray($s));

        return response()->json($services);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
        ]);

        $maxOrder = Service::max('sort_order') ?? -1;

        $service = Service::create([
            ...$validated,
            'icon_type' => 'Box',
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json($this->serviceToArray($service), 201);
    }

    public function update(Request $request, Service $service): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:500',
        ]);

        $service->update($validated);

        return response()->json($this->serviceToArray($service));
    }

    public function destroy(Service $service): JsonResponse
    {
        $service->delete();

        return response()->json(['message' => 'Servicio eliminado']);
    }

    public function uploadImage(Request $request, Service $service): JsonResponse
    {
        $url = $this->storeUpload($request, 'service_'.$service->id.'_img');

        $service->update(['image_url' => $url]);

        return response()->json(['url' => $url, 'service' => $this->serviceToArray($service->fresh())]);
    }

    public function uploadIcon(Request $request, Service $service): JsonResponse
    {
        $url = $this->storeUpload($request, 'service_'.$service->id.'_icon', 2048);

        $service->update(['icon_url' => $url]);

        return response()->json(['url' => $url, 'service' => $this->serviceToArray($service->fresh())]);
    }

    private function storeUpload(Request $request, string $prefix, int $maxKb = 10240): string
    {
        $request->validate([
            'file' => 'required|file|mimes:png,jpg,jpeg,webp,svg|max:'.$maxKb,
        ]);

        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        $name = $prefix.'_'.time().'.'.strtolower($ext);
        $path = $file->storeAs('uploads', $name, 'public');

        return asset('storage/'.$path);
    }
}
