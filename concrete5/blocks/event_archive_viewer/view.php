<div class="container">
	<div class="row">

		<?php
		$blockWidth = intval(12 / $this->controller->getNumEntries());
		$cssColTypeMap = $this->controller->getCSSColType();
		for ($i=0; $i < $this->controller->getNumEntries(); $i++) {
			$output = "<div class=\" eventFrame ".$cssColTypeMap[$blockWidth];
			if ($i == $this->controller->getNumEntries()-1) {
				$output .= " last";
			}
			$output .= "\">\n";
			$output .= '<div style="background: black; padding:10px;">';
			$output .= "<img src=\"".$this->controller->getFieldFromNthMostRecentEvent('Image',$i)."\" alt=\"Test\" />\n";
			$output .= '<p style="color:white;">'.$this->controller->getFieldFromNthMostRecentEvent('Event Name',$i)
						.'</p><p style="color:white;">'.$this->controller->getFieldFromNthMostRecentEvent('Date',$i).'</p>';
			$output .= "</div></div>\n";
			echo $output;
		} 
		?>
	</div>
</div>