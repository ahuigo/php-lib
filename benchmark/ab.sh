# auto create web's benchmark via ab
for ip in 192.168.0.1 192.168.0.2;
do
        echo $'\n'"$ip" >> bench.txt
		url='xxx.com'
        for c in `seq 50 50 400`; do
                # ab -c $c -n 5000 $url;
                cmd="ab -r -c $c -n 3000 -t 20 $url ";
                echo $cmd;
                rate=$(eval $cmd | grep -F 'Requests per second:' | awk '{print $4}');
                echo -n "$rate," >> bench.txt;
                cat bench.txt;
        done

done
