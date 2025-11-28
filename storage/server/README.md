# Local Development Server Configurations

This directory is used when running in **local** or **dev** mode (`APP_ENV=local` or `APP_ENV=dev`).

## Purpose

In local/development environments, the application writes all server configurations here instead of directly to system directories (`/etc/nginx/`, `/etc/php/`, etc.). This provides several benefits:

1. **Safety**: No risk of accidentally breaking system configurations
2. **No sudo required**: Direct file operations without elevated privileges
3. **Easy inspection**: All configs are in your project directory
4. **Version control optional**: You can commit these to test configurations

## Directory Structure

```
storage/server/
├── nginx/
│   ├── sites-available/     # Generated Nginx vhost configs
│   └── sites-enabled/        # Symlinks to enabled sites
├── php/
│   └── {version}/
│       └── pool.d/          # PHP-FPM pool configurations
├── pm2/                     # PM2 ecosystem configs (Node.js)
├── www/
│   └── domain.com/          # Webroot directories (local mode only)
└── logs/
    ├── nginx/               # Nginx access & error logs
    ├── php{version}-fpm/    # PHP-FPM logs
    └── pm2/                 # PM2 application logs
```

## Environment Detection

The system automatically detects your environment from `.env`:

- **Local Mode**: `APP_ENV=local`, `APP_ENV=dev`, or `APP_ENV=development`
  - Writes to `storage/server/`
  - Skips system service reloads (nginx, php-fpm)
  - Uses direct file operations (no sudo)
  - Logs with `[LOCAL]` prefix

- **Production Mode**: `APP_ENV=production`
  - Writes to `/etc/nginx/`, `/etc/php/`, etc.
  - Reloads system services
  - Uses sudo for file operations
  - Requires sudoers configuration

## Checking Generated Configurations

When you create or update a website in local mode:

1. **Nginx config**: `storage/server/nginx/sites-available/{domain}.conf`
2. **PHP-FPM pool** (PHP projects): `storage/server/php/{version}/pool.d/{pool_name}.conf`
3. **PM2 ecosystem** (Node.js projects): `storage/server/pm2/ecosystem.{domain}.config.js`
4. **Webroot** (PHP projects): `storage/server/www/{domain}/` - Auto-created with sample `index.html`
5. **Logs**: `storage/server/logs/nginx/`, `storage/server/logs/php*/`, `storage/server/logs/pm2/`

**Notes:**

- In local mode, webroot is created in `storage/server/www/` for safe testing. In production, it uses the actual path from website settings.
- PM2 ecosystem files are generated for Node.js projects to manage process lifecycle with PM2 process manager.

You can inspect these files to verify the configurations before deploying to production.

### Example Directory Structure After Creating a Website

```
storage/server/
├── www/
│   └── example.com/               # Auto-created webroot (local mode)
│       ├── index.html             # Sample file for testing
│       └── (add your files here)
├── nginx/
│   ├── sites-available/
│   │   └── example.com.conf       # Nginx vhost config
│   └── sites-enabled/
│       └── example.com.conf       # Symlink
├── php/
│   └── 8.2/
│       └── pool.d/
│           └── example_com.conf   # PHP-FPM pool
└── logs/
    ├── nginx/
    │   ├── example.com-access.log
    │   └── example.com-error.log
    └── php8.2-fpm/
        ├── example_com-access.log
        └── example_com-slow.log
```

## Switching to Production

When ready to deploy:

1. Change `APP_ENV=production` in `.env`
2. Ensure sudoers configuration is set up (see PREREQUISITES.md)
3. The application will automatically write to system directories

## .gitignore

By default, this directory's contents are gitignored (except this README):

```
storage/server/*
!storage/server/README.md
```

## Notes

- Generated configs use `storage_path()` for socket and log paths in local mode
- Service reload commands are skipped in local mode
- Nginx/PHP-FPM test commands are mocked in local mode
- All operations are logged with environment markers for easy debugging

## Testing in Local Mode

To test the complete flow in local mode:

1. Set `APP_ENV=local` in `.env`
2. Create a website through the web interface
3. Check `storage/server/nginx/sites-available/` for generated config
4. Check `storage/server/php/*/pool.d/` for PHP-FPM pool config
5. Review the configurations to ensure they're correct
6. When satisfied, switch to `APP_ENV=production` for real deployment

---

**Safety First!** This local mode prevents accidental system configuration changes during development. 🛡️
