<?php
// ACME 管理面板 - 单文件版（增加：RSA4096、ECC、泛域名支持 + 多格式下载：PEM/CRT/CHAIN/PKCS#12/PCKS12）
// 已增加：语言切换（中文/English）
// 使用说明：将此文件放到站点根目录，访问 ?page=login 并登录后使用。"production" 默认会向 Let's Encrypt 正式 API 请求证书。
// 安全提醒：请立即修改 $config->auth_password 并通过防火墙 / HTTP 基本认证限制面板访问。

session_start();

// --------------------- 配置 ---------------------
$config = (object)[
    'use' => 'production', // 'production' 或 'staging'
    'directories' => (object)[
        'staging' => 'https://acme-staging-v02.api.letsencrypt.org/directory',
        'production' => 'https://acme-v02.api.letsencrypt.org/directory',
    ],
    'auth_password' => '12345678', // 请修改为强密码或从环境变量读取
    'data_dir' => __DIR__ . '/data',
    'certs_dir' => __DIR__ . '/certs',
    'logs_dir' => __DIR__ . '/logs',
    'poll_interval_seconds' => 3,
    'poll_max_attempts' => 50,
    'auto_check_on_dashboard' => true,
];

// --------------------- 语言支持（中文 / English） ---------------------
// 默认中文；若希望默认英文，可改为 'en'
if (!isset($_SESSION['lang'])) $_SESSION['lang'] = 'zh';
$lang = $_SESSION['lang'];

$TRANSLATIONS = [
    // 通用
    'app_title' => ['zh'=>'ACME 管理', 'en'=>'ACME Manager'],
    'dashboard' => ['zh'=>'控制台', 'en'=>'Dashboard'],
    'new_order' => ['zh'=>'申请证书', 'en'=>'New Order'],
    'certs' => ['zh'=>'证书列表', 'en'=>'Certificates'],
    'logs' => ['zh'=>'日志', 'en'=>'Logs'],
    'diagnostics' => ['zh'=>'诊断', 'en'=>'Diagnostics'],
    'logout' => ['zh'=>'登出', 'en'=>'Logout'],
    'login' => ['zh'=>'登录', 'en'=>'Login'],
    'confirm' => ['zh'=>'确认', 'en'=>'Confirm'],

    // login page
    'login_title' => ['zh'=>'登录 - ACME 管理', 'en'=>'Login - ACME Manager'],
    'password' => ['zh'=>'密码', 'en'=>'Password'],
    'password_wrong' => ['zh'=>'密码错误', 'en'=>'Incorrect password'],

    // dashboard/page texts
    'welcome' => ['zh'=>'欢迎', 'en'=>'Welcome'],
    'current_directory' => ['zh'=>'当前目录', 'en'=>'Current directory'],
    'wildcard_note' => ['zh'=>'泛域名（Wildcard）仅支持 DNS-01 验证。', 'en'=>'Wildcard certificates require DNS-01 validation.'],
    'no_orders' => ['zh'=>'暂无证书记录。', 'en'=>'No saved certificates.'],
    'id' => ['zh'=>'ID', 'en'=>'ID'],
    'domain' => ['zh'=>'域名', 'en'=>'Domain'],
    'type' => ['zh'=>'类型', 'en'=>'Type'],
    'status' => ['zh'=>'状态', 'en'=>'Status'],
    'expires' => ['zh'=>'到期', 'en'=>'Expires'],
    'actions' => ['zh'=>'操作', 'en'=>'Actions'],
    'view' => ['zh'=>'查看', 'en'=>'View'],
    'renew' => ['zh'=>'续期', 'en'=>'Renew'],
    'cancel' => ['zh'=>'取消', 'en'=>'Cancel'],
    'delete' => ['zh'=>'删除', 'en'=>'Delete'],

    // new order form
    'new_order_title' => ['zh'=>'申请新证书', 'en'=>'Request a new certificate'],
    'email' => ['zh'=>'邮箱（用于注册 ACME 账户）', 'en'=>'Email (used for ACME account)'],
    'main_domain' => ['zh'=>'主域名', 'en'=>'Main domain'],
    'extra_domains' => ['zh'=>'额外域名（逗号分隔）', 'en'=>'Extra domains (comma separated)'],
    'request_wildcard' => ['zh'=>'请求泛域名证书（Wildcard）', 'en'=>'Request wildcard certificate'],
    'challenge_type' => ['zh'=>'挑战类型', 'en'=>'Challenge type'],
    'http01' => ['zh'=>'HTTP-01（手动上传 .well-known 文件）', 'en'=>'HTTP-01 (manual .well-known file)'],
    'dns01' => ['zh'=>'DNS-01（手动添加 TXT 记录）', 'en'=>'DNS-01 (manual TXT record)'],
    'key_type' => ['zh'=>'证书密钥类型', 'en'=>'Key type'],
    'submit_order' => ['zh'=>'提交申请', 'en'=>'Submit'],

    // challenge instructions
    'instructions' => ['zh'=>'请按以下说明为每个授权添加验证', 'en'=>'Please follow the instructions below to complete each authorization'],
    'http01_file' => ['zh'=>'在目标域的 HTTP 根目录创建文件：', 'en'=>'Create a file at the site root:'],
    'dns_txt' => ['zh'=>'在目标域的 DNS 中添加 TXT 记录：', 'en'=>'Add a TXT record to the domain DNS:'],
    'record_name' => ['zh'=>'记录名（主机名）', 'en'=>'Record name (host)'],
    'record_value' => ['zh'=>'记录值（TXT）', 'en'=>'Record value (TXT)'],
    'i_added_start_verify' => ['zh'=>'我已添加，开始验证（针对该验证项）', 'en'=>'I have added it, start verification (this item)'],

    // verify challenge
    'verify_title' => ['zh'=>'验证挑战', 'en'=>'Verify challenge'],
    'precheck_failed' => ['zh'=>'预检查显示验证内容尚未生效。ACME 可能会失败。请确保域名配置正确再继续。', 'en'=>'Pre-check shows the validation is not yet active. ACME may fail. Ensure DNS/HTTP is configured and try again.'],
    'precheck_ok' => ['zh'=>'预检查通过，继续向 ACME 报告挑战。', 'en'=>'Pre-check OK. Reporting challenge to ACME.'],
    'polling' => ['zh'=>'正在轮询授权状态（最多约 %s 秒）……', 'en'=>'Polling authorization status (up to ~%s seconds) ...'],
    'verify_failed' => ['zh'=>'验证未通过，状态：%s', 'en'=>'Verification did not pass, status: %s'],
    'finalize_missing' => ['zh'=>'订单缺少 finalize URL。', 'en'=>'Order missing finalize URL.'],

    // certificate saved
    'cert_saved' => ['zh'=>'证书签发成功，已保存到 %s。', 'en'=>'Certificate issued and saved to %s.'],
    'view_and_download' => ['zh'=>'查看订单并下载证书', 'en'=>'View order and download'],

    // cancel/delete
    'cancel_confirm_title' => ['zh'=>'取消订单确认', 'en'=>'Cancel order confirmation'],
    'cancel_confirm_msg' => ['zh'=>'取消后，ACME 服务器上的该订单将会被标记为 cancelled，本地订单状态也会更新为 cancelled。已签发的证书文件不会被自动删除。', 'en'=>'Cancelling will mark the order as cancelled on the ACME server (if possible) and locally. Existing certificate files are not removed automatically.'],
    'delete_confirm_title' => ['zh'=>'删除订单确认', 'en'=>'Delete order confirmation'],
    'delete_confirm_msg' => ['zh'=>'删除操作会移除本地订单记录。若你确认，也可以选择同时删除存放在 certs 目录下对应的证书文件（包含私钥）。此操作不可恢复。', 'en'=>'This will remove the local order record. Optionally delete related certificate files (including private key). This action is irreversible.'],
    'delete_files_checkbox' => ['zh'=>'同时删除本地证书文件（如果存在）', 'en'=>'Also remove local certificate files (if present)'],
    'deleted_local' => ['zh'=>'已从本地移除订单 %s', 'en'=>'Locally deleted order %s'],

    // download
    'download_formats' => ['zh'=>'格式', 'en'=>'Format'],
    'pfx_password' => ['zh'=>'PKCS#12 密码（可选）', 'en'=>'PKCS#12 password (optional)'],
    'download_btn' => ['zh'=>'生成并下载', 'en'=>'Generate & Download'],
    'pfx_note' => ['zh'=>'说明：PKCS#12 若设置密码，导出的 .pfx 会使用该密码保护私钥。', 'en'=>'Note: if set, the PKCS#12 file will be protected with the provided password.'],

    // logs / diagnostics
    'operation_logs' => ['zh'=>'操作日志', 'en'=>'Operation logs'],
    'env_diag' => ['zh'=>'环境诊断', 'en'=>'Environment diagnostics'],
];

