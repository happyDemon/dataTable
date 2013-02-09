<?php
class NotePad_Validation extends Validation {
	
	public function errors($file = NULL, $translate = TRUE)
	{
		if ($file === NULL)
		{
			// Return the error list
			return $this->_errors;
		}
	
		// Create a new message list
		$messages = array();
	
		foreach ($this->_errors as $field => $set)
		{
			list($error, $params) = $set;
	
			// Get the label for this field
			$label = $this->_labels[$field];
	
			if ($translate)
			{
				if (is_string($translate))
				{
					// Translate the label using the specified language
					$label = __($label, NULL, $translate);
				}
				else
				{
					// Translate the label
					$label = __($label);
				}
			}
	
			// Start the translation values list
			$values = array(
					':field' => $label,
					':value' => Arr::get($this, $field),
			);
	
			if (is_array($values[':value']))
			{
				// All values must be strings
				$values[':value'] = implode(', ', Arr::flatten($values[':value']));
			}
	
			if ($params)
			{
				foreach ($params as $key => $value)
				{
					if (is_array($value))
					{
						// All values must be strings
						$value = implode(', ', Arr::flatten($value));
					}
					elseif (is_object($value))
					{
						// Objects cannot be used in message files
						continue;
					}
	
					// Check if a label for this parameter exists
					if (isset($this->_labels[$value]))
					{
						// Use the label as the value, eg: related field name for "matches"
						$value = $this->_labels[$value];
	
						if ($translate)
						{
							if (is_string($translate))
							{
								// Translate the value using the specified language
								$value = __($value, NULL, $translate);
							}
							else
							{
								// Translate the value
								$value = __($value);
							}
						}
					}
	
					// Add each parameter as a numbered value, starting from 1
					$values[':param'.($key + 1)] = $value;
				}
			}
	
			if ($message = Kohana::message($file, "{$field}.errors.{$error}") AND is_string($message))
			{
				// Found a message for this field and error
			}
			elseif ($message = Kohana::message($file, "{$field}.errors.default") AND is_string($message))
			{
				// Found a default message for this field
			}
			elseif ($message = Kohana::message($file, 'errors.'.$error) AND is_string($message))
			{
				// Found a default message for this error
			}
			elseif ($message = Kohana::message('validation', $error) AND is_string($message))
			{
				// Found a default message for this error
			}
			else
			{
				// No message exists, display the path expected
				$message = "{$file}.{$field}.{$error}";
			}
	
			if ($translate)
			{
				if (is_string($translate))
				{
					// Translate the message using specified language
					$message = __($message, $values, $translate);
				}
				else
				{
					// Translate the message using the default language
					$message = __($message, $values);
				}
			}
			else
			{
				// Do not translate, just replace the values
				$message = strtr($message, $values);
			}
	
			// Set the message for this field
			$messages[$field] = $message;
		}
	
		return $messages;
	}
}