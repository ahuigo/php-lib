function FindProxyForURL(url, host) {
    var PROXY = "PROXY 192.168.0.100:8080";
    var PROXY_US = "SOCKS 192.168.0.100:9090";
    var PROXY_US_NEW = "PROXY 192.168.0.102:8080";
    var DEFAULT = "DIRECT";
    if(shExpMatch(host, "*monitor.huajiao.com")) return "SOCKS 192.168.1.106:7890";
    return DEFAULT;
}
