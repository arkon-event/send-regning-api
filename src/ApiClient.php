<?php
namespace ArkonEvent\SendRegningApi;

use \GuzzleHttp\RequestOptions;

/**
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Simple PHP API Client for sendregning.no's REST API, see API documentation for data structures and paths http://sendregning.github.io/
 *
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 */
class ApiClient
{

    /**
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    const VERSION_LATEST = 'LATEST';

    const API_BASE_URL = 'https://www.sendregning.no/';

    /**
     *
     * @param string $username            
     * @param string $password            
     * @param string $sendRegningAccountId            
     * @param string $version            
     * @param string $baseUrl            
     * @param array $additionalHttpOptions            
     */
    public function __construct($username, $password, $sendRegningAccountId, $version = self::VERSION_LATEST, $baseUrl = self::API_BASE_URL, array $additionalHttpOptions = [])
    {
        $httpOptions = [
            'base_uri' => $baseUrl,
            RequestOptions::HEADERS => [
                'Originator-Id' => $sendRegningAccountId,
                'Accept' => 'application/json'
            ],
            RequestOptions::AUTH => [
                $username,
                $password
            ]
        ];
        
        $httpOptions = array_merge($httpOptions, $additionalHttpOptions);
        
        if ($version != self::VERSION_LATEST) {
            $httpOptions[RequestOptions::HEADERS]['API-Version'] = $version;
        }
        
        $this->client = new \GuzzleHttp\Client($httpOptions);
    }

    /**
     * Post data to the API using a standard object ready for json_encoding
     *
     * @param string $path            
     * @param \stdClass|array $data            
     */
    public function post($path, $data = null)
    {
        if (! is_null($data)) {
            $options = [
                RequestOptions::JSON => $data
            ];
        } else {
            $options = [];
        }
        $this->client->request('POST', $path, $options);
    }

    /**
     * Get data from API, returned as json_decoded object if $returnJsonAsString is not set to true
     *
     * @param string $path            
     * @param string $returnJsonAsString            
     * @return string|mixed
     */
    public function get($path, $returnJsonAsString = false)
    {
        $response = $this->client->request('GET', $path);
        
        $data = (string) $response->getBody();
        if (! $returnJsonAsString) {
            $data = json_decode($data);
        }
        return $data;
    }
}