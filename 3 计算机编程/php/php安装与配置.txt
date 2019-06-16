php

一 源码安装
1 下载源代码
2 解压 
	tar zxvf xxx.tar.gz
3 进入解压目录
4 配置
	./configure --help  ＃ 有基础一定要看看
	./configure --prefix=/usr/local/php  \
		--with-config-file-path=/usr/local/php/lib  \
		--with-mysqli  \
		--with-pdo-mysql  \
		--enable-dba=shared  \
		--with-pdo-sqlite=shared  \
		--with-libxml-dir  \
		--with-openssl  \
		--with-curl  \
		--with-gd  \
		--with-freetype-dir  \
		--with-jpeg-dir  \
		--with-png-dir  \
		--enable-gd-jis-conv  \
		--enable-gd-native-ttf  \
		--with-zlib  \
		--enable-zip  \
		--with-bz2  \
		--enable-fpm  \
		--with-fpm-user=www  \
		--with-fpm-group=www  \
		--enable-mbstring  \
		--with-mcrypt  \
		--with-mhash  \
		--enable-soap  \
		--enable-sockets  \
		--enable-bcmath  \
		--enable-shmop  \
		--enable-sysvsem  \
		--enable-pcntl  \
		--enable-ftp  \
		--disable-ipv6 \
		--with-iconv-dir

	注释：
	 	--prefix					# 设置安装路径, 默认 /usr/local, 强烈建议要设置该参数！！
		--with-config-file-path		# 设置配置文件路径,默认是 prefix/lib
		--with-mysqli				# 对数据库的操作
		--with-pdo-mysql			# 对数据库的操作
		--enable-dba=shared			# 对数据库的操作
		--with-pdo-sqlite=shared	# 对数据库的操作
		--with-libxml-dir			# 对xml文件的操作
		--with-openssl				# 对openssl的支持
		--with-curl					# 对curl的支持
		--with-gd					# 图像处理
		--with-freetype-dir			# 图像处理
		--with-jpeg-dir				# 对jpeg的支持
		--with-png-dir				# 对png的支持
		--enable-gd-jis-conv		# 对日文字体的支持
		--enable-gd-native-ttf		# 对字体的支持
		--with-zlib					# 对.gz文件的操作
		--enable-zip				# 对.zip文件的操作
		--with-bz2					# 对.bz2文件的操作
		--enable-fpm				# 启用php-fpm
		--with-fpm-user				# 设置php-fpm的用户
		--with-fpm-group		 	# 设置php-fpm的用户组
		--enable-mbstring 	 		# 多字节字符串操作
		--with-mcrypt				# 加密
		--with-mhash				# 加密
		--enable-soap				# soa
		--enable-sockets			# socket
		--enable-bcmath				# 支持精确数学运算
		--enable-shmop				# 共享内存
		--enable-sysvsem			# 支持系统命令，如“ls”
		--enable-pcntl				# 进程操作
		--enable-ftp				# 对ftp的支持
		--disable-ipv6	 			# 对ipv6的支持
		--with-iconv-dir			# 支持编码转换
q
5 编译
	make clean (如果之前在当前目录下编译过，需要执行该命令清理编译环境)
	make

6 安装
	make install 

* 程序运行时，按以下顺序搜索配置文件：
	1. -c 命令指定的位置
	2. PHPRC 环境变量指定的位置 
	3. 当前工作目录？？
	4. php 二进制文件所在目录
	5. --with-config-file-path 指定的位置

二 扩展
	是什么
		为特定的应用提供现成的函数或者类
	怎么安装
		1 编译选项, 默认加载, 不需要再配置php.ini
		2 pecl install extname, 该命令将下载 extname 的源代码，编译之，并将 extname.so 安装到 extension_dir 中。然后 extname.so 就可以通过 php.ini 加载了。
		3 独立编译
			2.1 进入源代码的ext下的指定目录，如 ‘curl’
			2.2 依次运行

				pecl download extname
				$ tar -zxvf extname.tgz
				$ mv extname-x.x.x extname

				$ cd /your/phpsrcdir 
				$ rm configure
				$ ./buildconf --force
				$ ./configure --help
				$ ./configure --with-extname --enable-someotherext --with-foobar
				$ make
				$ make install


				phpize
				./configure  -with-php-config=PATH
				make && make install
			2.3 配置php.ini，加载该模块
				添加 “extension=curl.so”
	查看
		php -m

三 php-fpm
	是什么
		FastCGI 进程管理
	配置
		默认在php安装路径的etc目录下

	配置成系统服务


四 怎么升级php
	另外的文件夹安装
	1 替换软连接
	2 关掉现有进程，开启新进程
	done




