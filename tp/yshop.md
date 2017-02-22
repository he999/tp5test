# yshop项目文件
## 创建时间
2016-9-19
## 需求概况
微商城，与原微唯宝分销功能相近，增强界面。支持多商城。
## 目标结构
## 类库设计
### 基础类库
#### Coms
#### Categories
#### User
### 微信类库
#### DiyMenu
#### WeixinUser
### 项目类库
#### 商店 Shops
#### 商品 Goods
##### 商品属性 GoodsAttrs
##### 商品图片 GoodsImages
##### 定单 Orders
##### 定单商品 OrdersGoods
#### 购物车 Cert
#### 地区 Regions
#### 商品分类 GoodsCategory
#### 支付方式 Payment
#### 顾客 Customer
## 控制器设计
## 现存问题
### 没有模板选择
### 商品没有属性
### 没有物流
### 没有支付
### 没有分享
### 购物车采用的SESSION，而不是数据库
