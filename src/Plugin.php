<?php
namespace wishanddell\userlog;

use yii\base\Event;
use Craft;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\web\twig\variables\Cp;
use craft\web\User;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Utilities;

class Plugin extends \craft\base\Plugin
{
    public bool $hasCpSettings = true;
    // public $hasCpSection = true;
    
    public function init()
    {
        parent::init();
        
        // Set the controllerNamespace based on whether this is a console or web request
        if (\Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'wishanddell\\userlog\\console\\controllers';
        } else {
            $this->controllerNamespace = 'wishanddell\\userlog\\controllers';
        }
        
        $this->setComponents([
            'log' => \wishanddell\userlog\services\Log::class,
        ]);
        
        // Register custom utility
        Event::on(Utilities::class,
            Utilities::EVENT_REGISTER_UTILITIES,
            function(RegisterComponentTypesEvent $event) {
              $event->types[] = \wishanddell\userlog\utilities\UserLog::class;
            }
        );
        
        // Register twig variables
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $e) {
            /** @var CraftVariable $variable */
            $variable = $e->sender;
    
            // Attach a service:
            $variable->set('userlog', \wishanddell\userlog\services\Log::class);
        });
        
        // On user login via Craft
        Event::on(
            User::class,
            User::EVENT_AFTER_LOGIN,
            function ($event) {
                try {
                    $ip = \Craft::$app->request->remoteIp;
                    $this->log->onLogin($event->identity, $ip);
                } catch (\Exception $e) {}
            }
        );
        
        // On user login via FutureActivities Rest API plugin
        if (\Craft::$app->plugins->isPluginInstalled('rest')) {
            Event::on(
                \futureactivities\rest\controllers\v1\UserController::class,
                \futureactivities\rest\controllers\v1\UserController::EVENT_AFTER_LOGIN,
                function ($event) {
                    try {
                        $ip = \Craft::$app->request->getParam('ipAddress') ?? \Craft::$app->request->remoteIp;
                        $this->log->onLogin($event->identity, $ip);
                    } catch (\Exception $e) {}
                }
            );
        }
        
        // Hook to display activity tab on user view
        \Craft::$app->view->hook('cp.users.edit', function(array &$context) {
            if (empty($context['user']->id))
                return;
            
            $context['tabs']['activity'] = [
                "label" => "Login Activity",
                "url" => "#activity"
            ];
        });
        \Craft::$app->view->hook('cp.users.edit.content', function(array &$context) {
            if (empty($context['user']->id))
                return;
            
            return \Craft::$app->getView()->renderTemplate('userlog/user', [
                'logs' => \wishanddell\userlog\Plugin::getInstance()->log->getLogByUser($context['user']->id)
            ]);
        });
    }
    
    protected function createSettingsModel(): ?craft\base\Model
    {
        return new \wishanddell\userlog\models\Settings();
    }
    
    protected function settingsHtml(): ?string
    {
        return \Craft::$app->getView()->renderTemplate('userlog/settings', [
            'settings' => $this->getSettings()
        ]);
    }
}