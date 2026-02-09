docker镜像是运行在80端口docker镜像是部署好这个项目的镜像
The Docker image runs on port 80 and is the image deployed for this project

# 🛡️ ACME 单文件管理面板

<div align="center">

![PHP Version](https://img.shields.io/badge/PHP-7.2%2B-blue?style=flat-square&logo=php)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)
![Version](https://img.shields.io/badge/Version-5.0-orange?style=flat-square)
![Platform](https://img.shields.io/badge/Platform-Linux%20%7C%20Windows-lightgrey?style=flat-square)

**轻量级单文件 ACME 证书管理解决方案**

[English](#english) · [中文](#中文) · [部署教程](#部署教程) · [常见问题](#常见问题)

</div>

---

## 📋 目录

- [项目简介](#项目简介)
- [✨ 功能特性](#功能特性)
- [🚀 快速开始](#快速开始)
- [🌏 语言切换](#语言切换)
- [⚙️ 配置与使用](#配置与使用)
- [🔒 安全建议](#安全建议)
- [📝 日志与调试](#日志与调试)
- [❓ 常见问题](#常见问题)
- [📄 许可证](#许可证)

---

## 项目简介

### 中文简介

ACME 单文件管理面板是一个基于 PHP 的轻量级单文件 ACME 证书管理工具，支持通过 Let's Encrypt（或 ACME v2 兼容证书颁发机构）申请和管理 SSL/TLS 证书。该工具设计简洁，部署方便，无需数据库，所有数据以 JSON 格式存储，适合个人网站、小型项目和企业内部使用。

面板提供了完整的证书申请流程支持，包括 HTTP-01 和 DNS-01 两种验证方式，支持 RSA 和 ECC 多种密钥类型，可导出 PEM、PKCS#12 等多种格式的证书文件。同时具备订单管理、日志记录、移动端适配等实用功能，界面支持中英文切换，满足不同用户的使用需求。

### English Introduction

ACME Single-file Manager is a lightweight PHP-based ACME certificate management tool that supports applying for and managing SSL/TLS certificates through Let's Encrypt or ACME v2 compatible Certificate Authorities. This tool features a compact design with easy deployment, requires no database, and stores all data in JSON format, making it suitable for personal websites, small projects, and internal enterprise use.

The panel provides complete certificate application workflow support, including HTTP-01 and DNS-01 verification methods, multiple key types such as RSA and ECC, and can export certificate files in PEM, PKCS#12, and other formats. It also features order management, logging, mobile adaptation, and other practical functions, with a bilingual UI supporting Chinese and English to meet different users' needs.

---

## ✨ 功能特性

### 🔐 证书管理功能

| 功能 | 描述 | 支持情况 |
|------|------|----------|
| RSA 2048/4096 密钥生成 | 支持 RSA 算法不同密钥长度 | ✅ 完全支持 |
| ECC P-256/P-384 密钥生成 | 支持椭圆曲线密钥算法 | ✅ 完全支持 |
| 泛域名证书申请 | 通过 DNS-01 验证方式申请通配符证书 | ✅ 完全支持 |
| HTTP-01 验证 | 通过 Web 服务器路径验证域名所有权 | ✅ 完全支持 |
| DNS-01 验证 | 通过 DNS TXT 记录验证域名所有权 | ✅ 完全支持 |
| 证书导出 | 支持 PEM、完整链、私钥等多种格式 | ✅ 完全支持 |
| PKCS#12 导出 | 导出 .pfx/.p12 格式证书包 | ✅ 完全支持 |
| 订单取消 | 可向 ACME 服务器发送订单取消请求 | ✅ 完全支持 |
| 订单删除 | 本地订单记录管理，可选删除证书文件 | ✅ 完全支持 |

### 🖥️ 系统功能

| 功能 | 描述 | 支持情况 |
|------|------|----------|
| 单文件架构 | 整个应用仅需一个 PHP 文件 | ✅ 核心优势 |
| 无数据库设计 | 数据以 JSON 格式本地存储 | ✅ 轻量设计 |
| 中英文界面 | 支持界面语言实时切换 | ✅ 完整支持 |
| 移动端适配 | 响应式设计，支持手机和平板访问 | ✅ 完整支持 |
| 操作日志 | 记录所有管理操作到日志文件 | ✅ 完整支持 |
| 错误调试 | 详细的错误信息记录 | ✅ 完整支持 |

### 📦 数据存储

面板采用无数据库设计，所有数据以结构化格式存储，便于备份和迁移：

```
acme-panel/
├── index.php              # 主程序文件
├── data/
│   └── orders.json        # 订单数据存储
├── certs/
│   └── <domain>/
│       ├── cert.pem       # 证书文件
│       ├── chain.pem      # 证书链
│       ├── fullchain.pem  # 完整证书链
│       └── private.key    # 私钥文件
└── logs/
    ├── actions.log        # 操作日志
    └── debug_error.log    # 错误调试日志
```

---

## 🚀 快速开始

### 环境要求

在开始部署之前，请确保您的服务器满足以下环境要求：

| 组件 | 最低要求 | 推荐配置 |
|------|----------|----------|
| PHP 版本 | 7.2+ | 7.4 或 8.x |
| PHP 扩展 | openssl, curl, json | openssl, curl, json, mbstring |
| Web 服务器 | Apache/Nginx/IIS | Nginx 或 Apache |
| 操作系统 | Linux/Windows | Linux (Ubuntu/CentOS/Debian) |
| 磁盘空间 | 50MB+ | 100MB+ |

> **注意**：PKCS#12 (.pfx/.p12) 格式导出需要 PHP 编译时包含 `openssl_pkcs12_export` 函数支持。

### 第一步：下载程序文件

#### 方法一：直接下载

从项目仓库下载最新的 `index.php` 文件：

```bash
# 使用 wget 下载
wget https://your-domain.com/path/to/index.php

# 或使用 curl
curl -o index.php https://your-domain.com/path/to/index.php
```

#### 方法二：克隆仓库

```bash
# 克隆整个仓库
git clone https://github.com/your-username/acme-panel.git

# 进入目录
cd acme-panel

# 复制主文件到网站根目录
cp index.php /www/wwwroot/your-site/
```

### 第二步：创建必要目录

为确保程序正常运行，需要创建以下目录并设置适当权限：

```bash
# 进入网站根目录
cd /www/wwwroot/your-site/

# 创建必要的目录结构
mkdir -p data certs logs

# 设置目录权限（Linux 系统）
chmod -R 755 data certs logs

# 设置文件所有者（根据您的 Web 服务器用户调整）
# Nginx 常见用户: www-data, nginx, www
# Apache 常见用户: www-data, apache, www
chown -R www:www /www/wwwroot/your-site/

# 验证目录权限
ls -la /www/wwwroot/your-site/
```

> **重要提示**：确保 Web 服务器用户（如 `www`、`www-data`、`nginx`）对 `data/`、`certs/` 和 `logs/` 目录具有读写权限。

### 第三步：配置安全设置

#### 修改默认密码

打开 `index.php` 文件，找到配置区域并修改默认密码：

```php
// 找到以下配置项并修改
$config->auth_password = 'your_strong_password_here';
```

> **安全警告**：必须立即修改默认密码！默认密码安全性极低，不修改将导致严重安全风险。

#### 设置强密码建议

创建强密码时请遵循以下原则：

- 长度至少 16 个字符
- 包含大小写字母、数字和特殊字符
- 避免使用常见单词或个人信息
- 建议使用密码管理器生成和存储

#### 使用环境变量（推荐）

为提高安全性，建议使用环境变量存储密码：

```php
// 使用环境变量加载密码
$config->auth_password = getenv('ACME_PANEL_PASSWORD') ?: 'default_password';
```

然后在系统环境变量中设置：

```bash
# Linux/macOS - 添加到 ~/.bashrc 或 /etc/environment
export ACME_PANEL_PASSWORD="your_strong_password_here"

# Windows - 通过系统属性设置环境变量
# 或在命令行中临时设置
set ACME_PANEL_PASSWORD=your_strong_password_here
```

### 第四步：选择运行环境

#### 生产环境配置

```php
// 使用 Let's Encrypt 生产环境
$config->use = 'production';
```

#### 测试环境配置（推荐首次使用）

在正式部署前，建议先使用 Let's Encrypt 的测试环境：

```php
// 使用 Let's Encrypt staging 环境
$config->use = 'staging';
```

> **提示**：测试环境签发的证书不被浏览器信任，但不会消耗生产环境的申请配额，非常适合功能测试和配置调试。

### 第五步：访问面板

完成上述配置后，通过浏览器访问面板：

```
https://your-domain.com/index.php?page=login
```

登录后即可开始使用面板进行证书申请和管理操作。

---

## 🌏 语言切换

面板提供中英文双语界面，您可以根据需要随时切换语言。

### 界面切换方法

界面右上角提供语言切换按钮，也可以通过 URL 参数直接切换：

| 语言 | URL 参数 | 访问地址 |
|------|----------|----------|
| 中文 | `?page=set_lang&lang=zh` | `https://your-domain.com/index.php?page=set_lang&lang=zh` |
| 英文 | `?page=set_lang&lang=en` | `https://your-domain.com/index.php?page=set_lang&lang=en` |

### 切换效果

切换语言后，整个面板界面（包括所有按钮、提示信息和帮助文档）将立即以所选语言显示。所有已保存的订单和数据不会受到语言切换的影响。

---

## ⚙️ 配置与使用

### 申请新证书

#### 创建订单

1. 登录面板后，点击「申请证书 / New Order」按钮
2. 填写申请信息：
   - **域名**：输入要申请证书的域名（支持单域名和泛域名，如 `example.com` 或 `*.example.com`）
   - **密钥类型**：选择 RSA2048、RSA4096、ECC P-256 或 ECC P-384
   - **验证方式**：选择 HTTP-01 或 DNS-01
3. 提交订单申请

#### HTTP-01 验证流程

HTTP-01 验证适用于可以通过 Web 服务器访问的域名：

1. 面板会生成验证文件路径和内容
2. 在 Web 服务器上配置相应的路由规则
3. 确保 Let's Encrypt 服务器可以访问验证文件
4. 点击「完成验证 / Complete Challenge」按钮
5. 等待验证结果

#### DNS-01 验证流程

DNS-01 验证适用于泛域名或无法通过 HTTP 访问的情况：

1. 面板会生成 DNS TXT 记录名称和值
2. 登录域名服务商的控制台
3. 添加对应的 DNS TXT 记录
4. 等待 DNS 生效（通常几分钟到几小时）
5. 点击「完成验证 / Complete Challenge」按钮
6. 等待验证结果

> **提示**：DNS-01 验证支持泛域名证书申请，这是 HTTP-01 验证无法实现的功能。

### 证书导出

证书签发成功后，可以在订单详情页面导出证书：

| 导出格式 | 文件扩展名 | 用途 |
|----------|------------|------|
| 证书 | `cert.pem` | 服务器配置 |
| 证书链 | `chain.pem` | 中间证书 |
| 完整证书链 | `fullchain.pem` | 服务器配置（推荐） |
| 私钥 | `private.key` | 服务器配置 |
| 合并 PEM | `combined.pem` | 多用途 |
| PKCS#12 | `.pfx` / `.p12` | Windows、Java 应用 |

### 订单管理

#### 查看订单列表

面板首页展示所有订单的列表，包括订单状态、域名、创建时间和操作按钮。

#### 订单状态说明

| 状态 | 描述 | 可用操作 |
|------|------|----------|
| pending | 待验证 | 完成验证、取消订单 |
| processing | 处理中 | 等待完成 |
| valid | 已签发 | 导出证书、续期、删除 |
| invalid | 验证失败 | 查看原因、重新申请 |
| cancelled | 已取消 | 删除订单 |

#### 取消订单

对于尚未签发或待处理的订单，可以执行取消操作：

1. 在订单列表中找到要取消的订单
2. 点击「取消 / Cancel」按钮
3. 确认取消操作

取消操作会向 ACME 服务器发送取消请求，并将本地订单状态标记为 `cancelled`。无论远端服务器是否接受请求，本地都会完成状态更新并记录日志。

#### 删除订单

可以从本地删除订单记录：

1. 在订单列表中找到要删除的订单
2. 点击「删除 / Delete」按钮
3. 确认删除操作
4. 可选：同时删除对应的证书文件

> **警告**：删除操作不可恢复！删除前请确保已备份所有需要的证书文件。

---

## 🔒 安全建议

### 访问控制

#### 设置 HTTP 基本认证

在反向代理或 Web 服务器层面添加额外的访问控制：

**Nginx 配置示例**：

```nginx
server {
    listen 443 ssl;
    server_name your-domain.com;
    
    # SSL 配置
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/private.key;
    
    # HTTP 基本认证
    auth_basic "Restricted Area";
    auth_basic_user_file /path/to/.htpasswd;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

**Apache 配置示例**：

```apache
<VirtualHost *:443>
    ServerName your-domain.com
    
    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/private.key
    
    # HTTP 基本认证
    <Directory "/path/to/panel/">
        AuthType Basic
        AuthName "Restricted Area"
        AuthUserFile /path/to/.htpasswd
        Require valid-user
    </Directory>
</VirtualHost>
```

#### 限制 IP 访问

通过防火墙限制只有特定 IP 可以访问管理面板：

**使用 UFW（Ubuntu）**：

```bash
# 只允许特定 IP 访问
ufw allow from 192.168.1.100 to any port 443

# 或只允许内网访问
ufw allow from 192.168.0.0/16 to any port 443
```

**使用 iptables**：

```bash
# 只允许特定 IP 访问 443 端口
iptables -A INPUT -p tcp -s 192.168.1.100 --dport 443 -j ACCEPT
iptables -A INPUT -p tcp --dport 443 -j DROP
```

### 文件权限

#### 目录权限设置

遵循最小权限原则设置文件权限：

```bash
# 设置目录权限
chmod 755 /www/wwwroot/your-site/
chmod 755 /www/wwwroot/your-site/data/
chmod 755 /www/wwwroot/your-site/certs/
chmod 755 /www/wwwroot/your-site/logs/

# 设置文件权限
chmod 644 /www/wwwroot/your-site/index.php

# 敏感文件设置更严格权限
chmod 600 /www/wwwroot/your-site/data/orders.json
chmod 700 /www/wwwroot/your-site/certs/
chmod 600 /www/wwwroot/your-site/certs/*/private.key
```

#### 防止目录遍历

在 Web 服务器配置中禁用目录遍历：

**Nginx**：

```nginx
location / {
    autoindex off;
    try_files $uri $uri/ /index.php?$query_string;
}
```

**Apache**：

```apache
<Directory /www/wwwroot/your-site/>
    Options -Indexes +FollowSymLinks
</Directory>
```

### SSL/TLS 配置

确保面板本身通过 HTTPS 访问，保护认证信息传输安全：

**推荐 SSL 配置（Nginx）**：

```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com;
    
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 1d;
    
    # SSL 配置
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/private.key;
}
```

### 定期更新

保持面板程序和服务器软件更新：

```bash
# 更新 PHP（Ubuntu/Debian）
sudo apt update
sudo apt install php php-cli php-curl php-json php-openssl

# 更新 Nginx
sudo apt update
sudo apt install nginx
```

---

## 📝 日志与调试

### 日志文件位置

面板维护两个主要的日志文件：

| 日志文件 | 位置 | 用途 |
|----------|------|------|
| 操作日志 | `logs/actions.log` | 记录所有管理操作 |
| 错误日志 | `logs/debug_error.log` | 记录程序错误和异常 |

### 查看操作日志

```bash
# 查看最新操作日志
tail -f /www/wwwroot/your-site/logs/actions.log

# 或使用 less 查看
less /www/wwwroot/your-site/logs/actions.log

# 搜索特定操作
grep "certificate" /www/wwwroot/your-site/logs/actions.log
```

### 调试错误

#### 常见错误处理

**权限错误**：

```
错误信息：Permission denied
解决方法：检查 data/、certs/、logs/ 目录权限，确保 Web 服务器用户有读写权限
```

**PHP 扩展缺失**：

```
错误信息：Call to undefined function curl_init()
解决方法：安装 PHP curl 扩展
# Ubuntu/Debian
sudo apt install php-curl
# CentOS/RHEL
sudo yum install php-curl
```

**OpenSSL 错误**：

```
错误信息：OpenSSL function openssl_pkcs12_export not available
解决方法：检查 PHP OpenSSL 扩展是否启用，或使用 PEM 格式导出
```

#### 启用详细错误显示

在开发调试时，可以临时启用 PHP 错误显示：

```php
// 在 index.php 开头添加
error_reporting(E_ALL);
ini_set('display_errors', '1');
```

> **注意**：生产环境不要启用详细错误显示，以免泄露敏感信息。

### 日志轮转

为防止日志文件过大，建议配置日志轮转：

**创建 logrotate 配置文件**：

```bash
sudo nano /etc/logrotate.d/acme-panel
```

**配置文件内容**：

```
/www/wwwroot/your-site/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 644 www www
}
```

---

## ❓ 常见问题

### Q1：如何切换到测试（staging）环境？

**问题描述**：首次使用面板时，担心申请失败消耗生产环境配额。

**解决方法**：修改 `index.php` 中的配置：

```php
// 找到配置项并修改
$config->use = 'staging';
```

测试完成后，切换回生产环境：

```php
$config->use = 'production';
```

---

### Q2：PFX/PKCS#12 格式导出报错怎么办？

**问题描述**：尝试导出 .pfx 或 .p12 格式证书时出现错误。

**可能原因**：
1. PHP 未编译 `openssl_pkcs12_export` 函数
2. OpenSSL 扩展未正确加载
3. 缺少必要的 PHP 扩展

**解决方法**：

第一步，检查 PHP 配置：

```bash
# 查看 PHP 已加载的扩展
php -m | grep -i openssl

# 检查 OpenSSL 函数是否可用
php -r "echo function_exists('openssl_pkcs12_export') ? 'Available' : 'Not Available';"
```

第二步，安装或启用扩展：

```bash
# Ubuntu/Debian
sudo apt install php-openssl
sudo systemctl restart php-fpm  # 或重启 Apache

# CentOS/RHEL
sudo yum install php-openssl
sudo systemctl restart php-fpm
```

第三步，如果仍然不可用，使用 PEM 格式作为替代方案。PEM 格式在大多数服务器环境下都能正常工作。

---

### Q3：DNS-01 验证时 DNS 记录不生效怎么办？

**问题描述**：添加了 DNS TXT 记录，但验证一直失败。

**诊断步骤**：

第一步，检查 DNS 记录是否正确添加：

```bash
# 使用 dig 查询 TXT 记录
dig TXT _acme-challenge.example.com

# 或使用 nslookup
nslookup -type=TXT _acme-challenge.example.com
```

第二步，等待 DNS 传播：

DNS 记录的全球生效通常需要几分钟到几小时。您可以使用以下命令监控记录状态：

```bash
# 持续监控 DNS 记录变化
watch -n 10 "dig TXT _acme-challenge.example.com +short"
```

第三步，检查域名服务商设置：

确认域名确实解析到当前服务器，且没有设置错误的 DNSSEC 或其他限制。

---

### Q4：泛域名证书申请失败怎么办？

**问题描述**：尝试申请 `*.example.com` 类型的泛域名证书失败。

**原因分析**：泛域名证书必须使用 DNS-01 验证方式，HTTP-01 验证不支持泛域名。

**解决方法**：

第一步，在申请订单时选择 DNS-01 验证方式。

第二步，根据面板提示，在域名服务商处添加相应的 DNS TXT 记录。

第三步，等待 DNS 记录生效后完成验证。

第四步，如果仍然失败，检查以下几点：
- 域名是否正确配置 DNS 服务器
- 是否存在冲突的 DNS 记录
- DNS 服务商是否支持自动验证

---

### Q5：如何备份和恢复证书数据？

**问题描述**：需要迁移服务器或备份证书数据。

**备份步骤**：

第一步，备份整个面板目录：

```bash
# 使用 tar 打包
tar -czvf acme-panel-backup-$(date +%Y%m%d).tar.gz /www/wwwroot/your-site/

# 或只备份关键数据
cp -r /www/wwwroot/your-site/data/ /backup/path/
cp -r /www/wwwroot/your-site/certs/ /backup/path/
```

第二步，导出 orders.json：

```bash
cat /www/wwwroot/your-site/data/orders.json | jq '.' > orders-backup.json
```

**恢复步骤**：

第一步，将备份文件恢复到新服务器：

```bash
# 解压备份
tar -xzvf acme-panel-backup-20240101.tar.gz -C /

# 恢复数据目录
cp -r /backup/path/data/* /www/wwwroot/your-site/data/
cp -r /backup/path/certs/* /www/wwwroot/your-site/certs/
```

第二步，设置正确的文件权限：

```bash
chown -R www:www /www/wwwroot/your-site/
chmod -R 755 /www/wwwroot/your-site/
```

第三步，验证恢复结果：

```bash
# 检查 orders.json 是否完整
cat /www/wwwroot/your-site/data/orders.json | jq '.' | head -20
```

---

### Q6：移动端访问界面异常怎么办？

**问题描述**：在手机或平板上访问面板时显示异常。

**原因分析**：面板采用响应式设计，但某些浏览器或屏幕尺寸可能导致显示问题。

**解决方法**：

第一步，尝试清除浏览器缓存：

```javascript
// 在浏览器控制台执行
location.reload(true);
```

第二步，尝试不同的浏览器访问：

推荐使用以下浏览器的最新版本：
- Chrome（桌面版和移动版）
- Firefox
- Safari
- Edge

第三步，检查浏览器缩放设置：

确保页面缩放比例在 100% 左右，过大或过小的缩放可能影响布局显示。

---

### Q7：orders.json 文件格式变化会影响现有数据吗？

**问题描述**：更新面板版本后担心 orders.json 格式变化导致数据丢失。

**说明**：

面板在更新版本时可能会在 orders.json 中添加新字段（如 `status`、`private_key_path` 等）。这些变化是向后兼容的，不会影响现有数据的读取和使用。

**注意事项**：

如果您有外部脚本直接读取 orders.json，请注意以下几点：
- 新字段会被添加到 JSON 对象中
- 脚本应忽略未知字段而不是报错
- 建议使用 JSON 解析库而不是正则表达式

**建议**：

在更新面板版本前，备份 orders.json：

```bash
cp /www/wwwroot/your-site/data/orders.json /backup/path/orders.json.$(date +%Y%m%d)
```

---

## 📄 许可证

本项目采用 MIT 许可证开源。您可以自由使用、修改和分发本软件，但需要保留原始版权声明和许可证文本。

```
MIT License

Copyright (c) 2025 ACME Panel Contributors

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 🧪 功能测试状态

以下表格列出了各功能模块的测试状态，确保所有功能正常运行：

| 功能模块 | 功能项 | 状态 | 测试说明 |
|----------|--------|------|----------|
| **语言切换** | 中文界面 | ✅ 通过 | 点击"中文"按钮正常切换，按钮高亮状态正确 |
| **语言切换** | 英文界面 | ✅ 通过 | 点击"English"按钮正常切换，按钮高亮状态正确 |
| **语言切换** | 按钮高亮 | ✅ 通过 | 当前语言按钮正确显示高亮状态 |
| **FAQ 模块** | 展开/收起 | ✅ 通过 | 点击问题可正常展开/收起答案 |
| **代码复制** | 复制功能 | ✅ 通过 | 点击 Copy 按钮可复制代码内容 |
| **响应式设计** | 桌面端 | ✅ 通过 | 1920px/1440px 等分辨率显示正常 |
| **响应式设计** | 平板端 | ✅ 通过 | 768px/1024px 等分辨率显示正常 |
| **响应式设计** | 移动端 | ✅ 通过 | 375px/414px 等分辨率显示正常 |
| **表格展示** | 功能表格 | ✅ 通过 | 表格内容显示完整，响应式换行正常 |
| **代码块** | 语法高亮 | ✅ 通过 | 代码块背景、对齐正常 |
| **导航栏** | 固定定位 | ✅ 通过 | 导航栏固定在顶部，滚动时正常显示 |

### 最近更新

| 日期 | 版本 | 更新内容 |
|------|------|----------|
| 2025-12-10 | v5.0 | 初始版本发布 |
| 2025-12-10 | v5.0.1 | **语言切换修复**：移除内联 onclick 属性，使用 data-lang 属性和 addEventListener 绑定事件，确保语言切换功能完全正常工作 |
| 2025-12-10 | v5.0.1 | **代码优化**：简化 initSmoothScroll 函数，移除不需要的语言特定 ID 处理逻辑 |
| 2025-12-10 | v5.0.1 | **调试增强**：在 switchLang 函数中添加 console.log 调试信息，便于排查问题 |

### 已知问题

当前版本未发现已知问题。如有任何问题，请查看[常见问题](#常见问题)部分或提交 Issue。

---

## 📞 技术支持

如果您在使用过程中遇到问题，可以通过以下方式获取帮助：

- 查看本文档的[常见问题](#常见问题)部分
- 查看日志文件 `logs/debug_error.log` 获取详细错误信息
- 在 GitHub 仓库提交 Issue

---

<div align="center">

**ACME 单文件管理面板** · 轻量级 · 高性能 · 易部署

*Version 5.0.1 · 2025-12-10*

</div>
