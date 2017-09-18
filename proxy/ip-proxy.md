# ip-proxy
免费代理
http://www.xicidaili.com/nn/1
http://www.kuaidaili.com/free/
http://cn-proxy.com
http://www.goubanjia.com/
http://ip.zdaye.com/

    for i in `seq 10` ;
    do
        wget http://www.kuaidaili.com/free/inha/$i/ -O - | grep -P '"IP"|"PORT"' | gawk 'flag==0 && match($0,/>([[:digit:].]+)<\/td>/, m){flag=1;printf("%s:", m[1])} flag==1 &&match($0, />([[:digit:]]+)<\/td>/, m){flag=0;print m[1]}' | xargs -n1 -I % sh -c "curl -m 2 -s 'http://1212.ip138.com/ic.asp' -x %  1>/dev/null && echo % 此代理可用"
    done

    curl -s 'http://www.xicidaili.com/wn' | grep -A 2 '<td><img'  | gawk  'match($0, />([[:digit:].]+)<\/td>/, m)&&flag!=1{flag=1;printf("%s:", m[1]);next}flag==1 && match($0, />([[:digit:].]+)<\/td>/, m){print m[1];flag=0;}' | xargs -n1 -I %   sh -c "curl -m 2 -s 'http://1212.ip138.com/ic.asp' -x %  1>/dev/null && echo % 此代理可用"

