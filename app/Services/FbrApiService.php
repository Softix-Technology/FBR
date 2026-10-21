<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class FbrApiService
{
    private User $user;

    /**
     * Constructor to inject user dependency
     */
    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * Set the user for this service instance
     */
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get the environment (sandbox or production) based on user preference
     */
    private function getEnvironment(): string
    {
        if (!$this->user) {
            // Default to sandbox if no user is set
            return 'sandbox';
        }

        return $this->user->use_sandbox ? 'sandbox' : 'production';
    }

    /**
     * Get the endpoint URL for the current environment
     */
    private function getEndpoint(string $endpointKey): string
    {
        $environment = $this->getEnvironment();
        $endpoint = config("fbr.endpoints.{$environment}.{$endpointKey}");

        if (!$endpoint) {
            throw new Exception("Endpoint '{$endpointKey}' not found for environment '{$environment}'");
        }

        return $endpoint;
    }

    /**
     * Sanitize invoice items to conform strictly to FBR decimal limits:
     * - Quantity: max 4 decimal places
     * - Monetary/Tax/Price fields: max 2 decimal places
     */
    private function sanitizeInvoiceData(array $invoiceData): array
    {
        if (isset($invoiceData['items']) && is_array($invoiceData['items'])) {
            foreach ($invoiceData['items'] as &$item) {
                if (!is_array($item)) {
                    continue;
                }

                // Quantity: up to 4 decimal places allowed by FBR
                if (isset($item['quantity']) && is_numeric($item['quantity'])) {
                    $item['quantity'] = round((float) $item['quantity'], 4);
                }

                // Currency & Amount fields: up to 2 decimal places allowed by FBR
                $twoDecimalFields = [
                    'totalValues',
                    'valueSalesExcludingST',
                    'salesTaxApplicable',
                    'fixedNotifiedValueOrRetailPrice',
                    'salesTaxWithheldAtSource',
                    'furtherTax',
                    'fedPayable',
                    'discount',
                    'extraTax',
                    'rateValues',
                ];

                foreach ($twoDecimalFields as $field) {
                    if (isset($item[$field]) && is_numeric($item[$field])) {
                        $item[$field] = round((float) $item[$field], 2);
                    }
                }
            }
            unset($item);
        }

        return $invoiceData;
    }

    /**
     * Post invoice data to FBR
     */
    public function postInvoiceData(string $accessToken, array $invoiceData): array
    {
        try {
            $invoiceData = $this->sanitizeInvoiceData($invoiceData);
            $apiUrl = $this->getEndpoint('post_invoice');

            // Log the complete payload and API URL for debugging
            Log::info('=== FBR GENERATE INVOICE API CALL ===', [
                'environment' => $this->getEnvironment(),
                'api_url' => $apiUrl,
                'access_token' => $accessToken,
                'complete_payload' => $invoiceData,
                'payload_json' => json_encode($invoiceData, JSON_PRETTY_PRINT),
                'date_value' => $invoiceData['invoiceDate'] ?? 'Not set',
                'date_format_check' => 'Expected format: YYYY-MM-DD, Current: ' . ($invoiceData['invoiceDate'] ?? 'Not set')
            ]);
            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->asJson()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                    'Accept-Encoding' => 'gzip, deflate, br',
                ])->post($apiUrl, $invoiceData);

            return $this->handleResponse($response, $apiUrl, $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in postInvoiceData: ' . $e->getMessage());
            $apiUrl = $this->getEndpoint('post_invoice');

            return [
                'success' => false,
                'message' => 'Failed to post invoice data: ' . $e->getMessage(),
                'api_url' => $apiUrl,
                'access_token' => $accessToken,
            ];
        }
    }

    /**
     * Validate invoice data with FBR
     */
    public function validateInvoiceData(string $accessToken, array $invoiceData): array
    {
        try {
            $invoiceData = $this->sanitizeInvoiceData($invoiceData);
            $apiUrl = $this->getEndpoint('validate_invoice');

            // Log the complete payload and API URL for debugging
            Log::info('=== FBR VALIDATE INVOICE API CALL ===', [
                'environment' => $this->getEnvironment(),
                'api_url' => $apiUrl,
                'complete_payload' => $invoiceData,
                'payload_json' => json_encode($invoiceData, JSON_PRETTY_PRINT),
                'date_value' => $invoiceData['invoiceDate'] ?? 'Not set',
                'date_format_check' => 'Expected format: YYYY-MM-DD, Current: ' . ($invoiceData['invoiceDate'] ?? 'Not set'),
                'payload_structure_check' => [
                    'has_flat_structure' => !isset($invoiceData['seller']) && !isset($invoiceData['buyer']),
                    'has_sellerNTNCNIC' => isset($invoiceData['sellerNTNCNIC']),
                    'has_buyerNTNCNIC' => isset($invoiceData['buyerNTNCNIC']),
                    'items_count' => count($invoiceData['items'] ?? [])
                ]
            ]);

            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->asJson()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                ])->post($apiUrl, $invoiceData);

            return $this->handleResponse($response, $apiUrl, $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in validateInvoiceData: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to validate invoice data: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get province codes
     */
    public function getProvinceCodes(string $accessToken): array
    {
        try {
            $apiUrl = $this->getEndpoint('provinces');

            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json'
                ])->get($apiUrl);

            return $this->handleResponse($response, $apiUrl, $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in getProvinceCodes: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch province codes: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get document type IDs
     */
    public function getDocumentTypeIds(string $accessToken): array
    {
        try {
            $apiUrl = $this->getEndpoint('document_types');

            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json'
                ])->get($apiUrl);

            return $this->handleResponse($response, $apiUrl, $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in getDocumentTypeIds: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch document type IDs: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get item codes
     */
    public function getItemCodes(string $accessToken): array
    {
        try {
            $apiUrl = $this->getEndpoint('item_codes');

            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json'
                ])->get($apiUrl);

            return $this->handleResponse($response, $apiUrl, $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in getItemCodes: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch item codes: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get transaction type codes
     */
    public function getTransactionTypeCodes(string $accessToken): array
    {
        try {
            $apiUrl = $this->getEndpoint('transaction_types');

            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json'
                ])->get($apiUrl);

            return $this->handleResponse($response, $apiUrl, $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in getTransactionTypeCodes: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch transaction type codes: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get units of measurement
     */
    public function getUnitsOfMeasurement(string $accessToken): array
    {
        try {
            $apiUrl = $this->getEndpoint('units_of_measurement');

            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json'
                ])->get($apiUrl);

            return $this->handleResponse($response, $apiUrl, $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in getUnitsOfMeasurement: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch units of measurement: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get SRO schedules
     */
    public function getSroSchedules(string $accessToken): array
    {
        try {
            $apiUrl = $this->getEndpoint('sro_schedules');

            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json'
                ])->get($apiUrl);

            return $this->handleResponse($response, $apiUrl, $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in getSroSchedules: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch SRO schedules: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get tax rates
     */
    public function getTaxRates(string $accessToken): array
    {
        try {
            $apiUrl = $this->getEndpoint('tax_rates');

            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json'
                ])->get($apiUrl);

            return $this->handleResponse($response, $apiUrl, $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in getTaxRates: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch tax rates: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get HS codes with UOM
     */
    public function getHsCodesWithUom(string $accessToken, string $hsCode = null): array
    {
        try {
            $apiUrl = $this->getEndpoint('hs_codes_with_uom');
            $queryParams = [
                'annexure_id' => 3
            ];

            // Add HS code parameter if provided
            if ($hsCode) {
                $queryParams['hs_code'] = $hsCode;
            }

            Log::info('=== FBR HS CODES WITH UOM API CALL ===', [
                'environment' => $this->getEnvironment(),
                'api_url' => $apiUrl,
                'query_params' => $queryParams,
                'full_url' => $apiUrl . '?' . http_build_query($queryParams),
                'hs_code_provided' => !empty($hsCode)
            ]);

            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json'
                ])->get($apiUrl, $queryParams);

            return $this->handleResponse($response, $apiUrl . '?' . http_build_query($queryParams), $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in getHsCodesWithUom: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch HS codes with UOM: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get item description codes (HS Codes)
     */
    public function getItemDescriptionCodes(string $accessToken): array
    {
        try {
            $apiUrl = $this->getEndpoint('item_codes');

            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json'
                ])->get($apiUrl);

            return $this->handleResponse($response, $apiUrl, $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in getItemDescriptionCodes: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch item description codes: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get SRO item IDs
     */
    public function getSroItemIds(string $accessToken): array
    {
        try {
            $apiUrl = $this->getEndpoint('sro_item_ids');

            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json'
                ])->get($apiUrl);

            return $this->handleResponse($response, $apiUrl, $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in getSroItemIds: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch SRO item IDs: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get registration type for a given registration number
     */
    public function getRegistrationType(string $accessToken, string $registrationNumber): array
    {
        try {
            $apiUrl = $this->getEndpoint('registration_type');

            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])->post($apiUrl, [
                    'Registration_No' => $registrationNumber
                ]);

            return $this->handleResponse($response, $apiUrl, $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in getRegistrationType: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch registration type: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get tax rate based on date, transaction type, and province
     */
    public function getSaleTypeToRate(string $accessToken, string $date, int $transTypeId, int $originationSupplier): array
    {
        try {
            $apiUrl = $this->getEndpoint('sale_type_to_rate');
            $queryParams = [
                'date' => $date,
                'transTypeId' => $transTypeId,
                'originationSupplier' => $originationSupplier
            ];

            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json'
                ])->get($apiUrl, $queryParams);

            return $this->handleResponse($response, $apiUrl . '?' . http_build_query($queryParams), $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in getSaleTypeToRate: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch sale type to rate: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get SRO schedule based on rate ID, date, and origination supplier
     */
    public function getSroSchedule(string $accessToken, int $rateId, string $date, int $originationSupplierCsv): array
    {
        try {
            $apiUrl = $this->getEndpoint('sro_schedules');
            $queryParams = [
                'rate_id' => $rateId,
                'date' => $date,
                'origination_supplier_csv' => $originationSupplierCsv
            ];

            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json'
                ])->get($apiUrl, $queryParams);

            return $this->handleResponse($response, $apiUrl . '?' . http_build_query($queryParams), $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in getSroSchedule: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch SRO schedule: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get SRO items based on date and SRO ID
     */
    public function getSroItem(string $accessToken, string $date, int $sroId): array
    {
        try {
            $apiUrl = $this->getEndpoint('sro_item_ids');
            $queryParams = [
                'date' => $date,
                'sro_id' => $sroId
            ];

            $response = Http::timeout(config('fbr.timeout', 30))
                ->connectTimeout(config('fbr.connect_timeout', 10))
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json'
                ])->get($apiUrl, $queryParams);

            return $this->handleResponse($response, $apiUrl . '?' . http_build_query($queryParams), $accessToken);
        } catch (Exception $e) {
            Log::error('FBR API Error in getSroItem: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch SRO items: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Handle HTTP response from FBR API
     */
    private function handleResponse($response, string $apiUrl = null, string $accessToken = null): array
    {
        $statusCode = $response->status();
        $responseData = $response->json();
        $baseResponse = [
            'environment' => $this->getEnvironment(),
            'api_url' => $apiUrl,
            'access_token' => $accessToken ? substr($accessToken, 0, 10) . '...' : null, // Partial token for security
        ];

        switch ($statusCode) {
            case 200:
                return array_merge($baseResponse, [
                    'success' => true,
                    'data' => $responseData,
                    'status_code' => $statusCode
                ]);

            case 401:
                return array_merge($baseResponse, [
                    'success' => false,
                    'message' => 'Unauthorized - Invalid or expired FBR access token.',
                    'status_code' => $statusCode
                ]);

            case 400:
                return array_merge($baseResponse, [
                    'success' => false,
                    'message' => 'Bad Request - ' . ($responseData['message'] ?? 'Invalid request data'),
                    'status_code' => $statusCode,
                    'errors' => $responseData['errors'] ?? null
                ]);

            case 500:
                return array_merge($baseResponse, [
                    'success' => false,
                    'message' => $responseData,
                    'status_code' => $statusCode,
                    'response' => $responseData
                ]);

            default:
                Log::warning('Unexpected FBR API status code', [
                    'status_code' => $statusCode,
                    'response_data' => $responseData,
                    'api_url' => $apiUrl
                ]);

                return array_merge($baseResponse, [
                    'success' => false,
                    'message' => "Unexpected API response (Status: {$statusCode})" .
                               ($responseData ? ': ' . json_encode($responseData) : ''),
                    'status_code' => $statusCode,
                    'response' => $responseData
                ]);
        }
    }
}
