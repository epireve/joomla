<?php
class AdminPraiseModules {
	public function unpublishModule($moduleId) {
		$db = JFactory::getDBO();
		
		$query = 'UPDATE ' . $db->nameQuote('#__modules')
				. ' SET published = 0'
				. ' WHERE id = ' . $db->Quote($moduleId);
		$db->setQuery($query);
		return $db->query();
	}
}
?>
