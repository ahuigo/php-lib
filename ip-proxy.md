# ip-proxy
http://www.xicidaili.com/nn/1
curl -s 'http://www.xicidaili.com/wn' | grep -A 2 '<td><img'  | gawk  'match($0, /<td>([[:digit:].]+)<\/td>/, m)&&flag!=1{flag=1;printf("%s:", m[1]);next}flag==1 && match($0, /<td>([[:digit:].]+)<\/td>/, m){print m[1];flag=0;}' | xargs -n1 -I %   sh -c "curl -m 2 -s 'http://1212.ip138.com/ic.asp' -x %  1>/dev/null && echo % 此代理可用"
