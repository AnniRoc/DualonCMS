<?php 

$this->Html->script('jquery/jquery.quicksearch', false); 
$this->Html->script('/newsletter/js/newsletter', false);
echo $this->Html->css('/newsletter/css/newsletter', NULL, array('inline' => false));
$validationErrors = $this->Session->read('Validation.NewsletterRecipient.validationErrors');

echo $this->element('admin_menu', array('contentID' => $contentID));

echo $this->Session->flash('RecipientDeleted');
echo $this->Session->flash('NewsletterRecipient');

echo $this->Form->create('add',array(
	'url' => array(
		'plugin' => 'Newsletter',
		'controller' => 'NewsletterRecipients',
		'action' => 'add')));
echo $this->Form->input('NewsletterRecipient.email', array('label' => 'E-Mail:'));
echo $this->Html->div('validation_error',$validationErrors['email'][0]);
echo $this->Form->end('Add');
echo '</div>';
echo $this->Form->create('search');
echo $this->Form->input('NewsletterRecipient.email', array(
	'label' => 'Search Recipient:',
	'id' => 'search_recipient'));
echo $this->Form->end();
?>
<table id='recipients'>
	<thead>
	<tr>
		<th></th>
		<th>Email</th>
		<th>Username</th>
		<th></th>
	</tr>
	</thead>
	<tbody>
		<?php
		if (isset($recipients)){
			echo $this->Form->create('selectRecipients', array(
				'url' => array(
					'plugin' => 'Newsletter',
					'controller' => 'NewsletterRecipients',
					'action' => 'deleteSelected', $contentID),
				'onsubmit'=>'return confirm("Do you really want to delete the selected recipients?");'));
			foreach($recipients as $recipient){
				echo '<tr>';
					echo '<td>';
					echo $this->Form->checkbox($recipient['NewsletterRecipient']['id']);
					echo '</td>'; 
					echo '<td>'.$recipient['NewsletterRecipient']['email'].'</td>';
					echo '<td>'.$recipient['User']['username'].'</td>';
					echo '<td>';
						echo $this->Html->link($this->Html->image('/app/webroot/img/delete.png', array(
							'height' => 20, 
							'width' => 20, 
							'alt' => __('[x]Delete'))),
							array(
								'plugin' => 'Newsletter', 
								'controller' => 'NewsletterRecipients', 
								'action' => 'delete', $recipient['NewsletterRecipient']['id']),
								array(
									'escape' => false, 
									'title' => __('Delete recipient')),
									__('Do you really want to delete this recipient?'));
					echo '</td>';
				echo '</tr>';
			}//foreach
			echo '</tbody>';
			echo '<tfoot>';	
				echo '<tr>';
					echo '<td>';
					echo $this->Html->image('/app/webroot/img/selected_arrow.png', array(
						'height' => 20,
						'width' => 20));
					echo '</td>';
					echo '<td>';
						echo $this->Form->submit('Delete selection', array(
							'height' => 20,
							'width' => 20,
							'alt' => __('[x]Delete')));
						echo $this->Form->end();
					echo '</td>';
				echo '</tr>';
			echo '</tfoot>';	
		}	
		?>
	
</table>


