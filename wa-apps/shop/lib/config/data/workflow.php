<?php

return array(
    'states'  => array(
        'new'        => array(
            'name'              => _w('New'),
            'options'           => array(
                'icon'  => 'icon16 ss new',
                'style' => array(
                    'color'       => '#009900',
                    'font-weight' => 'bold',
                ),
            ),
            'available_actions' => array(
                'process',
                'initpay',
                'pay',
                'ship',
                'complete',
                'comment',
                'edit',
                'editcode',
                'editshippingdetails',
                'message',
                'delete',
            ),
        ),
        'processing' => array(
            'name'              => _w('Confirmed'),
            'options'           => array(
                'icon'  => 'icon16 ss confirmed',
                'style' => array(
                    'color'      => '#008800',
                    'font-style' => 'italic',
                ),
            ),
            'available_actions' => array(
                'initpay',
                'pay',
                'ship',
                'complete',
                'comment',
                'edit',
                'editcode',
                'editshippingdetails',
                'message',
                'delete',
            ),
        ),
        'auth'       => array(
            'name'              => _w('Payment is authorized'),
            'options'           => array(
                'icon'  => 'icon16 ss flag-white',
                'style' => array(
                    'color'      => '#008800',
                    'font-style' => 'italic',
                ),
            ),
            'available_actions' => array(
                'capture',
                'cancel',
                'comment',
                'message',
            ),
        ),
        'paid'       => array(
            'name'              => _w('Paid'),
            'options'           => array(
                'icon'  => 'icon16 ss flag-yellow',
                'style' => array(
                    'color'       => '#FF9900',
                    'font-weight' => 'bold',
                    'font-style'  => 'italic',
                ),
            ),
            'available_actions' => array(
                'ship',
                'editcode',
                'editshippingdetails',
                'complete',
                'refund',
                'comment',
                'message',
            ),
        ),
        'shipped'    => array(
            'name'              => _w('Sent'),
            'options'           => array(
                'icon'  => 'icon16 ss sent',
                'style' => array(
                    'color'      => '#0000FF',
                    'font-style' => 'italic',
                ),
            ),
            'available_actions' => array(
                'editcode',
                'editshippingdetails',
                'complete',
                'comment',
                'delete',
                'message',
            ),
        ),
        'completed'  => array(
            'name'              => _w('Completed'),
            'options'           => array(
                'icon'  => 'icon16 ss completed',
                'style' => array(
                    'color' => '#800080',
                ),
            ),
            'available_actions' => array(
                'editcode',
                'comment',
                'refund',
                'message',
            ),
        ),
        'pos'        => array(
            'name'              => _w('POS order'),
            'options'           => array(
                'icon'  => 'icon16 ss new',
                'style' => array(
                    'color'       => '#bae1ba',
                ),
            ),
            'available_actions' => array(
                'process',
                'initpay',
                'pay',
                'ship',
                'complete',
                'comment',
                'edit',
                'editcode',
                'editshippingdetails',
                'message',
                'delete',
            ),
        ),
        'refunded'   => array(
            'name'              => _w('Refunded'),
            'options'           => array(
                'icon'  => 'icon16 ss refunded',
                'style' => array(
                    'color' => '#cc0000',
                ),
            ),
            'available_actions' => array(
                'message',
            ),
        ),
        'deleted'    => array(
            'name'              => _w('Deleted'),
            'options'           => array(
                'icon'  => 'icon16 ss trash',
                'style' => array(
                    'color' => '#aaaaaa',
                ),
            ),
            'available_actions' => array(
                'restore',
                'message',
            ),
        ),

    ),
    'actions' => array(
        'create'              => array(
            'classname' => 'shopWorkflowCreateAction',
            'internal'  => true,
            'name'      => _w('Create'),
            'options'   => array(
                'log_record' => _w('Order was placed'),
            ),
            'state'     => 'new',
        ),
        'process'             => array(
            'classname' => 'shopWorkflowProcessAction',
            'name'      => _w('Process'),
            'options'   => array(
                'log_record'   => _w('Order was confirmed and accepted for processing'),
                'button_class' => 'green',
                'description'  => sprintf(_w('Order status will be changed to “%s”.'), _w('Processing')),
            ),
            'state'     => 'processing',
        ),
        'pay'                 => array(
            'classname' => 'shopWorkflowPayAction',
            'name'      => _w('Paid'),
            'options'   => array(
                'log_record'   => _w('Order was paid'),
                'button_class' => 'yellow',
                'description'  => sprintf(_w('Order status will be changed to “%s”.'), _w('Paid'))
                    .' '._w('A payment date will be saved.'),
            ),
            'state'     => 'paid',
        ),
        'initpay'       => array(
            'classname' => 'shopWorkflowInitpayAction',
            'name'      => _w('Initiate POS payment'),
            'options'   => array(
                'log_record'   => _w('Order sent to payment terminal in manual mode'),
                'button_class' => 'green',
                'description'  => _w('Sends order to payment terminal in manual mode.'),
                'position' => 'top',
                'icon' => 'fas fa-fax',
            ),
        ),
        'ship'                => array(
            'classname' => 'shopWorkflowShipAction',
            'name'      => _w('Sent'),
            'options'   => array(
                'log_record'   => _w('Order was shipped'),
                'button_class' => 'blue',
                'description'  => sprintf(_w("Order status will be changed to “%s”."), _w('Sent')),
            ),
            'state'     => 'shipped',
        ),
        'refund'              => array(
            'classname' => 'shopWorkflowRefundAction',
            'name'      => _w('Refund'),
            'options'   => array(
                'log_record'   => _w('Order was refunded'),
                'button_class' => 'red',
                'description'  => sprintf(_w("Order status will be changed to “%s”."), _w('Refunded'))
                    .' '._w('Quantities of ordered products and their SKUs, if non-empty, will be increased accordingly. Order payment date will be cleared.'),
            ),
            'state'     => 'refunded',
        ),
        /*
                'split' => array(
                    'classname' => 'shopWorkflowSplitAction',
                    'name' => _w('Split order'),

                ),
        */
        'edit'                => array(
            'classname' => 'shopWorkflowEditAction',
            'name'      => _w('Edit order'),
            'options'   => array(
                'position'   => 'top',
                'icon'       => 'edit',
                'log_record' => _w('Order was edited'),
            ),
        ),
        'editcode' => array(
            'classname' => 'shopWorkflowEditcodeAction',
            'name' => _w('Edit product codes'),
            'options' => array(
                'position' => 'top',
                'icon' => 'ss parameter',
                'log_record' => _w('Product codes were changed'),
            ),
        ),
        'editshippingdetails' => array(
            'classname' => 'shopWorkflowEditshippingdetailsAction',
            'name'      => _w('Edit shipping details'),
            'options'   => array(
                'position'   => 'top',
                'icon'       => 'clock',
                'log_record' => _w('Shipping details changed'),
            ),
        ),
        'delete'              => array(
            'classname' => 'shopWorkflowDeleteAction',
            'name'      => _w('Delete'),
            'options'   => array(
                'log_record'  => _w('Order was deleted'),
                'description' => sprintf(_w('Order status will be changed to “%s”.'), _w('Deleted'))
                    .' '._w('Quantities of ordered products and their SKUs, if non-empty, will be increased accordingly. Order payment date will be cleared.'),
            ),
            'state'     => 'deleted',
        ),
        'restore'             => array(
            'classname' => 'shopWorkflowRestoreAction',
            'name'      => _w('Restore'),
            'options'   => array(
                'icon'         => 'restore',
                'log_record'   => _w('Order was re-opened'),
                'button_class' => 'green',
                'description'  => _w('Order status will be changed to the one the order had before deletion.'),
            ),
        ),
        'complete'            => array(
            'classname' => 'shopWorkflowCompleteAction',
            'name'      => _w('Mark as Completed'),
            'options'   => array(
                'log_record'   => _w('Order was marked as completed'),
                'button_class' => 'purple',
                'description'  => sprintf(_w('Order status will be changed to “%s”.'), _w('Completed'))
                    .' '._w('A payment date will be saved.'),
            ),
            'state'     => 'completed',
        ),

        'message' => array(
            'classname' => 'shopWorkflowMessageAction',
            'name'      => _w('Contact customer'),
            'options'   => array(
                'position'   => 'top',
                'icon'       => 'email',
                'log_record' => _w('Message was sent'),
            ),
        ),

        'comment'  => array(
            'classname' => 'shopWorkflowCommentAction',
            'name'      => _w('Add comment'),
            'options'   => array(
                'position'     => 'bottom',
                'icon'         => 'add',
                'button_class' => 'inline-link',
                'log_record'   => _w('A comment was added for the order'),
            ),
        ),
        'callback' => array(
            'classname' => 'shopWorkflowCallbackAction',
            'internal'  => true,
            'name'      => _w('Callback'),
            'options'   => array(
                'log_record' => _w('Callback'),
            ),

        ),
        'auth' => array(
            'classname' => 'shopWorkflowAuthAction',
            'internal'  => true,
            'name'      => _w('Authorize payment'),
            'state'     => 'auth',
            'options'   => array(
                'log_record' => _w('Payment was authorized'),
            ),

        ),
        'settle'   => array(
            'classname' => 'shopWorkflowSettleAction',
            'internal'  => true,
            'name'      => _w('Merge'),
            'options'   => array(
                'log_record' => _w('Order was settled'),
            ),
        ),
        'cancel'   => array(
            'classname' => 'shopWorkflowCancelAction',
            'internal'  => true,
            'name'      => _w('Cancel payment'),
            'state'     => 'refunded',
            'options'   => array(
                'button_class' => 'red',
                'log_record'   => _w('Order payment was canceled'),
            ),
        ),
        'capture'  => array(
            'classname' => 'shopWorkflowCaptureAction',
            'internal'  => true,
            'state'     => 'paid',
            'name'      => _w('Capture payment'),
            'options'   => array(
                'button_class' => 'red',
                'log_record'   => _w('Order payment was captured'),
            ),
        ),
    ),
);
