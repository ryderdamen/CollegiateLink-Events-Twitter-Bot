.PHONY: build
build:
	@echo "Building & Tagging"; \
	docker build -t gcr.io/radical-sloth/brock-events .; \

.PHONY: push
push:
	@echo "Pushing to GCR"; \
	docker push gcr.io/radical-sloth/brock-events
