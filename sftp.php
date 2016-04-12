<?php
class Sftp {
    private $connection;
    private $sftp;

    public function __construct($host, $port=22) {
        $this->connection = ssh2_connect($host, $port);
        if (! $this->connection)
            throw new \Exception("Could not connect to $host on port $port.");
		return $this;
    }

	static function instance($host, $port=22){
		new self($host, $port);
	}

    public function login($username, $password) {
        if (! ssh2_auth_password($this->connection, $username, $password))
            throw new \Exception("Could not authenticate with username $username " .
                                "and password $password.");

        $this->sftp = ssh2_sftp($this->connection);
        if (! $this->sftp)
            throw new \Exception("Could not initialize SFTP subsystem.");
    }

    public function uploadFile($local_file, $remote_file) {
        $sftp = $this->sftp;
        $stream = fopen("ssh2.sftp://$sftp$remote_file", 'w');

        if (! $stream)
            throw new \Exception("Could not open file: $remote_file");

        $data_to_send = file_get_contents($local_file);
        if ($data_to_send === false)
            throw new \Exception("Could not open local file: $local_file.");

        if (fwrite($stream, $data_to_send) === false)
            throw new \Exception("Could not send data from file: $local_file.");

        fclose($stream);
    }

    public function iteratorFile($remote_file) {
        $sftp = $this->sftp;
        $stream = fopen("ssh2.sftp://$sftp$remote_file", 'r');

        if (! $stream)
            throw new \Exception("Could not open file: $remote_file");

		while($line = fgets($stream) ){
			yield $line;
		}
        fclose($stream);
    }
}
