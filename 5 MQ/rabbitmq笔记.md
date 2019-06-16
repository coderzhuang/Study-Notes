一 安装rabbitmq
	wget http://download.fedoraproject.org/pub/epel/7/x86_64/e/epel-release-7-8.noarch.rpm
	yum install epel-release-7-8.noarch.rpm
	wget http://www.rabbitmq.com/releases/rabbitmq-server/v3.6.6/rabbitmq-server-3.6.6-1.el7.noarch.rpm
	yum install rabbitmq-server-3.6.6-1.el7.noarch.rpm 

	//开启浏览器访问
	rabbitmq-plugins enable rabbitmq_management  

	// 运行服务
	service rabbitmq-server restart  

	// 添加用户
	添加用户:rabbitmqctl add_user admin admin
	添加权限:rabbitmqctl set_permissions -p "/" admin ".*" ".*" ".*"
	修改用户角色rabbitmqctl set_user_tags admin administrator

	// 登录控制面板
	http://X.X.X.X:15672

二 安装amqp
	wget http://pecl.php.net/get/amqp-1.7.1.tgz
	tar xzvf amqp-1.7.1.tgz
	cd amqp-1.7.1.tgz
	phpize
	./configure --with-php-config=php-config
	make && make install
	vi php.ini
	添加：
	extension=amqp.so
	php -m

三 测试

1 队列
	存储消息的地方
	理论上没有大小限制(受限于内存大小)
	生产者可以直接往队列推消息（还可以通过交换机），消费者只能从队列接受消息
	一个队列可以被多个消费者监听，但是同一个消息只会指派到一个消费者
	消息生产者，消费者，队列不一定在同一个服务器上
	消息的持久化，本质就是将数据写到本地文件，要求队列和消息都必须是持久化的
	消息的确认机制--当消费者获取到消息时，服务端并不会删除该消息，直到接收到消费者的确认包，否则，就重发
	不允许用不同的参数重定义队列

2 发布／订阅
	使用交换机，消息不会直接推送到消息队列，而会推送到交换机
交换机根据设置的规则，再推送到不同的消息队列

2.1 广播
	交换机将消息推动到所有绑定的消息队列
2.2 路由键
	交换机将消息推动到所有匹配路由键的消息队列
2.3 主题
	交换机将消息推动到所有匹配路由键的消息队列，这个其实就是2.2的升级版，支持模糊匹配



















