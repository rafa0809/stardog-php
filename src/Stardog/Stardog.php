<?php


namespace StardogPhp\Stardog;


use StardogPhp\Request\CurlRequestPerformer;
use StardogPhp\Request\Request;

class Stardog
{

    private $token;

    /**
     * @var CurlRequestPerformer
     */
    private $requestPerformer;

    /**
     * @var StardogEndpointFactory
     */
    private $endpointFactory;

    /**
     * @param $url
     * @param $user
     * @param $password
     */
    public function __construct($url, $user, $password)
    {
        $this->token = base64_encode( $user . ':' . $password );
        $this->requestPerformer = new CurlRequestPerformer( $this->token );
        $this->endpointFactory = StardogEndpointFactory::withServerUrl( $url );
    }

    /**
     * @param $db
     * @return TransactionFluent
     * @throws \Exception
     */
    public function beginTransaction($db)
    {
        $url = $this->endpointFactory->getBeginTransactionEndpoint( $db );
        $request = new Request( 'POST', $url );
        $response = $this->requestPerformer->performRequest( $request );
        if ( !$response->isSuccess() ) {
            throw new \Exception( 'Exception in begin transaction: ' . $response );
        }
        $transactionId = $response->getContent();
        return new TransactionFluent( $transactionId, $db, $this->requestPerformer, $this->endpointFactory );
    }


}