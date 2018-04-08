## redis 配置文件
&nbsp;&nbsp;&nbsp;&nbsp;修改配置后需要杀死进程再启动才会生效
###  include
可以通过 include 指令，包含其他配置文件
要注意的是，后面的配置会覆盖前面的配置
所以，可以引用一个公用的配置文件，然后再配置新的参数覆盖引用文件中的值

###  loadmodule
可以通过 loadmodule 指令，加载新的模块

### bind
监听主机，如果没有设置，Redis将监听0.0.0.0

### protected-mode
如果开启了保护模式，同时没有明文设置“bind”指令且没有配置密码，redis将只接收来自127.0.0.1的链接
取值：yes | no

### port
设置监听端口，默认开启6379

### tcp-backlog
影响tcp accept队列的大小 = min(tcp-backlog,内核的somaxconn值)
当一个请求尚未被处理或建立时，就会进入backlog，处理后会移出队列。当server处理请求较慢，以至于监听队列被填满后，新来的请求会被拒绝。
tcp-backlog 511

### unixsocket
监听socket文件，默认不启用
unixsocket /tmp/redis.sock

### timeout
客户端最大空闲时间（秒），超过N秒后服务端关闭连接（0禁用）
该参数对主从节点，阻塞操作，订阅节点无效

### tcp-keepalive
tcp-keepalive赋值给TCP_KEEPIDLE // 闲置时间达到该值，发送心跳包，单位：秒
TCP_KEEPINTVL = TCP_KEEPIDLE/3 //两个心跳包的发送间隔
TCP_KEEPCNT = 3 // 心跳包发送次数
当对端没有响应心跳包，就重置链接

## ==== 一般配置 ====
### daemonize
开启后台模式

取值：yes |no

### supervised

是否通过upstart或systemd管理守护进程，实现类似start/restart/stop的命令来方便管理服务，该参数仅支持特定的系统

取值：upstart | systemd |auto |no

### pidfile

设置pid文件路径

pidfile /var/run/redis.pid

### loglevel
日志级别
	debug (a lot of information, useful for development/testing)
	verbose (many rarely useful info, but not a mess like the debug level)
	notice (moderately verbose, what you want in production probably) 默认级别
	warning (only very important / critical messages are logged)

### logfile
日志路径，置空的话会输出到标准输出，后台运行的话会丢弃日志

### syslog-enabled
是否开启系统日志，开启后日志会接入syslog
取值：yes |no

### syslog-ident
指定系统日志标识，如果运行了多个redis实例，使用该参数来区别

### syslog-facility
指定系统日志功能，用于openlog函数的facility参数，日志文件按 syslog.conf 配置文件中的描述进行组织
取值：user | local0-local7
### databases
设置数据库个数
###always-show-logo
设置是否显示redis logo图标，没什么用
取值：yes |  no
## ==== 安全 ====
### requirepass
设置密码，为了向后兼容不建议设置密码，同时因为redis每秒可以尝试150k次密码，所以要求密码要够复杂

### rename-command
重命名命令,可以重命名比较危险的命令避免错误操作
同时要注意，重命名可能会对从节点造成影响
如果要禁用命令，可以重命名为空
rename-command keys ""

### maxclients
客户端最大连接数

## ==== 内存管理 ====
### maxmemory
设置最大内存限制

### maxmemory-policy
设置内存回收策略
	volatile-lru - > 使用类LRU算法删除过期集合中的键
	allkeys-lru - >使用类LRU算法删除任何密钥。
	volatile-lfu - > 使用类LFU算法删除过期集合中的键
	allkeys-lfu - >使用类LFU算法删除任何密钥。
	volatile-random - >随机删除过期集合中的键
	allkeys-random - >随机删除任何键
	volatile-ttl - >删除最近过期的键
	noviction - >不要驱逐任何东西，只需在写入操作时返回错误。

* lru（最近最少使用）算法
该值将影响贴近真实lru算法的程度，10会很接近但会消耗更多的cpu
maxmemory-samples 5

* lfu（最不常用）算法
lfu计数器只有8位，最大值255，所以它并不是来一次访问就自增1的
而是根据概率自增，对应的概率分布计算公式为：
    1/((counter-LFU_INIT_VAL)*server.lfu_log_factor+1)
由此可见，概率会随计数器增长而变化
lfu-log-factor 设置越大，可表示的范围越大，但是精确度就越小
lfu-log-factor 10
衰减因子，N分钟内没有访问，counter就要减N*lfu-decay-time。
lfu-decay-time 1

