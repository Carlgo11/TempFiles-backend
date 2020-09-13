# TempFiles Backend
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/Carlgo11/Tempfiles-backend/Test%20PHPUnit?style=for-the-badge)](https://github.com/Carlgo11/Tempfiles-backend/actions)
[![GitHub](https://img.shields.io/github/license/carlgo11/tempfiles-backend?style=for-the-badge)](https://github.com/Carlgo11/TempFiles-backend/blob/master/LICENSE)
[![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/carlgo11/tempfiles-backend?style=for-the-badge)](https://github.com/Carlgo11/TempFiles-backend/releases)

## API calls :mega:
A list of available API calls can be found over at [Postman](https://documenter.getpostman.com/view/1675224/SW7ezkZn).

## Contributing :writing_hand:

To edit the TempFiles-Backend code you need:
* An IDE/Editor that supports [EditorConfig](https://editorconfig.org/#download).
* Composer
* PHP 7.2 (Or later)
* PHP extensions: curl, xsl, mbstring

To install all dependencies, run the following command:
```BASH
composer install
```

## Deployment via Docker Compose

1. Copy nginx.conf.  
    Set up a virtual server config for nginx to use.
    ```NGINX
    # API
    server {
        listen 5392;
        server_name _;
        root "/app";
        index API.php;
        rewrite ^/(.*)+$ /API.php?$1;
        include /opt/docker/etc/nginx/vhost.common.d/*.conf;
    }
    
    # Download
    server {
        listen 5393;
        server_name _;
        root "/app";
        index Download.php;
        rewrite ^/(.*)+$ /Download.php?$1;
        include /opt/docker/etc/nginx/vhost.common.d/*.conf;
    }
    ```
2. Create a docker-compose.yml file.  
    Load the previously created Nginx-file as a volume.
    ```YAML 
    version: '3.2'
    services:
      tmpfiles:
        image: carlgo11/tempfiles-backend
        ports:
          - "5392:5392"
          - "5393:5393"
        volumes:
          - ./resources/nginx.conf:/opt/docker/etc/nginx/vhost.conf
    
        environment:
          - PHP_POST_MAX_SIZE=128M
          - PHP_UPLOAD_MAX_FILESIZE=128M
        restart: always
    ```
3. Set up a second webserver as a reverse proxy for the docker container(s).
    ```NGINX
    # API config
    server {
            listen 443 ssl http2;
            server_name api.tempfiles.download;
    
            ssl_certificate <certificate path>;
            ssl_certificate_key <certificate key path>;
            ssl_ciphers 'CDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:ECDHE-ECDSA-AES256-SHA384:ECDHE-RSA-AES256-SHA384';

            # 100M = Total file upload limit of 100 MegaBytes.
            client_body_buffer_size 100M;
            client_max_body_size 100M;

            location / {
                    proxy_pass http://127.0.0.1:5392;
            }
    }
   
   # Download config
   server {
           listen 443 ssl http2;
           server_name d.tempfiles.download;
   
           ssl_certificate <certificate path>;
           ssl_certificate_key <certificate key path>;
           ssl_ciphers 'CDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:ECDHE-ECDSA-AES256-SHA384:ECDHE-RSA-AES256-SHA384';

           location / {
                   proxy_pass http://127.0.0.1:5393;
           }
   }
   ```

## Manual Deployment :desktop_computer:

1. Install PHP, Nginx, Git, Composer  
   ```BASH
   sudo apt update
   sudo apt upgrade
   sudo apt install nginx php php-fpm composer php-curl php-mbstring git
   ```

2. Download the source code  
   ```BASH
   git clone https://github.com/Carlgo11/Tempfiles-backend.git
   cd Tempfiles-backend/
   ```

3. Download dependencies  
   ```BASH
   composer install --no-dev
   ```

4. Set file path  
   ```BASH
   nano src/com/carlgo11/tempfiles/config.php
   ```
   Change `'file-path'` to a suitable directory and create said directory.
   ```BASH
   mkdir /tempfiles # file path directory
   chown www-data:www-data /tempfiles -R
   chmod 0700 /tempfiles -R
   ```

5. Copy the Nginx configurations to the sites-available directory.  
   ```BASH
   cp ./ressouces/nginx/*.conf > /etc/nginx/sites-available/
   ```

6. Generate certificates.  
   For HTTPS to work you'll need a certificate. Due to the many different certificate companies and their different ways of generating certificates I won't go into that in this text.
   When you have a certificate, change the following lines in both nginx configs:
   ```BASH
   nano /etc/nginx/sites-available/*.conf
   ```
   ```
   ssl_certificate {path_to_cert}/cert.pem; #Change path
   ssl_certificate_key {path_to_key}/privkey.pem; #Change path
   ```

7. Restart Nginx  
   ```BASH
   sudo systemctl restart nginx
   ```
