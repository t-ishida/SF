<?php
namespace SF;


use Loula\HttpRequest;
use SF\Query\Query;

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
            } elseif ($throwBadRequest) {
                throw $e;
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


    public function query(Query $query)
    {
        return $this->get('/services/data/v26.0/query', array('q' => $query->toString()));
    }

    /**
     * @param $apiResult
     * @return array
     */
    public function createResult($apiResult)
    {
        $result = array_map(function($row){return $this->flat($row);}, $apiResult->records);
        isset($apiResult->nextRecordsUrl) &&
        $result = array_merge($result, $this->createResult($this->get($apiResult->nextRecordsUrl)));
        return $result;
    }

    /**
     * @return array
     */
    public function flat()
    {
        $key = null; $val = null;
        $argc = func_num_args();
        if ($argc === 1) {
            list($val) = func_get_args();
        } elseif ($argc === 2) {
            list($key, $val) = func_get_args();
        }
        is_object($val) && $val = (array)$val;
        $result = array();
        if (isset($val['attributes'])) {
            unset($val['attributes']);
        }
        foreach ($val as $key2 => $val2) {
            if (is_object($val2) || is_array($val2)) {
                list($key2, $val2) = $this->flat($key2, $val2);
                foreach ($val2 as $key3 => $val3) {
                    $result[$key ? "$key=>$key3" : $key3] = $val3;
                }
            } else {
                $result[$key ? "$key=>$key2" : $key2] = $val2;
            }
        }
        return $key === null ? $result : array($key, $result);
    }
}