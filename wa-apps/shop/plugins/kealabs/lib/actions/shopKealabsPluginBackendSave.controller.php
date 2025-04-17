<?php

class shopKealabsPluginBackendSaveController extends waJsonController
{
    public function execute()
    {
        try {
            $plugin = wa()->getPlugin('kealabs');
            $enabled = waRequest::post('enabled', 0, waRequest::TYPE_INT);
            $token = waRequest::post('token', '', waRequest::TYPE_STRING_TRIM);
            $secret = waRequest::post('secret', '', waRequest::TYPE_STRING_TRIM);
            if (empty($token)) {
                throw new waException('Token is not specified.');
            }
            if (empty($secret)) {
                throw new waException('Secret key is not specified.');
            }
            $plugin->setSettings('enabled', $enabled);
            $plugin->setSettings('token', $token);
            $plugin->setSettings('secret', $secret);
            $this->response['message'] =  'Saved';
            $this->response['enabled'] =  $enabled;
            $this->response['token'] =  $token;
        } catch (Exception $e) {
            $this->setError($e->getMessage());
        }
    }
}