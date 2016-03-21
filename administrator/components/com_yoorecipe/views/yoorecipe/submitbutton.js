Joomla.submitbutton = function(task)
{
	if (task == '')
	{
		return false;
	}
	else
	{
		var isValid=true;
		var action = task.split('.');
		if (action[1] != 'cancel' && action[1] != 'close')
		{
			isValid = adminFormValidator.validate();
		}
		
		if (isValid)
		{
			Joomla.submitform(task);
			return true;
		}

		return false;
	}
}