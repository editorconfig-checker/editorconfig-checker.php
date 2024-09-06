docker-run:
	docker run -it --rm --volume=$$PWD:/usr/src/myapp -u $$(id -u):$$(id -g) --name editorconfig-checker editorconfig-checker /bin/bash
