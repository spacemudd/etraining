upstream app {
    server backend-web:8085;
}
upstream wordpress-ar {
    server wordpress-ar:80;
}
upstream wordpress-en {
    server wordpress-en:80;
}

# limit_req_zone $binary_remote_addr zone=WPRATELIMIT:10m rate=2r/s;

server {
  listen 443 ssl http2;
  server_name cubtar.com www.cubtar.com;
  server_tokens off;
  proxy_http_version 1.1;
  fastcgi_hide_header X-Powered-By;
  proxy_hide_header X-Powered-By;
  fastcgi_param HTTP_PROXY "";
  autoindex off;
  add_header Strict-Transport-Security "max-age=31536000";
  add_header X-Content-Type-Options nosniff;
  add_header X-XSS-Protection "1; mode=block";

  client_max_body_size 30M;
  ssl_certificate /etc/letsencrypt/cert.crt;
  ssl_certificate_key /etc/letsencrypt/cert.key;

  # deny running scripts inside writable directories
  location ~* /(images|cache|media|logs|tmp|wp-uploads|uploads|wp-includes|akismet)/.*\.(php|pl|py|jsp|asp|sh|cgi)$ {
    return 403;
  }

  location / {
    proxy_pass https://app;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
  }

  location /ar/blog {
    proxy_pass  http://wordpress-ar;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
  }

  location /en/blog {
    proxy_pass  http://wordpress-en;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
  }

  # Block access to dot files
  location ~ /\. {
    deny  all;
  }
}

server {
  listen      80;
  listen [::]:80;

  server_tokens off;
  fastcgi_hide_header X-Powered-By;
  proxy_hide_header X-Powered-By;

  location / {
    rewrite ^ https://$host$request_uri? permanent;
  }
}
