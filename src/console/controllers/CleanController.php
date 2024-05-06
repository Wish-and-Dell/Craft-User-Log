<?php 
namespace wishanddell\userlog\console\controllers;

use Craft;
use craft\helpers\Console;
use yii\console\Controller;
use yii\console\ExitCode;
use wishanddell\userlog\Plugin;

/**
 * Console command to remove log entries older than X days
 */
class CleanController extends Controller
{
    protected $allowAnonymous = true;
    
    public function actionIndex(): int
    {
        Plugin::getInstance()->log->clean();
        return ExitCode::OK;
    }
}