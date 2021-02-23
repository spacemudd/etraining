FROM nginx

ARG FQDN=etraining.ptc-ksa.com
ENV FQDN "$FQDN"

COPY nginx.conf.tpl /etc/nginx/conf.d/default.conf.tpl

RUN sed -i 's/%fqdn_name%/'${FQDN}'/g' /etc/nginx/conf.d/default.conf.tpl

RUN cp /etc/nginx/conf.d/default.conf.tpl /etc/nginx/conf.d/default.conf

EXPOSE 8085
