<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MailSettingsService
{
    protected static function filePath(): string
    {
        return storage_path('app/mail_settings.json');
    }

    public static function getSettings(): array
    {
        $fileData = [];
        $file = static::filePath();

        if (File::exists($file)) {
            try {
                $fileData = json_decode(File::get($file), true) ?: [];
            } catch (Throwable $e) {
                // Ignore json read errors
            }
        }

        $dbData = [];
        try {
            if (Schema::hasTable('settings')) {
                $setting = Setting::query()->first();
                if ($setting) {
                    $columns = Schema::getColumnListing('settings');
                    $keys = [
                        'notification_emails',
                        'notification_email_mode',
                        'mail_mailer',
                        'mail_host',
                        'mail_port',
                        'mail_username',
                        'mail_password',
                        'mail_encryption',
                        'mail_from_address',
                        'mail_from_name',
                    ];

                    foreach ($keys as $key) {
                        if (in_array($key, $columns, true) && filled($setting->{$key})) {
                            $dbData[$key] = $setting->{$key};
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            // Ignore DB read errors
        }

        return array_merge([
            'notification_emails' => null,
            'notification_email_mode' => 'assigned_and_custom',
            'mail_mailer' => 'smtp',
            'mail_host' => null,
            'mail_port' => '587',
            'mail_username' => null,
            'mail_password' => null,
            'mail_encryption' => 'tls',
            'mail_from_address' => null,
            'mail_from_name' => 'Travel Wave',
        ], $fileData, $dbData);
    }

    public static function saveSettings(array $input): array
    {
        $current = static::getSettings();

        $keys = [
            'notification_emails',
            'notification_email_mode',
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
        ];

        $updated = $current;
        foreach ($keys as $key) {
            if (array_key_exists($key, $input)) {
                $updated[$key] = $input[$key];
            }
        }

        // 1. Always save to storage/app/mail_settings.json
        try {
            $directory = dirname(static::filePath());
            if (! File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
            File::put(static::filePath(), json_encode($updated, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (Throwable $e) {
            report($e);
        }

        // 2. Try to save to DB table settings if columns exist or can be created
        try {
            if (Schema::hasTable('settings')) {
                static::ensureColumnsExist();

                $setting = Setting::query()->firstOrCreate([]);
                $columns = Schema::getColumnListing('settings');
                $dbPayload = [];

                foreach ($updated as $key => $val) {
                    if (in_array($key, $columns, true)) {
                        $dbPayload[$key] = $val;
                    }
                }

                if (! empty($dbPayload)) {
                    $setting->forceFill($dbPayload)->save();
                }
            }
        } catch (Throwable $e) {
            report($e);
        }

        static::applyConfig();

        return $updated;
    }

    public static function cleanPassword(?string $password): ?string
    {
        if (is_null($password)) {
            return null;
        }

        // If password contains spaces (e.g. Google App Password "scok ujev gfgf zjkg"), strip spaces
        return str_replace(' ', '', trim($password));
    }

    public static function applyConfig(): void
    {
        $s = static::getSettings();

        if (filled($s['mail_host'])) {
            $host = trim($s['mail_host']);
            $port = (int) ($s['mail_port'] ?: 587);
            $rawEncryption = strtolower(trim($s['mail_encryption'] ?? 'tls'));

            if ($rawEncryption === 'null' || $rawEncryption === 'none' || empty($rawEncryption)) {
                $encryption = null;
            } elseif ($port === 465 && ($rawEncryption === 'tls' || $rawEncryption === 'ssl')) {
                $encryption = 'ssl';
            } else {
                $encryption = $rawEncryption;
            }

            $username = trim($s['mail_username'] ?? '');
            $password = static::cleanPassword($s['mail_password'] ?? '');

            config([
                'mail.default' => $s['mail_mailer'] ?: 'smtp',
                'mail.mailers.smtp' => [
                    'transport' => 'smtp',
                    'host' => $host,
                    'port' => $port,
                    'encryption' => $encryption,
                    'username' => $username,
                    'password' => $password,
                    'timeout' => 15,
                ],
            ]);
        }

        if (filled($s['mail_from_address'])) {
            config([
                'mail.from.address' => trim($s['mail_from_address']),
                'mail.from.name' => $s['mail_from_name'] ?: config('app.name', 'Travel Wave'),
            ]);
        }

        try {
            \Illuminate\Support\Facades\Mail::purge();
        } catch (Throwable $e) {
            // Ignore if Mail facade not booted yet
        }
    }

    protected static function ensureColumnsExist(): void
    {
        try {
            $columns = Schema::getColumnListing('settings');
            $mailColumns = [
                'notification_emails' => 'TEXT NULL',
                'notification_email_mode' => "VARCHAR(50) NOT NULL DEFAULT 'assigned_and_custom'",
                'mail_mailer' => "VARCHAR(50) NULL DEFAULT 'smtp'",
                'mail_host' => 'VARCHAR(255) NULL',
                'mail_port' => 'VARCHAR(10) NULL',
                'mail_username' => 'VARCHAR(255) NULL',
                'mail_password' => 'VARCHAR(255) NULL',
                'mail_encryption' => 'VARCHAR(20) NULL',
                'mail_from_address' => 'VARCHAR(255) NULL',
                'mail_from_name' => 'VARCHAR(255) NULL',
            ];

            foreach ($mailColumns as $col => $definition) {
                try {
                    if (! in_array($col, $columns, true)) {
                        DB::statement("ALTER TABLE `settings` ADD COLUMN `{$col}` {$definition}");
                    }
                } catch (Throwable $e) {
                    // Ignore alter table error if restricted by host
                }
            }
        } catch (Throwable $e) {
            // Ignore schema error
        }
    }
}
