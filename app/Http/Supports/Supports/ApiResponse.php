<?php

namespace App\Http\Supports;
/**
 * Trait Response
 * @package App\Http\Supports
 */
trait ApiResponse
{
    /**
     * @var int
     */
    protected $_code = 200;
    /**
     * @var string
     */
    protected $_message = '';
    /**
     * @var array
     */
    protected $_data = array();
    /**
     * @var array
     */
    protected $_metaData = [];
    /**
     * @var bool
     */
    protected $_ok = true;

    /**
     * @var integer
     * @var array
     * @return \Illuminate\Http\JsonResponse
     */
    public function renderErrorJson($code = 200, $data = [])
    {
        $this->setOk(false);
        return $this->renderJson($data, $code);
    }

    /**
     * @var integer
     * @var array
     * @return
     */
    public function renderErrorXml($code = 200, $data = [])
    {
        $this->setOk(false);
        return $this->renderXml($data, $code);
    }

    /**
     * @var integer
     * @var array
     * @return \Illuminate\Http\JsonResponse
     */
    public function renderJson($data = [], $code = 200)
    {
        $data = $this->_buildResponse($data);
        $this->fireEvent(getEventName('controller.before_render_json'), $data);
        $result = \Illuminate\Support\Facades\Response::json($data, $code);
        $this->fireEvent(getEventName('controller.after_render_json'), $result);
        return $result;
    }

    /**
     * @var integer
     * @var array
     * @return
     */
    public function renderXml($data = [], $code = 200)
    {
        $data = $this->_buildResponse($data);
        $this->fireEvent(getEventName('controller.before_render_json'), $data);
        $result = \Illuminate\Support\Facades\Response::xml($data, $code);
        $this->fireEvent(getEventName('controller.after_render_json'), $result);
        return $result;
    }

    protected function _buildResponse($data = [])
    {
        return array_merge(array(
            'ok' => $this->isOk(),
            'message' => $this->getMessage(),
            'data' => $this->getData(),
            'meta' => $this->getMetaData()
        ), $data);
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @param int $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->_code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->_message = $message;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param array $data
     **@return $this
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getMetaData()
    {
        return $this->_metaData;
    }

    /**
     * @param array $metaData
     */
    public function setMetaData($metaData)
    {
        $this->_metaData = $metaData;
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return $this->_ok;
    }

    /**
     * @param bool $ok
     * @return $this
     */
    public function setOk($ok)
    {
        $this->_ok = $ok;
        return $this;
    }
}