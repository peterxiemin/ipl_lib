<?php

/**
 * Created by PhpStorm.
 * User: xiemin
 * Date: 2015/4/13
 * Time: 14:33
 */

//define('STDIN', fopen('php://stdin', 'r'));
//define('STDOUT', fopen('php://stdout', 'w'));
//define('STDERR', fopen('php://stderr', 'w'));

class MutiProcess
{


    /* child process count */
    const PROCESS_NUM = 1;

    const PROCESS_SHUTDOWN = 0;

    const PROCESS_RUNNING = 1;

    public static $daemonize;

    public static $stdoutFile = '/dev/null';

    public static $FILENAME;

    protected static $workers = array();
    protected static $worker = array();
    protected static $childprocesses = array();
    protected static $task_data_array;

    public function __construct()
    {
    }

    public function __destruct()
    {
    }

    public static function parseCommand()
    {
        global $argv;
        $exec = $argv[0];
        if (!isset($argv[1])) {
            exit("Usage: php yourphpfile.php {start|stop|restart|reload|status}\n");
        }
        $command1 = trim($argv[1]);
        if (!isset($argv[2]))
            $command2 = '';
        else
            $command2 = trim($argv[2]);

        switch ($command1) {
            case 'start'    :
                if ($command2 == '-d') {
                    self::$daemonize = 1;
                }
                if (self::getMasterPid() !== false) {
                    exit("server has started\n");
                }
                break;
            case 'restart'  :
            case 'stop'     :
                $master_pid = self::getMasterPid();
                $master_pid && posix_kill($master_pid, SIGINT);
                $timeout = 5;
                $start_time = time();
                while(1) {
                    // 检查主进程是否存活
                    $master_is_alive = $master_pid && posix_kill($master_pid, 0);
                    if ($master_is_alive) {
                        // 检查是否超过$timeout时间
                        if (time() - $start_time >= $timeout) {
                            echo "Workerman stop fail\n";
                            exit(0);
                        }
                        usleep(10000);
                        continue;
                    }
                    else {
                        break;
                    }
                }

                if ($command1 == 'restart') {
                    if ($command2 == '-d') {
                        self::$daemonize = 1;
                    }
                    break;
                }
                exit(0);
                break;

            case 'reload'   :
                /* 热加载 */
                exit(0);
                break;
            case 'status'   :
                exit(0);
                break;
            default:
                exit("Usage: php yourphpfile.php {start|stop|restart|reload|status}\n");
        }
    }

    public static function stopAll()
    {
        $pid = posix_getpid();
        if (self::getMasterPid() == $pid) {
            foreach (self::$workers as $pid => $worker) {
                if ($worker['status'] === self::PROCESS_RUNNING) {
                    posix_kill($pid, SIGINT);
                    $worker['status'] === self::PROCESS_SHUTDOWN;
                }
            }
            self::exitServ();
        }
        exit(0);
    }

    public static function setTicks()
    {
        declare(ticks = 1);
    }

    public static function initConf()
    {
        /* 读取配置文件 */
        self::$FILENAME = substr(__FILE__,  0, (strlen(__FILE__) - 4)).".pid";
    }


    public static function setSignal()
    {
        // stop
        pcntl_signal(SIGINT,  array('MutiProcess', 'sigHandler'), false);
        // reload
        pcntl_signal(SIGUSR1, array('MutiProcess', 'sigHandler'), false);
        // status
        pcntl_signal(SIGUSR2, array('MutiProcess', 'sigHandler'), false);
        // ignore
        pcntl_signal(SIGPIPE, SIG_IGN, false);
    }

    public static function sigHandler($signo)
    {
        switch ($signo) {
            case SIGINT:
                echo "SIGINT\n";
                self::stopAll();
                break;
            case SIGUSR1:
                self::reload();
                break;
            case SIGUSER2:
                break;
            default:
                // 处理所有其他信号
                echo "ignore singnal.";
        }
    }

    public static function daemoNize()
    {
        if (self::$daemonize) {
            $pid = pcntl_fork();
            if ($pid < 0) {
                throw new Exception("1st fork failed");
            }
            if ($pid > 0) {
                exit(0);
            }
            if(-1 == posix_setsid())
            {
                throw new Exception("setsid fail");
            }
            // fork again avoid SVR4 system regain the control of terminal
            $pid = pcntl_fork();
            if(-1 == $pid)
            {
                throw new Exception("fork fail");
            }
            elseif(0 !== $pid)
            {
                exit(0);
            }
        }

    }

