fastcgi_cache_path /dev/shm levels=1:2 keys_zone=laravel:100m;
fastcgi_cache_key "$scheme$request_method$host$request_uri$query_string";

upstream app {
    #server internal-prod-alb-internal-596022634.eu-central-1.elb.amazonaws.com;
    server backend:9000;
}

server {
  listen 8085;
  listen [::]:8085;
  server_name _;

   location / {
       fastcgi_cache laravel;
       fastcgi_ignore_headers Cache-Control;
       fastcgi_no_cache $http_authorization $cookie_laravel_session;
       fastcgi_cache_lock on;
       fastcgi_cache_lock_timeout 10s;
       add_header X-Proxy-Cache $upstream_cache_status;
       fastcgi_pass   app;
       fastcgi_index  index.php;
       fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
       fastcgi_read_timeout 900s;
       include        fastcgi_params;
   }

#  location / {
#    proxy_pass http://app;
#    proxy_set_header Host $host;
#    proxy_set_header X-Real-IP $remote_addr;
#    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
#    proxy_set_header X-Forwarded-Proto $scheme;
#  }

  location ~ /\. {
    deny  all;
  }
}