// 简单翻译函数
function L($key, ...$args) {
    global $TRANSLATIONS;
    $lang = $_SESSION['lang'] ?? 'zh';
    $txt = $TRANSLATIONS[$key][$lang] ?? ($TRANSLATIONS[$key]['zh'] ?? $key);
    if (!empty($args)) {
        return vsprintf($txt, $args);
    }
    return $txt;
}

// 切换语言处理（通过 page=set_lang&lang=xx）
if (isset($_GET['page']) && $_GET['page'] === 'set_lang') {
    $new = $_GET['lang'] ?? null;
    if ($new && in_array($new, ['zh','en'])) {
        $_SESSION['lang'] = $new;
    }
    $referer = $_SERVER['HTTP_REFERER'] ?? '?page=dashboard';
    header('Location: ' . $referer);
    exit;
}

// --------------------- 路由 ---------------------
$action = $_GET['page'] ?? 'dashboard';
if (isset($_GET['action'])) $action = $_GET['action'];

// --------------------- 公共函数（整合与增强） ---------------------
function ensure_dirs_global($cfg) {
    foreach (['data_dir','certs_dir','logs_dir'] as $k) {
        $d = $cfg->$k;
        if (!is_dir($d)) mkdir($d, 0755, true);
    }
}
ensure_dirs_global($config);

function log_action($msg) {
    global $config;
    $line = "[" . date('c') . "] " . $msg . PHP_EOL;
    file_put_contents($config->logs_dir . '/actions.log', $line, FILE_APPEND);
}

