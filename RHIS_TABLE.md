# RHIS 数据库表结构文档

> 本文档记录所有数据库表的最终结构，每次新增或修改 Migration 后需同步更新此文件。

---

## 1. users（用户表 / 员工表）

| 字段名            | 类型        | 长度   | 是否可为空 | 默认值 | 备注         |
| ---------------- | --------- | ---- | ----- | --- | ---------- |
| id               | bigint    | 20   | 否    | 自增  | 主键         |
| name             | varchar   | 255  | 否    | -   | 姓名         |
| username         | varchar   | 255  | 是    | -   | 用户名（唯一）   |
| email            | varchar   | 255  | 否    | -   | 邮箱（唯一）    |
| email_verified_at| timestamp | -    | 是    | -   | 邮箱验证时间    |
| password         | varchar   | 255  | 否    | -   | 密码         |
| position         | varchar   | 100  | 是    | -   | 职位         |
| remember_token   | varchar   | 100  | 是    | -   | 记住我 Token |
| created_at       | timestamp | -    | 是    | -   | 创建时间       |
| updated_at       | timestamp | -    | 是    | -   | 更新时间       |

**关联关系：**
- 多对多 `departments`（通过 `department_user` 表）
- 多对多 `consumptionRecords`（通过 `consumption_record_user` 表）

---

## 2. departments（部门表）

| 字段名         | 类型        | 长度  | 是否可为空 | 默认值 | 备注             |
| ----------- | --------- | --- | ----- | --- | ------------ |
| id          | bigint    | 20  | 否    | 自增  | 主键             |
| parent_id   | bigint    | 20  | 否    | 0   | 父部门ID，顶级部门为0 |
| name        | varchar   | 100 | 否    | -   | 部门名称           |
| code        | varchar   | 50  | 是    | -   | 部门编码           |
| level       | tinyint   | 3   | 否    | 1   | 部门层级           |
| sort        | int       | 11  | 否    | 0   | 排序             |
| status      | tinyint   | 1   | 否    | 1   | 状态：0-禁用，1-启用  |
| description | text      | -   | 是    | -   | 部门描述           |
| created_at  | timestamp | -   | 是    | -   | 创建时间           |
| updated_at  | timestamp | -   | 是    | -   | 更新时间           |

**索引：** `parent_id`, `level`, `status`

**关联关系：**
- 自引用 `parent`（belongsTo）
- 自引用 `children`（hasMany）
- 多对多 `users`（通过 `department_user` 表）

---

## 3. department_user（部门-用户关联表）

| 字段名            | 类型        | 长度 | 是否可为空 | 默认值 | 备注             |
| -------------- | --------- | -- | ----- | --- | ------------ |
| id             | bigint    | 20 | 否    | 自增  | 主键             |
| user_id        | bigint    | 20 | 否    | -   | 用户ID           |
| department_id  | bigint    | 20 | 否    | -   | 部门ID           |
| is_primary     | tinyint   | 1  | 否    | 0   | 是否主部门：0-否，1-是 |
| created_at     | timestamp | -  | 否    | 当前时间 | 创建时间          |

**唯一约束：** `(user_id, department_id)`
**索引：** `department_id`, `is_primary`

---

## 4. icd_codes（ICD 编码表）

| 字段名         | 类型        | 长度  | 是否可为空 | 默认值 | 备注            |
| ----------- | --------- | --- | ----- | --- | ------------- |
| id          | bigint    | 20  | 否    | 自增  | 主键            |
| code        | varchar   | 20  | 否    | -   | ICD编码（唯一）    |
| name        | varchar   | 200 | 否    | -   | 疾病名称          |
| category    | varchar   | 100 | 是    | -   | 分类            |
| description | text      | -   | 是    | -   | 描述            |
| status      | tinyint   | 1   | 否    | 1   | 状态：0-禁用，1-启用 |
| created_at  | timestamp | -   | 是    | -   | 创建时间          |
| updated_at  | timestamp | -   | 是    | -   | 更新时间          |

**索引：** `category`, `status`

---

## 5. rehab_packages（康复套餐表）

