<?php
App::uses('AppModel', 'Model');
/**
 * Product Model.
 */
class WebshopProduct extends AppModel {
	
	/**
	 * Pagination
	 */
	public $paginate = array(
	        'limit' => 10,
	        'order' => array(
	            'WebshopProduct.id' => 'asc'
	)
	);
	
	
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
}
