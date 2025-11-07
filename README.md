# Laravel Print Template

基于Laravel框架实现的打印模板管理功能，提供完整的增删改查API接口。

## 功能特性

- 打印模板的创建、读取、更新、删除操作
- 模板配置以JSON格式存储
- 自动生成模板编码（DY+四位数字格式）
- 使用oh86/laravel-http-tools包构建标准化JSON响应
- 支持软删除
- 支持关联资源
- 支持组织ID隔离（多租户）
- 自动从认证Guard获取用户ID和组织ID作为创建人和修改人信息
- 支持预加载创建者和修改者用户信息

## 安装

```bash
composer require xx19941215/laravel-print-template
```

```bash
php artisan vendor:publish --provider="Xx19941215\PrintTemplate\PrintTemplateServiceProvider"
```

```bash
php artisan migrate
```

## 配置

发布配置文件后，可以在 `config/print_template.php` 中进行配置：

- `user_model`: 用户模型类路径（默认: App\Models\Admin）
- `routes`: API路由配置

可以通过环境变量设置用户模型类路径：
```env
PRINT_TEMPLATE_USER_MODEL=App\\Models\\Admin
```

## 数据库结构

打印模板表包含以下字段：
- `id`: 主键
- `org_id`: 组织ID（用于多租户隔离）
- `assoc_type`: 关联资源类型
- `assoc_id`: 关联资源ID
- `code`: 模板编码（DY+四位数字格式）
- `name`: 模板名称
- `config`: 模板配置(JSON格式)
- `creator_id`: 创建人ID
- `modifier_id`: 修改人ID
- `modified_at`: 修改时间
- `deleted_at`: 软删除时间
- `created_at`: 创建时间
- `updated_at`: 更新时间

## API接口

### 获取模板列表
```
GET /printTemplate/list
```
参数：
- offset: 偏移量（必填）
- limit: 限制数量（必填）
- assoc_type: 关联资源类型（可选）
- assoc_id: 关联资源ID（可选）
- name: 模板名称（模糊搜索，可选）
- with_creator: 是否返回创建者信息（布尔值，默认false，可选）
- with_modifier: 是否返回修改者信息（布尔值，默认false，可选）

响应示例：
```json
{
    "code": 0,
    "message": "ok",
    "data": {
        "total": 1,
        "list": [
            {
                "id": 1,
                "org_id": 1,
                "assoc_type": null,
                "assoc_id": null,
                "code": "DY0001",
                "name": "销售合同模板",
                "config": {
                    "template": "<html>...</html>",
                    "settings": {
                        "orientation": "portrait",
                        "paper_size": "A4"
                    }
                },
                "creator_id": 1,
                "modifier_id": 1,
                "modified_at": "2023-01-01 12:00:00",
                "created_at": "2023-01-01 12:00:00",
                "updated_at": "2023-01-01 12:00:00",
                "deleted_at": null,
                "creator": {
                    "id": 1,
                    "name": "张三",
                    // ... 其他用户字段
                },
                "modifier": {
                    "id": 1,
                    "name": "张三",
                    // ... 其他用户字段
                }
            }
        ]
    }
}
```

### 创建模板
```
POST /printTemplate/create
```
参数：
- assoc_type: 关联资源类型（必填）
- assoc_id: 关联资源ID（可选）
- name: 模板名称（必填）
- config: 模板配置（数组格式，可选）

注意：org_id、creator_id和modifier_id字段会自动从认证Guard中获取当前用户信息。

### 获取模板详情
```
GET /printTemplate
```
参数：
- id: 模板ID（必填）
- with_creator: 是否返回创建者信息（布尔值，默认false，可选）
- with_modifier: 是否返回修改者信息（布尔值，默认false，可选）

### 更新模板
```
POST /printTemplate/update
```
参数：
- id: 模板ID（必填）
- assoc_type: 关联资源类型（必填）
- assoc_id: 关联资源ID（可选）
- name: 模板名称（必填）
- config: 模板配置（数组格式，可选）

注意：modifier_id字段会自动从认证Guard中获取当前用户ID。

### 删除模板
```
POST /printTemplate/delete
```
参数：
- id: 模板ID（必填）

## 响应格式

所有API接口均使用oh86/laravel-http-tools包返回标准化JSON格式响应：

成功响应：
```json
{
    "code": 0,
    "message": "ok",
    "data": {}
}
```

错误响应：
```json
{
    "code": 1,
    "message": "错误信息",
    "data": null
}
```

## 使用示例

```php
use Xx19941215\PrintTemplate\Models\PrintTemplate;

// 创建模板
$template = new PrintTemplate();
$template->assoc_type = 'contract';
$template->name = '销售合同模板';
$template->config = [
    'template' => '<html><body>销售合同模板内容</body></html>',
    'settings' => [
        'orientation' => 'portrait',
        'paper_size' => 'A4'
    ]
];
// org_id、creator_id和modifier_id会自动设置
$template->modified_at = now();
$template->save();

// 自动生成编码
$template->code = PrintTemplate::genCode($orgId);
$template->save();

// 获取模板编码
echo $template->code; // 输出: DY0001

// 获取模板列表（包含创建者和修改者信息）
$templates = PrintTemplate::with(['creator', 'modifier'])->get();

// 获取模板列表（通过参数控制是否包含创建者和修改者信息）
$templates = PrintTemplate::when($withCreator, function ($query) {
    return $query->with('creator');
})->when($withModifier, function ($query) {
    return $query->with('modifier');
})->get();
```