<?php

class shopSetAddMethod extends shopApiMethod
{
    protected $method = 'POST';

    public function execute()
    {
        $id = $this->post('id', true);
        $name = $this->post('name', true);

        $set_model = new shopSetModel();
        if ($set_model->idExists($id)) {
            throw new waAPIException('invalid_param', sprintf_wp('A set with ID “%s” already exists.', $id));
        }

        if ($set_model->add(array(
            'id' => $id,
            'name' => $name
        ))) {
            $_GET['id'] = $id;
            $method = new shopSetGetInfoMethod();
            $this->response = $method->getResponse(true);
        } else {
            throw new waAPIException('server_error', 500);
        }
    }
}
