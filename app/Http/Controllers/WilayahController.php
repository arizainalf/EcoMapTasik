<?php
namespace App\Http\Controllers;

use App\Services\WilayahService;
use App\Traits\JsonResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class WilayahController extends Controller
{
    use JsonResponder;

    private WilayahService $wilayahService;

    public function __construct(WilayahService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
    }

    /**
     * Ambil semua data provinsi
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getProvinsi(Request $request): JsonResponse
    {
        try {
            $forceRefresh = $request->boolean('refresh', false);
            $cacheKey     = 'wilayah:provinsi';

            // Jika force refresh, hapus cache dulu
            if ($forceRefresh) {
                Cache::forget($cacheKey);
            }

            // Cek cache dulu sebelum hit service
            $data = Cache::remember($cacheKey, config('services.wilayah.cache_timeout', 86400), function () {
                return $this->wilayahService->getProvinsi();
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

    /**
     * Ambil data kota/kabupaten berdasarkan ID provinsi
     *
     * @param Request $request
     * @param string $provinsiId
     * @return JsonResponse
     */
    public function getKota(Request $request, string $provinsiId): JsonResponse
    {
        try {
            // Validasi input
            $this->validateId($provinsiId, 'Provinsi ID');

            $forceRefresh = $request->boolean('refresh', false);
            $cacheKey     = "wilayah:kota:{$provinsiId}";

            // Jika force refresh, hapus cache dulu
            if ($forceRefresh) {
                Cache::forget($cacheKey);
            }

            // Cek cache dulu sebelum hit service
            $data = Cache::remember($cacheKey, config('services.wilayah.cache_timeout', 86400), function () use ($provinsiId) {
                return $this->wilayahService->getKota($provinsiId);
            });

            return $this->successResponse($data, 'Data kota/kabupaten berhasil diambil', 200, [
                'provinsi_id' => $provinsiId,
                'total'       => count($data),
                'cached'      => ! $forceRefresh,
                'cache_key'   => $cacheKey,
            ]);

        } catch (InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);

        } catch (\Exception $e) {
            Log::error('Error getting kota data', [
                'provinsi_id' => $provinsiId,
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(null, 'Gagal mengambil data kota: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Ambil data kecamatan berdasarkan ID kota
     *
     * @param Request $request
     * @param string $kotaId
     * @return JsonResponse
     */
    public function getKecamatan(Request $request, string $kotaId): JsonResponse
    {
        try {
            $this->validateId($kotaId, 'Kota ID');

            $forceRefresh = $request->boolean('refresh', false);
            $cacheKey     = "wilayah:kecamatan:{$kotaId}";

            if ($forceRefresh) {
                Cache::forget($cacheKey);
            }

            $data = Cache::remember($cacheKey, config('services.wilayah.cache_timeout', 86400), function () use ($kotaId) {
                return $this->wilayahService->getKecamatan($kotaId);
            });

            return $this->successResponse($data, 'Data kecamatan berhasil diambil', 200, [
                'kota_id'   => $kotaId,
                'total'     => count($data),
                'cached'    => ! $forceRefresh,
                'cache_key' => $cacheKey,
            ]);

        } catch (InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);

        } catch (\Exception $e) {
            Log::error('Error getting kecamatan data', [
                'kota_id' => $kotaId,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(null, 'Gagal mengambil data kecamatan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Ambil data kelurahan berdasarkan ID kecamatan
     *
     * @param Request $request
     * @param string $kecamatanId
     * @return JsonResponse
     */
    public function getKelurahan(Request $request, string $kecamatanId): JsonResponse
    {
        try {
            $this->validateId($kecamatanId, 'Kecamatan ID');

            $forceRefresh = $request->boolean('refresh', false);
            $cacheKey     = "wilayah:kelurahan:{$kecamatanId}";

            if ($forceRefresh) {
                Cache::forget($cacheKey);
            }

            $data = Cache::remember($cacheKey, config('services.wilayah.cache_timeout', 86400), function () use ($kecamatanId) {
                return $this->wilayahService->getKelurahan($kecamatanId);
            });

            return $this->successResponse($data, 'Data kelurahan berhasil diambil', 200, [
                'kecamatan_id' => $kecamatanId,
                'total'        => count($data),
                'cached'       => ! $forceRefresh,
                'cache_key'    => $cacheKey,
            ]);

        } catch (InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);

        } catch (\Exception $e) {
            Log::error('Error getting kelurahan data', [
                'kecamatan_id' => $kecamatanId,
                'error'        => $e->getMessage(),
                'trace'        => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(null, 'Gagal mengambil data kelurahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Pencarian wilayah berdasarkan nama
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchWilayah(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'q'    => 'required|string|min:2|max:100',
                'type' => 'sometimes|string|in:provinsi,kabupaten,kecamatan,kelurahan',
            ]);

            $query = $validated['q'];
            $type  = $validated['type'] ?? 'kabupaten';

            $data = $this->wilayahService->searchWilayah($query, $type);

            return $this->successResponse($data, 'Pencarian wilayah berhasil', 200, [
                'query'  => $query,
                'type'   => $type,
                'total'  => count($data),
                'cached' => true,
            ]);

        } catch (ValidationException $e) {
            return $this->errorResponse(null, 'Data tidak valid ' . $e->errors(), 422, );

        } catch (\Exception $e) {
            Log::error('Error searching wilayah', [
                'query' => $request->get('q'),
                'type'  => $request->get('type'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(null, 'Gagal mencari wilayah: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Hapus cache wilayah
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function clearCache(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => 'sometimes|string|in:provinsi,kota,kecamatan,kelurahan',
                'id'   => 'sometimes|string|max:50',
            ]);

            $type = $validated['type'] ?? null;
            $id   = $validated['id'] ?? null;

            $result = $this->wilayahService->clearCache($type, $id);

            $message = 'Cache berhasil dihapus';
            if ($type && $id) {
                $message .= " untuk {$type} ID: {$id}";
            } elseif ($type) {
                $message .= " untuk semua data {$type}";
            } else {
                $message .= " untuk semua data wilayah";
            }

            return $this->successResponse(['cleared' => $result], $message);

        } catch (ValidationException $e) {
            return $this->errorResponse(null, 'Data tidak valid ' . $e->errors(), 422);

        } catch (\Exception $e) {
            Log::error('Error clearing cache', [
                'type'  => $request->get('type'),
                'id'    => $request->get('id'),
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse(null, 'Gagal menghapus cache: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cek status API wilayah
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getApiStatus(Request $request): JsonResponse
    {
        try {
            $status = $this->wilayahService->getApiStatus();

            if ($status['status'] === 'healthy') {
                return $this->successResponse(null, 'API wilayah dalam kondisi baik ' . $status);
            } else {
                return $this->errorResponse(null, 'API wilayah mengalami gangguan. ' . $status, 503);
            }

        } catch (\Exception $e) {
            Log::error('Error checking API status', [
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse(null, 'Gagal memeriksa status API: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Ambil data wilayah lengkap berdasarkan ID
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getWilayahLengkap(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'provinsi_id'  => 'required|string',
                'kota_id'      => 'sometimes|string',
                'kecamatan_id' => 'sometimes|string',
                'kelurahan_id' => 'sometimes|string',
            ]);

            $result = [];

            // Ambil data provinsi
            $provinsi         = $this->wilayahService->getProvinsi();
            $selectedProvinsi = collect($provinsi)->firstWhere('id', $validated['provinsi_id']);

            if (! $selectedProvinsi) {
                return $this->errorResponse(null, 'Provinsi tidak ditemukan', 404);
            }

            $result['provinsi'] = $selectedProvinsi;

            // Ambil data kota jika ada
            if (! empty($validated['kota_id'])) {
                $kota         = $this->wilayahService->getKota($validated['provinsi_id']);
                $selectedKota = collect($kota)->firstWhere('id', $validated['kota_id']);

                if (! $selectedKota) {
                    return $this->errorResponse(null, 'Kota tidak ditemukan', 404);
                }

                $result['kota'] = $selectedKota;

                // Ambil data kecamatan jika ada
                if (! empty($validated['kecamatan_id'])) {
                    $kecamatan         = $this->wilayahService->getKecamatan($validated['kota_id']);
                    $selectedKecamatan = collect($kecamatan)->firstWhere('id', $validated['kecamatan_id']);

                    if (! $selectedKecamatan) {
                        return $this->errorResponse(null, 'Kecamatan tidak ditemukan', 404);
                    }

                    $result['kecamatan'] = $selectedKecamatan;

                    // Ambil data kelurahan jika ada
                    if (! empty($validated['kelurahan_id'])) {
                        $kelurahan         = $this->wilayahService->getKelurahan($validated['kecamatan_id']);
                        $selectedKelurahan = collect($kelurahan)->firstWhere('id', $validated['kelurahan_id']);

                        if (! $selectedKelurahan) {
                            return $this->errorResponse(null, 'Kelurahan tidak ditemukan', 404);
                        }

                        $result['kelurahan'] = $selectedKelurahan;

                    }
                }
            }

            return $this->successResponse($result, 'Data wilayah lengkap berhasil diambil');

        } catch (ValidationException $e) {
            return $this->errorResponse(null, 'Data tidak valid ' . $e->errors(), 422);

        } catch (\Exception $e) {
            Log::error('Error getting complete wilayah data', [
                'params' => $request->all(),
                'error'  => $e->getMessage(),
                'trace'  => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(null, 'Gagal mengambil data wilayah lengkap: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Validasi ID parameter
     *
     * @param mixed $id
     * @param string $fieldName
     * @throws InvalidArgumentException
     */
    private function validateId($id, string $fieldName): void
    {
        if (empty($id) || (! is_string($id) && ! is_numeric($id))) {
            throw new InvalidArgumentException("{$fieldName} tidak boleh kosong dan harus berupa string atau angka");
        }

        if (is_string($id) && strlen(trim($id)) === 0) {
            throw new InvalidArgumentException("{$fieldName} tidak boleh kosong");
        }

        // Validasi panjang maksimal untuk mencegah abuse
        if (strlen((string) $id) > 50) {
            throw new InvalidArgumentException("{$fieldName} terlalu panjang");
        }
    }
}
