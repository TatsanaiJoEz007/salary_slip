server {
    listen 80;
    server_name stm-salaryslip.com www.stm-salaryslip.com;

    root /var/www/stm-salaryslip.com;
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;  # ปรับตามเวอร์ชัน PHP ที่ติดตั้ง
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
