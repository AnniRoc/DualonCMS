<!--  Produkt Administrations View -->
	<?php
	//LOAD js
	 $this->Html->script('/web_shop/js/admin', false); 
	
	//LOAD style-sheet
	echo $this->Html->css('/web_shop/css/webshop');
	
	//LOAD menu
	echo $this->element('admin_menu', array('contentID' => $contentID));
	?>
	
	<div id="webshop_product_administration">
		<h1>Produkt-Administration</h1>	
		<table>
			<thead>
				<tr>
					<th colspan="3"><p>Produkte</p><?php echo $this->Form->postLink("Neu", array('controller' => 'WebShop', 'action' => 'create', $contentID), array('style' => 'float: right', 'class' => 'webshop_button')); ?></th>
				</tr>
			</thead>
			<?php foreach ($products as $product): ?>
			    <tr>
				    <td><?php echo $product['Product']['name']; ?></td>
				    <td class="webshop_orientation_right"><?php echo $product['Product']['created']; ?></td>
				    <td class="webshop_orientation_right"><?php echo $this->Html->link(
				    			 		$this->Html->image("edit.png", array('width' => '32px')), 
				    					array('action' => 'edit', $contentID, $product['Product']['id']),
				    					array('escape' => False)
				    				);?>
				    	<?php echo $this->Html->link(
				    					$this->Html->image("delete.png", array('width' => '32px')), 
				    					array('action' => 'remove', $contentID, $product['Product']['id']),
				    					array('escape' => False)
				    				);?>
				    </td>
			    </tr>
			<?php endforeach; ?>
		</table>
	</div>