# TempFiles Backend
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/Carlgo11/Tempfiles-backend/Test%20PHPUnit?style=for-the-badge)](https://github.com/Carlgo11/Tempfiles-backend/actions)

## API calls :mega:
A list of available API calls can be found over at [Postman](https://documenter.getpostman.com/view/1675224/SW7ezkZn).

## Local installation :desktop_computer:

1. Install PHP, Nginx, Git
   ```BASH
   sudo apt update
   sudo apt upgrade
   sudo apt install nginx php php-fpm php-curl php-mbstring git
   ```

2. Download the source code
   ```BASH
   git clone https://github.com/Carlgo11/Tempfiles-backend.git
   cd Tempfiles-backend/
   ```

3. Set file path
   ```BASH
   nano src/com/carlgo11/tempfiles/config.php
   ```
   Change `'file-path'` to a suitable directory and create said directory.
   ```BASH
   mkdir /tempfiles # file path directory
   chown www-data:www-data /tempfiles -R
   chmod 0700 /tempfiles -R
   ```

4. Copy the Nginx configurations to the sites-available directory.
   ```BASH
   cp ./ressouces/nginx/*.conf > /etc/nginx/sites-available/
   ```

5. Generate certificates.
   For HTTPS to work you'll need a certificate. Due to the many different certificate companies and their different ways of generating certificates I won't go into that in this text.
   When you have a certificate, change the following lines in both nginx configs:
   ```BASH
   nano /etc/nginx/sites-available/*.conf
   ```
   ```
   ssl_certificate {path_to_cert}/cert.pem; #Change path
   ssl_certificate_key {path_to_key}/privkey.pem; #Change path
   ```

6. Restart Nginx
   ```BASH
   sudo systemctl restart nginx
   ```
