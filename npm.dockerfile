#FROM alpine:3.9
FROM node:lts-alpine

WORKDIR /var/www

RUN apk update

RUN apk add --no-cache python3

RUN apk add --no-cache bash
RUN apk add --update npm

RUN npm install
RUN npm watch

EXPOSE 6001

CMD ["-f","/dev/null"]