| 字段名              | 类型        | 长度  | 是否可为空 | 默认值  | 备注                                                      |
| ---------------- | --------- | --- | ----- | ---- | ------------------------------------------------------- |
| id               | bigint    | 20  | 否    | 自增   | 主键                                                      |
| package_code     | varchar   | 50  | 否    | -    | 套餐编码（唯一）                                               |
| name             | varchar   | 200 | 否    | -    | 套餐名称                                                    |
| description      | text      | -   | 是    | -    | 套餐描述                                                    |
| price            | decimal   | 10,2| 否    | 0    | 套餐价格                                                    |
| total_sessions   | int       | 11  | 否    | 0    | 总次数                                                     |
| validity_days    | int       | 11  | 否    | 0    | 有效期（天）                                                 |
| status           | tinyint   | 1   | 否    | 1    | 状态：0-禁用，1-启用                                          |
| package_type     | varchar   | 50  | 否    | 单次| 套餐类型：单次, 疗程卡, 月卡, 季卡, 特惠次卡, 单项服务 |
| original_price   | decimal   | 10,2| 否    | 0    | 原始价格                                                    |
| average_price    | decimal   | 10,2| 否    | 0    | 均价                                                      |
| is_extendable    | boolean   | -   | 否    | false| 是否可延期                                                   |
| extension_days   | int       | 11  | 否    | 0    | 可延期天数                                                   |
| is_shareable     | boolean   | -   | 否    | false| 是否可共享                                                   |
| commission_per_service | decimal | 10,2 | 否 | 0 | 单次服务提成金额 |
| created_at       | timestamp | -   | 是    | -    | 创建时间                                                    |
| updated_at       | timestamp | -   | 是    | -    | 更新时间                                                    |

**索引：** `status`

---

## 6. patient_profiles（客户档案表）

| 字段名              | 类型        | 长度  | 是否可为空 | 默认值 | 备注       |
| ---------------- | --------- | --- | ----- | --- | -------- |
| id               | bigint    | 20  | 否    | 自增  | 主键       |
| wechat_openid    | varchar   | 255 | 是    | -   | 微信OpenID（唯一） |
| bind_token       | varchar   | 255 | 是    | -   | 绑定Token（唯一） |
| patient_id       | varchar   | 255 | 否    | -   | 客户编号（唯一） |
| name             | varchar   | 255 | 否    | -   | 姓名       |
| phone            | varchar   | 255 | 是    | -   | 联系电话     |
| membership_no    | varchar   | 255 | 是    | -   | 会员号       |
| join_date        | date      | -   | 是    | -   | 建档日期     |
| initial_symptoms | text      | -   | 是    | -   | 初始症状     |
| created_at       | timestamp | -   | 是    | -   | 创建时间     |
| updated_at       | timestamp | -   | 是    | -   | 更新时间     |

**关联关系：**
- 一对多 `physicalAssessments`（康复体态评估）
- 一对多 `imagingRecords`（影像记录）
- 一对多 `patientPackages`（套餐包）
- 一对多 `consumptionRecords`（消费记录）

---

## 7. patient_packages（客户套餐包表 / 资产表）

| 字段名                | 类型        | 长度   | 是否可为空 | 默认值   | 备注                    |
| ------------------- | --------- | ---- | ----- | ----- | --------------------- |
| id                  | bigint    | 20   | 否    | 自增    | 主键                    |
| patient_profile_id  | bigint    | 20   | 否    | -     | 关联客户ID                |
| package_code        | varchar   | 50   | 是    | -     | 套餐编码                  |
| package_name        | varchar   | 200  | 否    | -     | 套餐名称                  |
| package_type        | varchar   | 50   | 是    | -     | 套餐类型                  |
| total_sessions      | int       | 11   | 否    | 0     | 总次数                   |
| remaining_sessions  | int       | 11   | 否    | 0     | 剩余次数                  |
| price               | decimal   | 10,2 | 否    | 0     | 套餐价格                  |
| original_price      | decimal   | 10,2 | 否    | 0     | 原价                    |
| average_price       | decimal   | 10,2 | 否    | 0     | 均价                    |
| status              | varchar   | 20   | 否    | active| 状态：active-有效, completed-已完成 |
| description         | text      | -    | 是    | -     | 套餐描述                  |
| is_extendable       | tinyint   | 1    | 否    | 0     | 是否可延期                 |
| extension_days      | int       | 11   | 否    | 0     | 可延期天数                |
| is_shareable        | tinyint   | 1    | 否    | 0     | 是否可共享                 |
| purchase_date       | date      | -    | 是    | -     | 购买日期                  |
| expiry_date         | date      | -    | 是    | -     | 到期日期                  |
| created_at          | timestamp | -    | 是    | -     | 创建时间                  |
| updated_at          | timestamp | -    | 是    | -     | 更新时间                  |