    public static function masterStart()
    {
        self::setTicks();
        self::setSignal();

        for ($i = 0; $i < self::PROCESS_NUM; $i++) {
            $pid = pcntl_fork();
            //父进程和子进程都会执行下面代码
            if ($pid == -1) {
                //错误处理：创建子进程失败时返回-1.
                throw new Exception("master fork failed");
                exit(0);
            } else if ($pid > 0) {
                self::$worker['status'] = self::PROCESS_RUNNING;
                self::$workers[$pid] = self::$worker;
            } else {
                /* child worker process */
                self::setTaskProcess();
                exit(0);
            }
        }
    }

    public static function Reload()
    {
        $pid = posix_getpid();
        if (self::getMasterPid() == $pid) {

        }
        else {

        }
    }

    public static function monitorWorkers()
    {
        while (1) {
            pcntl_signal_dispatch();
            $pid = pcntl_wait($status, WUNTRACED);
            if ($pid > 0) {
                self::$workers[$pid]['status'] = self::PROCESS_SHUTDOWN;
            } else {
                echo "all child process over\n";
                break;
            }
        }
    }

    public static function exitServ()
    {
        unlink(self::$FILENAME);
    }

    public static function disPlayUI()
    {
        echo "################################\n";
        echo "################################\n";
        echo "#####MASTER-WORKER-MODULE#######\n";
        echo "################################\n";
        echo "################################\n";
    }

    public static function saveMasterPid()
    {
        /* 获取masterid */
        file_put_contents(self::$FILENAME, posix_getpid());
    }

    public static function getMasterPid()
    {
        if (file_exists(self::$FILENAME)) {
            $ret = file_get_contents(self::$FILENAME);
            return $ret;
        }
        return false;
    }

    public static function runAll()
    {
        self::initConf();
        self::parseCommand();
        self::daemoNize();
        self::resetStd();
        self::setSignal();
        self::disPlayUI();
        self::saveMasterPid();
        self::setShareVar();
        self::putData2Memcacheq();
        self::masterStart();
        self::monitorWorkers();
        self::exitServ();
    }

    public static function resetStd()
    {
        if(!self::$daemonize)
        {
            return;
        }
        global $STDOUT, $STDERR;
        $handle = fopen(self::$stdoutFile,"a");
        if($handle)
        {
            unset($handle);
            @fclose(STDOUT);
            @fclose(STDERR);
            $STDOUT = fopen(self::$stdoutFile,"a");
            $STDERR = fopen(self::$stdoutFile,"a");
        }
        else
        {
            throw new Exception('can not open stdoutFile ' . self::$stdoutFile);
        }
    }

    public static function setTaskProcess()
    {
        foreach (glob(__DIR__."/workerdir/*") as $php_file) {
            if (file_exists($php_file)) {
                $cname = self::getClassName($php_file);
                $obj = new $cname();
                $obj->realRun();
            }
        }
    }

    public static function getClassName($filename)
    {
        $pos = strrpos($filename, "/");
        $ret = substr($filename, $pos + 1);
        return trim($ret, ".php");
    }

    public static function setShareVar()
    {
//        self::$task_data_array = array();
//        $sum_count = 11136406;
//        $step = ceil($sum_count / self::PROCESS_NUM);
//        for ($i = 1; $i < $sum_count; $i+=$step) {
//            $arr = array('min'  => $i, 'max'    => $i+$step);
//            array_push(self::$task_data_array, $arr);
//        }
        self::$task_data_array = array();
        for ($i = 0; $i < 1; $i++) {
            array_push(self::$task_data_array, "10.32.28.".($i+50));
        }
    }

    public static function putData2Memcacheq()
    {
        global $conf_store;
        $memc = new MutilProcessMemcq($conf_store);
        if (is_array(self::$task_data_array)) {
            /* 请这里分配给客户端的任务*/
            foreach (self::$task_data_array as $task_data) {
                $memc->set(json_encode($task_data), 0, 0);
            }
        }
    }
}

require_once(__DIR__."/__init.php");
MutiProcess::runAll();



?>