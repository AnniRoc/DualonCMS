<?php
/**
 * Component for WebShopComponent.
 *
 * @author Patrick Zamzow
 * @version 26.12.2011
 */
class WebShopComponent extends Component {
	
   /**
	* Method to transfer data from plugin to CMS.
	*/
	public function getData($controller, $params, $url, $contentId, $myUrl)
	{		
		//CHECK url
		if (isset($url)){
			$data['Element'] = array_shift($url);
		} else {
			$data['Element'] = 'productOverview';
		}
		
		//CALL corresponding comp. method
		if (method_exists($this, $data['Element'])){
			$func_data = $this->{$data['Element']}($controller, $url, $params, $myUrl);
			if (isset($func_data['data'])) {
				$data['data'] = $func_data['data'];
			}
			if (isset($func_data['Element'])) {
				$data['Element'] = $func_data['Element'];
			}
		}
		
		//RETURN data
		if (!isset($data['data'])) { $data['data'] = null; }
			
		return $data;
	}
	
	/**
	 * Product-Overview.
	 */
	function productOverview($controller, $url_params, $contentValues){
		
		//LOAD model
		$controller->loadModel("Product");
		
		//Default NumberOfEntries
		if(!isset($contentValues['NumberOfEntries']))
			$contentValues['NumberOfEntries'] = 5;
			
		//PAGINATION options
		$controller->paginate = array('order' => array( 'Product.created' => 'desc'),
						       	  'limit' => $contentValues['NumberOfEntries']);
		
		//Result data
		$result['Product'] = $controller->paginate('Product');
		$result['Limit'] = $contentValues['NumberOfEntries'];
		
		//RETURN results for view
		return array('data' => $result);
	}
	
   /**
	* Product-Search.
	*/
	function search($controller, $url_params, $contentValues){
		
		//LOAD model
		$controller->loadModel('Product');
		
		//DATA from request
		if (!empty($controller->data)) {
			
			//PAGINATION options
			$controller->paginate = array(
					        'conditions' => array('MATCH(Product.name,Product.description) AGAINST("'.$controller->data['Search']['Suche'].'" IN BOOLEAN MODE)'),
					        'limit' => $contentValues['NumberOfEntries']
			);
			
			//WRITE search-key to session
			$controller->Session->write('searchkey', $controller->data['Search']['Suche']);
			
			//RETURN results for view
			return array('data' => $controller->paginate('Product'));
		}
		
		//DATA from session
		$search_key = $controller->Session->read('searchkey');
		
		if (!empty($search_key)){
			
			//PAGINATION options
			$controller->paginate = array(
								        'conditions' => array('MATCH(Product.name,Product.description) AGAINST("'.$search_key.'" IN BOOLEAN MODE)'),
								        'limit' => $contentValues['NumberOfEntries']
			);
			
			//RETURN results for view
			return array('data' => $controller->paginate('Product'));
		}
	}
	
   /**
	* Dislays product details.
	*/
	function view($controller, $id=null) {
		
		//LOAD model
		$controller->loadModel('Product');
		
		//RETURN product
		return array('data' => $controller->Product->findById($id));
	}
	
   /**
	* Displays all the products of shopping cart.
	*/
	function cart($controller) {
		
		//ATTRIBUTES
		$data = array();
		
		//LOAD model
		$controller->loadModel('Product');
		
		//GET all IDs (+ amount) from session
		$productIDs = $controller->Session->read('products');
		
		//COLLECT data
		foreach ((!isset($productIDs)) ? array() : $productIDs as $productID) {
			$product = $controller->Product->findById($productID['id'], array('fields' => 'Product.id, Product.name, Product.price, Product.picture'));
			$product['count'] = $productID['count'];
			array_push($data, $product);
		}
		
		//RETURN products
		return array('data' => $data);
	}
	
   /**
	* Adds product to shopping cart.
	*/
	function add($controller, $id=null, $contentValues=null, $url=null) {
		
		//ATTRIBUTES
		$productIDs = $controller->Session->read('products');
		$positon = array();
		$results = false;
	
		//CHECK existing products in cart
		for($i = 0; $i < count($productIDs); $i++){
			if ($productIDs[$i]['id'] == $id){
				$productIDs[$i]['count'] = $productIDs[$i]['count'] + 1;
				$results = true;
				break;
			}
		}

		//ADD if new
		if(!$results){
			$positon['id'] = $id;
			$positon['count'] = 1;
				
			if ($productIDs == null) {
				$productIDs[0] = $positon;
			} else {
				array_push($productIDs, $positon);
			}
		}
		
		//SORT
		sort($productIDs);
			
		//WRITE to SESSION		
		$controller->Session->write('products', $productIDs);
		
		//REDIRECT to cart
		$controller->redirect($url.'/webshop/cart');
	}
	
   /**
	* Removes product from shopping cart.
	*/
	function remove($controller, $id=null, $contentValues=null, $url=null) {
		
		//GET all IDs (+ amount) from session
		$productIDs = $controller->Session->read('products');
	
		//REMOVE prod. from cart
		for($i = 0; $i < count($productIDs); $i++){
			if ($productIDs[$i]['id'] == $id){
				
				if($productIDs[$i]['count'] == 1)
					unset($productIDs[$i]);
				else
					$productIDs[$i]['count'] = $productIDs[$i]['count'] - 1;
				
				break;
			}
		}
		
		//SORT
		sort($productIDs);
	
		//WRITE to SESSION
		$controller->Session->write('products', $productIDs);
	
		//REDIRECT to cart
		$controller->redirect($url.'/webshop/cart');
	}
	
	/**
	 * Submit oder to Administrator.
	 */
	function submitOrder($controller, $id=null, $contentValues=null, $url=null){
		
		//LOAD model
		$controller->loadModel('Product');
		
		//GET all IDs (+ amount) from session
		$productIDs = $controller->Session->read('products');
		
		//BUILD mail
		App::uses('CakeEmail', 'Network/Email');
		$email = new CakeEmail();
		$email->template('WebShop.order', 'email')
			  ->emailFormat('html')
			  ->to('maximilian.stueber@me.com')
	          ->from('maximilian.stueber@me.com'/*'noreply@'.env('SERVER_NAME'), env('SERVER_NAME')*/)
			  ->subject('Order')
			  ->viewVars(array(
		        	'order' => $productIDs,
					'url' => 'localhost'/*env('SERVER_NAME')*/,
		))
		->send();
		
		//UNSET cart
		$controller->Session->write('products', null);
		
		//REDIRECT to cart
		$controller->redirect($url.'/webshop/cart');
	}	
}