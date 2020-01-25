# TempFiles Backend
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/Carlgo11/Tempfiles-backend/Test%20PHPUnit?style=for-the-badge)](https://github.com/Carlgo11/Tempfiles-backend/actions)

## API calls :mega:
A list of available API calls can be found over at [Postman](https://documenter.getpostman.com/view/1675224/SW7ezkZn).

## Local installation :desktop-computer:

1. Install PHP, Nginx, Git, MySQL
   ```BASH
   sudo apt update
   sudo apt upgrade
   sudo apt install nginx php php-fpm php-mysql git mysql
   ```

2. MySQL will ask you to generate a new password. Remember this password for later.
   ![MySQL Password input](https://cloud.githubusercontent.com/assets/3535780/25774895/c03b5a3c-3298-11e7-94ac-e10cc4d92b39.png)

3. Download the source code
   ```BASH
   git clone https://github.com/Carlgo11/Tempfiles-backend.git
   cd Tempfiles-backend/
   ```

4. Install the MySQL database.
   ```BASH
   mysql -u root -p < ./resources/install_mysql.sql
   ```

5. Sign in to MySQL and create a new user
   ```mysql
   mysql - u root -p
   CREATE USER 'tempfiles'@'localhost' IDENTIFIED BY '<password>';
   grant all privileges on tempfiles.files to `tempfiles`@`localhost`;
   flush privileges;
   exit;
   ```
   Optionally, if you want to set stricter permissions, The MySQL user only needs _SELECT_, _INSERT_, _UPDATE_, _DELETE_ permissions to the `files` table.

6. Copy the Nginx configurations to the sites-available directory.
   ```BASH
   cp ./ressouces/nginx/*.conf > /etc/nginx/sites-available/
   ```

7. Set the mysql password and username in the Nginx configurations.
   ```BASH
   nano /etc/nginx/sites-available/*.conf
   ```
   Change the fastcgi_param variable values. Each variable has a comment suffix that describes it's usage.
   ```
   # Env vars
   fastcgi_param ag44jc7aqs2rsup2bb6cx7utc 'localhost';	# hostname
   fastcgi_param hp7wz20wu4qfbfcmqywfai1j4 'tempfiles';	# username
   fastcgi_param mom8c5hrbn8c1r5lro1imfyax 'password';	# password
   fastcgi_param qb1yi60nrz3tjjjqqb7l2yqra 'tempfiles';	# database
   fastcgi_param rb421p9wniz81ttj7bdgrg0ub 'files';	# table
   ```

8. Generate certificates.
   For HTTPS to work you'll need a certificate. Due to the many different certificate companies and their different ways of generating certificates I won't go into that in this text.
   When you have a certificate, change the following lines in both nginx configs:
   ```
   ssl_certificate {path_to_cert}/cert.pem; #Change path
   ssl_certificate_key {path_to_key}/privkey.pem; #Change path
   ```

9. Restart Nginx
   ```BASH
   sudo systemctl restart nginx
   ```
