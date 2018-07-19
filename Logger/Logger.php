<?php
/**
 * Logger.php
 *
 * @author ZhangHan <zhanghan@thefair.net.cn>
 * @version 1.0
 * @copyright 2015-2025 TheFair
 */

namespace TheFairLib\Logger;

use TheFairLib\Utility\Utility;

class Logger
{
    private $_name = null;
    private static $instance = null;
    private $_type = null;

    public function __construct($appName)
    {
        if (!empty($appName)) {
            $this->_name = $appName;
        }
    }

    static public function Instance($appName = '')
    {
        if (empty($appName) && defined('APP_NAME')) {
            $appName = APP_NAME;
        }
        if (empty(self::$instance)) {
            self::$instance = new static($appName);
        }
        return self::$instance;
    }

    public function info($s)
    {
        $this->_type = 'info';
        $this->output("[INFO]\t$s");
    }

    public function error($s)
    {
        $this->_type = 'error';
        $this->output("[ERROR]\t$s");
    }

    private function output($s)
    {
        $s = date("Y-m-d H:i:s +u") . ": $s\n";

        $dir = '/tmp/logs/www/' . str_replace('.', '/', strtolower($this->_name));
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($dir . '/' . date("Y-m-d") . '_' . $this->_type . '.log', $s, FILE_APPEND | LOCK_EX);
    }


    public function access($dateTime, $logType, $eventType, $responseTime = 0, $serverIp = '127.0.0.1', $clientIp = '127.0.0.1', $url = 'null', array $param = [], $code = 0, $msg = '')
    {

        if (empty($dateTime) || empty($logType) || empty($eventType)) {
            return false;
        }
        if (!in_array($logType, ['error', 'info'])) {
            return false;
        }
        $this->_type = 'access';
        $param = Utility::encode($param);
        $responseTime = round($responseTime, 4);
        $msg = empty($msg) ? 'null' : str_replace("\n", "<br/>", $msg);
        //请求时间||数据类型||事件类型||响应时间||出错码||客户端IP||请求URI||请求参数||服务端IP||数据信息
        $log = "{$dateTime}||{$logType}||{$eventType}||{$responseTime}||{$code}||{$clientIp}||{$url}||{$param}||{$serverIp}||{$msg}\n";

        $this->output($log);
    }
}