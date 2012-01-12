<?php
App::uses('AppModel', 'Model');
/**
 * Product Model.
 */
class WebshopProduct extends AppModel {
	
	/**
	 *  Validation
	 */
	public $validate = array(
		        'name' => array(
		        	'rule' => 'notEmpty',
					'required' => true,
		        	'message' => '"Name" ist ein Pflichtfeld.'
		        ),
		        
		        'description' => array(
		        	'rule' => 'notEmpty',
					'required' => true,
			        'message' => '"Beschreibung" ist ein Pflichtfeld.'
		        ),
		        
				'price' => array(
					'rule' => 'numeric',
				    'required' => true,
					'allowEmpty' => false,
				    'message'  => '"Preis" ist eine Zahl.'
				)
	);
	
   /**
	*  DB-Relationship
	*/
	public $hasAndBelongsToMany = array(
        'WebshopProduct' =>
            array(
                'className'              => 'WebshopOrder',
                'joinTable'              => 'webshop_positions',
                'foreignKey'             => 'product_id',
                'associationForeignKey'  => 'order_id',
                'unique'                 => true
            )
    );
}
