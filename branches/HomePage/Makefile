all: production

production:
	#######################################
	# Updating Production Environment
	@ rsync -rlv --exclude='.*' --exclude='instfiles' --exclude='Makefile' ./ zo13de@shell.berlios.de:/home/groups/yafa/htdocs
	@ ssh zo13de@shell.berlios.de "find /home/groups/yafa/htdocs -type d -exec chmod a+rx {} \; &"
	@ ssh zo13de@shell.berlios.de "find /home/groups/yafa/htdocs -type f -exec chmod a+r  {} \; &"
	# Finished
	#######################################

development:
	#######################################
	# Updating Production Environment
	@ rsync -rlv --exclude='.*' --exclude='instfiles' --exclude='Makefile' ./ zo13de@shell.berlios.de:/home/groups/yafa/htdocs
	@ ssh zo13de@shell.berlios.de "find /home/groups/yafa/htdocs -type d -exec chmod a+rx {} \; &"
	@ ssh zo13de@shell.berlios.de "find /home/groups/yafa/htdocs -type f -exec chmod a+r  {} \; &"
	# Finished
	#######################################
	