function json_load($path) {
    if (!file_exists($path)) return [];
    $c = file_get_contents($path);
    $j = json_decode($c, true);
    return $j ?: [];
}
function json_save($path, $data) {
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function b64u($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function http_request($url, $method='GET', $headers = [], $body = null, $timeout = 30) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HEADER, true);
    if (!empty($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $resp = curl_exec($ch);
    $errno = curl_errno($ch);
    $err = curl_error($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    if ($resp === false) {
        return ['body'=>null, 'raw_headers'=>'', 'headers'=>[], 'info'=>$info, 'errno'=>$errno, 'error'=>$err];
    }

    $header_size = $info['header_size'] ?? 0;
    $raw_headers = substr($resp, 0, $header_size);
    $body = substr($resp, $header_size);

    $headers_arr = [];
    $lines = preg_split("/\r\n|\n|\r/", $raw_headers);
    foreach ($lines as $line) {
        if (strpos($line, ':') !== false) {
            list($k,$v) = explode(':', $line, 2);
            $k = strtolower(trim($k));
            $v = trim($v);
            if (isset($headers_arr[$k])) $headers_arr[$k] .= ', ' . $v; else $headers_arr[$k] = $v;
        }
    }

    return ['body'=>$body, 'raw_headers'=>$raw_headers, 'headers'=>$headers_arr, 'info'=>$info, 'errno'=>$errno, 'error'=>$err];
}

function get_acme_directory($cfg) {
    $url = $cfg->directories->{$cfg->use};
    $r = http_request($url, 'GET', [], null, 10);
    if ($r['errno'] !== 0) throw new Exception("Failed to fetch ACME directory: {$r['error']}");
    $d = json_decode($r['body'], true);
    if (!$d) throw new Exception("Invalid ACME directory response");
    return $d;
}

function get_nonce($dir) {
    $url = $dir['newNonce'];
    $r = http_request($url, 'HEAD', [], null, 10);
    if (!empty($r['headers']['replay-nonce'])) return $r['headers']['replay-nonce'];
    $r2 = http_request($url, 'GET', [], null, 10);
    if (!empty($r2['headers']['replay-nonce'])) return $r2['headers']['replay-nonce'];
    foreach ([$r, $r2] as $res) {
        if (!empty($res['raw_headers'])) {
            if (preg_match("/Replay-Nonce:\s*(\S+)/i", $res['raw_headers'], $m)) {
                return trim($m[1]);
            }
        }
    }
    throw new Exception("Unable to obtain Replay-Nonce from ACME server");
}

// 账户密钥暂保持 RSA2048（兼容性）
function rsa_generate($bits=2048) {
    $res = openssl_pkey_new(['private_key_bits' => $bits, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    openssl_pkey_export($res, $privkey_pem);
    return $privkey_pem;
}

function jwk_from_pem_rsa($pem) {
    $res = openssl_pkey_get_private($pem);
    $details = openssl_pkey_get_details($res);
    if (!$details || !isset($details['rsa'])) throw new Exception("Unable to extract RSA details");
    $n = b64u($details['rsa']['n']);
    $e = b64u($details['rsa']['e']);
    return ['kty'=>'RSA','n'=>$n,'e'=>$e];
}

function jws_sign($url, $payload_obj, $privkey_pem, $nonce, $kid = null) {
    $payload = json_encode($payload_obj);
    $jwk = jwk_from_pem_rsa($privkey_pem);
    $protected = ['alg'=>'RS256', 'nonce'=>$nonce, 'url'=>$url];
    if ($kid) $protected['kid'] = $kid; else $protected['jwk'] = $jwk;
    $protected_b64 = b64u(json_encode($protected));
    $payload_b64 = b64u($payload);
    $data = $protected_b64 . '.' . $payload_b64;
    $res = openssl_sign($data, $signature, $privkey_pem, OPENSSL_ALGO_SHA256);
    if (!$res) throw new Exception("OpenSSL sign failed");
    $sig_b64 = b64u($signature);
    return json_encode(['protected'=>$protected_b64, 'payload'=>$payload_b64, 'signature'=>$sig_b64]);
}

function acme_signed_post($url, $payload_obj, $account, $dir) {
    $nonce = get_nonce($dir);
    $signed = jws_sign($url, $payload_obj, $account['privkey_pem'], $nonce, isset($account['kid']) ? $account['kid'] : null);
    $r = http_request($url, 'POST', ['Content-Type: application/jose+json'], $signed, 30);
    $body = $r['body'];
    $info = $r['info'];
    $http_code = $info['http_code'] ?? 0;
    $json = json_decode($body, true);
    return ['code'=>$http_code, 'body'=>$body, 'json'=>$json, 'headers'=>$r['headers'], 'info'=>$info, 'curl_errno'=>$r['errno'], 'curl_err'=>$r['error']];
}

function account_store_path($cfg) {
    return $cfg->data_dir . '/accounts.json';
}
function account_get($email) {
    global $config;
    $path = account_store_path($config);
    $all = json_load($path);
    return $all[$email] ?? null;
}
function account_save($email, $account) {
    global $config;
    $path = account_store_path($config);
    $all = json_load($path);
    $all[$email] = $account;
    json_save($path, $all);
}

function acme_ensure_account_kid($email, $acct, $dir) {
    if (!empty($acct['kid'])) {
        $kid_url = $acct['kid'];
        $parsed_kid = parse_url($kid_url);
        $parsed_dir = parse_url($dir['newAccount']);
        if ($parsed_kid !== false && $parsed_dir !== false) {
            $kid_host = $parsed_kid['host'] ?? null;
            $dir_host = $parsed_dir['host'] ?? null;
            if ($kid_host && $dir_host && strtolower($kid_host) === strtolower($dir_host)) {
                return $acct;
            }
        }
    }

    $payload = ['termsOfServiceAgreed'=>true, 'contact'=>["mailto:{$email}"]];
    $res = acme_signed_post($dir['newAccount'], $payload, $acct, $dir);
    if ($res['code'] == 201 || $res['code'] == 200) {
        $kid = null;
        if (!empty($res['headers']['location'])) {
            $kid = $res['headers']['location'];
        } else {
            $raw = $res['info']['request_header'] ?? '';
            if (!$kid && preg_match('/Location:\s*(\S+)/i', $raw, $m)) $kid = $m[1];
        }
        if ($kid) {
            $acct['kid'] = $kid;
            account_save($email, $acct);
            log_action("Updated account kid for {$email}");
            return $acct;
        } else {
            account_save($email, $acct);
            log_action("acme_ensure_account_kid: no Location header returned for {$email} (code {$res['code']})");
            return $acct;
        }
    } else {
        throw new Exception("Failed to ensure account kid: HTTP {$res['code']} Body: {$res['body']}");
    }
}

function acme_create_account($email, $dir) {
    $privpem = rsa_generate(2048); // account key stays RSA2048
    $acct = ['email'=>$email, 'privkey_pem'=>$privpem, 'kid'=>null];
    $payload = ['termsOfServiceAgreed'=>true, 'contact'=>["mailto:{$email}"]];
    $res = acme_signed_post($dir['newAccount'], $payload, $acct, $dir);
    if ($res['code'] == 201 || $res['code'] == 200) {
        $kid = null;
        if (!empty($res['headers']['location'])) {
            $kid = $res['headers']['location'];
        } else {
            $raw = $res['info']['request_header'] ?? '';
            if (!$kid && preg_match('/Location:\s*(\S+)/i', $raw, $m)) $kid = $m[1];
        }
        if ($kid) $acct['kid'] = $kid;
        account_save($email, $acct);
        log_action("Created ACME account for {$email}");
        return $acct;
    } else {
        throw new Exception("Failed to create ACME account: HTTP {$res['code']} Body: {$res['body']}");
    }
}

function acme_new_order($account, $dir, $identifiers) {
    $payload = ['identifiers'=>$identifiers];
    $res = acme_signed_post($dir['newOrder'], $payload, $account, $dir);
    if ($res['code'] == 201 || $res['code'] == 200) {
        $order = $res['json'] ?? [];
        if (!empty($res['headers']['location'])) {
            $order['_order_url'] = $res['headers']['location'];
        }
        return $order;
    } else {
        throw new Exception("Failed to create order: HTTP {$res['code']} Body: {$res['body']}");
    }
}

function acme_get($url, $account, $dir) {
    $nonce = get_nonce($dir);
    $protected = ['alg'=>'RS256','nonce'=>$nonce,'url'=>$url];
    if (isset($account['kid']) && $account['kid']) $protected['kid'] = $account['kid']; else $protected['jwk'] = jwk_from_pem_rsa($account['privkey_pem']);
    $protected_b64 = b64u(json_encode($protected));
    $payload_b64 = b64u('');
    $data = $protected_b64 . '.' . $payload_b64;
    openssl_sign($data, $sig, $account['privkey_pem'], OPENSSL_ALGO_SHA256);
    $sig_b64 = b64u($sig);
    $jws = json_encode(['protected'=>$protected_b64,'payload'=>$payload_b64,'signature'=>$sig_b64]);

    $r = http_request($url, 'POST', ['Content-Type: application/jose+json'], $jws, 30);
    $json = json_decode($r['body'], true);
    return ['code'=>$r['info']['http_code'] ?? 0, 'json'=>$json, 'body'=>$r['body'], 'info'=>$r['info'], 'headers'=>$r['headers']];
}

function acme_respond_challenge($challenge_url, $account, $dir) {
    return acme_signed_post($challenge_url, new stdClass(), $account, $dir);
}

function poll_authorization($auth_url, $account, $dir, $interval, $max_attempts) {
    for ($i=0;$i<$max_attempts;$i++) {
        $res = acme_get($auth_url, $account, $dir);
        if (isset($res['json']['status'])) {
            if ($res['json']['status'] == 'valid') return ['status'=>'valid','json'=>$res['json']];
            if ($res['json']['status'] == 'invalid') return ['status'=>'invalid','json'=>$res['json']];
        }
        sleep($interval);
    }
    return ['status'=>'timeout'];
}

function poll_order($order_url, $account, $dir, $interval, $max_attempts) {
    for ($i=0;$i<$max_attempts;$i++) {
        $res = acme_get($order_url, $account, $dir);
        if (isset($res['json']['status'])) {
            if ($res['json']['status'] == 'valid' && isset($res['json']['certificate'])) return ['status'=>'valid','json'=>$res['json']];
            if ($res['json']['status'] == 'processing' || $res['json']['status']=='pending') {
            }
            if ($res['json']['status'] == 'invalid') return ['status'=>'invalid','json'=>$res['json']];
        }
        sleep($interval);
    }
    return ['status'=>'timeout'];
}

// create_csr_and_key：支持 rsa2048 / rsa4096 / ecp256 / ecp384
function create_csr_and_key($domains, $key_type = 'rsa') {
    $dn = ['commonName' => $domains[0]];
    $privkey = null;
    $privkey_pem = null;
    $csr_res = null;

    if ($key_type === 'rsa') {
        $privkey = openssl_pkey_new(['private_key_bits'=>2048, 'private_key_type'=>OPENSSL_KEYTYPE_RSA]);
    } elseif ($key_type === 'rsa4096') {
        $privkey = openssl_pkey_new(['private_key_bits'=>4096, 'private_key_type'=>OPENSSL_KEYTYPE_RSA]);
    } else {
        $curve = 'prime256v1';
        if ($key_type === 'ecp384' || $key_type === 'p384' || $key_type === 'secp384r1') $curve = 'secp384r1';
        if ($key_type === 'ecp256' || $key_type === 'p256' || $key_type === 'prime256v1') $curve = 'prime256v1';
        $privkey = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => $curve]);
    }

    if (!$privkey) throw new Exception("Failed to generate private key: " . openssl_error_string());
    $san = "DNS:" . implode(",DNS:", $domains);
    $tmpcnf = tempnam(sys_get_temp_dir(), 'acme_cnf_');
    $cnf = <<<EOCNF
[req]
distinguished_name = req_distinguished_name
req_extensions = v3_req
[req_distinguished_name]
[v3_req]
subjectAltName = {$san}
EOCNF;
    file_put_contents($tmpcnf, $cnf);

    $csr_res = openssl_csr_new($dn, $privkey, ['config' => $tmpcnf, 'digest_alg'=>'sha256', 'req_extensions'=>'v3_req']);
    if (!$csr_res) {
        unlink($tmpcnf);
        throw new Exception("Failed to create CSR: " . openssl_error_string());
    }
    openssl_csr_export($csr_res, $csr_pem);
    openssl_pkey_export($privkey, $privkey_pem);
    unlink($tmpcnf);
    return ['csr_pem'=>$csr_pem, 'privkey_pem'=>$privkey_pem];
}

function csr_pem_to_b64u_der($csr_pem) {
    $lines = explode("\n", trim($csr_pem));
    $data = '';
    foreach ($lines as $line) {
        if (strpos($line, '-----') === 0) continue;
        $data .= trim($line);
    }
    $der = base64_decode($data);
    return b64u($der);
}

function acme_finalize_order($finalize_url, $csr_b64u, $account, $dir) {
    $payload = ['csr'=>$csr_b64u];
    return acme_signed_post($finalize_url, $payload, $account, $dir);
}

function acme_get_certificate_pem($cert_url, $account, $dir) {
    $res = acme_get($cert_url, $account, $dir);
    if ($res['code'] == 200 && $res['body']) {
        return $res['body'];
    }
    throw new Exception("Failed to download certificate: HTTP {$res['code']}");
}

function check_http_challenge_path($domain, $token, $expected_content) {
    $url = "http://{$domain}/.well-known/acme-challenge/{$token}";
    $r = http_request($url, 'GET', [], null, 10);
    if ($r['errno'] !== 0) return false;
    $body = $r['body'];
    return trim($body) === trim($expected_content);
}

function check_dns_txt($domain, $expected_txt) {
    $txts = @dns_get_record($domain, DNS_TXT);
    if (!$txts) return false;
    foreach ($txts as $t) {
        if (isset($t['txt']) && trim($t['txt']) === trim($expected_txt)) return true;
    }
    return false;
}

function orders_store_path($cfg) {
    return $cfg->data_dir . '/orders.json';
}
function orders_save_entry($id, $entry) {
    global $config;
    $path = orders_store_path($config);
    $all = json_load($path);
    $all[$id] = $entry;
    json_save($path, $all);
}
function orders_load_all() {
    global $config;
    $path = orders_store_path($config);
    return json_load($path);
}
function orders_delete_entry($id) {
    global $config;
    $path = orders_store_path($config);
    $all = json_load($path);
    if (isset($all[$id])) {
        unset($all[$id]);
        json_save($path, $all);
        return true;
    }
    return false;
}

// --------------------- 取消订单 / 删除文件工具 ---------------------
function acme_cancel_order($order_url, $account, $dir) {
    // According to ACME RFC, posting {"status":"cancelled"} to order URL should cancel it.
    $payload = ['status'=>'cancelled'];
    $res = acme_signed_post($order_url, $payload, $account, $dir);
    return $res;
}

function rrmdir($dir) {
    if (!is_dir($dir)) return;
    $objects = scandir($dir);
    foreach ($objects as $object) {
        if ($object == "." || $object == "..") continue;
        $path = $dir . DIRECTORY_SEPARATOR . $object;
        if (is_dir($path)) rrmdir($path); else @unlink($path);
    }
    @rmdir($dir);
}

// --------------------- 辅助：解析证书链，导出 PKCS#12 ---------------------
function split_pem_chain($pem_chain) {
    // returns array of cert PEM blocks in order (leaf first)
    $parts = preg_split('/(?=-----BEGIN CERTIFICATE-----)/', trim($pem_chain));
    $out = [];
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p) $out[] = $p;
    }
    return $out;
}

function build_pkcs12($cert_pem_chain, $privkey_pem, $password = '') {
    if (!function_exists('openssl_pkcs12_export')) {
        throw new Exception("openssl_pkcs12_export is not available on this PHP build");
    }
    $parts = split_pem_chain($cert_pem_chain);
    if (count($parts) == 0) throw new Exception("证书内容无效");
    $leaf = array_shift($parts); // first is leaf
    $extracerts = $parts; // array of intermediates
    $args = [];
    if (!empty($extracerts)) $args['extracerts'] = $extracerts;
    $out = '';
    $ok = openssl_pkcs12_export($leaf, $out, $privkey_pem, $password, $args);
    if (!$ok) throw new Exception("Failed to export PKCS#12: " . openssl_error_string());
    return $out;
}

// --------------------- 简单 HTML 模板（含移动端适配 & 语言切换） ---------------------
function render_header($title = null) {
    $title = $title ?: L('app_title');
    ?>
    <!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title><?php echo htmlspecialchars($title); ?></title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial; background:#f7fafc; color:#111827; margin:0; padding:0; }
            .wrap { max-width:1100px; margin:24px auto; background:#fff; border:1px solid #e5e7eb; border-radius:8px; box-shadow:0 6px 18px rgba(15,23,42,0.06); padding:20px; }
            header { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; flex-wrap:wrap; gap:8px; }
            h1 { margin:0; font-size:20px; }
            nav { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
            nav a { margin-right:0; color:#0369a1; text-decoration:none; }
            nav a.button { background:#0369a1; color:#fff; padding:6px 10px; border-radius:6px; text-decoration:none; }
            table { width:100%; border-collapse:collapse; margin-top:12px; }
            th, td { padding:8px 10px; border-bottom:1px solid #eef2f7; text-align:left; font-size:14px; }
            th { background:#f1f5f9; color:#0f172a; }
            .muted { color:#6b7280; font-size:13px; }
            .notice { background:#fffbeb; border:1px solid #fef3c7; padding:8px; border-radius:6px; margin:12px 0; }
            form input[type="text"], form input[type="email"], form select, form input[type="password"] { padding:6px 8px; border:1px solid #e2e8f0; border-radius:6px; width:320px; max-width:100%; box-sizing:border-box; }
            button, input[type="submit"], a.btn { background:#0369a1; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; text-decoration:none; display:inline-block; }
            pre { background:#0f172a; color:#e6edf3; padding:12px; border-radius:6px; overflow:auto; }
            .small { font-size:13px; color:#374151; }
            .row { margin-top:10px; }
            .actions { display:flex; gap:8px; flex-wrap:wrap; }
            .danger { background:#dc2626; }
            .lang-switch { margin-left:8px; font-size:13px; }
            /* Responsive: mobile-friendly */
            @media (max-width: 700px) {
                .wrap { margin:12px; padding:12px; }
                header { flex-direction:column; align-items:flex-start; }
                nav { width:100%; }
                table { display:block; overflow:auto; white-space:nowrap; }
                th, td { font-size:13px; padding:8px; }
                .actions a, .actions button { width:100%; box-sizing:border-box; text-align:center; }
            }
        </style>
    </head>
    <body>
    <div class="wrap">
    <header>
        <h1><?php echo htmlspecialchars($title); ?></h1>
        <nav>
            <?php if (!empty($_SESSION['logged_in'])): ?>
                <a href="?page=dashboard"><?php echo htmlspecialchars(L('dashboard')); ?></a>
                <a href="?page=new_order"><?php echo htmlspecialchars(L('new_order')); ?></a>
                <a href="?page=certs"><?php echo htmlspecialchars(L('certs')); ?></a>
                <a href="?page=logs"><?php echo htmlspecialchars(L('logs')); ?></a>
                <a href="?page=diagnostics"><?php echo htmlspecialchars(L('diagnostics')); ?></a>
                <a class="button" href="?page=logout"><?php echo htmlspecialchars(L('logout')); ?></a>
            <?php else: ?>
                <a href="?page=login"><?php echo htmlspecialchars(L('login')); ?></a>
            <?php endif; ?>
            <span class="lang-switch">
                <?php $cur = $_SESSION['lang'] ?? 'zh'; ?>
                <?php if ($cur === 'zh'): ?>
                    <strong>中文</strong> | <a href="?page=set_lang&lang=en">English</a>
                <?php else: ?>
                    <a href="?page=set_lang&lang=zh">中文</a> | <strong>English</strong>
                <?php endif; ?>
            </span>
        </nav>
    </header>
    <?php
}

function render_footer() {
    ?>
    <footer class="small muted" style="margin-top:18px;"><?php echo htmlspecialchars(L('app_title')); ?> · <?php echo htmlspecialchars(L('wildcard_note')); ?></footer>
    </div>
    </body>
    </html>
    <?php
}

// --------------------- 页面实现 ---------------------

// Login
if ($action === 'login' || $action === 'do_login') {
    if ($action === 'do_login') {
        $pw = $_POST['password'] ?? '';
        if ($pw === $config->auth_password) {
            $_SESSION['logged_in'] = true;
            header('Location: ?page=dashboard'); exit;
        } else {
            $err = L('password_wrong');
        }
    }
    render_header(L('login_title'));
    if (!empty($err)) echo "<p style='color:#dc2626;'>".htmlspecialchars($err)."</p>";
    ?>
    <form method="post" action="?page=do_login">
        <div><label><?php echo htmlspecialchars(L('password')); ?>: <input type="password" name="password" required></label></div>
        <div class="row"><button type="submit"><?php echo htmlspecialchars(L('login')); ?></button></div>
    </form>
    <?php
    render_footer();
    exit;
}

// Logout
if ($action === 'logout') {
    session_destroy();
    header('Location: ?page=login'); exit;
}

if (empty($_SESSION['logged_in'])) { header('Location: ?page=login'); exit; }

// Dashboard
if ($action === 'dashboard') {
    if ($config->auto_check_on_dashboard) {
        $orders = orders_load_all();
        foreach ($orders as $id => $o) {
            if (!empty($o['cert']['fullchain'])) {
                $cert_pem = $o['cert']['fullchain'];
                $x = openssl_x509_parse($cert_pem);
                if ($x && isset($x['validTo_time_t'])) {
                    $expiry = $x['validTo_time_t'];
                    $days = ($expiry - time())/86400;
                    if ($days < 30 && empty($o['renew_attempted'])) {
                        $orders[$id]['renew_attempted'] = false;
                        orders_save_entry($id, $orders[$id]);
                    }
                }
            }
        }
    }

    $orders = orders_load_all();
    render_header(L('dashboard') . ' - ' . L('app_title'));
    ?>
    <h3><?php echo htmlspecialchars(L('welcome')); ?></h3>
    <p class="small muted"><?php echo htmlspecialchars(L('current_directory')); ?>: <strong><?php echo htmlspecialchars($config->use); ?></strong>。<?php echo htmlspecialchars(L('wildcard_note')); ?></p>

    <h3><?php echo htmlspecialchars(L('certs')); ?></h3>
    <?php if (empty($orders)): ?>
        <p><?php echo htmlspecialchars(L('no_orders')); ?></p>
    <?php else: ?>
        <table>
            <tr><th><?php echo htmlspecialchars(L('id')); ?></th><th><?php echo htmlspecialchars(L('domain')); ?></th><th><?php echo htmlspecialchars(L('type')); ?></th><th><?php echo htmlspecialchars(L('status')); ?></th><th><?php echo htmlspecialchars(L('expires')); ?></th><th><?php echo htmlspecialchars(L('actions')); ?></th></tr>
            <?php foreach ($orders as $id => $o):
                $domain = $o['identifiers'][0]['value'] ?? '(unknown)';
                $is_wild = (strpos($domain, '*.') === 0) ? 'Wildcard' : '';
                $status = $o['status'] ?? $o['acme_order']['status'] ?? 'n/a';
                $expiry = '-';
                if (!empty($o['cert']['fullchain'])) {
                    $xp = openssl_x509_parse($o['cert']['fullchain']);
                    if ($xp && isset($xp['validTo'])) $expiry = $xp['validTo'];
                }
            ?>
            <tr>
                <td><?php echo htmlspecialchars($id); ?></td>
                <td><?php echo htmlspecialchars($domain); ?></td>
                <td><?php echo htmlspecialchars($o['key_type'] ?? ''); ?> <?php echo $is_wild ? ' · 泛域名' : ''; ?></td>
                <td><?php echo htmlspecialchars($status); ?></td>
                <td><?php echo htmlspecialchars($expiry); ?></td>
                <td>
                    <div class="actions">
                        <a href="?page=view_order&id=<?php echo urlencode($id); ?>" class="btn"><?php echo htmlspecialchars(L('view')); ?></a>
                        <a href="?page=renew&id=<?php echo urlencode($id); ?>" class="btn"><?php echo htmlspecialchars(L('renew')); ?></a>
                        <?php
                            $st = strtolower($status);
                            // show cancel when not valid/cancelled/invalid
                            if (!in_array($st, ['valid','cancelled','invalid'])):
                        ?>
                            <a href="?page=cancel&id=<?php echo urlencode($id); ?>" class="btn"><?php echo htmlspecialchars(L('cancel')); ?></a>
                        <?php endif; ?>
                        <a href="?page=delete&id=<?php echo urlencode($id); ?>" class="btn danger"><?php echo htmlspecialchars(L('delete')); ?></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif;
    render_footer();
    exit;
}

// New order form (支持泛域名选择)
if ($action === 'new_order') {
    render_header(L('new_order_title'));
    ?>
    <h3><?php echo htmlspecialchars(L('new_order_title')); ?></h3>
    <form method="post" action="?page=process_order">
        <div><label><?php echo htmlspecialchars(L('email')); ?>: <input type="email" name="email" required></label></div>
        <div class="row"><label><?php echo htmlspecialchars(L('main_domain')); ?>: <input type="text" name="domain" required></label></div>
        <div class="row"><label><?php echo htmlspecialchars(L('extra_domains')); ?>: <input type="text" name="san" /></label></div>
        <div class="row"><label><?php echo htmlspecialchars(L('request_wildcard')); ?>: <input type="checkbox" name="wildcard" value="1" /> <span class="small muted"><?php echo htmlspecialchars(L('wildcard_note')); ?></span></label></div>
        <div class="row"><label><?php echo htmlspecialchars(L('challenge_type')); ?>:
            <select name="challenge" id="challenge">
                <option value="http-01"><?php echo htmlspecialchars(L('http01')); ?></option>
                <option value="dns-01"><?php echo htmlspecialchars(L('dns01')); ?></option>
            </select>
        </label></div>

        <div class="row"><label><?php echo htmlspecialchars(L('key_type')); ?>:
            <select name="key_type">
                <option value="rsa">RSA 2048</option>
                <option value="rsa4096">RSA 4096</option>
                <option value="ecp256">ECC P-256 (prime256v1)</option>
                <option value="ecp384">ECC P-384 (secp384r1)</option>
            </select>
        </label></div>

        <div class="row"><button type="submit"><?php echo htmlspecialchars(L('submit_order')); ?></button></div>
    </form>
    <script>
        // If wildcard checked, force DNS-01 selection client-side (still validated server-side)
        document.querySelector('input[name="wildcard"]')?.addEventListener('change', function(){
            if (this.checked) document.getElementById('challenge').value = 'dns-01';
        });
    </script>
    <p class="small muted" style="margin-top:10px;"><?php echo htmlspecialchars(L('wildcard_note')); ?></p>
    <?php
    render_footer();
    exit;
}

// Process order (POST) - 支持泛域名：如果 wildcard 选中，则添加 *.domain 标识符并强制 DNS 验证
if ($action === 'process_order') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location:?page=new_order'); exit; }
    $email = $_POST['email'] ?? '';
    $domain = trim($_POST['domain'] ?? '');
    $san_raw = trim($_POST['san'] ?? '');
    $challenge_type = $_POST['challenge'] ?? 'http-01';
    $key_type = $_POST['key_type'] ?? 'rsa';
    $wildcard = !empty($_POST['wildcard']) && $_POST['wildcard'] == '1';

    if (!$email || !$domain) {
        die("Email and main domain are required.");
    }

    if ($wildcard && $challenge_type !== 'dns-01') {
        // enforce DNS-01 for wildcard
        $challenge_type = 'dns-01';
    }

    try {
        $dir = get_acme_directory($config);

        $acct = account_get($email);
        if ($acct) {
            $acct = acme_ensure_account_kid($email, $acct, $dir);
        } else {
            $acct = acme_create_account($email, $dir);
            log_action("New account created for {$email}");
        }

        // build identifiers
        $identifiers = [];
        if ($wildcard) {
            $identifiers[] = ['type'=>'dns','value'=>"*.{$domain}"];
            // also include base domain to get cert for base name (optional but common)
            $identifiers[] = ['type'=>'dns','value'=>$domain];
        } else {
            $identifiers[] = ['type'=>'dns','value'=>$domain];
            if ($san_raw) {
                $more = array_filter(array_map('trim', explode(',', $san_raw)));
                foreach ($more as $m) if ($m) $identifiers[] = ['type'=>'dns','value'=>$m];
            }
        }

        $order = acme_new_order($acct, $dir, $identifiers);

        $order_id = uniqid('ord_');
        $entry = [
            'id'=>$order_id,
            'email'=>$email,
            'identifiers'=>$identifiers,
            'challenge_type'=>$challenge_type,
            'key_type'=>$key_type,
            'wildcard'=>$wildcard,
            'acme_order'=>$order,
            'status'=>'pending',
            'created_at'=>date('c'),
        ];
        orders_save_entry($order_id, $entry);
        log_action("Created order {$order_id} for domain {$domain} (wildcard: " . ($wildcard? 'yes':'no') . ")");

        // fetch authorization URLs
        $auth_urls = $order['authorizations'] ?? ($order['json']['authorizations'] ?? []);
        if (empty($auth_urls) && !empty($order['_order_url'])) {
            $res = acme_get($order['_order_url'], $acct, $dir);
            $auth_urls = $res['json']['authorizations'] ?? [];
        }
        if (empty($auth_urls)) {
            throw new Exception("未能从 ACME 返回 authorizations（order 数据：" . json_encode($order) . "）");
        }

        $challenges = [];
        foreach ($auth_urls as $auth_url) {
            $auth = acme_get($auth_url, $acct, $dir);
            $authj = $auth['json'] ?? null;
            if (empty($authj) || empty($authj['challenges'])) {
                throw new Exception("Authorization 对象不完整或缺少 challenges: " . json_encode($auth));
            }
            // choose challenge (prefer requested type)
            $found = null;
            foreach ($authj['challenges'] as $c) {
                if (isset($c['type']) && $c['type'] === $challenge_type) { $found = $c; break; }
            }
            if (!$found) $found = $authj['challenges'][0];
            if (empty($found['token']) || empty($found['url'])) {
                throw new Exception("挑战信息不完整（缺少 token 或 url）。");
            }
            $token = $found['token'];
            $jwk = jwk_from_pem_rsa($acct['privkey_pem']);
            $jwk_json = json_encode(['e'=>$jwk['e'],'kty'=>$jwk['kty'],'n'=>$jwk['n']]);
            $thumb = b64u(hash('sha256', $jwk_json, true));
            $keyAuth = $token . '.' . $thumb;
            $dns_value = b64u(hash('sha256', $keyAuth, true));
            $challenges[] = [
                'auth_url'=>$auth_url,
                'challenge_url'=>$found['url'],
                'type'=>$found['type'],
                'token'=>$token,
                'keyAuthorization'=>$keyAuth,
                'dns_value'=>$dns_value,
                'status'=>$authj['status'] ?? '',
                'identifier'=>$authj['identifier'] ?? null,
            ];
        }

        $entry['challenges'] = $challenges;
        orders_save_entry($order_id, $entry);

        // Show challenge instructions
        render_header(L('instructions') . ' - ' . $order_id);
        ?>
        <h3><?php echo htmlspecialchars(L('instructions')); ?> - <?php echo htmlspecialchars($order_id); ?></h3>
        <p><?php echo htmlspecialchars(L('instructions')); ?>（<?php echo htmlspecialchars(L('wildcard_note')); ?>）</p>
        <?php foreach($challenges as $i=>$c):
            $identifier = $c['identifier']['value'] ?? ($identifiers[$i]['value'] ?? '');
        ?>
            <hr/>
            <h4><?php echo htmlspecialchars($identifier); ?> （<?php echo htmlspecialchars($c['type']); ?>）</h4>
            <?php if ($c['type'] === 'http-01'): ?>
                <p><?php echo htmlspecialchars(L('http01_file')); ?></p>
                <p><code>http://<?php echo htmlspecialchars($identifier);?>/.well-known/acme-challenge/<?php echo htmlspecialchars($c['token']);?></code></p>
                <pre><?php echo htmlspecialchars($c['keyAuthorization']);?></pre>
            <?php else: ?>
                <p><?php echo htmlspecialchars(L('dns_txt')); ?></p>
                <p><?php echo htmlspecialchars(L('record_name')); ?>: <code>_acme-challenge.<?php echo htmlspecialchars(str_replace('*.', '', $identifier));?></code></p>
                <p><?php echo htmlspecialchars(L('record_value')); ?>:</p>
                <pre><?php echo htmlspecialchars($c['dns_value']);?></pre>
            <?php endif; ?>
            <form method="post" action="?page=verify_challenge" style="margin-top:8px;">
                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id);?>" />
                <input type="hidden" name="challenge_index" value="<?php echo intval($i);?>" />
                <button type="submit"><?php echo htmlspecialchars(L('i_added_start_verify')); ?></button>
            </form>
        <?php endforeach;
        render_footer();
        exit;

    } catch (Throwable $e) {
        log_action("process_order error for {$email}: " . $e->getMessage());
        file_put_contents(__DIR__ . '/logs/debug_error.log', "[".date('c')."] process_order Exception: ".$e->getMessage()."\n", FILE_APPEND);
        render_header('Error');
        echo "<div class='notice'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
        echo "<p class='small'>已记录到 logs/debug_error.log。请检查日志获得更多信息。</p>";
        render_footer();
        exit;
    }
}

// Verify challenge (POST)
if ($action === 'verify_challenge') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location:?page=dashboard'); exit; }
    $order_id = $_POST['order_id'] ?? '';
    $ci = isset($_POST['challenge_index']) ? intval($_POST['challenge_index']) : 0;
    if (!$order_id) die("Missing order_id");
    $orders = orders_load_all();
    if (!isset($orders[$order_id])) die("Order not found");
    $entry = $orders[$order_id];
    $email = $entry['email'];
    $acct = account_get($email);
    $dir = get_acme_directory($config);

    if (!isset($entry['challenges'][$ci])) die("Challenge not found");
    $c = $entry['challenges'][$ci];

    // precheck
    $precheck_ok = false;
    if ($c['type'] === 'http-01') {
        $domain = $c['identifier']['value'];
        $ok = check_http_challenge_path($domain, $c['token'], $c['keyAuthorization']);
        $precheck_ok = $ok;
    } else {
        $domain = '_acme-challenge.' . str_replace('*.', '', $c['identifier']['value']);
        $ok = check_dns_txt($domain, $c['dns_value']);
        $precheck_ok = $ok;
    }

    render_header(L('verify_title'));
    if (!$precheck_ok) {
        echo "<div class='notice'>".htmlspecialchars(L('precheck_failed'))."</div>";
    } else {
        echo "<p class='small'>".htmlspecialchars(L('precheck_ok'))."</p>";
    }

    try {
        $resp = acme_respond_challenge($c['challenge_url'], $acct, $dir);
        log_action("Sent challenge response for order {$order_id}, challenge {$c['challenge_url']}");
    } catch (Exception $e) {
        echo "<div class='notice'>向 ACME 发送挑战响应失败: " . htmlspecialchars($e->getMessage()) . "</div>";
        render_footer(); exit;
    }

    echo "<p class='small'>".htmlspecialchars(sprintf(L('polling'), $config->poll_interval_seconds*$config->poll_max_attempts))."</p>";

    $poll = poll_authorization($c['auth_url'], $acct, $dir, $config->poll_interval_seconds, $config->poll_max_attempts);
    if ($poll['status'] !== 'valid') {
        echo "<div class='notice'>".htmlspecialchars(sprintf(L('verify_failed'), $poll['status']))."</div>";
        echo "<p><a href='?page=view_order&id=" . urlencode($order_id) . "'>".htmlspecialchars(L('view'))."</a></p>";
        render_footer(); exit;
    }

    // finalize if all valid
    $order_url = $entry['acme_order']['_order_url'] ?? null;
    if (!$order_url) { echo "<div class='notice'>".htmlspecialchars(L('finalize_missing'))."</div>"; render_footer(); exit; }
    $o = acme_get($order_url, $acct, $dir);
    $orderj = $o['json'];
    $all_valid = true;
    foreach ($orderj['authorizations'] as $aurl) {
        $ar = acme_get($aurl, $acct, $dir);
        if (!isset($ar['json']['status']) || $ar['json']['status'] !== 'valid') {
            $all_valid = false;
            break;
        }
    }

    if (!$all_valid) {
        echo "<div class='notice'>尚有其它授权未通过，请逐项完成挑战后再 finalize。</div>";
        echo "<p><a href='?page=view_order&id=" . urlencode($order_id) . "'>".htmlspecialchars(L('view'))."</a></p>";
        render_footer(); exit;
    }

    // create CSR & key according to chosen key_type
    $domains = array_map(function($it){ return $it['value']; }, $entry['identifiers']);
    $key_type = $entry['key_type'] ?? 'rsa';
    try {
        $csr_pair = create_csr_and_key($domains, $key_type);
    } catch (Exception $e) {
        echo "<div class='notice'>创建 CSR/私钥失败：" . htmlspecialchars($e->getMessage()) . "</div>";
        render_footer(); exit;
    }
    $csr_b64u = csr_pem_to_b64u_der($csr_pair['csr_pem']);
    $finalize_url = $orderj['finalize'] ?? null;
    if (!$finalize_url) { echo "<div class='notice'>订单缺少 finalize URL。</div>"; render_footer(); exit; }

    $final = acme_finalize_order($finalize_url, $csr_b64u, $acct, $dir);
    if ($final['code'] != 200 && $final['code'] != 202) {
        echo "<div class='notice'>Finalize 请求失败: HTTP {$final['code']}</div>";
        render_footer(); exit;
    }

    // poll order until certificate ready
    $polres = poll_order($order_url, $acct, $dir, $config->poll_interval_seconds, $config->poll_max_attempts);
    if ($polres['status'] !== 'valid') {
        echo "<div class='notice'>订单未能在超时内完成签发: " . htmlspecialchars($polres['status']) . "</div>";
        render_footer(); exit;
    }
    $cert_url = $polres['json']['certificate'] ?? null;
    if (!$cert_url) { echo "<div class='notice'>证书 URL 未返回。</div>"; render_footer(); exit; }

    // download certificate PEM (chain)
    try {
        $cert_pem = acme_get_certificate_pem($cert_url, $acct, $dir);
    } catch (Exception $e) {
        echo "<div class='notice'>下载证书失败：" . htmlspecialchars($e->getMessage()) . "</div>"; render_footer(); exit;
    }

    // save certs and key
    $dom = $domains[0];
    $cert_dir = $config->certs_dir . '/' . preg_replace('/^\*\./','wildcard_',$dom);
    if (!is_dir($cert_dir)) mkdir($cert_dir, 0755, true);
    file_put_contents($cert_dir . '/privkey.pem', $csr_pair['privkey_pem']);
    file_put_contents($cert_dir . '/cert.pem', $cert_pem);
    file_put_contents($cert_dir . '/fullchain.pem', $cert_pem);

    $entry['status'] = 'valid';
    $entry['cert'] = ['fullchain'=>$cert_pem, 'saved_at'=>date('c')];
    $entry['private_key_path'] = $cert_dir . '/privkey.pem';
    orders_save_entry($order_id, $entry);
    log_action("Order {$order_id} completed; cert saved for {$dom}");

    echo "<div class='notice'>".htmlspecialchars(sprintf(L('cert_saved'), $cert_dir))."</div>";
    echo "<p><a href='?page=view_order&id=" . urlencode($order_id) . "'>".htmlspecialchars(L('view_and_download'))."</a> | <a href='?page=dashboard'>".htmlspecialchars(L('dashboard'))."</a></p>";
    render_footer();
    exit;
}

// View order (包含多格式下载表单)
if ($action === 'view_order') {
    $id = $_GET['id'] ?? '';
    if (!$id) { header('Location:?page=dashboard'); exit; }
    $orders = orders_load_all();
    if (empty($orders[$id])) { header('Location:?page=dashboard'); exit; }
    $order = $orders[$id];
    render_header(L('view') . ' - ' . $id);
    ?>
    <h3><?php echo htmlspecialchars(L('view') . ' - ' . $id); ?></h3>
    <table>
        <tr><th><?php echo htmlspecialchars(L('id')); ?></th><td><?php echo htmlspecialchars($id); ?></td></tr>
        <tr><th><?php echo htmlspecialchars(L('domain')); ?></th><td><?php echo htmlspecialchars(implode(', ', array_map(function($it){ return $it['value']; }, $order['identifiers']))); ?></td></tr>
        <tr><th><?php echo htmlspecialchars(L('status')); ?></th><td><?php echo htmlspecialchars($order['status'] ?? $order['acme_order']['status'] ?? 'n/a'); ?></td></tr>
        <tr><th>Created</th><td><?php echo htmlspecialchars($order['created_at'] ?? ''); ?></td></tr>
        <tr><th><?php echo htmlspecialchars(L('key_type')); ?></th><td><?php echo htmlspecialchars($order['key_type'] ?? 'rsa'); ?></td></tr>
        <tr><th>Wildcard</th><td><?php echo (!empty($order['wildcard']) ? 'Yes' : 'No'); ?></td></tr>
    </table>

    <?php if (!empty($order['cert']['fullchain'])): ?>
        <h4>Certificate & Download</h4>
        <p>Certificate issued and saved.</p>
        <form method="post" action="?page=download">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>" />
            <div class="row"><label><?php echo htmlspecialchars(L('download_formats')); ?>:
                <select name="format">
                    <option value="pem_fullchain">PEM - fullchain.pem</option>
                    <option value="pem_cert">PEM - cert.pem</option>
                    <option value="pem_chain">PEM - chain.pem</option>
                    <option value="key">Private key - privkey.pem</option>
                    <option value="pem_combined">PEM - privkey + fullchain</option>
                    <option value="pfx">PKCS#12 (.pfx/.p12)</option>
                </select>
            </label></div>
            <div class="row" id="pfx_pass_row" style="display:none;">
                <label><?php echo htmlspecialchars(L('pfx_password')); ?>: <input type="password" name="pfx_pass" /></label>
            </div>
            <div class="row"><button type="submit"><?php echo htmlspecialchars(L('download_btn')); ?></button></div>
        </form>
        <script>
            document.querySelector('select[name="format"]').addEventListener('change', function(){
                document.getElementById('pfx_pass_row').style.display = (this.value === 'pfx') ? 'block' : 'none';
            });
        </script>
        <p class="small muted"><?php echo htmlspecialchars(L('pfx_note')); ?></p>
    <?php endif; ?>

    <?php if (!empty($order['challenges']) && is_array($order['challenges'])): ?>
        <h4>Challenges</h4>
        <?php foreach ($order['challenges'] as $i=>$c):
            $ident = $c['identifier']['value'] ?? ($order['identifiers'][$i]['value'] ?? '');
        ?>
            <hr/>
            <h5>Item <?php echo $i+1; ?> - <?php echo htmlspecialchars($ident); ?></h5>
            <p><strong>Type:</strong> <?php echo htmlspecialchars($c['type'] ?? ''); ?> &nbsp; <strong><?php echo htmlspecialchars(L('status')); ?>:</strong> <?php echo htmlspecialchars($c['status'] ?? ''); ?></p>
            <?php if (($c['type'] ?? '') === 'http-01'): ?>
                <p><?php echo htmlspecialchars(L('http01_file')); ?></p>
                <p><code>http://<?php echo htmlspecialchars($ident);?>/.well-known/acme-challenge/<?php echo htmlspecialchars($c['token'] ?? ''); ?></code></p>
                <pre><?php echo htmlspecialchars($c['keyAuthorization'] ?? ''); ?></pre>
            <?php else: ?>
                <p><?php echo htmlspecialchars(L('dns_txt')); ?></p>
                <p><?php echo htmlspecialchars(L('record_name')); ?>： <code>_acme-challenge.<?php echo htmlspecialchars(str_replace('*.','',$ident)); ?></code></p>
                <pre><?php echo htmlspecialchars($c['dns_value'] ?? ''); ?></pre>
            <?php endif; ?>
            <form method="post" action="?page=verify_challenge" style="display:inline;">
                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($id); ?>" />
                <input type="hidden" name="challenge_index" value="<?php echo intval($i); ?>" />
                <button type="submit"><?php echo htmlspecialchars(L('i_added_start_verify')); ?></button>
            </form>
            &nbsp;<a href="?page=renew&id=<?php echo urlencode($id); ?>"><?php echo htmlspecialchars(L('renew')); ?></a>
        <?php endforeach; ?>
    <?php else: ?>
        <h4>Challenges</h4>
        <p>No local challenge information saved for this order.</p>
    <?php endif; ?>

    <h4><?php echo htmlspecialchars(L('actions')); ?></h4>
    <div class="actions">
        <a href="?page=renew&id=<?php echo urlencode($id); ?>" class="btn"><?php echo htmlspecialchars(L('renew')); ?></a>
        <?php
            $st = strtolower($order['status'] ?? $order['acme_order']['status'] ?? '');
            if (!in_array($st, ['valid','cancelled','invalid'])):
        ?>
            <a href="?page=cancel&id=<?php echo urlencode($id); ?>" class="btn"><?php echo htmlspecialchars(L('cancel')); ?></a>
        <?php endif; ?>
        <a href="?page=delete&id=<?php echo urlencode($id); ?>" class="btn danger"><?php echo htmlspecialchars(L('delete')); ?></a>
    </div>

    <h4>Order JSON (debug)</h4>
    <pre><?php echo htmlspecialchars(json_encode($order, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></pre>

    <p><a href="?page=dashboard"><?php echo htmlspecialchars(L('dashboard')); ?></a></p>
    <?php
    render_footer();
    exit;
}

// Cancel confirm (GET) and perform cancel (POST)
if ($action === 'cancel') {
    $id = $_GET['id'] ?? '';
    if (!$id) { header('Location:?page=dashboard'); exit; }
    $orders = orders_load_all();
    if (empty($orders[$id])) { header('Location:?page=dashboard'); exit; }
    $order = $orders[$id];
    // show confirmation page
    render_header(L('cancel_confirm_title'));
    ?>
    <h3><?php echo htmlspecialchars(L('cancel_confirm_title')); ?></h3>
    <p><?php echo htmlspecialchars(L('cancel_confirm_msg')); ?></p>
    <form method="post" action="?page=do_cancel">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>" />
        <div class="row">
            <button type="submit"><?php echo htmlspecialchars(L('confirm')); ?></button>
            &nbsp;<a href="?page=view_order&id=<?php echo urlencode($id); ?>" class="btn"><?php echo htmlspecialchars(L('view')); ?></a>
        </div>
    </form>
    <?php
    render_footer();
    exit;
}

if ($action === 'do_cancel') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location:?page=dashboard'); exit; }
    $id = $_POST['id'] ?? '';
    if (!$id) die("Missing id");
    $orders = orders_load_all();
    if (empty($orders[$id])) die("Order not found");
    $order = $orders[$id];
    $order_url = $order['acme_order']['_order_url'] ?? ($order['acme_order']['url'] ?? null);
    $email = $order['email'];
    $acct = account_get($email);
    $dir = get_acme_directory($config);

    if (!$order_url) {
        // cannot call ACME; just mark cancelled locally
        $order['status'] = 'cancelled';
        orders_save_entry($id, $order);
        log_action("Locally marked order {$id} as cancelled (no order_url)");
        header('Location:?page=view_order&id=' . urlencode($id));
        exit;
    }

    try {
        $res = acme_cancel_order($order_url, $acct, $dir);
        // Regardless of server response, mark locally
        $order['status'] = 'cancelled';
        orders_save_entry($id, $order);
        log_action("Cancelled order {$id} via ACME; response HTTP {$res['code']}");
        header('Location:?page=view_order&id=' . urlencode($id));
        exit;
    } catch (Throwable $e) {
        log_action("Failed to cancel order {$id}: " . $e->getMessage());
        render_header('Cancel failed');
        echo "<div class='notice'>取消订单时发生错误: " . htmlspecialchars($e->getMessage()) . "</div>";
        echo "<p><a href='?page=view_order&id=" . urlencode($id) . "'>Return to order</a></p>";
        render_footer();
        exit;
    }
}

// Delete confirm (GET) and perform delete (POST)
if ($action === 'delete') {
    $id = $_GET['id'] ?? '';
    if (!$id) { header('Location:?page=dashboard'); exit; }
    $orders = orders_load_all();
    if (empty($orders[$id])) { header('Location:?page=dashboard'); exit; }
    $order = $orders[$id];
    render_header(L('delete_confirm_title'));
    ?>
    <h3><?php echo htmlspecialchars(L('delete_confirm_title')); ?></h3>
    <p><?php echo htmlspecialchars(L('delete_confirm_msg')); ?></p>
    <form method="post" action="?page=do_delete">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>" />
        <div class="row"><label><input type="checkbox" name="remove_files" value="1" /> <?php echo htmlspecialchars(L('delete_files_checkbox')); ?></label></div>
        <div class="row">
            <button type="submit" class="danger"><?php echo htmlspecialchars(L('delete')); ?></button>
            &nbsp;<a href="?page=view_order&id=<?php echo urlencode($id); ?>" class="btn"><?php echo htmlspecialchars(L('view')); ?></a>
        </div>
    </form>
    <?php
    render_footer();
    exit;
}

if ($action === 'do_delete') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location:?page=dashboard'); exit; }
    $id = $_POST['id'] ?? '';
    $remove_files = !empty($_POST['remove_files']) && $_POST['remove_files'] == '1';
    if (!$id) die("Missing id");
    $orders = orders_load_all();
    if (empty($orders[$id])) die("Order not found");
    $order = $orders[$id];
    // attempt to delete files if requested
    if ($remove_files) {
        $domains = array_map(function($it){ return $it['value']; }, $order['identifiers']);
        $dom = $domains[0] ?? null;
        if ($dom) {
            $cert_dir = $config->certs_dir . '/' . preg_replace('/^\*\./','wildcard_',$dom);
            if (is_dir($cert_dir)) {
                rrmdir($cert_dir);
                log_action("Removed cert files for order {$id} at {$cert_dir}");
            } else {
                log_action("No cert dir to remove for order {$id} at {$cert_dir}");
            }
        }
    }
    // remove order entry
    if (orders_delete_entry($id)) {
        log_action("Deleted order {$id} from local store (remove_files=" . ($remove_files ? 'yes':'no') . ")");
        header('Location:?page=dashboard'); exit;
    } else {
        die("Delete failed");
    }
}

// Download handler (POST) - 根据用户选择导出不同格式
if ($action === 'download') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location:?page=dashboard'); exit; }
    $id = $_POST['id'] ?? '';
    $format = $_POST['format'] ?? 'pem_fullchain';
    $pfx_pass = $_POST['pfx_pass'] ?? '';
    $orders = orders_load_all();
    if (empty($orders[$id])) { header('HTTP/1.1 404 Not Found'); echo "订单未找到"; exit; }
    $o = $orders[$id];
    if (empty($o['cert']['fullchain'])) { header('HTTP/1.1 404 Not Found'); echo "证书不存在"; exit; }
    $cert_pem = $o['cert']['fullchain'];
    $priv_path = $o['private_key_path'] ?? null;
    $privkey_pem = $priv_path && file_exists($priv_path) ? file_get_contents($priv_path) : null;
    $parts = split_pem_chain($cert_pem);
    $leaf = $parts[0] ?? '';
    $chain_only = '';
    if (count($parts) > 1) {
        $chain_only = implode("\n", array_slice($parts, 1));
    }

    try {
        if ($format === 'pem_fullchain') {
            header('Content-Type: application/x-pem-file');
            header('Content-Disposition: attachment; filename="fullchain.pem"');
            echo $cert_pem;
            exit;
        } elseif ($format === 'pem_cert') {
            header('Content-Type: application/x-pem-file');
            header('Content-Disposition: attachment; filename="cert.pem"');
            echo $leaf;
            exit;
        } elseif ($format === 'pem_chain') {
            header('Content-Type: application/x-pem-file');
            header('Content-Disposition: attachment; filename="chain.pem"');
            echo $chain_only ?: '';
            exit;
        } elseif ($format === 'key') {
            if (!$privkey_pem) { header('HTTP/1.1 404 Not Found'); echo "私钥不存在"; exit; }
            header('Content-Type: application/x-pem-file');
            header('Content-Disposition: attachment; filename="privkey.pem"');
            echo $privkey_pem;
            exit;
        } elseif ($format === 'pem_combined') {
            if (!$privkey_pem) { header('HTTP/1.1 404 Not Found'); echo "私钥不存在"; exit; }
            header('Content-Type: application/x-pem-file');
            header('Content-Disposition: attachment; filename="privkey_fullchain.pem"');
            echo $privkey_pem . "\n" . $cert_pem;
            exit;
        } elseif ($format === 'pfx') {
            if (!$privkey_pem) { header('HTTP/1.1 404 Not Found'); echo "私钥不存在"; exit; }
            $pfx = build_pkcs12($cert_pem, $privkey_pem, $pfx_pass);
            header('Content-Type: application/x-pkcs12');
            header('Content-Disposition: attachment; filename="certificate.p12"');
            echo $pfx;
            exit;
        } else {
            header('HTTP/1.1 400 Bad Request'); echo "未知的格式"; exit;
        }
    } catch (Throwable $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo "导出失败: " . htmlspecialchars($e->getMessage());
        exit;
    }
}

// Renew - 创建新的订单（保持原逻辑）
if ($action === 'renew') {
    $id = $_GET['id'] ?? '';
    if (!$id) { header('Location:?page=dashboard'); exit; }
    $orders = orders_load_all();
    if (empty($orders[$id])) die("订单未找到");
    $old = $orders[$id];
    $email = $old['email'];
    $dir = get_acme_directory($config);
    $acct = account_get($email);
    if (!$acct) die("Account not found for {$email}");

    try {
        $order = acme_new_order($acct, $dir, $old['identifiers']);
    } catch (Exception $e) { die("创建续期订单失败: " . $e->getMessage()); }

    $new_id = uniqid('ord_');
    $entry = [
        'id'=>$new_id,
        'email'=>$email,
        'identifiers'=>$old['identifiers'],
        'challenge_type'=>$old['challenge_type'],
        'key_type'=>$old['key_type'] ?? 'rsa',
        'wildcard'=>$old['wildcard'] ?? false,
        'acme_order'=>$order,
        'status'=>'pending',
        'created_at'=>date('c'),
    ];
    orders_save_entry($new_id, $entry);
    log_action("Started renewal order {$new_id} for original {$id}");

    header('Location: ?page=view_order&id=' . urlencode($new_id));
    exit;
}

// Logs
if ($action === 'logs') {
    $logfile = $config->logs_dir . '/actions.log';
    $logs = file_exists($logfile) ? file_get_contents($logfile) : '';
    render_header(L('operation_logs'));
    echo "<h3>".htmlspecialchars(L('operation_logs'))."</h3>";
    echo "<pre style='max-height:400px; overflow:auto;'>" . htmlspecialchars($logs) . "</pre>";
    render_footer();
    exit;
}

// Diagnostics
if ($action === 'diagnostics') {
    render_header(L('env_diag'));
    echo "<h3>".htmlspecialchars(L('env_diag'))."</h3>";
    echo "<pre>";
    echo "PHP Version: " . PHP_VERSION . PHP_EOL;
    echo "SAPI: " . PHP_SAPI . PHP_EOL;
    echo "data_dir: " . $config->data_dir . PHP_EOL;
    echo "certs_dir: " . $config->certs_dir . PHP_EOL;
    echo "logs_dir: " . $config->logs_dir . PHP_EOL;
    echo PHP_EOL;
    echo "Required extensions: openssl: " . (extension_loaded('openssl') ? 'yes' : 'MISSING') . ", curl: " . (extension_loaded('curl') ? 'yes' : 'MISSING') . ", json: " . (extension_loaded('json') ? 'yes' : 'MISSING') . PHP_EOL;
    echo "openssl_pkcs12_export: " . (function_exists('openssl_pkcs12_export') ? 'available' : 'missing') . PHP_EOL;
    echo PHP_EOL;
    $orders = orders_load_all();
    echo "Saved orders: " . count($orders) . PHP_EOL;
    echo "Accounts saved: " . count(json_load($config->data_dir . '/accounts.json')) . PHP_EOL;
    echo "</pre>";
    render_footer();
    exit;
}

header('Location:?page=dashboard');
exit;

?>