## ==== 延迟释放 ====

redis删除键有两种方式
1. 阻塞删除
  如del，如果删除的键比较小，响应很快，但是如果键比较大，就需要比较长的时间，在这期间，redis不接收新的命令

2. 非阻塞删除
  unlink 以及FLUSHALL 和 FLUSHDB 命令的ASYNC选项。这些命令执行后，删除会在恒定的时间内执行由另一个线程将尽可能快地增量释放资源。


删除操作是用户的可控的，但是，有时候redis会主动执行删除操作

* 设置了maxmemory
* 设置了键的过期时间
* 键的副作用，如rename 会删除旧键名
* 在复制期间，当从服务器与主服务器执行完全重新同步时，整个数据库的内容将被删除，以便加载刚刚传输的RDB文件。

为了设置上述删除键的阻塞模式，可以通过以下命令设置：
lazyfree-lazy-eviction no
lazyfree-lazy-expire no
lazyfree-lazy-server-del no
slave-lazy-flush no

## ==== lua脚本 ====

以毫秒为单位限定lua脚本的最大执行时间.设置为0 或者一个负数来取消时间限定.
lua-time-limit 5000

## ==== 慢查询 ====
### slowlog-log-slower-than
指定慢查询时间（微秒），该时间仅仅是执行命令的时间，不包含io操作时间，可理解为进程阻塞不能响应其他命令的时间。负数不记录，置0将记录每一条命令

### slowlog-max-len
指定日志队列长度，当超过该值，最旧的日志会被移除，可以通过SLOWLOG RESET重置日志队列

## ==== 延迟监控 ====

延迟监控子系统，通过LATENCY命令，可以为用户打印出相关信息的图形和报告
这个系统只会记录运行时间超出指定时间（毫秒）值的命令，如果设置为0，这个监控将会被关闭
latency-monitor-threshold 0

## ==== 事件通知 ====

允许用户订阅相关事件消息
详见http://redis.io/topics/notifications
notify-keyspace-events ""

## ==== 碎片整理 ====

redis支持在线碎片整理，以回收利用内存
此功能在默认情况下处于禁用状态，只有使用Jemalloc副本编译的redis才支持该特性
如果您没有碎片问题，则无需启用此功能。如果不是很理解参数，建议不要改动

### activedefrag
是否启用活动碎片整理
取值：yes |  no

### active-defrag-ignore-bytes
开始主动碎片整理的最小碎片浪费量
active-defrag-ignore-bytes 100mb

### active-defrag-threshold-lower
启动活动碎片整理碎片的最小百分比
active-defrag-threshold-lower 10

### active-defrag-threshold-upper
我们使用最大努力的碎片最大百分比
active-defrag-threshold-upper 100

### active-defrag-cycle-min
尽可能减少CPU百分比的碎片整理
active-defrag-cycle-min 25

### active-defrag-cycle-max
尽最大的努力在CPU百分比中整理碎片
active-defrag-cycle-max 75


## ==== 高级配置 ====
### hash-max-ziplist-entries  & hash-max-ziplist-value
当hash的子节点小于hash-max-ziplist-entries，且每个子节点小于hash-max-ziplist-value时，会使用内存优化的数据结构进行存储。<font color=red>我是黑体字该值如果设置比较大会怎么样？？</font>
hash-max-ziplist-entries 512
hash-max-ziplist-value 64

### list-max-ziplist-size & list-compress-depth
满足以下条件时，列表也以特殊方式编码以节省大量空间。
可以用-1～-5或具体的元素个数
list-max-ziplist-size -2

压缩的深度，0表示不压缩 1 表示除开头第1个节点和倒数第1个节点外，都压缩，2以此类推
list-compress-depth 0

### set-max-intset-entries
当一个集合由恰好是以64位有符号整数范围内的10进制的整数组成的字符串组成时。
set-max-intset-entries指定了集合中的元素小于该值时使用压缩算法
set-max-intset-entries 512

### zset-max-ziplist-entries & zset-max-ziplist-value
有序集合满足以下配置时会使用数据压缩算法
zset-max-ziplist-entries 128
zset-max-ziplist-value 64

### hll-sparse-max-bytes
hyperloglog 满足以下配置时会使用数据压缩算法
hll-sparse-max-bytes 3000

### activerehashing
rehash可以提高列表的hash命中，但是会加大cpu的使用，可能会有2微秒的延迟
取值：yes |  no

