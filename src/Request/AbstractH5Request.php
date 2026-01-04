<?php

namespace Omnipay\Bocom\Request;

use Omnipay\Bocom\Common\Constants;
use Omnipay\Bocom\Core\BocomPublicKeySpi;
use Omnipay\Bocom\Core\NormalPublicKeyApi;
use Omnipay\Bocom\Response\BocomDataResponse;
use Omnipay\Bocom\Util\ArrUtil;
use Omnipay\Bocom\Util\RsaUtil;
use Omnipay\Bocom\Util\StrUtil;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractRequest as CommonAbstractRequest;

abstract class AbstractH5Request extends CommonAbstractRequest
{
    /**
     * @var BocomPublicKeySpi $bocomPublicKeyApi
     */
    protected $bocomPublicKeyApi;

    protected $publicKey;

    protected $privateKey;

    protected $mchId;

    protected $env;

    protected $endpoints = [
        Constants::ENV_PRODUCTION => 'https://open.bankcomm.com',
        Constants::ENV_SANDBOX    => 'https://117.184.192.242:9443',
    ];

    public function initialize(array $parameters = array())
    {
        parent::initialize(array_merge($this->getDefaultParameters(), $parameters));

        if (isset($parameters['openPlatformPublicKey'])) {
            $this->bocomPublicKeyApi = new NormalPublicKeyApi($parameters['openPlatformPublicKey']);
        }

        return $this;
    }

    /**
     * @throws InvalidRequestException
     */
    public function getEndpoint($environment = null)
    {
        $environment = $environment ?: $this->getEnvironment();
        if (empty($environment)) {
            $environment = Constants::ENV_PRODUCTION;
        }

        if (!array_key_exists($environment, $this->endpoints)) {
            throw new InvalidRequestException("Unknown environment: $environment");
        }

        return $this->endpoints[$environment];
    }

    abstract public function getUriPath();

    abstract public function isNeedEncrypt();

    public function getDefaultParameters()
    {
        return [
            'timestamp' => date('Y-m-d H:i:s'),
            'charset'   => 'UTF-8',
            'fmt_type'  => 'json',
            'msg_id'    => StrUtil::uuid()
        ];
    }

    public function getDefaultHeaders()
    {
        return [
            'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
            'Expect' => '',
        ];
    }

    protected function getRequestMethod()
    {
        return 'POST';
    }

    /**
     * @throws InvalidRequestException
     */
    protected function getRequestUrl()
    {
        return sprintf("%s%s", $this->getEndpoint(), $this->getUriPath());
    }

    /**
     * @return BocomDataResponse|null
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     */
    public function sendData($data = [])
    {
        $method = $this->getRequestMethod();
        $url    = $this->getRequestUrl();

        $headers = $this->getDefaultHeaders();
        $body = $this->prepareParameters($data);

        $response = $this->httpClient->send($method, $url, $headers, $body);
        return $this->handleResponse((string) $response->getBody());
    }

    /**
     * @throws InvalidResponseException
     */
    public function handleResponse($response)
    {
        $response = ArrUtil::toArray($response);

        $sign = $response['sign'];
        $rspBizContent = $response['rsp_biz_content'];

        $result = $this->bocomPublicKeyApi->verify(ArrUtil::toJsonString($rspBizContent), $sign);
        if (!$result) {
            throw new InvalidResponseException('Invalid response from API');
        }

        return new BocomDataResponse($rspBizContent, $this);
    }

    public function getRequestBody()
    {
        return $this->getParameter(Constants::REQUEST_BODY_KEY);
    }

    public function setRequestBody($data)
    {
        $traceNo = null;
        $keys = [
            'trace_no',
            'out_trade_no',
            'orig_trace_no',
            'mcht_order_no',
            'orig_mcht_order_no',
        ];

        foreach ($keys as $k) {
            if (array_key_exists($k, $data)) {
                $traceNo = $data[$k];
                break;
            }
        }

        $item = [
            'req_head' => [
                'term_trans_time' => date('YmdHis'),
                'trace_no' => $traceNo,
            ],
            'req_body' => $data,
        ];

        return $this->setBizContent(ArrUtil::toJsonString($item));
    }

    /**
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $data = $this->getParameters();
        $this->validate('app_id', 'msg_id', 'fmt_type', 'charset', 'timestamp', 'biz_content');

        return $this->getParameters();
    }

    protected function prepareParameters($parameters = [])
    {
        if ($this->isNeedEncrypt()) {
            $this->setIsEncrypt(true);
            $bizContent = $this->getParameter('biz_content');

            $this->setEncryptKey($this->bocomPublicKeyApi->encrypt($bizContent));
            $this->setBizContent(RsaUtil::encrypt($bizContent, $this->getPublicKey()));
        }

//        $parameters = array_merge($this->getDefaultParameters(), $parameters);
        ksort($parameters);
        $plainText = self::buildQueryString($parameters);
        $plainText = sprintf("%s?%s", $this->getUriPath(), $plainText);

        $parameters['sign'] = RsaUtil::sign($plainText, $this->getPrivateKey());

        return $parameters;
    }

    protected function buildQueryString($parameters)
    {
        $rows = [];
        foreach($parameters as $k => $v) {
            $rows[] = "$k=$v";
        }

        return implode('&', $rows);
    }

    public function setEnvironment($environment)
    {
        $this->env = $environment;

        return $this;
    }

    public function getEnvironment()
    {
        return $this->env;
    }

    /**
     * @return mixed
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @param mixed $privateKey
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
    }

    /**
     * @return mixed
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @param mixed $publicKey
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;
    }

    public function setAppId($value)
    {
        return $this->setParameter('app_id', $value);
    }

    public function getAppId()
    {
        return $this->getParameter('app_id');
    }

    public function setMsgId($value)
    {
        return $this->setParameter('msg_id', $value);
    }

    public function getMsgId()
    {
        return $this->getParameter('msg_id');
    }

    public function setFmtType($value)
    {
        return $this->setParameter('fmt_type', $value);
    }

    public function getFmtType()
    {
        return $this->getParameter('fmt_type');
    }

    public function setCharset($value)
    {
        return $this->setParameter('charset', $value);
    }

    public function getCharset()
    {
        return $this->getParameter('charset');
    }

    public function setIsEncrypt($value)
    {
        return $this->setParameter('is_encrypt', $value);
    }

    public function getIsEncrypt()
    {
        return $this->getParameter('is_encrypt');
    }

    public function setTimestamp($value)
    {
        return $this->setParameter('timestamp', $value);
    }

    public function getTimestamp()
    {
        return $this->getParameter('timestamp');
    }

    public function setBizContent($value)
    {
        return $this->setParameter('biz_content', $value);
    }

    public function getBizContent()
    {
        return $this->getParameter('biz_content');
    }

    public function setSign($value)
    {
        return $this->setParameter('sign', $value);
    }

    public function getSign()
    {
        return $this->getParameter('sign');
    }

    public function setEncryptKey($value)
    {
        return $this->setParameter('encrypt_key', $value);
    }

    public function getEncryptKey()
    {
        return $this->getParameter('encrypt_key');
    }

    public function setMchId($value)
    {
        return $this->mchId = $value;
    }

    public function getMchId()
    {
        return $this->mchId;
    }

}