upstream app {
    server internal-prod-alb-internal-596022634.eu-central-1.elb.amazonaws.com:9000;
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
