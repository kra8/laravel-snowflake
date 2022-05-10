run:
	docker run -it --rm --name kra8-laravel-snowflake -v $(PWD):/work -w /work laravel-snowflake ash

build:
	docker build -t laravel-snowflake .