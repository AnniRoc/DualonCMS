<?php
App::uses('Sanitize', 'Utility');
$this->Helpers->load('BBCode');
?>

<div id='guestbook_display'>
		
	<?php foreach($data as $GuestbookPost):?>
	
		<div class='guestbook_post border-color1'>		
			<div class='guestbook_post_author'>
				<?php echo Sanitize::html($GuestbookPost['GuestbookPost']['author']) . __d('Guestbook', ' on ') . $this->Time->format('d.m.Y', $GuestbookPost['GuestbookPost']['created']) . __d('Guestbook', ' at ') . $this->Time->format('H:i:s',$GuestbookPost['GuestbookPost']['created'])?>
			</div>				
			<div class='guestbook_post_title'>
				<?php echo Sanitize::html($GuestbookPost['GuestbookPost']['title']);?>
				<?php // creates release and delete links for admins/editors						
					if ($this->PermissionValidation->actionAllowed($pluginId, 'delete')) {
						echo $this->Form->postLink($this->Html->image('/img/delete.png', array( 'alt' => __d('Guestbook','Delete'))),
							array('plugin' => 'Guestbook', 'controller' => 'GuestbookPost', 'action' => 'delete', $contentId, $GuestbookPost['GuestbookPost']['id']),
							array('escape' => false, 'title' => __d('Guestbook','Delete')),
							__d('Guestbook','Do you really want to delete this post?'));
					}
				?>	
			</div>			
			<div class='guestbook_post_text'>
				<?php echo $this->BBCode->transformBBCode(Sanitize::html($GuestbookPost['GuestbookPost']['text']));?>
			</div>			
		</div>
		
	<?php endforeach;?>

	<div class='guestbook_navigation'>
		<?php // Pagination get currnet page and create prev / next page accordingly - $url is used to get working links
			$paging_params = $this->Paginator->params();
			if ($paging_params['count'] > 0){
				echo 'Page ';
				$currentPageNumber = $this->Paginator->current();
				if ($this->Paginator->hasPrev()){
					echo $this->Html->link('<<', $url . '/page:' . ($currentPageNumber - 1));
					echo '&nbsp';
					echo $this->Html->link(($currentPageNumber -1), $url . '/page:' . ($currentPageNumber - 1));
					echo ' | ';
				}
				echo $currentPageNumber;
				if ($this->Paginator->hasNext()){
					echo ' | ';
					echo $this->Html->link(($currentPageNumber +1), $url . '/page:' . ($currentPageNumber + 1));
					echo '&nbsp';
					echo $this->Html->link('>>', $url . '/page:' . ($currentPageNumber + 1));
				}
			}
		?>
	</div>

</div>