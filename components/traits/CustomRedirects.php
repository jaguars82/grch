<?php

namespace app\components\traits;

use app\components\exceptions\AppException;

/**
 * Description of customRedirects
 */
trait CustomRedirects 
{
    /**
     * Redirect to given url and setting success flash message
     * 
     * @param string $url redirected url
     * @param string $message success message
     * @return Response
     */
    protected function redirectWithSuccess($url, $message)
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
            \Yii::$app->response->data = [
                'message' => $message,
            ];
            
            return;
        }
        
        \Yii::$app->session->setFlash('success', $message);
        
        return $this->redirect($url);
    }
    
    /**
     * Redirect to given url and setting error flash message
     * 
     * @param string $url redirected url
     * @param string $message error message
     * @return Response
     */
    protected function redirectWithError($url, $message)
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            \Yii::$app->response->statusCode = 400;
            \Yii::$app->response->data = [
                'message' => $message,
            ];
            
            return;
        }
        
        \Yii::$app->session->setFlash('error', $message);
        
        return $this->redirect($url);
    }
    
    /**
     * Redirect back and setting exception flash message
     * 
     * @param \Exception $e
     * @return Response
     */
    protected function redirectBackWhenException(\Exception $e)
    {        
        // echo '<pre>'; var_dump($e); echo '</pre>'; die();
        return $this->redirectWithError(\Yii::$app->request->referrer, ($e instanceof AppException) ? $e->getMessage() : 'Произошла ошибка. Обратитесь в службу поддержки');
    }
}
