```markdown
# ACME 单文件管理面板 / ACME Single-file Manager

中文（简要）
---------
这是一个单文件的 ACME 管理面板，支持通过 Let's Encrypt（或 ACME v2 兼容 CA）申请证书，包含：
- RSA2048 / RSA4096 / ECC (P-256 / P-384) 密钥生成
- 泛域名（Wildcard，DNS-01）支持
- HTTP-01 / DNS-01 手动挑战流程（面板展示指令）
- 多格式导出：PEM (cert/fullchain/chain)、私钥、合并 PEM、PKCS#12 (.pfx/.p12)
- 新增：取消订单（向 ACME 提交 cancelled）、删除本地订单（可选同时删除 certs 文件）
- 界面语言：中文 / English（可切换）
- 移动端友好样式与操作日志记录（logs/actions.log）

English (Quick)
---------------
Single PHP file ACME manager for Let's Encrypt (ACME v2). Features:
- RSA2048 / RSA4096 / ECC P-256 / P-384 key support
- Wildcard certificates via DNS-01
- HTTP-01 and DNS-01 manual challenge workflows
- Export certificates in PEM, combined PEM, and PKCS#12 (.pfx/.p12)
- New: Cancel order (POST {"status":"cancelled"} to ACME order URL when available) and Delete order (local removal, optional file deletion)
- Bilingual UI: Chinese / English (switchable)
- Mobile-friendly UI and action logging (logs/actions.log)

Requirements
------------
- PHP 7.2+ recommended
- PHP extensions: openssl, curl, json
- Optional for PFX export: openssl_pkcs12_export available in PHP build
- Web server with write permissions for the web user to the repository's `data/`, `certs/`, and `logs/` directories

安装与快速开始 / Installation & Quick Start
-------------------------------------------
1. 将 `index.php` 放到你的网站根目录，确保目录可写：
   - data/ certs/ logs/
2. 修改配置：
   - 打开 `index.php`，找到 `$config->auth_password`，请立即修改为强密码或使用环境变量加载。
   - 若在测试环境请将 `$config->use = 'staging'`（使用 Let's Encrypt staging）。
3. 访问面板：`https://your-host/index.php?page=login`
4. 登录后可通过 “申请证书 / New Order” 创建订单，按页面提示在 DNS/HTTP 添加验证记录并完成验证。
5. 成功签发后可在订单详情导出所需格式或在仪表盘进行续期、取消或删除操作。

语言切换 / Language
--------------------
- UI 右上角提供中文 / English 切换，或直接访问：
  - 切换到英文: `?page=set_lang&lang=en`
  - 切换到中文: `?page=set_lang&lang=zh`

取消订单与删除（新增功能） / Cancel & Delete (New)
--------------------------------------------------
- 取消（Cancel）：对于尚未签发或待处理的订单，面板会尝试向 ACME 的 order URL POST {"status":"cancelled"}。不论远端是否接受，面板都会在本地将订单状态标记为 `cancelled` 并写入日志。
- 删除（Delete）：从本地 `orders.json` 删除订单记录。可选同时递归删除 `certs/<domain>` 目录（包含私钥）。删除前会弹出确认页面，且操作不可恢复。

安全建议 / Security
--------------------
- 必须修改默认密码（$config->auth_password）。
- 推荐在反向代理 / Web 服务器上添加 HTTP 基本认证或仅在内网访问。
- 将 `certs/` 目录权限限制为仅必要用户可读写（避免私钥泄漏）。
- 强烈建议先在 staging 环境测试（避免触发 Let's Encrypt 生产配额）。

日志与调试 / Logs & Debug
--------------------------
- 操作日志：`logs/actions.log`
- 错误调试：`logs/debug_error.log`（主要异常在申请流程中写入）

兼容性与注意事项
-----------------
- orders.json 可能包含新字段（如 `status`, `private_key_path`）。如果有外部脚本读取 orders.json，请注意兼容性。
- PKCS#12 导出依赖 PHP 的 openssl_pkcs12_export；若不可用，会提示错误。
- 删除操作不可恢复，请在删除前确认备份。

版本信息（摘要）
----------------
- 版本：v5.0（2025-12-10）
- 新增：取消订单、删除订单（可选删除文件）、中/英文语言支持、移动端优化与日志增强。

常见问题（FAQ）
---------------
Q: 如何切换到测试（staging）环境？
A: 修改 `$config->use = 'staging'`，即可使用 Let's Encrypt staging endpoint。

Q: PFX 导出报错？
A: 请确认 PHP 已编译 openssl_pkcs12_export，或在系统上安装/启用相应扩展。

贡献与许可证 / Contributing & License
--------------------------------------
- 这是单文件工具示例，按需要自行调整并在受控环境中运行。
- 请在生产环境前做好备份与访问控制。
- LICENSE: 根据仓库现有 LICENSE（如无，请自行添加合适开源协议）。

如果你希望，我可以：
- 将上述 README.md 提交为仓库文件（需 repo 写权限），或把 README 翻译为更详细的安装步骤与示例命令。
- 把语言资源抽出为独立文件（JSON），方便后续扩展更多语言。
```