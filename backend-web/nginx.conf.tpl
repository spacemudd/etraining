upstream app {
    server prod-alb-internal-1645638095.eu-central-1.elb.amazonaws.com;
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
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
  }

  location ~ /\. {
    deny  all;
  }
}
