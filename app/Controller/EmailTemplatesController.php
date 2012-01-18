<?php
App::uses('AppController', 'Controller');
/**
 * Plugin Controller
 *
 */
class EmailTemplatesController extends AppController
{
	
    function beforeFilter()
    {
        parent::beforeFilter();
        $this->PermissionValidation->actionAllowed(null, 'EmailTemplate', true);
    }

    function index($templateId = Null) {
        $this->layout = 'overlay';
        if(isset($templateId)) {
    		$selectedTemplate = $this->EmailTemplate->find('first', array('conditions' => array('EmailTemplate.id' => $templateId)));
    	} else {
			$selectedTemplate = $this->EmailTemplate->find('first', array('conditions' => array('EmailTemplate.active' => '1')));    		
    	}  	

        $emailTemplateNames = $this->EmailTemplate->find('list', array('fields' => array('EmailTemplate.name')));
        
        $this->set('names', $emailTemplateNames);
        $this->set('selectedTemplate', $selectedTemplate);
    }
    
    function showOrCreate() {
    	if(isset($this->params['data']['ShowTemplate'])) {    		
			$this->redirect('/email_templates/index/'.$this->request->data['EmailTemplate']['id']);
    	}
        if(isset($this->params['data']['CreateTemplate'])) {
        	$this->redirect('/email_templates/create/');
        }
        $this->redirect($this->referer());
    }
    
    function create() {
   		$this->layout = 'overlay';
    }
    
    function edit($templateId) {
    	$this->layout = 'overlay';
    	$selectedTemplate = $this->EmailTemplate->find('first', array('conditions' => array('EmailTemplate.id' => $templateId)));
    	$this->set('selectedTemplate', $selectedTemplate);
    }
    
    function save($templateId) {
    	$this->EmailTemplate->set($this->request->data);
    	if(isset($templateId)) {
    		$this->EmailTemplate->set('id',$templateId);
    	}
    	if(!($this->checkContent($this->request->data['EmailTemplate']['content']))) {
    		$this->Session->setFlash(__('Saving failed. You have to include the text "EMAILTEXTCONTENT" once.'));
    		$this->redirect($this->referer());
    	} else {
    		$content = $this->prepareContent($this->request->data['EmailTemplate']['content']);
    		$this->EmailTemplate->set('content',$content);
	    	if ($this->EmailTemplate->save()) {
	        	$this->Session->setFlash(__('Successfully saved'));
	        } else {
	            $this->Session->setFlash(__('Saving failed'));
			}
			if(isset($templateId)) {
				$this->redirect($this->referer());			
			} else {
				$this->redirect('/email_templates/index/');
			}    		
    	}   	
    }
    
	function prepareContent($checkString) {
    	$pattern = "/src=\"\/uploads\//";
		$replacement = "src=\"http://".env('SERVER_NAME')."/uploads/";
		$string = preg_replace($pattern, $replacement, $checkString);
		return $string;
    }
    	
    function checkContent($checkString) {
		$pattern = "/EMAILTEXTCONTENT/";
		if(preg_match($pattern, $checkString) != 0) {
			return true;
		}
		return false;
    }
    
    function delete($templateId) {
    	$selectedTemplate = $this->EmailTemplate->find('first', array('conditions' => array('EmailTemplate.id' => $templateId)));
    	if($selectedTemplate['EmailTemplate']['active'] == '1') {
    		$this->Session->setFlash(__('This template is currently active. You cannot delete active templates.'));
    	} else {
	    	if ($this->EmailTemplate->delete($templateId)) {
	        	$this->Session->setFlash(__('Successfully deleted'));
	        } else {
	            $this->Session->setFlash(__('Deletion failed'));
			}    		
    	}
		$this->redirect($this->referer());
    }
    
    function activate($templateId) {
    	if ($this->request->is('post') || $this->request->is('put')) {
        	$selectedTemplate = $this->EmailTemplate->find('first', array('conditions' => array('EmailTemplate.active' => '1')));
        	$selectedTemplate['EmailTemplate']['active'] = '0';        	
        	$newTemplate = $this->EmailTemplate->find('first', array('conditions' => array('EmailTemplate.id' => $this->request->data['EmailTemplate']['id'])));
        	$newTemplate['EmailTemplate']['active'] = '1';
			if ($this->EmailTemplate->save($newTemplate)) {
				$this->Session->setFlash(__('Successfully saved'));
	        		if($newTemplate['EmailTemplate']['id'] != $selectedTemplate['EmailTemplate']['id']) {
						$this->EmailTemplate->save($selectedTemplate);                	
	        		}
	        } else {
	        	$this->Session->setFlash(__('Saving failed'));
	        }
    	}
    }
}
