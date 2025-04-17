<?php

class shopSetDeleteMethod extends shopApiMethod
{
    protected $method = 'POST';

    public function execute()
    {
        $id = $this->post('id', true);
        $set_model = new shopSetModel();
        $set = $set_model->getById($id);
        if (!$set) {
            throw new waAPIException('invalid_param', _w('Set not found.'), 404);
        }
        $set_model->delete($id);
        $this->response = true;
    }
}
