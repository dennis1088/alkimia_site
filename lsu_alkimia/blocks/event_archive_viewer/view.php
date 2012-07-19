<div class="container">
	<div class="row">

		<?php
		$blockWidth = intval(12 / $this->controller->getNumEntries());
		$cssColTypeMap = $this->controller->getCSSColType();
		for ($i=0; $i < $this->controller->getNumEntries(); $i++) {
			$output = "<div class=\" event ".$cssColTypeMap[$blockWidth];
			if ($i == $this->controller->getNumEntries()-1) {
				$output .= " last";
			}
			$output .= "\">\n";
			$output .= '<a href="#" class="thumbnail">';
			$output .= "<img src=\"".$this->controller->getFieldFromNthMostRecentEvent('Image',$i)."\" alt=\"Test\" /></a>\n";
			$output .= '<h3>'.$this->controller->getFieldFromNthMostRecentEvent('Event Name',$i)
						.'</h3><p>'.$this->controller->getFieldFromNthMostRecentEvent('Date',$i).'</p>';
			$output .= "</div>\n";
			echo $output;
		} 
		?>
	</div>
</div>