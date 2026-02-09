# 使用官方PHP 8.3 Apache镜像作为基础镜像
FROM php:8.3-apache

# 设置工作目录
WORKDIR /var/www/html

# 将当前目录下的index.php复制到容器的工作目录
COPY index.php /var/www/html/

# 可选：复制整个项目目录（如果需要复制所有文件，取消下面的注释）
# COPY . /var/www/html/

# 可选：如果需要其他PHP扩展，可以在这里安装
# RUN docker-php-ext-install pdo pdo_mysql

# 启用Apache的rewrite模块（如果网站使用URL重写）
RUN a2enmod rewrite

# 设置Apache配置文件，确保正确监听到80端口
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 暴露80端口
EXPOSE 80

# 使用Apache作为前台进程运行
CMD ["apache2-foreground"]
