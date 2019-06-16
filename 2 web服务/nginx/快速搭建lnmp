快速搭建lnmp＋redis

一 安装虚拟机
1 下载安装vitualbox 或 vm
2 下载centos镜像文件（下文以centos7 为例）
3 新建虚拟机
4 其他
	4.1 开启网卡 
	vi /etc/sysconfig/network-scripts/ifcfg-eXXXXX 文件中,记得先把onboot改成yes
	service network restart

	4.2 yum install wget -y

 	4.3 修改yum源配置， 按照教程来就好了
	http://mirrors.163.com/.help/centos.html  推荐
	http://mirrors.aliyun.com/help/centos  

	4.4 安装常用工具包 
	yum install net-tools // 包含ifconfig，netstat等命令
	yum groupinstall "Development tools" //编译环境
	
二 安装nginx
	wget http://nginx.org/download/nginx-1.11.5.tar.gz
	tar xzvf nginx-1.11.5.tar.gz
	cd nginx-1.11.5
	yum install -y pcre-devel openssl-devel  // 不安装的话，待会编译会报错
	有兴趣的可以看下 "./configure --help" 里面有具体的配置参数及说明
	./configure --prefix=/usr/local/nginx-1.11.5 --with-http_ssl_module --with-pcre
	make && make install

	安装完成，设置软连接
	ln -s /usr/local/nginx-1.11.5/sbin/nginx /usr/local/bin/nginx

	备份配置文件 
	cp /usr/local/nginx-1.11.5/conf/nginx.conf /usr/local/nginx-1.11.5/conf/nginx.conf.bak
	修改配置文件
	vi /usr/local/nginx-1.11.5/conf/nginx.conf
	++++++++++++ nginx.conf start ++++++++++++
	user  nginx;
	worker_processes  1;
	pid  /var/run/nginx.pid;

	events {
	    worker_connections  1024;
	}
	http {
	    include       mime.types;
	    default_type  application/octet-stream;
	    sendfile        on;
	    keepalive_timeout  65;
	    server {
	        listen       80;
	        server_name  www.test.com;
	        access_log /data/logs/nginx_test.access.log;
	        error_log  /data/logs/nginx_test.error.log;
	        location / {
	            root   /data/www/test;
	            index  index.html index.htm;
	        }
	    }
	}
	++++++++++++ nginx.conf end ++++++++++++
	mkdir /data/logs -p //创建需要的目录
	useradd nginx -s /sbin/nologin  // 创建用户

	开启nginx
	nginx -c /usr/local/nginx-1.11.5/conf/nginx.conf

	mkdir /data/www/test/ -p //创建需要的目录
	vi /data/www/test/index.html
	++++++++++++ index.html start ++++++++++++
	hello world
	++++++++++++ index.html end ++++++++++++

	在本机编辑host文件，添加一条：
	虚拟主机IP www.test.com

	现在可以打开浏览器，输入： http://www.test.com/

三 安装php 
	1 下载源码包 http://php.net/downloads.php
	2 解压并进入目录 
	tar xzcf php-7.0.11.tar.gz
	cd php-7.0.11
	
	3 安装相关的库文件
	yum install -y libxml2-devel 

	4 编译， 安装
	./configure --prefix=/usr/local/php-7.0.11 --with-config-file-path=/usr/local/php-7.0.11/lib --enable-fpm --enable-mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd
	make && make install
	cp php.ini-production /usr/local/php-7.0.11/lib/php.ini

	5 设置软连接
	ln -s /usr/local/php-7.0.11/bin/php /usr/local/bin/php
	ln -s /usr/local/php-7.0.11/bin/phpize /usr/local/bin/phpize
	ln -s /usr/local/php-7.0.11/bin/pecl /usr/local/bin/pecl
	
	6 fpm 的配置
	cp /usr/local/php-7.0.11/etc/php-fpm.conf.default /usr/local/php-7.0.11/etc/php-fpm.conf
	cp /usr/local/php-7.0.11/etc/php-fpm.d/www.conf.default /usr/local/php-7.0.11/etc/php-fpm.d/www.conf
	vi /usr/local/php-7.0.11/etc/php-fpm.d/www.conf
	++++++++++++ www.conf start ++++++++++++
		[www]
		user = php
		group = php
		listen = 127.0.0.1:9000
		pm = dynamic
		pm.max_children = 5
		pm.start_servers = 2
		pm.min_spare_servers = 1
		pm.max_spare_servers = 3
	++++++++++++ www.conf end ++++++++++++
	修改 vi /usr/local/php-7.0.11/etc/php-fpm.conf
	pid = /var/run/php-fpm.pid

	ln -s /usr/local/php-7.0.11/sbin/php-fpm /usr/local/bin/php-fpm

	7 开启fpm进程
	useradd php -s /sbin/nologin  // 添加用户
	php-fpm

	8 查看端口
	netstat -lntp	// 看到端口9000开启了

