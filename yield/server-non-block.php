<?php
/**
 * Refer to:
 * 	http://hilojack/p/php-coroutines
 *
 **/
class SystemCall {
	protected $callback;

	public function __construct(callable $callback) {
		$this->callback = $callback;
	}

	public function __invoke(Task $task, Scheduler $scheduler) {
		$callback = $this->callback; // Can't call it directly in PHP :/
		return $callback($task, $scheduler);
	}
}
function getTaskId() {
    return new SystemCall(function(Task $task, Scheduler $scheduler) {
        $task->setSendValue($task->getTaskId());
        $scheduler->schedule($task);
    });
}
function newTask(Generator $coroutine) {
	return new SystemCall(
		function(Task $task, Scheduler $scheduler) use ($coroutine) {
			$task->setSendValue($scheduler->newTask($coroutine));
			$scheduler->schedule($task);
		}
	);
}
function waitForRead($socket) {
    return new SystemCall(
        function(Task $task, Scheduler $scheduler) use ($socket) {
            $scheduler->waitForRead($socket, $task);
        }
    );
}

function waitForWrite($socket) {
    return new SystemCall(
        function(Task $task, Scheduler $scheduler) use ($socket) {
            $scheduler->waitForWrite($socket, $task);
        }
    );
}

class Task {
	protected $taskId;
	protected $coroutine;
	protected $sendValue = null;
	protected $beforeFirstYield = true;

	public function __construct($taskId, Generator $coroutine) {
		$this->taskId = $taskId;
		$this->coroutine = $coroutine;
	}

	public function getTaskId() {
		return $this->taskId;
	}

	public function setSendValue($sendValue) {
		$this->sendValue = $sendValue;
	}

	public function run() {
		if ($this->beforeFirstYield) {
			$this->beforeFirstYield = false;
			return $this->coroutine->current();
		} else {
			$retval = $this->coroutine->send($this->sendValue);
			$this->sendValue = null;
			return $retval;
		}
	}
	public function isFinished() {
		return !$this->coroutine->valid();
	}
}
class Scheduler {
	protected $maxTaskId = 0;
	protected $taskMap = []; // taskId => task
	protected $taskQueue;

	public function __construct() {
		$this->taskQueue = new SplQueue();
	}

	// resourceID => [socket, tasks]
	protected $waitingForRead = [];
	protected $waitingForWrite = [];

	public function waitForRead($socket, Task $task) {
		if (isset($this->waitingForRead[(int) $socket])) {
			$this->waitingForRead[(int) $socket][1][] = $task;
		} else {
			$this->waitingForRead[(int) $socket] = [$socket, [$task]];
		}
	}

	public function waitForWrite($socket, Task $task) {
		if (isset($this->waitingForWrite[(int) $socket])) {
			$this->waitingForWrite[(int) $socket][1][] = $task;
		} else {
			$this->waitingForWrite[(int) $socket] = [$socket, [$task]];
		}
	}

	protected function ioPoll($timeout) {
		$rSocks = [];
		foreach ($this->waitingForRead as list($socket)) {
			$rSocks[] = $socket;
		}

		$wSocks = [];
		foreach ($this->waitingForWrite as list($socket)) {
			$wSocks[] = $socket;
		}

		$eSocks = []; // dummy

		if(empty($rSocks) && empty($wSocks)){
			echo "no socks!\n";
			return;
		}
		if (!stream_select($rSocks, $wSocks, $eSocks, $timeout)) {
			return;
		}

		foreach ($rSocks as $socket) {
			list(, $tasks) = $this->waitingForRead[(int) $socket];
			unset($this->waitingForRead[(int) $socket]);

			foreach ($tasks as $task) {
				$this->schedule($task);
			}
		}

		foreach ($wSocks as $socket) {
			list(, $tasks) = $this->waitingForWrite[(int) $socket];
			unset($this->waitingForWrite[(int) $socket]);

			foreach ($tasks as $task) {
				$this->schedule($task);
			}
		}
	}
	protected function ioPollTask() {
		while (true) {
			if ($this->taskQueue->isEmpty()) {
				$this->ioPoll(null);
			} else {
				$this->ioPoll(0);
			}
			yield;
		}
	}
	public function newTask(Generator $coroutine) {
		$tid = ++$this->maxTaskId;
		$task = new Task($tid, $coroutine);
		$this->taskMap[$tid] = $task;
		$this->schedule($task);
		return $tid;
	}

	public function schedule(Task $task) {
		$this->taskQueue->enqueue($task);
	}

	public function run() {
		$this->newTask($this->ioPollTask());
		while (!$this->taskQueue->isEmpty()) {
			$task = $this->taskQueue->dequeue();
			$retval = $task->run();
			//var_dump(['task run reutn'=>$retval]);

			if ($retval instanceof SystemCall) {
				$retval($task, $this);
				continue;
			}

			if ($task->isFinished()) {
				unset($this->taskMap[$task->getTaskId()]);
			} else {
				$this->schedule($task);
			}
		}
	}

}

//Lets try out scheduler with an example:
function server($port) {
	echo "Starting server at port $port...\n";

	//$socket = @stream_socket_server("tcp://localhost:$port", $errNo, $errStr);
	$socket = @stream_socket_server("tcp://0:$port", $errNo, $errStr);
	if (!$socket) throw new Exception($errStr, $errNo);

	stream_set_blocking($socket, 0);

	while (true) {
		echo "server:waitForRead\n";
		yield waitForRead($socket);
		$clientSocket = stream_socket_accept($socket, 0);

		echo "server:handleClient\n";
		yield newTask(handleClient($clientSocket));
	}
}

function handleClient($socket) {
	echo "handclient:waitForRead\n";
	yield waitForRead($socket);
	echo "handclient:Read\n";
	$data = fread($socket, 8192);

	$msg = "Received following request:\n\n$data";
	$msgLength = strlen($msg);

	$response = <<<RES
HTTP/1.1 200 OK\r
Content-Type: text/plain\r
Content-Length: $msgLength\r
Connection: close\r
\r
$msg
RES;

	echo "handclient:waitForWrite\n";
	yield waitForWrite($socket);
	echo "handclient:Write\n";
	fwrite($socket, $response);

	fclose($socket);
}

$scheduler = new Scheduler;
$scheduler->newTask(server(8000));
$scheduler->run();
