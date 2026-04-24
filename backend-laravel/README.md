# DarNa Laravel Backend

Main application for the DarNa public site and admin backoffice.

## Local Run

Start the stack from the project root:

```powershell
powershell -ExecutionPolicy Bypass -File ..\scripts\start-darna-stack.ps1
```

Or run Laravel directly:

```powershell
C:\xampp\php\php.exe artisan serve --host=127.0.0.1 --port=8000
```

## Production Hosting

1. Copy [.env.production.example](/c:/Users/badie/OneDrive/Desktop/pro/backend-laravel/.env.production.example) to `.env`.
2. Set your real domain in `APP_URL` and `SESSION_DOMAIN`.
3. Fill real database and SMTP credentials.
4. Keep `APP_ENV=production` and `APP_DEBUG=false`.
5. If your host uses a reverse proxy or load balancer, keep `TRUSTED_PROXIES=*` or replace it with the proxy IPs.
6. Keep `SECURITY_FORCE_HTTPS=true` and install an SSL certificate on the server.
7. Run:

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan app:create-admin "Your Name" you@example.com "StrongPassword123!"
php artisan optimize
```

## Security Notes

- Admin pages and reservation listing are admin-only.
- Public registration can be disabled in production with `ALLOW_PUBLIC_REGISTRATION=false`.
- Login, registration, password reset, and reservation creation are rate-limited.
- Security headers and HTTPS redirects are applied through application middleware.
- CORS is locked to `APP_URL` in production unless you override `CORS_ALLOWED_ORIGINS`.
