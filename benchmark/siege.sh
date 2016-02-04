# auto create web's benchmark via ab

for ip in 192.168.0.1 192.168.0.2;
do
        echo $'\n'"$ip" >> bench.txt
		url='xxx.com'
        for c in `seq 50 50 400`; do
                cmd="siege -q -b -r 10 -c $c $url ";
                echo $cmd;
                rate=$(eval $cmd 2>&1 | grep -F 'Transaction rate:' | awk '{print $3}');
                echo -n "$rate," >> bench.txt;
                cat bench.txt;
        done
		echo ;

done
