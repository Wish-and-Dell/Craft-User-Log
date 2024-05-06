<?php 
namespace wishanddell\userlog\controllers;

use Craft;
use craft\web\Controller;
use craft\elements\Entry;
use wishanddell\userlog\Plugin;

class LogController extends Controller
{
    /**
     * Remove the selected log entries
     */
    public function actionRemove()
    {
        $this->requireCpRequest();
        
        $params = Craft::$app->request->getBodyParams();
        
        if (!isset($params['log']))
            return $this->redirectToPostedUrl();
            
        $logIds = $params['log'];
        Plugin::getInstance()->log->remove($logIds);
        
        return $this->redirectToPostedUrl();
    }
}