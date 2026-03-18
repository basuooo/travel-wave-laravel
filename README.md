# Travel Wave

Travel Wave is a bilingual travel and tourism website built with Laravel, Blade, MySQL-friendly migrations, and a practical CMS-style admin dashboard.

## Stack

- Laravel 9
- Blade templating
- Bootstrap 5 + custom branded CSS
- MySQL-ready schema
- Laravel storage for uploads
- Bilingual content fields with `*_en` and `*_ar`

Note: this workspace currently runs on PHP `8.0.30`, so Laravel 9 was used as the newest version compatible with the available runtime.

## Included Modules

- Public frontend:
  - Home
  - Overseas visas
  - Visa category pages
  - Single visa country pages
  - Domestic tourism
  - Single destination pages
  - Flights
  - Hotels
  - About
  - Blog list and single article
  - Contact
- Admin dashboard:
  - Admin login
  - Dashboard stats
  - Site settings
  - Singleton page management
  - Visa categories CRUD
  - Visa countries CRUD
  - Destinations CRUD
  - Blog categories CRUD
  - Blog posts CRUD
  - Testimonials CRUD
  - Navigation CRUD
  - Inquiry management

## Seeded Content

The seeder includes content based on the bilingual Travel Wave content file, including:

- Homepage content
- France visa sample page
- Multiple visa categories
- Sharm El Sheikh sample page
- Flights, hotels, about, contact, and blog singleton pages
- Blog posts
- Testimonials
- Navigation items
- Site settings
- Sample inquiry
- Admin user

## Admin Credentials

- Email: `admin@travelwave.test`
- Password: `password123`

## Setup

1. Install dependencies:

```bash
composer install
```

2. Configure your `.env` file for MySQL.

3. Generate the storage symlink:

```bash
php artisan storage:link
```

4. Run migrations and seeders:

```bash
php artisan migrate --seed
```

5. Start the local server:

```bash
php artisan serve
```

6. Open:

- Website: `http://127.0.0.1:8000`
- Admin: `http://127.0.0.1:8000/admin/login`

## Testing

Run the feature tests with:

```bash
php artisan test
```

## Uploads

Uploaded files are stored on Laravel's `public` disk and served through the `public/storage` symlink.

## Notes

- The site supports Arabic and English through a real locale switcher.
- Arabic pages render in RTL.
- Public content is driven from database records rather than hardcoded Blade copy.
