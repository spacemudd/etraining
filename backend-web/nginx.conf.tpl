fastcgi_cache_path /dev/shm levels=1:2 keys_zone=laravel:100m;
fastcgi_cache_key "$scheme$request_method$host$request_uri$query_string";

upstream app {
    #server internal-prod-alb-internal-596022634.eu-central-1.elb.amazonaws.com;
    server backend;
}

server {
  listen 8085;
  listen [::]:8085;
  server_name app.ptc-ksa.com;

  server_tokens off;
  fastcgi_hide_header X-Powered-By;
  proxy_hide_header X-Powered-By;

  gzip on;
  gzip_http_version  1.1;
  # Compression level (1-9).
  # 5 is a perfect compromise between size and cpu usage, offering about
  # 75% reduction for most ascii files (almost identical to level 9).
  gzip_comp_level    5;

  # Don't compress anything that's already small and unlikely to shrink much
  # if at all (the default is 20 bytes, which is bad as that usually leads to
  # larger files after gzipping).
  gzip_min_length    256;

  # Compress data even for clients that are connecting to us via proxies,
  # identified by the "Via" header (required for CloudFront).
  gzip_proxied       any;

  # Tell proxies to cache both the gzipped and regular version of a resource
  # whenever the client's Accept-Encoding capabilities header varies;
  # Avoids the issue where a non-gzip capable client (which is extremely rare
  # today) would display gibberish if their proxy gave them the gzipped version.
  gzip_vary          on;

  # Compress all output labeled with one of the following MIME-types.
  gzip_types
   application/atom+xml
   application/javascript
   application/json
   application/rss+xml
   application/vnd.ms-fontobject
   application/x-font-ttf
   application/x-web-app-manifest+json
   application/xhtml+xml
   application/xml
   font/opentype
   image/svg+xml
   image/x-icon
   text/css
   text/plain
   text/x-component;
   # text/html is always compressed by HttpGzipModule

  location ~* \.(jpg|jpeg|png|gif|ico|css|js|eot|ttf|woff|woff2)$ {
    expires max;
    add_header Cache-Control public;
    add_header Access-Control-Allow-Origin *;
    access_log off;
    try_files $uri $uri/ /index.php?$query_string;
  }

  location /healthcheck {
    return 200;
  }

  location / {
    proxy_pass http://app;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
  }

  location ~ /\. {
    deny  all;
  }
}
