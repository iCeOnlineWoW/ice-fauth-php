<?php

/**
 * FAuth client class
 */
class FAuthClient
{
    /** @var \GuzzleHttp\Client */
    private $restClient;
    /** @var array */
    private $fauthNodes = [];
    /** @var string */
    private $service = null;
    /** @var string */
    private $lang = null;

    /** login request endpoint */
    const LOGIN_URI_BASE = "/login-request";

    const LOGIN_PARAM_CALLBACK = "callback";
    const LOGIN_PARAM_SERVICE = "service";
    const LOGIN_PARAM_LANGUAGE = "lang";

    /** validate request endpoint */
    const VALIDATE_URI_BASE = "/validate-token";

    /**
     * FAuth client class main constructor
     * @param string $service
     * @param array $nodes
     * @param string $lang
     * @throws Exception
     */
    public function __construct($service, $nodes = [], $lang = null)
    {
        $this->service = $service;
        $this->lang = $lang;
        $this->fauthNodes = $nodes;

        if (!is_string($service) || strlen($service) === 0)
            throw new Exception("Invalid service specified");
        if (empty($this->fauthNodes))
            throw new Exception("No FAuth nodes supplied");

        // TODO: we should probably warn developer, that not using HTTPS is a security risk,
        //       if any of supplied node URLs uses plain HTTP

        // validate nodes, remove / from the ending of each node URL
        foreach ($this->fauthNodes as $i => $node)
        {
            if ($node === null || strlen($node) === 0 || !is_string($node))
                throw new Exception("Invalid node URL in given node array");

            if ($node[strlen($node)-1] === '/')
                $this->fauthNodes[$i] = substr($node, 0, strlen($node)-1);
        }
    }

    /**
     * Select node from given array
     * @return int
     */
    protected function selectNode(): int
    {
        // TODO: node selection, for now, use first
        // TODO: when fixing this, see note in validateToken about chosing the same node for validation
        return 0;
    }

    /**
     * Retrieves auth URL for use with link - after (un)successfull authentication,
     * the client is redirected to callback URL
     * @param type $callbackUrl
     * @return string
     */
    public function getAuthURL($callbackUrl)
    {
        $nodeIdx = $this->selectNode();

        // create request URL
        $url = $this->fauthNodes[$nodeIdx].self::LOGIN_URI_BASE."?".
                self::LOGIN_PARAM_CALLBACK."=".urlencode($callbackUrl)."&".
                self::LOGIN_PARAM_SERVICE."=".urlencode($this->service);

        // language is optional parameter, server default could probably be english
        if ($this->lang)
            $url .= "&".self::LOGIN_PARAM_LANGUAGE."=".urlencode($this->lang);

        return $url;
    }

    /**
     * Validates given token - this method calls node API, so it may take some time!
     * @param type $token
     * @return boolean | array
     */
    public function validateToken($token)
    {
        // TODO: make selection bound to node used for authentication, so the verification
        //       process does not fail just because consistency between nodes hasn't been
        //       accomplished yet (W+R consistency is not appropriate for tokens)
        $nodeIdx = $this->selectNode();

        $url = $this->fauthNodes[$nodeIdx].self::VALIDATE_URI_BASE;

        $result = $this->rest()->request('POST', $url, [
            GuzzleHttp\RequestOptions::JSON => [
                'token' => $token,
                'services' => [ $this->service ]
            ]
        ]);

        if ($result->getStatusCode() !== 200)
            return false;

        $json = json_decode($result->getBody(), true);
        if ($json === null)
            return false;

        return $json;
    }

    /**
     * Lazyloading method for Guzzle REST client
     * @return \GuzzleHttp\Client
     */
    private function rest(): \GuzzleHttp\Client
    {
        if ($this->restClient === null)
            $this->restClient = new GuzzleHttp\Client();

        return $this->restClient;
    }
}
