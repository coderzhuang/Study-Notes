* ```` show dbs ````
    - 显示所有数据库
* ````use runoob````
    - 创建数据库
* ```` db ````
    - 显示当前数据库
* ```` db.dropDatabase() ````
    - 删除当前数据库
* ```` show tables/show collections````
    - 显示集合
* ```` db.createCollection("runoob")  ````
    - 创建集合，可以在插入的时候自动创建
* ```` db.COLLECTION_NAME.drop() ````
    - 删除集合
* ```` db.COLLECTION_NAME.insert({"name":"zdx"}) ````
    - 插入数据
* ```` db.collection.update(<query>,<update>,{upsert: <boolean>,multi: <boolean>,writeConcern: <document>}) ````
    - 更新
* ```` db.COLLECTION_NAME.find().pretty().limit(1).skip(1).sort({KEY:1}) ````
    - 查询, 类似 limit 1,1, sort其中 1 为升序排列，而 -1 是用于降序排列。
* ```` db.collection.remove(<query>,{justOne: <boolean>,writeConcern: <document>}) ````
    - 删除
* ```` db.col.remove({}) ````
    - 清空表
* ```` db.collection.createIndex(keys, options) ````
    - 创建索引



























