<?php
/**
 * Created by PhpStorm.
 * User: xiemin
 * Date: 2015/4/16
 * Time: 18:21
 */

class MutiProcessSSH2 {

    protected $connection;
    public function __construct($ip, $user, $pass)
    {
        $this->connection = ssh2_connect($ip, 22);
        ssh2_auth_password($this->connection, $user, $pass);
    }

    public function __destruct()
    {

    }

    public function getRemoteResult($cmd)
    {
        $stream = ssh2_exec($this->connection, $cmd);
        stream_set_blocking($stream, true);
        $content =  stream_get_contents($stream);
        return explode("\n", $content);
    }


}