# sendregning.no PHP API Client

This is a simple PHP HTTP wrapper for working with Sendregning.no's API. It takes care of structuring headers/authentication and allows an easy way to send messages to the API. It does not help you build JSON messages or validate them. See the SendRegning documentation for that http://sendregning.github.io/

This client was built for arkon.no's internal integration with sendregning.no, and is used by our production system. We are not affiliated with sendregning.no in any other way.

```php
<?php
use \ArkonEvent\SendRegningApi\ApiClient;
$client = new ApiClient('loginEmail', 'password', 'account-id');

//To execute a get request simply use get and add the path
$invoices = $client->get('/invoices/');

//or to get a single invoice
$invoice = $client->get('/invoices/14');

//use $queryParameters to send parameters
$params = ['from' => '2015-02-01','to' => '2015-06-01'];
$invoice = $client->get('/invoices/not-due',$params);

//use the callLink method to call links returned in the object
$client->callLink($invoice,ApiClient::LINK_SEND_AGAIN);

//To send a POST request without body
$client->post('/recipients/123456/available');

//To send a POST request with a body, data must be provided in format supported as input for json_encode 
$data = ['amount' => 0.25];
$client->post('/sales-ledger/123456/loss',$data);
?>
```
