<?php
namespace ArkonEvent\SendRegningApi;

use GuzzleHttp\RequestOptions;
use ArkonEvent\SendRegningApi\SendRegningApiException;

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

    const LINK_OFFER = 'offer';

    const LINK_ORDER_CONFIRMATION = 'orderConfirmation';

    const LINK_PDF = 'pdf';

    const LINK_SEND_AGAIN = 'sendAgain';

    const LINK_SEND_CREDIT_NOTE = 'sendCreditNote';

    const LINK_COLLECTION = 'collection';

    const LINK_SEND_DUNNING = 'sendDunning';

    const LINK_REGISTER_PAYMENT = 'registerPayment';

    const LINK_SEND_TO_DEBT_COLLECTION = 'sendToDebtCollection';

    const LINK_CANCEL_A_GIRO_REQUEST = 'cancelAgiroRequest';

    const LINK_CANCEL_COLLECTION = 'cancelCollection';

    const LINK_INVOICE_DUNNINGS = 'invoiceDunnings';

    const LINK_DEBT_COLLECTION_NOTICE = 'debtCollectionNotice';

    /**
     *
     * @param string $username            
     * @param string $password            
     * @param string $sendRegningAccountId            
     * @param string $version            
     * @param string $baseUrl            
     * @param array $additionalHttpOptions            
     */
    public function __construct($username, $password, $sendRegningAccountId, $apiVersion = self::VERSION_LATEST, $baseUrl = self::API_BASE_URL, array $additionalHttpOptions = [])
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
        
        if ($apiVersion != self::VERSION_LATEST) {
            $httpOptions[RequestOptions::HEADERS]['API-Version'] = $apiVersion;
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
     * @param bool $returnJsonAsString            
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

    /**
     * Call a link from a sendregning API response, will either return json or string depending on return format of sendregning API
     *
     * @param \stdClass $data            
     * @param string $linkName            
     * @param bool $returnJsonAsString            
     * @throws SendRegningApiException
     * @return string|\stdClass
     */
    public function callLink(\stdClass $data, $linkName, $returnJsonAsString = false)
    {
        if (! isset($data->{'_links'}->$linkName->uri)) {
            throw new SendRegningApiException('Link not found: ' . $linkName);
        }
        
        $path = $data->{'_links'}->$linkName->uri;
        
        if ($linkName == self::LINK_PDF) {
            $responseData = $this->client->getConfig('base_uri') . $path;
        } else {
            $responseData = $this->get($path, $returnJsonAsString);
        }
        
        return $data;
    }
}