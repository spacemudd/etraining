upstream app {
  server prod-alb-internal-1645638095.eu-central-1.elb.amazonaws.com;
}

server {
  listen 443 ssl http2;
  server_name app.jasarah-ksa.com www.app.jasarah-ksa.com;

  proxy_http_version 1.1;
  fastcgi_hide_header X-Powered-By;
  proxy_hide_header X-Powered-By;

  add_header Strict-Transport-Security "max-age=31536000";
  add_header X-Content-Type-Options nosniff;
  add_header X-XSS-Protection "1; mode=block";

  client_max_body_size 50M;
  ssl_certificate /etc/letsencrypt/cert.crt;
  ssl_certificate_key /etc/letsencrypt/cert.key;

  access_log /var/log/nginx/access.log combined;
  error_log /var/log/nginx/error.log warn;

  location / {
    proxy_pass http://app;  # Changed from https to http
    proxy_http_version 1.1;
    proxy_set_header Connection '';
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
  }

  error_page 403 /403-debug;
    location = /403-debug {
    return 403 "Forbidden: Nginx blocked this request.\n";
  }

  location ~ /\. {
    deny all;
  }
}

server {
  listen 80;
  listen [::]:80;
  location / {
    rewrite ^ https://$host$request_uri? permanent;
  }
}