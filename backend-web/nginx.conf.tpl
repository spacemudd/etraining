upstream app {
    server prod-alb-internal-1645638095.eu-central-1.elb.amazonaws.com:9000;
    #server backend:9000;
}

server {
  listen 8085;
  listen [::]:8085;
  server_name _;

  server_tokens off;
  fastcgi_hide_header X-Powered-By;
  proxy_hide_header X-Powered-By;
  fastcgi_hide_header X-Powered-By;
  proxy_hide_header X-Powered-By;

  client_max_body_size 500M;
  large_client_header_buffers 4 32k;
  keepalive_timeout 65;

  # Write temporary files to /tmp so they can be created as a non-privileged user
  client_body_temp_path /tmp/client_temp;
  proxy_temp_path /tmp/proxy_temp_path;
  fastcgi_temp_path /tmp/fastcgi_temp;
  uwsgi_temp_path /tmp/uwsgi_temp;
  scgi_temp_path /tmp/scgi_temp;

  gzip on;
  gzip_proxied any;
  gzip_types text/plain application/xml text/css text/js text/xml application/x-javascript text/javascript application/json application/xml+rss;
  gzip_vary on;
  gzip_disable "msie6";

  location /healthcheck {
    return 200;
  }

  location / {
    proxy_pass http://app;
    
    # Strip port from Host header if present
    set $host_without_port $host;
    if ($host ~* ^(.+):(\d+)$) {
      set $host_without_port $1;
    }
    proxy_set_header Host $host_without_port;
    
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    
    # Pass through or set X-Forwarded-Proto (preserve from ALB or default to https)
    set $forwarded_proto $http_x_forwarded_proto;
    if ($forwarded_proto = "") {
      set $forwarded_proto "https";
    }
    proxy_set_header X-Forwarded-Proto $forwarded_proto;
    
    # Pass through or set X-Forwarded-Port (preserve from ALB or default to 443)
    set $forwarded_port $http_x_forwarded_port;
    if ($forwarded_port = "") {
      set $forwarded_port "443";
    }
    proxy_set_header X-Forwarded-Port $forwarded_port;
    
    # Pass through or set X-Forwarded-Host (preserve from ALB or use current host without port)
    set $forwarded_host $http_x_forwarded_host;
    if ($forwarded_host = "") {
      set $forwarded_host $host_without_port;
    }
    proxy_set_header X-Forwarded-Host $forwarded_host;
  }

  location ~ /\. {
    deny  all;
  }
}
