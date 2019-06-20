## mysql的字符编码

#### 和mysql交互有关编码的变量如下：
1. character_set_client
    * 表明客户端使用的编码
1. character_set_connection
    * 服务端接收到客户端的请求后，会将编码从 character_set_client 转换到 character_set_connection
    * 如果字符串有引导词修饰则不会转换
    * 数字到字符串转换的字符集，例：CONCAT(a,b) REPLACE(a,b,c) 如果所有参数都是数字，则转换
    * **然而还不清楚这步转换的意义。。**
1. character_set_results
    * 服务端再将数据传输到客户端之前，会将编码转换成 character_set_results
#### 编码的转换流程
* 输入

character_set_client -> character_set_connection -> 内部编码

* 输出

内部编码 -> character_set_results

*内部编码

1. 使用每个数据字段的 CHARACTER SET 设定值； 
1. 若上述值不存在，则使用对应数据表的字符集设定值 
1. 若上述值不存在，则使用对应数据库的字符集设定值； 
1. 若上述值不存在，则使用 character_set_server 设定值。

#### 例子
以下以字符串“阿萨”为例（gbk=b0a2c8f8，utf8=E998BFE890A8），观察在不同编码格式下的输入输出：

文本流 | client | connection | 内部编码 | results | 存储 | 输出
- | - | - | - | - | - | - 
E998BFE890A8 | UTF8 | UTF8 | UTF8 | UTF8 | E998BFE890A8 | E998BFE890A8 
E998BFE890A8 | UTF8 | UTF8 | UTF8 | GBK | E998BFE890A8 | B0A2C8F8 
E998BFE890A8 | UTF8 | GBK | UTF8 | UTF8 | E998BFE890A8 | E998BFE890A8 
E998BFE890A8 | UTF8 | GBK | UTF8 | GBK | E998BFE890A8 | B0A2C8F8  
E998BFE890A8 | GBK | UTF8 | UTF8 | UTF8 | E99783E883AFE68383 | E99783E883AFE68383 
E998BFE890A8 | GBK | UTF8 | UTF8 | GBK | E99783E883AFE68383 | E998BFE890A8 
E998BFE890A8 | GBK | GBK | UTF8 | UTF8 | E99783E883AFE68383 | E99783E883AFE68383 
E998BFE890A8 | GBK | GBK | UTF8 | GBK | E99783E883AFE68383 | E998BFE890A8 
B0A2C8F8 | UTF8 | UTF8 | UTF8 | UTF8 | ERROR |
B0A2C8F8 | UTF8 | UTF8 | UTF8 | GBK | ERROR |
B0A2C8F8 | UTF8 | GBK | UTF8 | UTF8 | 3F3F3F3F | 3F3F3F3F 
B0A2C8F8 | UTF8 | GBK | UTF8 | GBK | 3F3F3F3F | 3F3F3F3F  
B0A2C8F8 | GBK | UTF8 | UTF8 | UTF8 | E998BFE890A8 | E998BFE890A8 
B0A2C8F8 | GBK | UTF8 | UTF8 | GBK | E998BFE890A8 | B0A2C8F8 
B0A2C8F8 | GBK | GBK | UTF8 | UTF8 | E998BFE890A8 | E998BFE890A8 
B0A2C8F8 | GBK | GBK | UTF8 | GBK | E998BFE890A8 | B0A2C8F8 

所以，只要保证文本真实的编码格式和指定的 client、results一致就能保证存储和输出都是一致的。

这里看不出来connect的作用，不过一般使用 ````set names utf8```` 统一设置client、connect、results就好了。