**外键：** `patient_profile_id` → `patient_profiles.id`（级联删除）
**索引：** `status`, `patient_profile_id`

**关联关系：**
- 属于 `patientProfile`（belongsTo）
- 一对多 `consumptionRecords`（消费流水）

---

## 8. consumption_records（消费记录表 / 流水表）

| 字段名                | 类型        | 长度  | 是否可为空 | 默认值 | 备注           |
| ------------------- | --------- | --- | ----- | --- | ------------ |
| id                  | bigint    | 20  | 否    | 自增  | 主键           |
| patient_profile_id  | bigint    | 20  | 否    | -   | 关联客户ID       |
| patient_package_id  | bigint    | 20  | 是    | -   | 关联客户套餐包ID   |
| package_name        | varchar   | 200 | 是    | -   | 套餐名称         |
| deducted_sessions   | int       | 11  | 否    | 1   | 本次扣减次数       |
| remaining_sessions  | int       | 11  | 否    | 0   | 剩余次数         |
| treatment_date      | date      | -   | 否    | -   | 康复日期         |
| treatment_content   | text      | -   | 是    | -   | 康复内容         |
| created_at          | timestamp | -   | 是    | -   | 创建时间         |
| updated_at          | timestamp | -   | 是    | -   | 更新时间         |

**外键：**
- `patient_profile_id` → `patient_profiles.id`（级联删除）
- `patient_package_id` → `patient_packages.id`（删除时设为 null）

**关联关系：**
- 属于 `patientProfile`（belongsTo）
- 属于 `patientPackage`（belongsTo）
- 多对多 `employees`（通过 `consumption_record_user` 表）

---

## 9. consumption_record_user（划扣记录-员工关联表）

| 字段名                | 类型        | 长度  | 是否可为空 | 默认值 | 备注           |
| ------------------- | --------- | --- | ----- | --- | ------------ |
| id                  | bigint    | 20  | 否    | 自增  | 主键           |
| consumption_record_id | bigint | 20 | 否 | - | 划扣记录ID |
| user_id             | bigint    | 20  | 否    | -   | 员工ID         |
| commission_amount   | decimal   | 10,2 | 否   | 0    | 分配给该员工的提成金额 |
| created_at          | timestamp | -   | 是    | -   | 创建时间         |
| updated_at          | timestamp | -   | 是    | -   | 更新时间         |

**外键：**
- `consumption_record_id` → `consumption_records.id`（级联删除）
- `user_id` → `users.id`（级联删除）

**索引：** `consumption_record_id`, `user_id`

---

## 10. physical_assessments（康复体态评估表）

| 字段名                | 类型        | 长度  | 是否可为空 | 默认值 | 备注                      |
| ------------------- | --------- | --- | ----- | --- | ----------------------- |
| id                  | bigint    | 20  | 否    | 自增  | 主键                      |
| patient_profile_id  | bigint    | 20  | 否    | -   | 关联客户ID                  |
| assessment_no       | varchar   | 50  | 否    | -   | 评估编号（唯一）              |
| assessment_date     | date      | -   | 否    | -   | 评估日期                   |
| assessment_type     | tinyint   | 1   | 否    | 1   | 类型：1-初评, 2-复评, 3-末评   |
| height              | decimal   | 5,2 | 是    | -   | 身高(cm)                  |
| weight              | decimal   | 5,2 | 是    | -   | 体重(kg)                  |
| bmi                 | decimal   | 5,2 | 是    | -   | BMI                     |
| body_fat_rate       | decimal   | 5,2 | 是    | -   | 体脂率(%)                  |
| circumference       | json      | -   | 是    | -   | 围度数据                    |
| flexibility         | json      | -   | 是    | -   | 柔软度数据                  |
| posture_tags        | json      | -   | 是    | -   | 体态标签                   |
| body_canvas_path    | varchar   | 255 | 是    | -   | 图谱路径                   |
| remark              | text      | -   | 是    | -   | 备注                      |
| status              | tinyint   | 1   | 否    | 0   | 状态：0-草稿, 1-已完成        |
| created_at          | timestamp | -   | 是    | -   | 创建时间                   |
| updated_at          | timestamp | -   | 是    | -   | 更新时间                   |

