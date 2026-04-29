# 康复机构信息管理系统 (RHIS) 需求文档

## 开发环境

- PHP 8.1
- Laravel 10
- MySQL 8.0
- Nginx 1.12.19
- Ubuntu 系统

## Docker 环境
容器名称：bt_dev_env
容器内项目根目录绝对路径：/www/wwwroot/linshe/rhis

需要运行项目的相关命令时，需要在命令前面加：docker exec -w /www/wwwroot/linshe/rhis bt_dev_env

例如：
- 执行迁移：docker exec -w /www/wwwroot/linshe/rhis bt_dev_env php artisan migrate
- 生成 Filament 资源：docker exec -w /www/wwwroot/linshe/rhis bt_dev_env php artisan make:filament-resource PatientProfile
- 清理缓存：docker exec -w /www/wwwroot/linshe/rhis bt_dev_env php artisan optimize:clear
- 生产静态文件：docker exec -w /www/wwwroot/linshe/rhis bt_dev_env nvm use 20 && npm install && npm run build


## 1.项目愿景

构建一个轻量化、数字化、无纸化的门诊康复管理系统。核心目标是实现客户档案的数字化存储，并强制执行“评估-康复-对比”的康复 SOP。

## 2.核心约束

- 适用场景：仅限门诊康复业务。
- 功能排除：不包含住院管理、检验检查申请（LIS/PACS）、药房管理、进销存管理、第三方硬件设备对接。
- 存储重点：支持海量静态图片与视频流存储，用于康复前后对比。

## 3.开发阶段规划

### 第一阶段：基础底座 (Base)

- 组织架构：多级科室管理。
- RBAC 权限：基于角色的菜单与按钮级权限控制。
- 数据字典：ICD 编码、收费项目、康复套餐、评估量表选项。
- 底层中间件：
  - 文件存储：集成 OSS/MinIO，支持大文件切片上传。
  - 审批流：简单的电子签名与单据审核（如退费、方案确认）。

### 第二阶段：数字化档案与收费 (Archive & Billing)

- 客户档案管理：
  - 命名规范：客户编号+姓名。
  - 档案树状结构：基本信息、医疗记录、康复记录（序号+日期）、法务/财务凭证。
- 计次收费系统：
  - 支持套餐购买与余额管理。
  - 康复后电子回执推送与客户电子签名确认。

### 第三阶段：康复专科 SOP (Core Workflow) - 关键模块

- 结构化评估系统：
  -身体维度录入：身高、体重、各部位围度（左右臂/腿、胸腹臀）、体脂比。
  - 柔软度矩阵：躯干、腘绳肌、髂腰肌、股四头肌、小腿、肩部（好/一般/差）。
  - 体态评估（侧面/背面）：针对头、颈、肩、胸椎、腰椎、骨盆、膝关节、足部进行标签化录入（如：前引、高低肩、骨盆前倾）。
  - 图谱画板：支持在人体 2D 图谱上进行手写标记（如痛点位置）。
- 康复前后影像对比 SOP：
  - 康复前强制步骤：上传 6 张静态照片（侧立、侧弯、正立、正弯、背立、侧坐）+ 1 段步姿视频（≥20秒）。
  - 康复后强制步骤：上传 6 张静态照片（其中背坐姿替换侧坐姿）+ 1 段步姿视频（≥20秒）。
  - 对比逻辑：支持同视角照片、视频的左右/上下分屏对比预览。

### 第四阶段：数据看板与触达 (Analysis)

- 运营看板：门诊量、耗卡率、康复有效率分析（基于评估标签变化）。
  - 患者端对接：微信通知预约提醒、消费提醒及康复报告查询。

## 4. 关键数据结构参考 (Entity Hints)

- PatientProfile: patient\_id, name, contact, join\_date, membership\_no, initial\_symptoms.
- PhysicalAssessment: record\_id, patient\_id, date, measurements (JSON), flexibility (JSON), posture\_tags (JSON), body\_canvas\_data (Blob/Path).
- ImagingRecord: record\_id, type (Pre/Post), media\_urls (Array), video\_url.
- ConsumptionRecord: record\_id, package\_id, remaining\_sessions, customer\_signature\_path.

## 5. 数据库表结构设计

### 5.1 权限与组织架构设计

#### 1.1 推荐方案：使用 spatie/laravel-permission 扩展包

**推荐理由：**

- 成熟稳定：Laravel 社区最流行的权限管理包
- 功能完善：支持角色、权限、多守卫、直接权限赋予等
- 文档完善：社区支持好，有大量最佳实践
- 符合需求：完美支持第一阶段的 RBAC 权限需求

**安装配置步骤：**

