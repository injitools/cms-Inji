<?php

return function($step = null, $params = []) {
    App::$cur->db->createTable('notifications_notification', [
        'notification_id' => 'pk',
        'notification_chanel_id' => 'int(11) UNSIGNED NOT NULL',
        'notification_name' => 'varchar(255) NOT NULL',
        'notification_text' => 'text NOT NULL',
        'notification_action' => 'text NOT NULL',
        'notification_date_create' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
    ]);
    App::$cur->db->createTable('notifications_chanel', [
        'chanel_id' => 'pk',
        'chanel_name' => 'varchar(255) NOT NULL',
        'chanel_alias' => 'varchar(255) NOT NULL',
        'chanel_path' => 'varchar(255) NOT NULL',
        'chanel_parent_id' => 'int(11) UNSIGNED NOT NULL',
        'chanel_date_create' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
    ]);
    App::$cur->db->createTable('notifications_subscriber', [
        'subscriber_id' => 'pk',
        'subscriber_key' => 'varchar(255) NOT NULL',
        'subscriber_user_id' => 'int(11) UNSIGNED NOT NULL',
        'subscriber_date_create' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
    ]);
    App::$cur->db->createTable('notifications_subscriber_device', [
        'subscriber_device_id' => 'pk',
        'subscriber_device_key' => 'varchar(255) NOT NULL',
        'subscriber_device_subscriber_id' => 'int(11) UNSIGNED NOT NULL',
        'subscriber_device_date_last_check' => 'timestamp NOT NULL DEFAULT 0',
        'subscriber_device_date_create' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ], [
        'UNIQUE INDEX ' . App::$cur->db->table_prefix . 'notifications_subscriber_device_subscriber_device_key (subscriber_device_key)'
    ]);
    App::$cur->db->createTable('notifications_subscribe', [
        'subscribe_id' => 'pk',
        'subscribe_chanel_id' => 'int(11) UNSIGNED NOT NULL',
        'subscribe_subscriber_id' => 'int(11) UNSIGNED NOT NULL',
        'subscribe_date_create' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
    ]);
};
