<?php

class shopKealabsPluginSettingsAction extends waViewAction
{
    public function execute()
    {
        $plugin = wa()->getPlugin('kealabs');
        $uuid = $plugin->getSettings('uuid');
        $token = $plugin->getSettings('token');
        $secret = $plugin->getSettings('secret');

        if (empty($uuid)) {
            $plugin->setSettings('uuid', self::generateUid());
            $plugin->setSettings('enabled', 1);
        }
        $configured = !(empty($token) || empty($secret));
        $autoconfigured = false;
        if (!$configured) {
            //check if other kealabs plugins are already installed and configured
            //we may reuse settings from them
            try {
                $autoconfigured = self::tryToConfigureFrom("kealabs_search");
                $configured = $autoconfigured;
            } catch (Exception $err) {
                //suppress this as error is expected and means- other Kea Labs plugins are not available
            }
        }
        $this->view->assign('configured', (int)$configured);
        $this->view->assign('autoconfigured', (int)$autoconfigured);
        $this->view->assign('uuid', $plugin->getSettings('uuid'));
        $this->view->assign('enabled', (int)$plugin->getSettings('enabled'));

        if (!$autoconfigured) {
            $this->view->assign('token', $plugin->getSettings('token'));
            $this->view->assign('secret', $plugin->getSettings('secret'));
        }
    }

    private function tryToConfigureFrom($name)
    {
        $pluginOther = wa()->getPlugin($name);
        $token = $pluginOther->getSettings('token');
        $secret = $pluginOther->getSettings('secret');
        $configured = !(empty($token) || empty($secret));
        if ($configured) {
            $this->view->assign('token', $token);
            $this->view->assign('secret', $secret);
            $this->view->assign('enabled', 1);

            shopKealabsPlugin::setSettings('token', $token);
            shopKealabsPlugin::setSettings('secret', $secret);
            shopKealabsPlugin::setSettings('enabled', 1);
        }
        return $configured;
    }

    private static function generateUid()
    {
        return (time() << 24) + rand(0, 2 ^ 16);
    }
}