**外键：** `patient_profile_id` → `patient_profiles.id`（级联删除）

**关联关系：**
- 属于 `patientProfile`（belongsTo）

---

## 11. imaging_records（影像记录表）

| 字段名                | 类型        | 长度  | 是否可为空 | 默认值 | 备注                    |
| ------------------- | --------- | --- | ----- | --- | --------------------- |
| id                  | bigint    | 20  | 否    | 自增  | 主键                    |
| patient_profile_id  | bigint    | 20  | 否    | -   | 关联客户ID                |
| record_no           | varchar   | 50  | 否    | -   | 记录编号（唯一）            |
| record_type         | tinyint   | 1   | 否    | 1   | 类型：1-康复前, 2-康复后     |
| treatment_date      | date      | -   | 否    | -   | 康复日期                 |
| photo_urls          | json      | -   | 是    | -   | 图片路径                 |
| video_url           | varchar   | 255 | 是    | -   | 视频路径                 |
| remark              | text      | -   | 是    | -   | 备注                    |
| created_at          | timestamp | -   | 是    | -   | 创建时间                 |
| updated_at          | timestamp | -   | 是    | -   | 更新时间                 |

**外键：** `patient_profile_id` → `patient_profiles.id`（级联删除）

**关联关系：**
- 属于 `patientProfile`（belongsTo）

---

## 12. Laravel 系统表

### 12.1 password_reset_tokens（密码重置 Token 表）

| 字段名       | 类型        | 长度  | 备注   |
| --------- | --------- | --- | ---- |
| email     | varchar   | 255 | 主键   |
| token     | varchar   | 255 | Token |
| created_at| timestamp | -   | 创建时间 |

### 12.2 failed_jobs（失败任务表）

| 字段名          | 类型        | 长度   | 备注     |
| ------------ | --------- | ---- | ------ |
| id           | bigint    | 20   | 主键     |
| uuid         | varchar   | 255  | 唯一标识  |
| connection   | text      | -    | 连接名称   |
| queue        | text      | -    | 队列名称   |
| payload      | longtext  | -    | 任务数据   |
| exception    | longtext  | -    | 异常信息   |
| failed_at    | timestamp | -    | 失败时间   |

### 12.3 personal_access_tokens（API Token 表）

| 字段名          | 类型        | 长度   | 备注        |
| ------------ | --------- | ---- | --------- |
| id           | bigint    | 20   | 主键        |
| tokenable_type | varchar | 255  | 可认证类型     |
| tokenable_id | bigint    | 20   | 可认证ID     |
| name         | varchar   | 255  | Token 名称 |
| token        | varchar   | 64   | Token 值  |
| abilities    | text      | -    | 权限列表      |
| last_used_at | timestamp | -    | 最后使用时间   |
| expires_at   | timestamp | -    | 过期时间      |
| created_at   | timestamp | -    | 创建时间      |
| updated_at   | timestamp | -    | 更新时间      |

---

## 已删除的表

- ~~charge_items（收费项目表）~~ - 已于 2026-04-17 移除，相关功能已整合至康复套餐表

---

## 表关联关系总览

```
users ────────< department_user >─────── departments
      │
      └─────< consumption_record_user >─────── consumption_records
                                              │
                                              └─── patient_packages
                                              │
rehab_packages（套餐字典，供客户购买参考）
                                              │
patient_profiles ────< physical_assessments
       │
       ├───────< imaging_records
       │
       └───────< patient_packages ────< consumption_records
```

**设计说明：**
- **资产与流水分离**：`patient_packages` 作为资产表记录客户的套餐余额，`consumption_records` 作为流水表记录每次消费明细
- `consumption_records` 通过 `patient_package_id` 关联具体套餐包，创建消费记录时自动扣减套餐包的 `remaining_sessions`
- **员工提成分配**：`consumption_record_user` 表记录划扣记录与员工的关联，并存储该员工的提成金额

---

*最后更新：2026-04-29*
