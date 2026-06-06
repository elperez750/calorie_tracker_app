# Calorie Tracker

A PHP + MySQL web app with a Python (Flask) API for searching foods via FatSecret.

## What each person needs installed

| Software | Purpose |
|----------|---------|
| **PHP 8+** | Web app (use MAMP, XAMPP, or similar) |
| **MySQL** | Database (included with MAMP/XAMPP) |
| **Python 3.9+** | Food search API server |
| **FatSecret API keys** | Food search (see below) |

## Quick start (for you or a friend)

### 1. Clone / copy the project

```bash
git clone <your-repo-url>
cd calorie_tracking_app
```

### 2. Set up MySQL

```bash
mysql -u root -p < database/schema.sql
```

If MySQL has no password (default MAMP/XAMPP):

```bash
mysql -u root < database/schema.sql
```

Edit `CalorieTracker/model/db_config.php` if your MySQL username/password differ:

```php
'username' => 'root',
'password' => '',   // or your MySQL password
```

On MAMP, if connection fails, uncomment the `unix_socket` line in that file.

### 3. Set up the Python API

```bash
cd api
pip install -r requirements.txt
cp .env.example .env
```

Edit `api/.env` with FatSecret credentials:

```
FATSECRET_CLIENT_ID=...
FATSECRET_CLIENT_SECRET=...
```

Start the API (leave this terminal open):

```bash
python app.py
```

You should see:

```
Starting server on http://127.0.0.1:5001
Whitelist this IP in FatSecret if searches fail: x.x.x.x
```

**Test it:** open http://127.0.0.1:5001/diagnostics in a browser. It should say `"search_ok": true`.

### 4. Run the PHP app

**Option A — MAMP / XAMPP**

1. Copy or symlink the `CalorieTracker` folder into your web root (`htdocs` on MAMP).
2. Start Apache + MySQL in MAMP.
3. Open http://localhost/CalorieTracker/

**Option B — PHP built-in server (quick test)**

```bash
cd CalorieTracker
php -S localhost:8080
```

Open http://localhost:8080

### 5. Use the app

1. Register an account
2. Set a calorie goal
3. Search for food (requires the Python API running)
4. Add foods to your daily log

---

## FatSecret API — important for groups

Each machine that runs `python app.py` calls FatSecret from **that computer's public IP**. FatSecret only allows whitelisted IPs.

### Option A: One shared API key (easiest for a team)

1. One person creates a FatSecret developer account.
2. In **Manage API Keys → IP Restrictions**, add `0.0.0.0/0` (allows any IPv4 — **dev/school use only**).
3. Share `FATSECRET_CLIENT_ID` and `FATSECRET_CLIENT_SECRET` with the team (e.g. in class Discord — **never commit to Git**).
4. Each friend copies them into their own `api/.env`.

### Option B: Each person whitelists their own IP

1. Share the same API keys (or each person registers their own).
2. Each person runs `python app.py` and opens http://127.0.0.1:5001/diagnostics.
3. Add the shown `public_ip` to FatSecret IP Restrictions.
4. On campus Wi‑Fi, IP may change — use a range (e.g. `72.233.242.0/24`) or `0.0.0.0/0` for demos.

### Option C: Each person has their own FatSecret account

Everyone registers at https://platform.fatsecret.com/ and uses their own keys in `.env`.

---

## Troubleshooting

| Problem | What to do |
|---------|------------|
| **Database Error** on login/search | MySQL not running, or wrong `db_config.php` credentials. Run `schema.sql`. |
| **Cannot reach the food API server** | Run `cd api && python app.py` in a separate terminal. |
| **IP not whitelisted** | Open `/diagnostics`, add IP to FatSecret, or use `0.0.0.0/0` for dev. |
| **Invalid credentials** | Check `api/.env` — file must be named `.env` (not `.env.example`). |
| **No results found** (with no red error) | Try clearing the calorie min/max filter on the search page. |
| Search worked before, stopped on new Wi‑Fi | Your public IP changed — update FatSecret whitelist. |

---

## Project structure

```
calorie_tracking_app/
├── CalorieTracker/     PHP web app (pages, models, styles)
├── api/                Flask food search API
├── database/           MySQL schema + migrations
└── README.md           This file
```

## What is NOT shared between machines

- MySQL data (each person has their own local database)
- `.env` file (gitignored — each person creates their own)
- FatSecret IP whitelist (each outbound IP must be allowed)

## What CAN be shared

- The whole codebase (Git)
- FatSecret API keys (if using `0.0.0.0/0` or team IP range)
- Setup instructions (this README)

---

## Demo checklist (for presenting)

1. API running (`python app.py`)
2. `/diagnostics` shows `search_ok: true`
3. Register → set goal → search "chicken" → add food → see it on Home
4. Log out and back in — data persists
