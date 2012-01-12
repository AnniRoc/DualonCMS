<?php
/**
 * WebShopController
 * 
 * @author Maximilian Stueber and Patrick Zamzow
 *
 */
class WebShopController extends AppController {
	
	//Attributes
	var $components = array('ContentValueManager');
	var $uses = array('WebShop.Product'); 
	var $layout = 'overlay';
	
   /**
	* Function for admin view.
	*/
	public function admin($contentID){
		$this->set('products', $this->Product->find('all'));
		$this->set('contentID', $contentID);
	}
	
   /**
	* Function to create product.
	*/
	public function create($contentID){
		if (isset($this->params['data']['cancel']))
			$this->redirect(array('action' => 'admin', $contentID));
		
		$this->set('contentID', $contentID);
	
		if (isset($this->params['data']['save']) and isset($this->data['Product'])){
			//UPLOAD image
			if (!empty($this->data['Product']['submittedfile']['name']))
				$result = $this->uploadImage($this->data['Product']['submittedfile'], null, true);
			
			if (isset($result)) {
				$file_name = $result['file_name'];
			} else {
				$file_name = 'no_image.png';
			}
			
			//SAVE on DB
			$this->Product->set(array(
						'name' => $this->data['Product']['name'],
						'description' => $this->data['Product']['description'],
						'price' => $this->data['Product']['price'],
						'picture' => $file_name
			));
			
			if ($this->Product->validates()) {
				$this->Product->save();
				//REDIRECT
				$this->redirect(array('action' => 'admin', $contentID));
			}
		}
	}
	
	/**
	* Function to edit product.
	*/
	public function edit($contentID, $productID=null){
		
		//Attributes
		$update_error = false;
		
		//SET id
		$this->Product->id = $productID;
		
		//CHECK request
		if (empty($this->data)) {
			$this->data = $this->Product->read();
			$this->set('contentID', $contentID);
				
			return;
		}
	
		//EDIT product
		if (isset($this->params['data']['save'])) {
	
			//UPDATE db info
			$data_old = $this->Product->read();
			$data_new = $this->data;
			
			//UPLOAD new file (if necessary)			
			if (!empty($data_new['Products']['submittedfile']['name'])){
				$result = $this->uploadImage($data_new['Products']['submittedfile'], $data_old['Product']['picture'], true);
				
				$data_new['Product']['picture'] = $result['file_name'];
				$update_error = $result['error'];
			}
			
			//SET new data
			if(!$update_error){
				$this->Product->set($data_old);
				$this->Product->set($data_new);
			
				//SAVE
				$update_error = !$this->Product->save();
			}
		}
		
		//REDIRECT
		$this->redirect(array('action' => 'admin', $contentID));
	}
	
	
   /**
	* Function to remove product.
	*/
	public function remove($contentID, $productID){
		
		//REMOVE picture
		$data = $this->Product->findById($productID);
		$file_path = WWW_ROOT.'../../plugins/WebShop/webroot//img/products/';
		
		if ($data['Product']['picture'] != 'no_image.png')
			@unlink($file_path.$data['Product']['picture']);
		
		//REMOVE db entry
		$this->Product->delete($productID);
		
		$this->redirect(array('action' => 'admin', $contentID));
	}
	
  
	
   /**
	* Function to upload image.
	*/
	function uploadImage($file, $file_old, $init_creation){
		
		/* FILE */
		$file_path = WWW_ROOT.'../../plugins/WebShop/webroot/img/products/';
		$file_name = str_replace(' ', '_', $file['name']);
		$upload_error = true;
		
		//CREATE folder
		if(!is_dir ($file_path))
			@mkdir($file_path);
			
		//CHECK filetype
		$permitted = array('image/gif','image/jpeg','image/pjpeg','image/png');
	
		foreach($permitted as $type) {
			if($type == $file['type']) {
				$upload_error = false;
				break;
			}
		}
		
		//REMOVE old image
		if(!$init_creation){
			@unlink($file_path.$file_old);
		}
	
		//CHECK filename
		if(file_exists($file_path.'/'.$file_name)) {
			//GET time
			ini_set('date.timezone', 'Europe/London');
			$now = date('Y-m-d-His');
	
			//NEW file-name
			$tmp = explode('.', $file_name);
			$file_name = $tmp[0].$now.'.'.$tmp[1];
		}
	
		//MOVE file
		if(!$upload_error){
			$upload_error = !@move_uploaded_file($file['tmp_name'], $file_path.$file_name);
		}
		
		//RESULT data
		$result['error'] = $upload_error;
		$result['file_name'] = $file_name;
		
		return $result;
	}
	
   /**
	* Function to set content values.
	*/
	public function setContentValues($contentID) {		
		if (!empty($this->data)) {
			if (isset($this->data['ContentValues']['NumberOfEntries'])) {
				$this->ContentValueManager->saveContentValues($contentID, array('NumberOfEntries' => $this->data['ContentValues']['NumberOfEntries']));
			}
		} else {
			$contentVars = $this->ContentValueManager->getContentValues($contentID);
			
			if (isset($contentVars['NumberOfEntries'])) {
				$this->data = array('ContentValues' => array('NumberOfEntries' => $contentVars['NumberOfEntries']));
			}
		}
		
		$this->set('contentID', $contentID);
		$this->render('settings');
	}
	
	/**
	 * Function BeforeFilter.
	 */
	public function beforeFilter(){
		$this->Auth->allow('*');
	}
}