1. 安装：`composer require spatie/laravel-permission`
2. 发布配置和迁移文件：`php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`
3. 运行迁移：`php artisan migrate`
4. 在 User 模型中使用 `HasRoles` trait

#### 1.2 组织架构表设计

##### 科室表 (departments)

| 字段名         | 类型        | 长度  | 备注           |
| ----------- | --------- | --- | ------------ |
| id          | bigint    | 20  | 主键，自增        |
| parent\_id  | bigint    | 20  | 父科室ID，顶级科室为0 |
| name        | varchar   | 100 | 科室名称         |
| code        | varchar   | 50  | 科室编码         |
| level       | tinyint   | 3   | 科室层级         |
| sort        | int       | 11  | 排序           |
| status      | tinyint   | 1   | 状态：0-禁用，1-启用 |
| description | text      | -   | 科室描述         |
| created\_at | timestamp | -   | 创建时间         |
| updated\_at | timestamp | -   | 更新时间         |

##### 部门用户关联表 (department\_user)

| 字段名            | 类型        | 长度 | 备注            |
| -------------- | --------- | -- | ------------- |
| id             | bigint    | 20 | 主键，自增         |
| user\_id       | bigint    | 20 | 用户ID          |
| department\_id | bigint    | 20 | 科室ID          |
| is\_primary    | tinyint   | 1  | 是否主科室：0-否，1-是 |
| created\_at    | timestamp | -  | 创建时间          |

### 5.2 核心业务实体设计

#### 2.1 患者档案表 (patient\_profiles)

| 字段名                | 类型        | 长度  | 备注              |
| ------------------ | --------- | --- | --------------- |
| id                 | bigint    | 20  | 主键，自增           |
| patient\_no        | varchar   | 50  | 患者编号，唯一         |
| name               | varchar   | 100 | 患者姓名            |
| gender             | tinyint   | 1   | 性别：0-未知，1-男，2-女 |
| birthday           | date      | -   | 出生日期            |
| age                | int       | 11  | 年龄              |
| id\_card           | varchar   | 18  | 身份证号            |
| contact            | varchar   | 20  | 联系电话            |
| emergency\_contact | varchar   | 20  | 紧急联系人电话         |
| emergency\_name    | varchar   | 100 | 紧急联系人姓名         |
| address            | varchar   | 255 | 住址              |
| membership\_no     | varchar   | 50  | 会员号             |
| join\_date         | date      | -   | 建档日期            |
| initial\_symptoms  | text      | -   | 初始症状描述          |
| icd\_codes         | json      | -   | ICD编码（JSON数组）   |
| status             | tinyint   | 1   | 状态：0-停用，1-正常    |
| remark             | text      | -   | 备注              |
| created\_by        | bigint    | 20  | 创建人ID           |
| updated\_by        | bigint    | 20  | 更新人ID           |
| created\_at        | timestamp | -   | 创建时间            |
| updated\_at        | timestamp | -   | 更新时间            |

#### 2.2 身体评估记录表 (physical\_assessments)

| 字段名                | 类型        | 长度  | 备注                                 |
| ------------------ | --------- | --- | ---------------------------------- |
| id                 | bigint    | 20  | 主键，自增                              |
| patient\_id        | bigint    | 20  | 患者ID                               |
| assessment\_no     | varchar   | 50  | 评估编号，唯一                            |
| assessment\_date   | date      | -   | 评估日期                               |
| assessment\_type   | tinyint   | 1   | 评估类型：1-初评，2-中评，3-末评                |
| height             | decimal   | 5,2 | 身高(cm)                             |
| weight             | decimal   | 5,2 | 体重(kg)                             |
| bmi                | decimal   | 5,2 | BMI                                |
| body\_fat\_rate    | decimal   | 5,2 | 体脂率(%)                             |
| circumference      | json      | -   | 围度数据（JSON：左右臂围、左右腿围、胸围、腰围、臀围）      |
| flexibility        | json      | -   | 柔软度矩阵（JSON：躯干、腘绳肌、髂腰肌、股四头肌、小腿、肩部）  |
| posture\_tags      | json      | -   | 体态标签（JSON数组：头、颈、肩、胸椎、腰椎、骨盆、膝关节、足部） |
| body\_canvas\_path | varchar   | 255 | 人体图谱画板文件路径                         |
| body\_canvas\_data | longblob  | -   | 人体图谱画板数据                           |
| assessor\_id       | bigint    | 20  | 评估人ID                              |
| remark             | text      | -   | 评估备注                               |
| status             | tinyint   | 1   | 状态：0-草稿，1-已完成                      |
| created\_by        | bigint    | 20  | 创建人ID                              |
| updated\_by        | bigint    | 20  | 更新人ID                              |
| created\_at        | timestamp | -   | 创建时间                               |
| updated\_at        | timestamp | -   | 更新时间                               |