四 联合nginx和php
1 重新配置 vi /usr/local/nginx-1.11.5/conf/nginx.conf 
	++++++++++++ nginx.conf start ++++++++++++
	...
	server {
	    listen       80;
	    server_name  www.test.com;
	    access_log /data/logs/nginx_test.access.log;
	    error_log  /data/logs/nginx_test.error.log;
	    root   /data/www/test;

	    location / {
	        index  index.php;
	    }

	    location ~ \.php$ {
	        fastcgi_pass   127.0.0.1:9000;
	        fastcgi_index  index.php;
	        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
	        include        fastcgi_params;
	    }
	}
	...
	++++++++++++ nginx.conf start ++++++++++++


2 新建 vi /data/www/test/index.php
	++++++++++++ index.php start ++++++++++++
	<?php
		echo 'hello world !!';
	++++++++++++ index.php start ++++++++++++

3 重启nginx
	nginx -s reload

4 打开浏览器，输入： http://www.test.com/

五 安装mysql
	1 设置yum源
	wget http://repo.mysql.com//mysql-community-release-el6-5.noarch.rpm
	rpm -ivh mysql-community-release-el6-5.noarch.rpm
	yum install -y mysql-server mysql-client mysql-devel

	// 开启mysql
	service mysqld start

	2 设置
	mysql -uroot -p
	初次安装mysql，root账户没有密码。
	>set password for 'root'@'localhost' = password('root');
	>grant all privileges on *.* to root@'%'identified by 'root';
	这样就可以在本机使用用户名root和密码登录虚拟机数据库了

六 在php中调用mysql
1 新建test数据库，再运行如下代码建立测试表及数据
	SET NAMES utf8;
	SET FOREIGN_KEY_CHECKS = 0;
	-- ----------------------------
	--  Table structure for `users`
	-- ----------------------------
	DROP TABLE IF EXISTS `users`;
	CREATE TABLE `users` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
	  `name` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '名字',
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
	-- ----------------------------
	--  Records of `users`
	-- ----------------------------
	BEGIN;
	INSERT INTO `users` VALUES ('1', '张三'), ('2', '李四');
	COMMIT;

	SET FOREIGN_KEY_CHECKS = 1;

2 编辑 vi /data/www/test/index.php
	++++++++++++ index.php start ++++++++++++
	<?php
		header ( 'Content-Type:text/html; charset=utf-8;' );
		$_mysqli = new mysqli('127.0.0.1','root','root','test');
		$_mysqli->set_charset('utf8');
		$_sql = "SELECT * FROM users";
		$_result = $_mysqli->query($_sql);

		while ($_row = $_result->fetch_assoc()) {
		      echo 'ID: '.$_row['id'].',  Name: '.$_row['name'].'<br />';
		}
		$_mysqli->close();
	++++++++++++ index.php end ++++++++++++
看到数据输出， 那么lnmp就算完成了

七 redis 
	// 下载安装
	wget http://download.redis.io/releases/redis-3.0.0.tar.gz
	tar zxvf redis-3.0.0.tar.gz 
	cd redis-3.0.0
	make
	ln -s /usr/local/src/redis-3.0.0/src/redis-server /usr/local/bin/redis-server
	ln -s /usr/local/src/redis-3.0.0/src/redis-cli /usr/local/bin/redis-cli

	// 启动
	mkdir /etc/redis
	vi /etc/redis/6770.conf
	++++++++++++ 6770.conf start ++++++++++++
	daemonize yes
	pidfile /var/run/redis-6770.pid
	port 6770
	timeout 0
	tcp-keepalive 0
	loglevel notice
	logfile ""
	databases 16
	dir /data/redis/6770
	++++++++++++ 6770.conf end ++++++++++++
	mkdir /data/redis/6770 -p
	redis-server /etc/redis/6770.conf

	// 链接客户端
	redis-cli -p 6770


	php 链接redis
	安装php的redis扩展
	git clone -b php7 https://github.com/phpredis/phpredis.git php-redis
	cd php-redis
	phpize
	./configure --with-php-config=/usr/local/php-7.0.11/bin/php-config
	make && make install

	vi /usr/local/php-7.0.11/lib/php.ini
	添加一行
	extension=redis.so

	重启php-fpm
	kill -USR2 `cat /var/run/php-fpm.pid`

	编辑 vi /data/www/test/index.php
	++++++++++++ index.php start ++++++++++++
	<?php
		$redis = new Redis();
		$redis->connect('127.0.0.1', 6770);
		$redis->set('a', 'aaa');
		$data = $redis->get('a');
		var_dump($data);
	++++++++++++ index.php start ++++++++++++

	再访问 http://www.test.com/


























                                