### client-output-buffer-limit
配置客户端输出缓冲主动断开超过设置的链接，一旦达到硬限制，或达到软限制并保持达到指定的秒数（连续），客户端立即断开连接。
client-output-buffer-limit <class> <hard limit> <soft limit> <soft seconds>
可选的class：
* normal -> 常规客户端
* slave  -> 子节点
* pubsub -> 订阅频道的客户端

client-output-buffer-limit normal 0 0 0
client-output-buffer-limit slave 256mb 64mb 60
client-output-buffer-limit pubsub 32mb 8mb 60

### client-query-buffer-limit
设置客户端查询缓冲的大小，管道，事务中需要注意
client-query-buffer-limit 1gb

### proto-max-bulk-len
<font color=red>设置bulk requests的大小？？</font>
proto-max-bulk-len 512mb

### hz
redis会在后台执行许多任务，如断开超时的链接，清除过期的键
但并非所有任务都以相同的频率执行，可以通过设置hz值调整该频率
hz越大，当redis空闲时，会使用更多的cpu来执行后台任务，这样会使键过期更及时精确以及其他效果

## ==== 快照，备份 ====
### save <seconds> <changes>
如果<seconds>秒内有 <changes> 个键发生改变，就保存快照 ,可以设置多个save

### stop-writes-on-bgsave-error
默认情况下，如果启用了RDB快照并且最新的后台保存失败，Redis将停止接受写入操作。
stop-writes-on-bgsave-error yes

### rdbcompression
生成快照时是否使用压缩，会有一定的cpu开支
rdbcompression yes

### rdbchecksum
是否对数据进行完整性校验，会有一定的cpu开支
rdbchecksum yes

### dbfilename
设置快照文件名
dbfilename dump.rdb

### dir
设置工作目录，相对于配置文件，快照文件会保存在该目录下
dir ./

# ==== AOF 模式 ====
### appendonly
是否开启AOF，默认redis使用RDB进行数据的持久化，然而RDB会有一定几率的数据丢失
AOF和RDB可以同时启用，但是恢复数据时优先使用AOF
appendonly yes

### appendfilename
指定aof文件名
appendfilename "appendonly.aof"

### appendfsync
指定数据写入磁盘的时机
* no 让系统决定何时写入，更快
* always 每次发生些操作就写入文件，慢但是安全
* everysec 每秒一次写入，比较平衡
appendfsync everysec

### no-appendfsync-on-rewrite
当设置appendfsync为always或everysec时，redis后台同步进程可能正在执行同步操作，此时再次调用同步进程会阻塞，如果设置no-appendfsync-on-rewrite为yes，那么当检测到有aof同步进程或rdb同步进程正在运行时，不会去执行fsync函数，这意味着在最坏的情况下将会失去30秒的日志(使用linux默认的设置)
no-appendfsync-on-rewrite no

### auto-aof-rewrite-percentage & auto-aof-rewrite-min-size
redis在同时满足以下条件时隐式调用BGREWRITEAOF来重写日志文件
1. 当前大小大于auto-aof-rewrite-min-size指定值
2. (aof_current_size-base)/base*100 大于等于auto-aof-rewrite-percentage指定值
base为最后一次改写后AOF文件的大小(如果重写自重启以来尚未发生，那么AOF文件的大小就是启动以来使用的大小)

auto-aof-rewrite-percentage 100
auto-aof-rewrite-min-size 64mb

### aof-load-truncated
是否支持加载被截断的AOF文件
AOF文件末尾可能被截断,如果将aof-load-truncated设置为no，则服务器将中止并出现错误并拒绝启动。否则加载截断的AOF文件，并且Redis服务器开始发出日志以通知用户该事件，这时需要使用“redis-check-aof”程序修复aof文件
aof-load-truncated yes

### aof-use-rdb-preamble
重写时是否采用混编,启用该选项会加快重写和恢复速度
aof-use-rdb-preamble no

### aof-rewrite-incremental-fsync
当子进程重写AOF文件的时候，以下选项将会允许等到存在32MB数据的时候才调用强制同步
<font color=red>这样可以降低IO上的延迟？？</font>
aof-rewrite-incremental-fsync yes

## ==== 复制 ====
### slaveof <masterip> <masterport>
1. Redis复制是异步的，但是如果它看起来没有连接至少给定数量的从站，您可以配置主站停止接受写入。？？
2. 如果复制链接在相对较短的时间内丢失，则Redis从节点能够与主节点执行部分重新同步。您可能需要根据您的需要合理配置复制积压大小。
3. 复制是自动的，不需要用户干预。

