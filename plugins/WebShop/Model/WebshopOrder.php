<?php
App::uses('AppModel', 'Model');
/**
 * Order Model.
 */
class WebshopOrder extends AppModel {
	
   /**
	*  DB-Relationship
	*/
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		)
	);
	
	public $hasAndBelongsToMany = array(
        'WebshopProduct' =>
            array(
                'className'              => 'WebshopProduct',
                'joinTable'              => 'webshop_positions',
                'foreignKey'             => 'order_id',
                'associationForeignKey'  => 'product_id',
                'unique'                 => true
            )
    );
	
	
}
