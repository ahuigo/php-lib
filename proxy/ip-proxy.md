# ip-proxy
免费代理
http://www.xicidaili.com/nn/1
http://www.kuaidaili.com/free/
http://cn-proxy.com
http://www.goubanjia.com/
http://ip.zdaye.com/

    for i in `seq 10` ;
    do
        wget http://www.kuaidaili.com/free/inha/$i/ -O - | grep -P '"IP"|"PORT"' | gawk 'flag==0 && match($0,/>([[:digit:].]+)<\/td>/, m){flag=1;printf("%s:", m[1])} flag==1 &&match($0, />([[:digit:]]+)<\/td>/, m){flag=0;print m[1]}' | xargs -n1 -I % sh -c "curl -m 2 -s 'http://2017.ip138.com/ic.asp' -x %  1>/dev/null && echo % 此代理可用"
    done

    curl -s 'http://www.xicidaili.com/wn' | grep -A 2 '<td><img'  | gawk  'match($0, />([[:digit:].]+)<\/td>/, m)&&flag!=1{flag=1;printf("%s:", m[1]);next}flag==1 && match($0, />([[:digit:].]+)<\/td>/, m){print m[1];flag=0;}' | xargs -n1 -I %   sh -c "curl -m 2 -s 'http://2017.ip138.com/ic.asp' -x %  1>/dev/null && echo % 此代理可用"

while read i;
    sh -c "curl -m 3 -s 'https://www.baidu.com/s?ie=UTF-8&wd=%E6%9F%A5ip' -x $i 1>/dev/null && echo $i 可用"
done <  <(
cat<<MM
163.172.220.221:8888
118.91.163.142:80
59.153.17.58:80
154.73.28.35:80
86.105.51.105:3128
217.182.76.229:8888
125.212.207.121:3128
47.52.222.165:80
89.236.17.108:3128
85.29.136.212:8080
45.124.58.170:80
36.67.116.147:80
192.99.245.228:3128
124.104.141.23:80
82.165.135.253:3128
MM
)
