docker-run:
	docker run -it --rm --volume=$$PWD:/usr/src/myapp -u $$(id -u):$$(id -g) --name ec ec /bin/bash
