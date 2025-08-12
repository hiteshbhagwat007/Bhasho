## Multi-Vendor Platform (Core PHP + MySQL)

### Requirements
- PHP 8.1+
- MySQL 5.7+/MariaDB 10.4+
- Web server pointing `public/` as document root

### Setup
1. Create database and import schema:
   - Create DB `multivendor` or set your own and update `app/config/config.php` or ENV vars.
   - Import `database/schema.sql` into your DB.
2. Configure DB credentials:
   - Via environment variables: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`, optional `APP_BASE_URL`.
   - Or edit `app/config/config.php` defaults.
3. Serve the app:
   - Point your web root to `public/`.
   - Or run with PHP's dev server:
     ```bash
     php -S 0.0.0.0:8080 -t public
     ```

### Login Credentials
- Admin: `admin@example.com` / `admin123`
- Register vendors via onboarding (login as seeded vendor not provided; create one directly in DB or extend registration).
- Distributors can log in after you create a user in DB with role `distributor`.

### Features
- Role-based auth and dynamic navigation
- Vendor onboarding stepper
- Product CRUD (vendor) with admin approval
- Enquiry list and submission (distributor)
- Vendor responds with offers; distributor accepts/rejects/requests revision
- Basic dashboards/analytics (counts)

### File Structure
- `public/` front controller, assets
- `app/config/` configuration
- `app/lib/` helpers, db, auth
- `app/pages/` role pages
- `app/partials/` header, nav, footer
- `database/schema.sql` schema + seed
- `docs/flowchart.mmd` Mermaid flowchart

### Notes
- This is an MVP scaffold. Extend validations, file uploads, pagination, security hardening, and analytics as needed.