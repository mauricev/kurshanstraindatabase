All runnable pages of php files must begin with 

The edit pages serve a dual role for entering new data and editing existing entries. For editing, they retrieve the info from selected list item (the edit entries list of whatever entity is radio button selected) and populate the appropriate edit fields. Each edit page checks each entry if it's being edited or not to know whether to display an empty entry or filled in from the retrieved info. When saving we pass both the initial info and any edited values so that we can determine if the user made any changes. Only if there were changes do we resave everything. 


