## docker-gtv（GTV 本地开发 Docker 环境）

### 快速开始

在仓库根目录执行：

```bash
cp docker-gtv/.env.example docker-gtv/.env
docker compose --env-file docker-gtv/.env -f docker-gtv/docker-compose.yml up -d --build
```

### 端口映射（避免与现有环境冲突）

- Nginx：`GTV_NGINX_HTTP_PORT`（默认 `8082`）
- MySQL：`GTV_MYSQL_PORT`（默认 `33072`）
- Redis：`GTV_REDIS_PORT`（默认 `6382`）
- Elasticsearch HTTP：`GTV_ES_HTTP_PORT`（默认 `9203`）
- Elasticsearch Transport：`GTV_ES_TRANSPORT_PORT`（默认 `9303`）

### 访问入口

Nginx 的站点根目录是 `gtv/public`，因此 URL 需要带上前缀：

- 官网入口：`http://localhost:${GTV_NGINX_HTTP_PORT}/gtv/public/index.php`
- Android API：`http://localhost:${GTV_NGINX_HTTP_PORT}/gtv/public/api.php`
- PWA：`http://localhost:${GTV_NGINX_HTTP_PORT}/gtv/public/pwa.php`
- 后台入口（如果需要）：`http://localhost:${GTV_NGINX_HTTP_PORT}/gtv/public/d.php`

### 数据库初始化

MySQL 首次启动会自动导入 `gtv/gtv-init.sql`（通过 `docker-gtv/docker-compose.yml` 挂载到 `docker-entrypoint-initdb.d`）。

