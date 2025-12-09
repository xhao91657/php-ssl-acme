# PHP ACME 客户端 单文件最新更新版本 旧版本已删档
说明（中文）

功能概述
- 通过 Let’s Encrypt ACME v2 API 向 CA 申请证书。
- 支持 HTTP-01 与 DNS-01（手动）：显示需要添加的文件 / TXT 内容，管理员手动在目标域上添加后点击“开始验证”。
- 验证通过后生成域名私钥与 CSR，向 ACME 完成签发并保存证书（certs/<domain>/）。
- 内部管理界面，密码保护（默认密码：12345678）。
- 保存操作历史与日志（data/orders.json、logs/actions.log）。
- 提供续期入口 renew.php。可通过外部定时任务访问此接口实现“自动续期”。
- 默认使用 Let's Encrypt 正式证书。

部署步骤
1. 检查 PHP 环境
   - PHP >= 7.2 推荐。
   - 必需扩展：openssl, curl, json
   - 确保 PHP 进程有权在站点目录下创建以下目录： data/, certs/, logs/
2. 上传文件
   - 将本仓库中的所有 PHP 文件部署到你的站点根目录（域名对应的根）。
3. 创建初始目录（若没有自动创建）
   - data/ （持久化账户、订单）
   - certs/ （证书保存）
   - logs/ （操作日志）
   - 这些目录的权限需要 PHP 写入权限（例如 755/775 视主机而定）。
4. 修改配置（可选）
   - 编辑 config.php，设置 `ACME_DIRECTORY` 为 staging 或 production（生产换为 https://acme-v02.api.letsencrypt.org/directory）。
   - 修改 `AUTH_PASSWORD`（默认 12345678）以提高安全性。
5. 访问与使用
   - 打开浏览器访问站点根目录，会跳到登录页面，输入密码进入 Dashboard。
   - 新建证书申请：填写邮箱、主域（和可选 SAN 列表），选择验证方式 HTTP 或 DNS。
   - 页面会显示具体“要在目标域上添加的内容”。目标域负责在其服务器 / DNS 上配置该内容并让 CA 可访问/查询。
   - 添加完毕后点击“我已完成，开始验证”。系统会向 ACME 报告挑战并轮询验证状态，验证成功后会完成签发并保存证书。
6. 自动续期（建议）
   - 由于无 shell 权限，你可以在外部定时任务（或你的托管面板）中配置定期访问 `https://your-site/renew.php`（带登录 session 或 token）（参考 README 中说明）来触发自动续期。
   - 我也在 Dashboard 中实现了访问触发逻辑：每次登录会检查并尝试续期即将到期的证书。

安全注意
- 私钥与证书非常敏感，请确保 `certs` 目录不可被公网列目录浏览或直接访问（建议在 Web 服务器上设置访问限制，或将其放置在站点根之外）。
- 强烈建议修改默认登录密码 `AUTH_PASSWORD`。
- 在生产环境下切换 `ACME_DIRECTORY` 到 production 前请确认逻辑与密钥管理已妥善处理。

调试与日志
- 日志文件： logs/actions.log
- 操作历史： data/orders.json

支持与限制
- 本实现为轻量级纯 PHP 客户端，适合内部使用与手动验证流程。若需自动使用 DNS API、扩展更多自动化，请考虑给出 DNS API 凭证并允许安装 acme.sh / certbot 的更完整解决方案。