### masterauth
如果主节点时密码保护的，需要指定密码
masterauth <master-password>

### slave-serve-stale-data
从节点断开与主节点的链接或同步仍在进行中,如果slave-serve-stale-data设置为yes，从节点仍然对外服务，不过可能会有旧数据或空数据;slave-serve-stale-data设置为no，返回错误

### slave-read-only
从节点只读
slave-read-only yes

### repl-diskless-sync
**无盘同步，该特性还在实验阶段**
新的从属设备和重新连接的从属设备无法继续复制过程，只能接收差异，需要一次“完全同步”。
同步的方式有两种：
1. 磁盘支持：Redis主服务器创建一个将RDB文件写入磁盘的新进程。之后，文件由父进程传递到从服务器。
2. 无盘：Redis master创建一个新的进程，直接将RDB文件写入从套接字，而不用接触磁盘。

With disk-backed replication, while the RDB file is generated, more slaves can be queued and served with the RDB file as soon as the current child producing the RDB file finishes its work. With diskless replication instead once the transfer starts, new slaves arriving will be queued and a new transfer will start when the current one terminates.？？
对于慢速磁盘和快速（大带宽）网络，无盘复制效果更好。
repl-diskless-sync no

### repl-diskless-sync-delay
设置无盘同步等待的时间
repl-diskless-sync-delay 5

### repl-ping-slave-period
主从心跳
repl-ping-slave-period 10

### repl-timeout
同步超时时间
repl-timeout 60

### repl-disable-tcp-nodelay
启用后，redis将在指定时间内合并小的tcp包来减少请求次数从而减少带宽，同样，这也造成此段时间内主从数据的不一致。
repl-disable-tcp-nodelay no

### repl-backlog-size
主从失去连接时，此缓冲区越大，失去连接的时间就可以越长。
repl-backlog-size 1mb

### repl-backlog-ttl
主机和从机出现短暂的断开，此段时间产生的需要同步缓存数据可保留多长时间，在这个时间内如果主从还是不能连接，则清理同步缓冲区中这些要同步的缓存数据。设置为0，则永不清理
repl-backlog-ttl 3600

### slave-priority
当有多个从机时用来指定从机变成主机的优先级,如果设置为0，永不可做主机
slave-priority 100

### min-slaves-to-write & min-slaves-max-lag
设置当一个master端的可用slave少于N个，延迟时间大于M秒时，不接收写操作
  `=0`禁用，`>0`且两个值必须同时`>0`才算启用
min-slaves-to-write 3
min-slaves-max-lag 10

### slave-announce-*
当使用端口转发或NAT网络时，可能通过不同的IP和端口访问从节点。 为了向主机报告一组特定的IP和端口，从机可以使用以下两个选项
slave-announce-ip 5.5.5.5
slave-announce-port 1234

## ==== redis 集群 ====
### cluster-enabled
运行在集群模式的redis实例才能加入集群
cluster-enabled yes

### cluster-config-file
每个集群节点需要一个集群配置文件，该文件不用认为编辑，由节点本身维护且该文件名必须在集群中唯一
cluster-config-file nodes-6379.conf

### cluster-node-timeout
节点超时时间（微秒）？？
cluster-node-timeout 15000

### cluster-slave-validity-factor
节点失效转移条件,如果子节点与主节点的交互超过(node-timeout * slave-validity-factor) + repl-ping-slave-period 秒,那么该节点不参与失效转移.将cluster-slave-validity-factor置0，将始终参与
cluster-slave-validity-factor 10

### cluster-migration-barrier
集群中的子节点可以迁移到孤儿主节点下
cluster-migration-barrier 指定了被迁移主节点必须维持的子节点数
迁移后少于该值则迁移不成功？？
cluster-migration-barrier 1

### cluster-require-full-coverage
默认，集群节点检测到有未覆盖的槽就停止对外服务
如果要取消该限制，使集群的部分子集仍然可用，可设置cluster-require-full-coverage no

## ==== 集群对docker/nat 的支持 ====
在某些部署中，Redis群集节点地址发现失败，因为地址是NAT或由于端口被转发（典型情况是Docker和其他容器）。
为了使Redis Cluster在这样的环境中工作，需要每个节点都知道其公共地址的静态配置。
cluster-announce-ip 10.1.1.5
cluster-announce-port 6379
cluster-announce-bus-port 6380
