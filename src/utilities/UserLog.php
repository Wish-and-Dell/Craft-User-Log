<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace wishanddell\userlog\utilities;

use Craft;
use craft\base\Utility;

class UserLog extends Utility
{
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('app', 'User Log');
    }

    /**
     * @inheritdoc
     */
    public static function id(): string
    {
        return 'user-log';
    }

    /**
     * @inheritdoc
     */
    public static function iconPath()
    {
        return Craft::getAlias('@wishanddell/userlog/icon.svg');
    }

    /**
     * @inheritdoc
     */
    public static function contentHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('userlog/utility', [
            'logs' => \wishanddell\userlog\Plugin::getInstance()->log->getLog(40)
        ]);
    }
}
