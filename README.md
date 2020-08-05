# SmsRT (Laravel Notification channel for sms.rt.ru)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/SmsRT.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/SmsRT)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/romach3/SmsRT-Channel/SmsRT.svg?style=flat-square)](https://travis-ci.org/github/romach3/SmsRT-Channel)
[![StyleCI](https://github.styleci.io/repos/285345276/shield?branch=SmsRT)](https://github.styleci.io/repos/285345276/shield?branch=SmsRT)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/:sensio_labs_id.svg?style=flat-square)](https://insight.sensiolabs.com/projects/:sensio_labs_id)
[![Quality Score](https://img.shields.io/scrutinizer/g/romach3/SmsRT-Channel.svg?style=flat-square)](https://scrutinizer-ci.com/g/romach3/SmsRT-Channel/)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/SmsRT.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/SmsRT)

This package makes it easy to send notifications using [SMS Rostelecom](https://sms.rt.ru) with Laravel 5.5+, 6.x and 7.x

## Contents

- [Installation](#installation)
	- [Setting up the SmsRT service](#setting-up-the-SmsRT-service)
- [Usage](#usage)
	- [Available Message methods](#available-message-methods)
	- [Manual Usage](#manual-usage)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

Install this package with Composer:

```bash
composer require laravel-notification-channels/SmsRT
```

The service provider gets loaded automatically. Or you can do this manually:

```php
// config/app.php
return [
    '...',
    'providers' => [
        '...',
        NotificationChannels\SmsRT\SmsRTServiceProvider::class,
    ],
    '...',
];
```

### Setting up the SmsRT service

Add your SMS RT login, password and shortcode to your `config/services.php`:

```php
// config/services.php
return [
    '...',
    'SmsRT' => [
        'login'  => env('SMSRT_LOGIN'),
        'password' => env('SMSRT_PASSWORD'),
        'shortcode' => env('SMSRT_SHORTCODE'),
    ],
    '...',
];
```

## Usage

You can use the channel in your `via()` method inside the notification:

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\SmsRT\SmsRTMessage;
use NotificationChannels\SmsRT\SmsRTChannel;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [SmsRTChannel::class];
    }

    public function toSmsRT($notifiable)
    {
        return new SmsRTMessage("Task #{$notifiable->id} is complete!");
    }
}
```

In your notifiable model, make sure to include a `routeNotificationForSmsRT()` method, which returns a phone number
or an array of phone numbers.

```php
public function routeNotificationForSmsRT()
{
    return $this->phone;
}
```

### Available Message methods

`setContent()`: Set a content of the notification message.

### Manual Usage

You can usage this package to send SMS or check status manually.

```php
$api = new \NotificationChannels\SmsRT\SmsRTApi('shortcode', 'login', 'password');
// or get instance with default params
app()->make(\NotificationChannels\SmsRT\SmsRTApi::class);

// Send  SMS
$message = new \NotificationChannels\SmsRT\SmsRTMessage('MESSAGE_BODY');
$result = $api->smsSend('79541234567', $message);
$messageId = $result['uid'];

// Get SMS Status
$result = $api->smsStatus($messageId);
$status = $result['status'];
```

See [sms.rt.ru/help](https://sms.rt.ru/help) for more details. 

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

You can use real API for test, see `tests/.example.env`

## Security

If you discover any security related issues, please email romach3@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [romach3](https://github.com/:author_username)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
