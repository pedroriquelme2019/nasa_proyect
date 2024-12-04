namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class DonkiService
{
    protected $client;
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = env('NASA_API_BASE_URL');
        $this->apiKey = env('NASA_API_KEY');
    }

    public function fetchData($endpoint)
    {
        try {
            $response = $this->client->get($this->baseUrl . $endpoint, [
                'query' => ['api_key' => $this->apiKey],
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            return [
                'error' => 'Client error',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        } catch (RequestException $e) {
            return [
                'error' => 'Request error',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'General error',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }

    public function getDonkiData($endpoint = 'DONKI/CME') // Puedes cambiar el endpoint por defecto
    {
        $data = $this->fetchData($endpoint);
        return response()->json($data);
    }
}
