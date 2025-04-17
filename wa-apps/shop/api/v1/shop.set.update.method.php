<?php

class shopSetUpdateMethod extends shopApiMethod
{
    protected $method = 'POST';

    public function execute()
    {
        $id = $this->get('id', true);
        $set_model = new shopSetModel();
        $set = $set_model->getById($id);
        if (!$set) {
            throw new waAPIException('invalid_param', _w('Set not found.'), 404);
        }

        $data = array();
        foreach (array('id', 'name') as $k) {
            if (waRequest::post($k)) {
                $data[$k] = waRequest::post($k);
            }
        }
        if (!$data) {
            throw new waAPIException('invalid_param', sprintf_wp(
                'Missing required parameter: %s.',
                sprintf_wp('“%s” or “%s”', 'id', 'name')
            ), 400);
        }

        $set_model->update($id, $data);

        $method = new shopSetGetInfoMethod();
        $this->response = $method->getResponse(true);
    }
}
