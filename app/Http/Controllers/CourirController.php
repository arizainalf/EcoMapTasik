<?php
namespace App\Http\Controllers;

use App\Services\CourirService;
use App\Traits\JsonResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CourirController extends Controller
{
    use JsonResponder;

    private CourirService $courirService;

    public function __construct(CourirService $courirService)
    {
        $this->courirService = $courirService;
    }

    /**
     * Ambil semua data provinsi
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDestination(Request $request): JsonResponse
    {
        try {
            $forceRefresh = $request->boolean('refresh', false);
            $cacheKey     = 'destination:id';

            // Jika force refresh, hapus cache dulu
            if ($forceRefresh) {
                Cache::forget($cacheKey);
            }

            $search = $request->get('search');

            // Cek cache dulu sebelum hit service
            $data = Cache::remember($cacheKey, config('services.ongkir.cache_timeout', 86400), function () use ($search) {
                return $this->courirService->getDestinationId($search);
            });

            return $this->successResponse($data, 'Data provinsi berhasil diambil', 200, [
                'total'     => count($data),
                'cached'    => ! $forceRefresh,
                'cache_key' => $cacheKey,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting provinsi data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(null, 'Gagal mengambil data provinsi: ' . $e->getMessage(), 500);
        }
    }
    public function getOngkir(Request $request): JsonResponse
    {
        try {
            $forceRefresh = $request->boolean('refresh', false);
            $cacheKey     = 'ongkir:'.$request->get('origin').':'.$request->get('destination').':'.$request->get('weight').':'.$request->get('courier');

            // Jika force refresh, hapus cache dulu
            if ($forceRefresh) {
                Cache::forget($cacheKey);
            }

            $origin = $request->get('origin');
            $destination = $request->get('destination');
            $weight = $request->get('weight');
            $courier = $request->get('courier');

            // Cek cache dulu sebelum hit service
            $data = Cache::remember($cacheKey, config('services.ongkir.cache_timeout', 86400), function () use ($origin, $destination, $weight, $courier) {
                return $this->courirService->getOngkir($origin, $destination, $weight, $courier);
            });

            return $this->successResponse($data, 'Data provinsi berhasil diambil', 200, [
                'total'     => count($data),
                'cached'    => ! $forceRefresh,
                'cache_key' => $cacheKey,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting provinsi data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(null, 'Gagal mengambil data provinsi: ' . $e->getMessage(), 500);
        }
    }
    //
}
