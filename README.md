# Pretix Integration for FreeScout

A FreeScout module that looks up Pretix event bookings by customer e-mail address and displays them in the conversation sidebar, with one-click deep links to the order pages in the Pretix backend.

## Features

- Shows all Pretix orders for the conversation's customer email in the sidebar
- Displays order code, event, date, status (Paid / Pending / Cancelled / Expired), total, and ticket count
- Each order card links directly to the order detail page in the Pretix admin
- Loads asynchronously so it never slows down the conversation page
- Configurable via FreeScout's built-in Settings panel — no config file editing required

## Requirements

- FreeScout 1.x (Laravel-based)
- PHP 7.4 or higher
- A Pretix installation (self-hosted or pretix.eu)
- A Pretix API token with **"Can view orders"** permission

## Installation

1. Copy (or `git clone`) this repository into your FreeScout `Modules/` directory and name the folder `PretixIntegration`:

   ```bash
   cd /path/to/freescout/Modules
   git clone https://github.com/bluehairminerboy/pretix-freescout-plugin PretixIntegration
   ```

2. Enable the module:

   ```bash
   php artisan module:enable PretixIntegration
   ```

3. Build FreeScout assets (registers the new route with laroute):

   ```bash
   php artisan freescout:build
   ```

4. Clear the cache:

   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

## Configuration

Navigate to **FreeScout → Settings → Pretix Integration** and fill in:

| Field | Description | Example |
|---|---|---|
| **Pretix Base URL** | Root URL of your Pretix instance | `https://pretix.eu` |
| **API Token** | Token from Pretix Settings → Teams → API Keys | `e1l6gq2ye72th…` |
| **Organizer Slug** | Short name from your Pretix URL | `myorg` |

### Creating a Pretix API Token

1. Log in to your Pretix admin.
2. Go to **Settings → Teams**.
3. Select (or create) a team with **"Can view orders"** for the relevant organizer/events.
4. Open the team and click **"API Keys"** → **"Create new token"**.
5. Copy the token and paste it into the FreeScout settings above.

## How It Works

When a FreeScout agent opens a conversation, the sidebar widget fires a background AJAX request to the FreeScout backend. The backend calls:

```
GET {base_url}/api/v1/organizers/{organizer}/orders/?email={customer_email}&ordering=-datetime
Authorization: Token {api_token}
```

The results (up to 50 most-recent orders) are rendered as cards and injected into the sidebar. Each card links to:

```
{base_url}/control/event/{organizer}/{event_slug}/orders/{order_code}/
```

## Module Structure

```
Modules/PretixIntegration/
├── module.json                          — module metadata
├── composer.json                        — PSR-4 autoload declaration
├── start.php                            — route bootstrap
├── Providers/
│   └── PretixServiceProvider.php        — hooks, settings, assets
├── Http/
│   ├── routes.php
│   └── Controllers/
│       └── PretixController.php         — AJAX endpoint → Pretix API
├── Resources/views/
│   ├── settings.blade.php               — settings form
│   └── partials/
│       ├── sidebar.blade.php            — sidebar placeholder
│       └── orders.blade.php             — rendered order cards
└── Public/
    ├── js/module.js                     — AJAX trigger on page load / PJAX
    └── css/module.css                   — sidebar card styles
```

## License

MIT
