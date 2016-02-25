<?php
namespace SF;


use Loula\HttpRequest;

/**
 * Class Client
 * @package SF
 */
class Client extends \Loula\HttpClient
{
    /**
     * @var APISettings
     */
    private $settings = null;

    /**
     * Client constructor.
     * @param APISettings $settings
     */
    public function __construct(APISettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param $url
     * @param null $params
     * @return mixed
     */
    public function get($url, $params = null)
    {
        return json_decode($this->sendOne(new HttpRequest(
            'GET',
            $this->settings->getInstanceUrl() . $url,
            $params,
            null,
            $this->buildHeader()))->getBody());
    }

    /**
     * @param $url
     * @param null $params
     * @param null $files
     * @return mixed
     */
    public function post($url, $params = null, $files = null)
    {
        return json_decode($this->sendOne(new HttpRequest(
            'POST',
            $this->settings->getInstanceUrl() . $url,
            $params,
            $files,
            $this->buildHeader()))->getBody());
    }

    /**
     * @param $url
     * @param null $params
     * @param null $files
     * @return mixed
     */
    public function put($url, $params = null, $files = null)
    {
        return json_decode($this->sendOne(new HttpRequest(
            'PUT',
            $this->settings->getInstanceUrl(). $url,
            $params,
            $files,
            $this->buildHeader()))->getBody());
    }

    /**
     * @param $url
     * @param null $params
     * @return mixed
     */
    public function delete($url, $params = null)
    {
        return json_decode($this->sendOne(new HttpRequest(
            'DELETE',
            $this->settings->getInstanceUrl() . $url,
            $params,
            null,
            $this->buildHeader()))->getBody()
        );
    }

    /**
     * @param HttpRequest $request
     * @param bool $throwBadRequest
     * @return \Loula\HttpResponse
     * @throws \Loula\Exception
     */
    public function sendOne(HttpRequest $request, $throwBadRequest = true)
    {
        $result = null;
        try {
            $result = parent::sendOne($request, $throwBadRequest);
        } catch (\Loula\Exception $e) {
            if ($e->getStatus() == 401 && $e->getBody() === "[{\"message\":\"Session expired or invalid\",\"errorCode\":\"INVALID_SESSION_ID\"}]") {
                $this->refreshToken();
                $result = parent::sendOne($request, $throwBadRequest);
            }
        }
        return $result;
    }


    /**
     * @return mixed
     */
    public function refreshToken () {
        $result = $this->post("https://login.salesforce.com/services/oauth2/token",array(
            "grant_type" => "refresh_token",
            "client_id" => $this->settings->getClientId(),
            "client_secret" => $this->settings->getClientSecret(),
            "refresh_token" => $this->settings->getRefreshToken(),
        ));
        $this->settings->set($result);
        $this->settings->save();
        return $result;
    }

    /**
     * @return array
     */
    public function buildHeader()
    {
        return array('Authorization: Bearer ' . $this->settings->getAccessToken());
    }
}