run:
	php -S 0:5000 -t .
	curl -D- http://127.0.0.1:5000/header.php
