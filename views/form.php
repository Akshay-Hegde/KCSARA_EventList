<ul>
	<li class="odd">
		<label>Sidebar Link</label>
		<?php echo form_input(array('name'=>'sidebar_link', 'value' => $options['sidebar_link'])); ?>
	</li>
	<li class="even">
		<label>Sidebar Article</label>
		<?php echo form_textarea(array('name'=>'sidebar_article', 'value' => $options['sidebar_article'])); ?>
	</li>
</ul>