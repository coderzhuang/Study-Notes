http://dev.mysql.com/doc



DDL
DML

innodb
	事务
	行锁
	mvcc
	非锁定读
	外键

// ************************************************************
innodb架构
	后台线程+内存池 <=> 文件系统

后台线程（7个）
	1 * master
	1 * 锁监控
	1 * 错误监控
	4 * IO（可调整）

内存池
	缓冲池 -- 按页读取（每页16K，内存回收：最近最少使用LRU）
		索引页
		数据页
		undo页
		插入缓冲
		自适应hash索引
		lock info
		数据字典信息
	重做日志缓冲池
	额外内存池

master thread
	void master_thread(){
		while(1){
			for(int i=0; i<10; i++){
				将重做日志缓冲刷新到文件
				if(io<5) 合并插入缓冲
				if(modify_io>max_dirty_page) 至多刷新100个脏页到磁盘
				如果没有用户活动，跳到 background_loop
				sleep 1 second if necessary
			}
			if(io<200) 刷新100个脏页到磁盘
			合并至多5个插入缓冲
			将重做日志缓冲刷新到文件
			删除无用undo页
			刷新100个或10脏页到磁盘
			产生一个检查点
		}
	}

	void background_loop()
	{
		删除无用的undo页
		合并20个插入缓冲
		if(空闲)
			flush_loop
		else
			master_thread
	}

	void flush_loop(){
		刷新100个脏页到磁盘
		if(modify_io>max_dirty_page) flush_loop
		suspend_loop
	}

	void suspend_loop(){
		waiting event
	}



插入缓冲
	非聚集索引 p46

// ************************************************************
事务（就是通过锁实现的）
A（Atomicity）原子性
	事务操作相关
C（Consistency）一致性
	保护数据免受崩溃
		双写
		崩溃恢复
I（Isolation）隔离级别
	RU级别下，读操作都不加锁（悲观/乐观），所以可能出现脏读，不可重复读，幻读等情况
	为了防止出现上述情况，我们尝试在读取数据时加行锁，这就避免了锁定行的修改和删除也就解决了脏读，不可重复读的问题
	但是仍然存在幻读问题，所以要想避免幻读需要加next-key锁。
	上述解决方案其实就是隔离的最高级别 SERIALIZABLE 的实现。可以看到，在读/写的时候都加锁，显然，这样的方案需要额外的性能开销，
	同时锁的使用降低了应用的并发性。
	所以innodb引进了一种乐观锁的机制，即MVCC（多版本并发控制），通过对版本号的塞选，解决了脏读，不可重复读的问题
	但是仍然存在幻读问题，这时，如果在select后加上“for update ” 或 “lock in share mode ” 就显示的将隔离级别提升为 SERIALIZABLE 了
	网上说的RR级别就能达到 SERIALIZABLE 的效果，说的就是这种情况

D（Durability）持久性
	涉及软件功能与特定硬件配置的交互；事务完成之后，它对于数据的修改是永久性的


// ************************************************************
数据是如何组织和存放的

表空间 
	段 
	区 
	页
		页结构：
		file header（38 bytes）
		page header（56 bytes）
		infimun + supremum records
		user records
		free space
		page directory
		file trailer（8 bytes）

	行
	antelope
		compact
			变长字段长度列表，NULL标志位，头信息（5bytes），列1，列2..（事务ID，rollback ID，可能的主键ID）
		redundant
			字段长度偏移列表，头信息（6bytes），列1，列2..
	barracuda
		compressed
		dynamic


// ************************************************************
事务（就是通过锁实现的）
A（Atomicity）原子性
	事务操作相关
C（Consistency）一致性
	保护数据免受崩溃
		双写
		崩溃恢复
I（Isolation）隔离级别
	RU级别下，读操作都不加锁（悲观/乐观），所以可能出现脏读，不可重复读，幻读等情况
	为了防止出现上述情况，我们尝试在读取数据时加行锁，这就避免了锁定行的修改和删除也就解决了脏读，不可重复读的问题
	但是仍然存在幻读问题，所以要想避免幻读需要加next-key锁。
	上述解决方案其实就是隔离的最高级别 SERIALIZABLE 的实现。可以看到，在读/写的时候都加锁，显然，这样的方案需要额外的性能开销，
	同时锁的使用降低了应用的并发性。
	所以innodb引进了一种乐观锁的机制，即MVCC（多版本并发控制），通过对版本号的塞选，解决了脏读，不可重复读的问题
	但是仍然存在幻读问题，这时，如果在select后加上“for update ” 或 “lock in share mode ” 就显示的将隔离级别提升为 SERIALIZABLE 了
	网上说的RR级别就能达到 SERIALIZABLE 的效果，说的就是这种情况

D（Durability）持久性
	涉及软件功能与特定硬件配置的交互；事务完成之后，它对于数据的修改是永久性的




//分布式事务 ？？







// ************************************************************
索引的使用













mysql
redis
soa/微服务
oop




























