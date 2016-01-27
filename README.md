# RROP (Request/Reply Online Proxy)
Allows remote clients to access you HTTP server wherever you are. All you need is to run a php script on you local 
machine and a proxy server on an publicly accessible server.  

## Proxy server (nginx) config

 * Websocket server
 
```
    server {
        listen       80;
        server_name  rrop.mad.uk.to;

        location / {
            proxy_pass http://127.0.0.1:1338;
            proxy_http_version 1.1;
            proxy_set_header Upgrade $http_upgrade;
            proxy_set_header Connection "upgrade";
        }
    }
```

 * HTTP Proxy
 
``` 
    server {
        listen       80;
        server_name  *.rrop.mad.uk.to;

        location / {
            proxy_pass   http://127.0.0.1:1337;
        }
    }
```

 * Run `bin\server.php` on that machine

## Local setup

 * Run `bin\client.php` on your machine
