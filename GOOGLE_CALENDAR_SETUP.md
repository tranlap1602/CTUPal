# Google Calendar API Integration - Setup Instructions

## 1. Install Dependencies

```bash
composer install
```

## 2. Google API Setup

1. Go to [Google Cloud Console](https://console.developers.google.com/)
2. Create a new project or select existing project
3. Enable Google Calendar API
4. Create OAuth 2.0 credentials:
   - Application type: Web application
   - Authorized redirect URIs: `http://your-domain.com/google-auth.php`
5. Copy Client ID and Client Secret

## 3. Configuration

Update `config/google.php` with your credentials:

```php
define('GOOGLE_CLIENT_ID', 'your-client-id.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'your-client-secret');
```

## 4. Usage Flow

1. User clicks "Kết nối Google Calendar" button
2. OAuth2 flow redirects to Google for authentication
3. User selects "Lịch học CTU" calendar from the list
4. User can view and edit events in the selected calendar

## 5. Files Created

- `composer.json` - Dependency management
- `config/google.php` - Google API configuration
- `google-auth.php` - OAuth2 authentication handler
- `calendar-list.php` - Display and select calendars
- `calendar-events.php` - Show events from selected calendar
- `edit-event.php` - Edit event form and handler
- `calendar.php` - Updated with new integration (backward compatible)

## 6. Features

- OAuth2 authentication with Google Calendar
- Calendar selection (prioritizes "Lịch học CTU")
- Event listing with upcoming events
- Event editing (title, time, location, description)
- Backward compatibility with existing iframe embedding
- Session-based token storage
- Error handling and user feedback