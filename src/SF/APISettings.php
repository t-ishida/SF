<?php
namespace SF;


use SF\Util\File;

class APISettings extends File
{
    private $clientId = null;
    private $clientSecret = null;
    private $instanceUrl = null;
    private $accessToken = null;
    private $refreshToken = null;

    public function  __construct($path = null)
    {
        parent::__construct($path, 'r+');
        $this->set(json_decode($this->readAll()));
    }

    /**
     * @return null
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return null
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @return null
     */
    public function getInstanceUrl()
    {
        return $this->instanceUrl;
    }

    /**
     * @return null
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return null
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    public function set($values)
    {
        $this->clientId = $values->client_id;
        $this->clientSecret = $values->client_secret;
        $this->instanceUrl = $values->instance_url;
        $this->accessToken = $values->access_token;
        $this->refreshToken = $values->refresh_token;
    }

    public function save()
    {
        $this->writeAll(json_encode(array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'instance_url' => $this->instanceUrl,
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
        )));
    }

    public function __destruct()
    {
        $this->save();
    }
}