#### 2.3 影像记录表 (imaging\_records)

| 字段名                | 类型        | 长度  | 备注               |
| ------------------ | --------- | --- | ---------------- |
| id                 | bigint    | 20  | 主键，自增            |
| patient\_id        | bigint    | 20  | 患者ID             |
| assessment\_id     | bigint    | 20  | 关联的评估记录ID        |
| record\_no         | varchar   | 50  | 记录编号，唯一          |
| record\_type       | tinyint   | 1   | 记录类型：1-康复前，2-康复后 |
| treatment\_session | int       | 11  | 第几次康复            |
| photo\_urls        | json      | -   | 照片URL数组（JSON）    |
| video\_url         | varchar   | 255 | 视频URL            |
| video\_duration    | int       | 11  | 视频时长（秒）          |
| uploader\_id       | bigint    | 20  | 上传人ID            |
| remark             | text      | -   | 备注               |
| status             | tinyint   | 1   | 状态：0-未审核，1-已审核   |
| created\_by        | bigint    | 20  | 创建人ID            |
| updated\_by        | bigint    | 20  | 更新人ID            |
| created\_at        | timestamp | -   | 创建时间             |
| updated\_at        | timestamp | -   | 更新时间             |

#### 2.4 消费记录表 (consumption\_records)

| 字段名                       | 类型        | 长度   | 备注                   |
| ------------------------- | --------- | ---- | -------------------- |
| id                        | bigint    | 20   | 主键，自增                |
| patient\_id               | bigint    | 20   | 患者ID                 |
| record\_no                | varchar   | 50   | 消费记录编号，唯一            |
| package\_id               | bigint    | 20   | 套餐ID                 |
| package\_name             | varchar   | 100  | 套餐名称（冗余字段）           |
| total\_sessions           | int       | 11   | 总次数                  |
| used\_sessions            | int       | 11   | 已使用次数                |
| remaining\_sessions       | int       | 11   | 剩余次数                 |
| amount                    | decimal   | 10,2 | 金额                   |
| transaction\_type         | tinyint   | 1    | 交易类型：1-购买，2-消费，3-退费  |
| customer\_signature\_path | varchar   | 255  | 客户电子签名路径             |
| operator\_id              | bigint    | 20   | 操作人ID                |
| remark                    | text      | -    | 备注                   |
| status                    | tinyint   | 1    | 状态：0-待审核，1-已完成，2-已拒绝 |
| approved\_by              | bigint    | 20   | 审核人ID                |
| approved\_at              | timestamp | -    | 审核时间                 |
| created\_by               | bigint    | 20   | 创建人ID                |
| updated\_by               | bigint    | 20   | 更新人ID                |
| created\_at               | timestamp | -    | 创建时间                 |
| updated\_at               | timestamp | -    | 更新时间                 |

### 5.3 关联关系说明

| 实体A                | 关系类型 | 实体B                | 说明                     |
| ------------------ | ---- | ------------------ | ---------------------- |
| User               | 多对多  | Department         | 一个用户可以属于多个科室，一个科室有多个用户 |
| PatientProfile     | 一对多  | PhysicalAssessment | 一个患者有多次评估记录            |
| PatientProfile     | 一对多  | ImagingRecord      | 一个患者有多次影像记录            |
| PatientProfile     | 一对多  | ConsumptionRecord  | 一个患者有多条消费记录            |
| PhysicalAssessment | 一对多  | ImagingRecord      | 一次评估可能关联多次影像记录（康复前/后）  |

### 5.4 补充数据字典表（第一阶段需要）

#### 4.1 数据字典表 (data\_dictionaries)

| 字段名         | 类型        | 长度  | 备注                                                                  |
| ----------- | --------- | --- | ------------------------------------------------------------------- |
| id          | bigint    | 20  | 主键，自增                                                               |
| type        | varchar   | 50  | 字典类型（如：icd\_code, charge\_item, rehab\_package, assessment\_option） |
| code        | varchar   | 50  | 字典编码                                                                |
| name        | varchar   | 200 | 字典名称                                                                |
| value       | text      | -   | 字典值                                                                 |
| sort        | int       | 11  | 排序                                                                  |
| status      | tinyint   | 1   | 状态：0-禁用，1-启用                                                        |
| parent\_id  | bigint    | 20  | 父级ID                                                                |
| created\_at | timestamp | -   | 创建时间                                                                |
| updated\_at | timestamp | -   | 更新时间                                                                |

